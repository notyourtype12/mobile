<?php
session_start();

include '../koneksi.php';

// Cek apakah session NIK ada (sudah login)
if (!isset($_SESSION['NIK'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: ../index.php");
    exit;
}

// Ambil NIK dari session
$nik = $_SESSION['NIK'];

// Query untuk mengambil data RT dan RW berdasarkan NIK
$sql = "SELECT rw FROM master_rt_rw WHERE nik = '$nik'";
$result = mysqli_query($conn, $sql);

// Jika data ditemukan, simpan di session dan tampilkan
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    // Menyimpan data rt dan rw dalam session
    $_SESSION['rw'] = $row['rw'];

} else {
    // Jika tidak ditemukan data RT/RW
    $_SESSION['rw'] = '';

    // Menampilkan pesan jika data tidak ditemukan
    echo "Data RT/RW tidak ditemukan.<br>";
}



// Query untuk menghitung jumlah baris dengan status 'Diajukan'
$rw = $_SESSION['rw'];  // Mendapatkan RW dari session

// Ambil data dari database untuk ditampilkan dalam tabel
$sql2 = "SELECT * FROM `view_pengajuan_diajukan` WHERE status = 'Disetujui RT' AND rw = '$rw'";
$q2 = mysqli_query($conn, $sql2);

// Menangani jika ada parameter id_pengajuan di URL
if (isset($_GET['op']) && $_GET['op'] == 'view' && isset($_GET['id_pengajuan'])) {
    $id_pengajuan = $_GET['id_pengajuan'];
    $sql = "SELECT * FROM `view_pengajuan_diajukan` WHERE id_pengajuan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pengajuan);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
} else {
    $data = null;
}

// Penanganan form penolakan
if (isset($_POST['tolak'])) {
    $id_pengajuan = $_POST['id_pengajuan']; 
    $alasan = $_POST['alasan'];

    if (empty($alasan)) {
        // Redirect with error if alasan is empty
        header("Location: " . $_SERVER['PHP_SELF'] . "?op=view&id_pengajuan=" . $id_pengajuan . "&error=Alasan penolakan harus diisi!");
        exit;
    }

    // Prepare the query to update the status and rejection reason
    $query = "UPDATE master_pengajuan SET status = ?, keterangan_ditolak = ? WHERE id_pengajuan = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $status = "Ditolak"; // Set status as "Ditolak" (Rejected)
        $stmt->bind_param("ssi", $status, $alasan, $id_pengajuan);

        if ($stmt->execute()) {
            // Redirect with success message
            header("Location: suratmasuk.php?success=Alasan penolakan berhasil disimpan!");
            exit;
        } else {
            // Log error if query execution fails
            error_log("Error executing rejection query: " . $stmt->error);
            header("Location: " . $_SERVER['PHP_SELF'] . "?op=view&id_pengajuan=" . $id_pengajuan . "&error=Terjadi kesalahan saat menyimpan alasan.");
            exit;
        }
        $stmt->close();
    } else {
        // Redirect with query preparation error
        header("Location: " . $_SERVER['PHP_SELF'] . "?op=view&id_pengajuan=" . $id_pengajuan . "&error=Gagal menyiapkan query.");
        exit;
    }
}

if (isset($_POST['simpan'])) {
    // Check if the 'id_pengajuan' is set in POST data
    if (isset($_POST['id_pengajuan'])) {
        // Get the id_pengajuan from the form
        $id_pengajuan = $_POST['id_pengajuan']; 
        $status = "Disetujui RW";  // The new status to update

        // Prepare the SQL query with the values directly inserted
        $query = "UPDATE master_pengajuan SET status = '$status' WHERE id_pengajuan = $id_pengajuan";

        // Execute the query
        if ($conn->query($query) === TRUE) {
            // Redirect with success message if the status is updated successfully
            header("Location: suratmasuk.php?success=Status berhasil diperbarui.");
            exit;
        } else {
            // Handle query execution failure
            header("Location: suratmasuk.php?error=Terjadi kesalahan saat memperbarui status.");
            exit;
        }
    } else {
        // If 'id_pengajuan' is not set, handle the error
        header("Location: suratmasuk.php?error=ID Pengajuan tidak ditemukan.");
        exit;
    }
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Masuk</title>
    <!-- Include Bootstrap CSS and SweetAlert -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function showAlert() {
            const urlParams = new URLSearchParams(window.location.search);
            const successMessage = urlParams.get('success');
            const errorMessage = urlParams.get('error');

            if (successMessage) {
                Swal.fire({
                    title: successMessage,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }

            if (errorMessage) {
                Swal.fire({
                    title: 'Gagal',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        }


        // Panggil fungsi saat halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', showAlert);

    </script>
</head>
<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>        
        <div class="main p-3">
            <div class="card">
                <div class="card-body">
                    <h1>Surat Masuk</h1>
                    <div class="d-flex flex-column align-items-end" style="width: 100%;">
                        <div class="input-group mb-3" style="max-width: 400px; border: none;">
                            <input type="text" class="form-control" placeholder="Search..." aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </div>
                    </div>

                    <table class="table table-bordered border-light">
                        <thead class="table-secondary" style="text-align: left;">
                            <tr>
                                <td>No</td>
                                <td>NIK</td>
                                <td>Nama</td>
                                <td>Jenis Surat</td>
                                <td>Waktu pengajuan</td>
                                <td>Status</td>
                                <td>Aksi</td>
                            </tr>
                        </thead>
                        <tbody style="text-align: left;">
                            <?php 
                            $urut = 1;
                            while ($r2 = mysqli_fetch_array($q2)) {
                                $id = $r2['id_pengajuan'];
                                $nik = $r2['nik'];
                                $nama_lengkap = $r2['nama_lengkap'];
                                $surat = $r2['nama_surat'];
                                $tanggal_diajukan = $r2['tanggal_diajukan'];
                                $status = $r2['status'];
                            ?>
                            <tr>
                                <td style="text-align: center;"><?= $urut++ ?></td>
                                <td><?= $nik ?></td>
                                <td><?= $nama_lengkap ?></td>
                                <td><?= $surat ?></td>
                                <td><?= $tanggal_diajukan ?></td>
                                <td><?= $status ?></td>
                                <td>
                                    <a href="?op=view&id_pengajuan=<?= $id ?>" class="btn btn-success">
                                        Proses surat
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Pratinjau Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden input for id_pengajuan -->
                        <input type="hidden" name="id_pengajuan" value="<?= $data ? $data['id_pengajuan'] : '' ?>" <?= isset($_GET['op']) && $_GET['op'] == 'view' ? 'readonly' : '' ?>>
                        <div class="mb-3">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" class="form-control" id="nik" value="<?= $data ? $data['nik'] : '' ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="namalengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="namalengkap" value="<?= $data ? $data['nama_lengkap'] : '' ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="surat" class="form-label">Jenis Surat</label>
                            <input type="text" class="form-control" id="surat" value="<?= $data ? $data['nama_surat'] : '' ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="lahir" class="form-label">Tempat, Tanggal lahir</label>
                            <input type="text" class="form-control" id="lahir" value="<?= $data ? $data['tempat_tanggal_lahir'] : '' ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="jeniskelamin" class="form-label">Jenis Kelamin</label>
                            <input type="text" class="form-control" id="jeniskelamin" value="<?= $data ? $data['jenis_kelamin'] : '' ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="bangsa" class="form-label">Kebangsaan / Agama</label>
                            <input type="text" class="form-control" id="bangsa" value="<?= $data ? $data['warga_agama'] : '' ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="bangsa" class="form-label">Bukti</label>
                            <?php 
                            for ($i = 1; $i <= 8; $i++) {
                                if (!empty($data['foto' . $i])) {
                                    echo '<div class="mb-2">';
                                    echo '<div class="mt-2 text-center">';  // Tambahkan kelas text-center untuk memusatkan gambar
                                    echo '<img src="../../../CRUDVolley/CRUDVolley/uploads/pengajuan/' . htmlspecialchars($data['foto' . $i]) . '" alt="Bukti ' . $i . '" style="max-width: 150px; object-fit: cover; margin: 0 auto;">';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            }
                            ?>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-danger" id="rejectbutton">Tolak</button> -->
                        <button type="submit" name="simpan" class="btn btn-primary">Setuju</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal untuk alasan penolakan -->
        <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="rejectForm" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectModalLabel">Alasan Penolakan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <input type="hidden" name="id_pengajuan" value="<?= $data ? $data['id_pengajuan'] : '' ?>" <?= isset($_GET['op']) && $_GET['op'] == 'view' ? 'readonly' : '' ?>>
                                <textarea class="form-control" id="alasan" name="alasan" rows="4" value="<?= $alasan ?>" required ></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" name="tolak" class="btn btn-danger">Kirim Alasan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

 <script>
    $(document).ready(function() {
    let isRejectButtonClicked = false; // Flag untuk mendeteksi klik tombol tolak

    const urlParams = new URLSearchParams(window.location.search);

    // Tampilkan modal jika ada parameter 'op=view'
    if (urlParams.get('op') === 'view') {
        $('#addDataModal').modal('show');
    }

    // Tombol Tolak
    $('#rejectbutton').on('click', function() {
        $('#addDataModal').modal('hide');
        $('#rejectModal').modal('show');
        isRejectButtonClicked = true;
    });

    // Hapus parameter URL saat modal ditutup
    $('#addDataModal, #rejectModal').on('hidden.bs.modal', function () {
        if (!isRejectButtonClicked && urlParams.get('op') === 'view') {
            urlParams.delete('op');
            urlParams.delete('id_pengajuan');
            window.history.replaceState(null, null, '?' + urlParams.toString());
        }
        isRejectButtonClicked = false;
    });
});

</script>
    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
