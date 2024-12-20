<?php
session_start();

if (!isset($_SESSION['NIK'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: ../index.php");
    exit;
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Keluarga</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</head>

<?php
include '../koneksi.php';

// Inisialisasi variabel untuk form
$no_kk = $nik = $nama_kepala_keluarga = $alamat = $rw = $rt = $kodepos = $desa = $kecamatan = $kabupaten = $provinsi = $tanggal_dibuat = "";

// Mengecek apakah sedang dalam mode edit
if (isset($_GET['op']) && $_GET['op'] === 'edit' && isset($_GET['no_kk'])) {
    $no_kk = $_GET['no_kk'];
    
    // Query untuk mengambil data dari master_kartukeluarga dan master_penduduk berdasarkan no_kk
    $sql1 = "SELECT kk.*, p.nama_lengkap, p.nik 
             FROM master_kartukeluarga kk 
             LEFT JOIN master_penduduk p ON kk.no_kk = p.no_kk 
             WHERE kk.no_kk = '$no_kk' AND p.status_keluarga = 'Kepala Keluarga'";
    
    $q1 = mysqli_query($conn, $sql1);
    if ($q1 && mysqli_num_rows($q1) > 0) {
        $r1 = mysqli_fetch_array($q1);
        $nik = $r1['nik'];
        $nama_kepala_keluarga = $r1['nama_lengkap'];
        $alamat = $r1['alamat'];
        $rw = $r1['rw'];
        $rt = $r1['rt'];
        $kodepos = $r1['kode_pos'];
        $desa = $r1['desa'];
        $kecamatan = $r1['kecamatan'];
        $kabupaten = $r1['kabupaten'];
        $provinsi = $r1['provinsi'];
        $tanggal_dibuat = $r1['tanggal_dibuat'];
    } else {
        echo "";
    }
}

// Ketika tombol simpan ditekan
if (isset($_POST['simpan'])) {
    $nik = $_POST['nik'];
    $nama_kepala_keluarga = $_POST['nama'];
    $no_kk = $_POST['no_kk'];
    $alamat = $_POST['alamat'];
    $rt = $_POST['rt'];
    $rw = $_POST['rw'];
    $desa = $_POST['desa'];
    $kecamatan = $_POST['kecamatan'];
    $kodepos = $_POST['kode_pos'];
    $kabupaten = $_POST['kabupaten'];
    $provinsi = $_POST['provinsi'];
    $tanggal_dibuat = $_POST['tanggal'];

    $op = isset($_GET['op']) ? $_GET['op'] : '';

    if ($op == 'edit') {
        // Proses update data
        try {
            $conn->begin_transaction(); // Memulai transaksi

            $sql = "UPDATE master_kartukeluarga SET 
                    alamat='$alamat', 
                    rt='$rt', 
                    rw='$rw', 
                    desa='$desa', 
                    kecamatan='$kecamatan', 
                    kode_pos='$kodepos', 
                    kabupaten='$kabupaten', 
                    provinsi='$provinsi', 
                    tanggal_dibuat='$tanggal_dibuat' 
                    WHERE no_kk='$no_kk'";

            $query_update_penduduk = "UPDATE master_penduduk SET 
                    nama_lengkap = '$nama_kepala_keluarga', 
                    status_keluarga = 'Kepala Keluarga', 
                    no_kk = '$no_kk' 
                    WHERE nik = '$nik'";
            
            if ($conn->query($sql) === TRUE && $conn->query($query_update_penduduk) === TRUE) {
                $conn->commit(); // Menyimpan perubahan
                echo "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil edit data',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = 'masterkk.php';
                            }
                        });
                    });
                </script>";
            }
        } catch (mysqli_sql_exception $e) {
            $conn->rollback(); // Membatalkan perubahan jika terjadi error
            $errorMessage = $e->getMessage();
            echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal edit data',
                        html: '<b>Error:</b> " . addslashes($errorMessage) . "',
                        confirmButtonText: 'OK'
                    });
                });
            </script>";
        }
    } else {
        // Simpan data ke tabel master_kartukeluarga
        try {
            $conn->begin_transaction(); // Memulai transaksi

            $query_kk = "INSERT INTO master_kartukeluarga (no_kk, alamat, rt, rw, desa, kecamatan, kode_pos, kabupaten, provinsi, tanggal_dibuat)
            VALUES ('$no_kk', '$alamat', '$rt', '$rw', '$desa', '$kecamatan', '$kodepos', '$kabupaten', '$provinsi', '$tanggal_dibuat')";

            // Simpan data ke tabel master_penduduk
            $query_penduduk = "INSERT INTO master_penduduk (nik, nama_lengkap, status_keluarga, no_kk)
            VALUES ('$nik', '$nama_kepala_keluarga', 'Kepala Keluarga', '$no_kk')";

            if ($conn->query($query_kk) === TRUE && $conn->query($query_penduduk) === TRUE) {
                $conn->commit(); // Menyimpan perubahan
                echo "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil tambah data',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = 'masterkk.php';
                            }
                        });
                    });
                </script>";
            }
        } catch (mysqli_sql_exception $e) {
            $conn->rollback(); // Membatalkan perubahan jika terjadi error
            $errorMessage = $e->getMessage();
            echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal tambah data',
                        html: '<b>Error:</b> " . addslashes($errorMessage) . "',
                        confirmButtonText: 'OK'
                    });
                });
            </script>";
        }
    }
}




// Jika tombol Delete diklik
if (isset($_GET['deleteData'])) {
    $id = $_GET['nokk']; 

    // Query untuk menghapus data dari tabel master_penduduk berdasarkan no_kk
    $query_delete_penduduk = "DELETE FROM master_penduduk WHERE no_kk = '$id'";

    // Query untuk menghapus data dari tabel master_kartukeluarga berdasarkan no_kk
    $query_delete_kk = "DELETE FROM master_kartukeluarga WHERE no_kk = '$id'";

    if ($conn->query($query_delete_penduduk) === TRUE && $conn->query($query_delete_kk) === TRUE) {
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil hapus data',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location = 'masterkk.php';
                    }
                });
            });
        </script>";
    } else {
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal hapus data',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
    }
}
?>


<body>
    <div class="wrapper">
         <?php include 'sidebar.php'; ?>
        <div class="main p-3">
            <div class="card">
                <div class="card-body">
                    <h1>Master Kartu Keluarga</h1>

                    <div class="d-flex flex-column align-items-end" style="width: 100%;">
                        <button id="addDataBtn" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addDataModal">
                            <i class="bi bi-plus-lg"></i> Tambah Data
                        </button>
                        <div class="input-group mb-3" style="max-width: 300px;">
                            <input type="text" class="form-control" placeholder="Search..." aria-label="Search" aria-describedby="basic-addon2">
                        </div>
                    </div>

                    <table class="table table-bordered border-light">
                        <thead class="table-secondary" style="text-align: left;">
                            <tr>
                                <td style="width: 4%;">No</td>
                                <td>No KK</td>
                                <td>Nama Lengkap</td>
                                <td style="width: 30%;">Alamat</td>
                                <td style="width: 4%;">RW</td>
                                <td style="width: 4%;">RT</td>
                                <td style="width: 15%;">Aksi</td>
                            </tr>
                        </thead>
                        <tbody style="text-align: left;">
                            <?php 
                            $sql2   = "select * from view_kartu_keluarga";
                            $q2     = mysqli_query($conn, $sql2);
                            $urut   = 1;
                            while ($r2 = mysqli_fetch_array($q2)) {
                                $kk       = $r2['no_kk'];
                                $nama_kepala   = $r2['nama_kepala'];
                                $almt     = $r2['alamat'];
                                $erwe     = $r2['rw'];
                                $erte     = $r2['rt'];
                            ?>
                            <tr>
                                <td style="text-align: center;"><?= $urut++ ?></td>
                                <td><?= $kk ?></td>
                                <td><?= $nama_kepala ?></td>
                                <td><?= $almt ?></td>
                                <td><?= $erwe ?></td>
                                <td><?= $erte ?></td>
                                <td>
                                    <a href="?op=edit&no_kk=<?= $kk ?>" class="btn btn-secondary" style="width: 50px;" data-bs-target="#addDataForm">
                                    <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="javascript:void(0)" data-nokk="<?= $kk ?>" class="btn btn-danger delete-btn" style="width: 50px;">
                                        <i class="bi bi-trash-fill"></i>
                                    <script>
                                        $(document).ready(function() {
                                            $('.delete-btn').on('click', function() {
                                                var nokk = $(this).data('nokk');  // Ambil nomor KK dari data-nokk

                                                // Tampilkan SweetAlert untuk konfirmasi penghapusan
                                                Swal.fire({
                                                    title: 'Apakah Anda yakin?',
                                                    text: "Data ini akan dihapus secara permanen!",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Ya, hapus!',
                                                    cancelButtonText: 'Batal'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        // Redirect ke URL untuk menghapus data jika konfirmasi ya
                                                        window.location.href = "?deleteData=true&nokk=" + nokk;
                                                    }
                                                });
                                            });
                                        });
                                    </script>

                                    </a>                                    
                                    <a href="adddataklrg.php?no_kk=<?= $kk ?>" class="btn btn-success" style="width: 50px;">
                                        <i class="bi bi-person-fill-add"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Modal for adding data -->
            <div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="addDataModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1><?= isset($_GET['op']) && $_GET['op'] == 'edit' ? 'Edit Data Kartu Keluarga' : 'Tambah Data Kartu Keluarga' ?></h1>
                            <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x"></i></button>
                        </div>
                        <div class="modal-body">
                            
                            <form id="addDataForm" method="POST" action="">
                                <div class="mb-3">
                                    <label for="no_kk" class="form-label">No. Kartu Keluarga</label>
                                    <input type="text" class="form-control" id="no_kk" name="no_kk" value="<?= $no_kk ?>" <?= isset($_GET['op']) && $_GET['op'] == 'edit' ? 'readonly' : '' ?> required>
                                </div>
                                <div class="mb-3">
                                    <label for="nik" class="form-label">NIK</label>
                                    <input type="text" class="form-control" id="nik" name="nik" value="<?= $nik ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Kepala Keluarga</label>
                                    <input type="text" class="form-control" id="nama" name="nama" value="<?= $nama_kepala_keluarga ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <input type="text" class="form-control" id="alamat" name="alamat" value="<?= $alamat ?>" required>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col">
                                        <label for="rw" class="form-label">RW</label>
                                        <input type="text" class="form-control" id="rw" name="rw" value="<?= $rw ?>" required>
                                    </div>
                                    <div class="col">
                                        <label for="rt" class="form-label">RT</label>
                                        <input type="text" class="form-control" id="rt" name="rt" value="<?= $rt ?>" required>
                                    </div>
                                    <div class="col">
                                        <label for="kode_pos" class="form-label">Kode Pos</label>
                                        <input type="text" class="form-control" id="kode_pos" name="kode_pos" value="<?= $kodepos ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col">
                                        <label for="desa" class="form-label">Kelurahan / Desa</label>
                                        <input type="text" class="form-control" id="desa" name="desa" value="<?= $desa ?>" required>
                                    </div>
                                    <div class="col">
                                        <label for="kecamatan" class="form-label">Kecamatan</label>
                                        <input type="text" class="form-control" id="kecamatan" name="kecamatan" value="<?= $kecamatan ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col">
                                        <label for="kabupaten" class="form-label">Kabupaten</label>
                                        <input type="text" class="form-control" id="kabupaten" name="kabupaten" value="<?= $kabupaten ?>" required>
                                    </div>
                                    <div class="col">
                                        <label for="provinsi" class="form-label">Provinsi</label>
                                        <input type="text" class="form-control" id="provinsi" name="provinsi" value="<?= $provinsi ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal" class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= $tanggal_dibuat ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary" name="simpan">
                                    <?= isset($_GET['op']) && $_GET['op'] == 'edit' ? 'Update' : 'Simpan' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   <script>
    $(document).ready(function() {
        // Jika URL mengandung parameter 'op=edit', maka buka modal
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('op') === 'edit') {
            $('#addDataModal').modal('show');
        }

        $('#addDataModal').on('hidden.bs.modal', function () {
            if (urlParams.get('op') === 'edit') {
                urlParams.delete('op');
                urlParams.delete('no_kk');
                window.location.search = urlParams.toString();
            }
        });
    });
    
</script>

</body>

</html>