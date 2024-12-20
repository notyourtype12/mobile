<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<?php
session_start();

if (!isset($_SESSION['NIK'])) {
    header("Location: ../index.php");
    exit;
}

include '../koneksi.php';

// Inisialisasi variabel form untuk mode tambah/edit data
$id = $nama = $gambar = "";

// Mengecek apakah sedang dalam mode edit
if (isset($_GET['op']) && $_GET['op'] == 'edit' && isset($_GET['id_surat'])) {
    $id = $_GET['id_surat'];
    $sql = "SELECT * FROM master_surat WHERE id_surat = '$id'";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $id = $row['id_surat'];
        $nama = $row['nama_surat'];
        $gambar = $row['image']; // Ambil gambar lama
    }
} else {
    // Membuat ID baru jika mode tambah
    $sqlGetLastId = "SELECT id_surat FROM master_surat ORDER BY id_surat DESC LIMIT 1";
    $result = mysqli_query($conn, $sqlGetLastId);
    $lastId = $result && mysqli_num_rows($result) > 0 ? mysqli_fetch_assoc($result)['id_surat'] : null;
    $id = $lastId ? 'SURAT' . str_pad(intval(substr($lastId, 5)) + 1, 3, '0', STR_PAD_LEFT) : 'SURAT001';
}

// Ketika tombol simpan ditekan
if (isset($_POST['simpan'])) {
    $id = $_POST['id_surat'];
    $nama = $_POST['nama_surat'];
    $gambarBaru = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];

    // Upload file jika ada file baru
    if ($gambarBaru && move_uploaded_file($tmp_name, "../img/" . $gambarBaru)) {
        $gambar = $gambarBaru;
    }

    // Mengecek jika proses adalah update atau insert
    if (isset($_GET['op']) && $_GET['op'] == 'edit') {
        // Proses update data
        $sql = "UPDATE master_surat SET nama_surat = '$nama'";
        if ($gambar) {
            $sql .= ", image = '$gambar'";
        }
        $sql .= " WHERE id_surat = '$id'";

        if ($conn->query($sql) === TRUE) {
            // Menampilkan SweetAlert untuk update berhasil
            echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil update data',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location = 'mastersurat.php';
                                }
                            });
                        });
                    </script>";
        } else {
            echo "<script>Swal.fire('Gagal!', 'Terjadi kesalahan saat mengupdate data!', 'error');</script>";
        }
    } else {
        // Proses tambah data baru
        $sql = "INSERT INTO master_surat (id_surat, nama_surat";
        if ($gambar) {
            $sql .= ", image";
        }
        $sql .= ") VALUES ('$id', '$nama'";
        if ($gambar) {
            $sql .= ", '$gambar'";
        }
        $sql .= ")";

        if ($conn->query($sql) === TRUE) {
            // Menampilkan SweetAlert untuk insert berhasil
            echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil tambah data',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location = 'mastersurat.php';
                                }
                            });
                        });
                    </script>";
        } else {
            echo "<script>Swal.fire('Gagal!', 'Terjadi kesalahan saat menyimpan data!', 'error');</script>";
        }
    }
}

// Jika tombol Delete diklik
if (isset($_GET['deleteData'])) {
    $id = $_GET['id']; // Id untuk data yang akan dihapus

    $sql = "DELETE FROM master_surat WHERE id_surat='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "
        <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil hapus data',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = 'mastersurat.php'; 
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
                    <h1 class="mb-5">Master Surat</h1>
                    <div class="d-flex flex-column align-items-end" style="width: 100%;">
                        <button id="addDataBtn" class="btn btn-primary mb-2" data-bs-toggle="modal"
                            data-bs-target="#addDataModal">
                            <i class="bi bi-plus-lg"></i> Tambah Surat
                        </button>
                        <div class="input-group mb-3" style="max-width: 400px; border: none;">
                            <input type="text" class="form-control" placeholder="Search..." aria-label="Recipient's username"
                                aria-describedby="basic-addon2">
                        </div>
                    </div>

                    <table class="table table-bordered border-light">
                        <thead class="table-secondary">
                            <tr>
                                <td>No</td>
                                <td>Nama Surat</td>         
                                <td>Gambar</td>
                                <td>Aksi</td>
                            </tr>
                        </thead>
                        <tbody style="text-align: left;">
                           <?php 
                            $sql2   = "SELECT * FROM master_SURAT";
                            $q2     = mysqli_query($conn, $sql2);
                            $urut   = 1;
                            while ($r2 = mysqli_fetch_array($q2)) {
                                $nomer      = $r2['id_surat'];
                                $jeneng     = $r2['nama_surat'];
                                $gwambar     = $r2['image'];
                            ?>
                            <tr>
                                <td style="text-align: center;"><?= $urut++ ?></td>
                                <td><?= htmlspecialchars($jeneng) ?></td>
                                <td scope="row"><img src="../img/<?= htmlspecialchars($gwambar); ?>" width="auto" height="125"> </td>
                                <td>
                                    <a href="?op=edit&id_surat=<?= $nomer ?>" class="btn btn-secondary" style="width: 50px;" data-bs-target="#addDataForm">
                                    <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="javascript:void(0)" data-id="<?= $nomer ?>" class="btn btn-danger delete-btn" style="width: 50px;">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal for adding/editing data -->
            <div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="addDataModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1><?= isset($_GET['op']) && $_GET['op'] == 'edit' ? 'Edit Surat' : 'Tambah Surat' ?></h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addDataForm" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <!-- <label for="id_surat" class="form-label">Kode Surat</label> -->
                                    <input type="hidden" class="form-control" id="id_surat" name="id_surat" 
                                           value="<?= $id ?>" <?= isset($_GET['op']) && $_GET['op'] == 'edit' ? 'readonly' : '' ?> rea>
                                </div>
                                <div class="mb-3">
                                    <label for="nama_surat" class="form-label">Nama Surat</label>
                                    <input type="text" class="form-control" id="nama_surat" name="nama_surat" 
                                           value="<?= $nama ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="image" class="form-label">Gambar Surat</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                    <?php if ($gambar): ?>
                                        <img src="../img/<?= htmlspecialchars($gambar) ?>" alt="Gambar Lama" class="mt-2" style="max-width: 250px;">
                                    <?php endif; ?>
                                </div>
                                <button type="submit" class="btn btn-primary" name="simpan">Simpan Data</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Script for handling modal edit functionality -->
            <script>
                $(document).ready(function() {
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.get('op') === 'edit') {
                        $('#addDataModal').modal('show');
                    }

                 // Ketika modal ditutup, hapus parameter 'op' dan 'id_berita' dari URL
                $('#addDataModal').on('hidden.bs.modal', function () {
            if (urlParams.get('op') === 'edit') {
                urlParams.delete('op');
                urlParams.delete('id_surat');
                window.location.search = urlParams.toString();
            }
        });
                    
                    // Delete confirmation
                    $('.delete-btn').on('click', function() {
                        const id = $(this).data('id');
                        Swal.fire({
                            title: 'Anda yakin ingin menghapus data ini?',
                            text: "Data yang dihapus mungkin tidak bisa dikembalikan!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Hapus'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = '?deleteData=true&id=' + id;
                            }
                        });
                    });
                });
            </script>
</body>
</html>