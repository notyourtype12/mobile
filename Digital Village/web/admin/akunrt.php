<?php
session_start();

if (!isset($_SESSION['NIK'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: ../index.php");
    exit;
}
?>

<?php
include '../koneksi.php';

// Inisialisasi variabel form untuk mode tambah data
$nik = $nama = $telepon = $rw = $rt = "";

if (isset($_GET['op']) && $_GET['op'] == 'edit' && isset($_GET['id_rtrw'])) {
    $id = $_GET['id_rtrw'];
    $sql1 = "SELECT * FROM master_rt_rw WHERE id_rtrw = '$id'";  // Perbaikan pada query WHERE
    $q1 = mysqli_query($conn, $sql1);
    if ($q1) {
        $r1 = mysqli_fetch_array($q1);
        $id = $r1['id_rtrw'];
        $nik = $r1['nik'];
        $nama = $r1['nama'];
        $telepon = $r1['no_hp'];
        $rw = $r1['rw'];
        $rt = $r1['rt'];
    }
}

if (isset($_POST['simpan'])) {
    $id = $_POST['id_rtrw'];
    $nik = $_POST['nik'];  // Pastikan nama input sesuai dengan name di form
    $nama = $_POST['nama_lengkap'];
    $telepon = $_POST['no_hp'];
    $rw = $_POST['rw'];
    $rt = $_POST['rt'];

    $op = isset($_GET['op']) ? $_GET['op'] : '';

    // Cek apakah nik ada di master_akun
    $nikCheckSql = "SELECT * FROM master_akun WHERE nik = '$nik'";
    $nikCheckResult = mysqli_query($conn, $nikCheckSql);

    if (mysqli_num_rows($nikCheckResult) == 0) {
        // Jika nik tidak ada di master_akun
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Nik tidak ditemukan!',
                    text: 'Nik yang Anda masukkan tidak ada di tabel master_akun. Harap periksa kembali.',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
    } else {
        // Cek apakah kombinasi RT dan RW sudah ada (kecuali untuk data yang sedang di-edit)
        $duplicateCheckSql = "SELECT * FROM master_rt_rw WHERE rt = '$rt' AND rw = '$rw' AND nik <> '$nik'";
        $duplicateCheckResult = mysqli_query($conn, $duplicateCheckSql);

        if (mysqli_num_rows($duplicateCheckResult) > 0) {
            // Jika kombinasi RT dan RW sudah ada
            echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kombinasi RT dan RW sudah ada!',
                        text: 'Data dengan RT dan RW yang sama sudah ada. Harap gunakan kombinasi yang berbeda.',
                        confirmButtonText: 'OK'
                    });
                });
            </script>";
        } else {
            // Cek apakah nik sudah ada di master_rt_rw (untuk mencegah duplikasi nik)
            $nikDupCheckSql = "SELECT * FROM master_rt_rw WHERE nik = '$nik'";
            $nikDupCheckResult = mysqli_query($conn, $nikDupCheckSql);

            if (mysqli_num_rows($nikDupCheckResult) > 0) {
                // Jika nik sudah ada di master_rt_rw
                echo "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Nik sudah terdaftar!',
                            text: 'Nik yang Anda masukkan sudah terdaftar di tabel master_rt_rw. Harap periksa kembali.',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>";
            } else {
                if ($op == 'edit') {
                    try {
                        $conn->begin_transaction();
                        $sql = "UPDATE master_rt_rw SET
                                nik = '$nik', 
                                nama = '$nama', 
                                no_hp = '$telepon', 
                                rw = '$rw',  
                                rt = '$rt'
                                WHERE id_rtrw = '$id'";  // Perbaikan pada query WHERE

                        $sql1 = "UPDATE master_akun SET
                                level = 3
                                WHERE nik = '$nik'";  // Update level pada akun yang terkait
                        
                        if ($conn->query($sql) === TRUE && $conn->query($sql1) === TRUE) {
                            $conn->commit();
                            echo "
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil edit data',
                                        confirmButtonText: 'OK'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location = 'akunrt.php'; 
                                        }
                                    });
                                });
                            </script>";
                        } 
                    } catch (mysqli_sql_exception $e) {
                        $conn->rollback();
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
                    try {
                        $conn->begin_transaction();
                        $sql2 = "INSERT INTO master_rt_rw (nik, nama, no_hp, rt, rw)
                                VALUES ('$nik', '$nama', '$telepon', '$rt', '$rw')";

                        $sql3 = "UPDATE master_akun SET
                                level = 3
                                WHERE nik = '$nik'";  // Update level untuk akun yang terkait

                        if ($conn->query($sql2) === TRUE && $conn->query($sql3) === TRUE) {
                            $conn->commit();
                            echo "
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil tambah data',
                                        confirmButtonText: 'OK'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location = 'akunrt.php'; 
                                        }
                                    });
                                });
                            </script>";
                        } 
                    } catch (mysqli_sql_exception $e) {
                        $conn->rollback();
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
        }
    }
}


// Jika tombol Delete diklik
if (isset($_GET['deleteData']) && isset($_GET['id_rtrw'])) {
    $id = $_GET['id_rtrw']; // Ambil id_rtrw untuk data yang akan dihapus

    // Query untuk menghapus data
    $sql = "DELETE FROM master_rt_rw WHERE id_rtrw='$id'";

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
                        window.location = 'akunrt.php'; 
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

if (isset($_POST['ajax']) && $_POST['ajax'] == 'get_name' && isset($_POST['nik'])) {
    $nik = $_POST['nik'];

    // Query to fetch the name and phone number based on the provided NIK
    $sql = "SELECT mp.nama_lengkap, ma.no_hp FROM master_penduduk mp JOIN master_akun ma ON mp.nik = ma.nik WHERE mp.nik = '$nik'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Return both name and phone number in JSON format
        echo json_encode([
            'nama_lengkap' => $row['nama_lengkap'],
            'no_hp' => $row['no_hp']
        ]);
    } else {
        echo '';  // If no record found, send an empty response
    }
    exit;  // Stop further execution after AJAX request
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun RT</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</head>
<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>        
        <div class="main p-3">
            <div class="card">
                <div class="card-body">
                    <h1 class="mb-5">Akun RT</h1>
                    <div class="d-flex flex-column align-items-end" style="width: 100%;">
                    <button id="addDataBtn" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addDataModal">
                        <i class="bi bi-plus-lg"></i> Tambah Data
                    </button>

                        <div class="input-group mb-3" style="max-width: 400px; border: none;">
                            <input type="text" class="form-control" placeholder="Search..." aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <!-- <button class="btn btn-primary"><i class="bi bi-search"></i></button> -->
                        </div>
                    </div>

                    <table class="table table-bordered border-light">
                        <style>
                            td {
                                padding: 10px;
                            }
                        </style>
                        <thead class="table-secondary" style="text-align: left;">
                            <tr>
                                <td style="width: 4%;">No</td>
                                <td>NIK</td>
                                <td>Nama</td>
                                <td style="width: 30%;">Telepon</td>
                                <td style="width: 4%;">RW</td>
                                <td style="width: 4%;">RT</td>
                                <td style="width: 15%;">Aksi</td>
                            </tr>
                        </thead>
                        <tbody style="text-align: left;">
                           <?php 
                            $sql2   = "SELECT * FROM master_rt_rw WHERE rt <> '';";
                            $q2     = mysqli_query($conn, $sql2);
                            $urut   = 1;
                            while ($r2 = mysqli_fetch_array($q2)) {
                                $nomer = $r2['id_rtrw'];
                                $enika      = $r2['nik'];
                                $jeneng     = $r2['nama'];
                                $hape  = $r2['no_hp'];
                                $erwe     = $r2['rw'];
                                $erte     = $r2['rt'];
                            ?>
                            <tr>
                                <td style="text-align: center;"><?= $urut++ ?></td>
                                <td><?= $enika ?></td>
                                <td><?= $jeneng ?></td>
                                <td><?= $hape ?></td>
                                <td><?= $erwe ?></td>
                                <td><?= $erte ?></td>
                                <td>
                                    <a href="?op=edit&id_rtrw=<?= $nomer ?>" class="btn btn-secondary" style="width: 50px;" data-bs-target="#addDataForm">
                                    <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="javascript:void(0)" data-id_rtrw="<?= $nomer ?>" class="btn btn-danger delete-btn" style="width: 50px;">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>

                                        <script>
                                            $(document).ready(function() {
                                                $('.delete-btn').on('click', function() {
                                                    var id_rtrw = $(this).data('id_rtrw'); // Ambil ID yang akan dihapus

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
                                                            // Redirect ke URL untuk menghapus data berdasarkan id_rtrw
                                                            window.location.href = "?deleteData=true&id_rtrw=" + id_rtrw;
                                                        }
                                                    });
                                                });
                                            });
                                        </script>


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
                <h1><?= isset($_GET['op']) && $_GET['op'] == 'edit' ? 'Edit Data RT' : 'Tambah Data RT' ?></h1>
                <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x"></i></button>
            </div>
            <div class="modal-body">
                <form id="addDataForm" method="POST" action="">
                    <div class="mb-3">
                        <input type="hidden" class="form-control" id="id_rtrw" name="id_rtrw" value="<?= isset($_GET['op']) && $_GET['op'] == 'edit' ? $id : '' ?>" <?= isset($_GET['op']) && $_GET['op'] == 'edit' ? 'readonly' : '' ?> required>
                        <label for="nik" class="form-label">Nomor Induk Kepundudukan</label>
                        <input type="text" class="form-control" id="nik" name="nik" value="<?= $nik ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= $nama ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_hp" class="form-label">Telepon</label>
                        <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?= $telepon ?>" required>
                    </div>
                    <div class="mb-3 row">
                        <div class="col">
                            <label for="rw" class="form-label">RW</label>
                            <input type="text" class="form-control" id="rw" name="rw" value="<?= $rw ?>" required>
                        </div>
                        <div class="col">
                            <label for="id_rtrw" class="form-label">RT</label>
                            <input type="text" class="form-control" id="rt" name="rt" value="<?= $rt ?>" required>
                        </div>
                    </div>
                    <!-- <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="text" class="form-control" id="password" name="password" value="" required>
                    </div> -->
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
                urlParams.delete('id_rtrw');
                window.location.search = urlParams.toString();
            }
        });

         // Fetch 'nama_lengkap' based on 'nik' input when it loses focus
        $('#nik').on('blur', function() {
            var nik = $(this).val().trim();
            if (nik) {
                $.ajax({
                    url: '', // The current PHP file will handle the request
                    type: 'POST',
                    data: { ajax: 'get_name', nik: nik },
                    success: function(response) {
                        try {
                            // Parse the JSON response
                            var data = JSON.parse(response);
                            if (data.nama_lengkap && data.no_hp) {
                                $('#nama_lengkap').val(data.nama_lengkap); // Populate 'nama_lengkap' field
                                $('#no_hp').val(data.no_hp); // Populate 'no_hp' field
                            } else {
                                // Clear both fields if no valid response
                                $('#nama_lengkap').val('');
                                $('#no_hp').val('');
                            }
                        } catch (e) {
                            alert("Failed to parse response. Please try again.");
                        }
                    },
                    error: function() {
                        alert("Failed to fetch name and phone number. Please try again.");
                    }
                });
            } else {
                // Clear both fields if NIK is empty
                $('#nama_lengkap').val('');
                $('#no_hp').val('');
            }
        });

    });
    </script>
</body>
</html>