
<?php
session_start();

if (!isset($_SESSION['NIK'])) {
    header("Location: ../index.php");
    exit;
}

include '../koneksi.php';

// Inisialisasi variabel form untuk mode tambah data
$id = $judul = $deskripsi = $gambar = "";

// Mengecek apakah sedang dalam mode edit
if (isset($_GET['op']) && $_GET['op'] == 'edit' && isset($_GET['id_berita'])) {
    $id = $_GET['id_berita'];
    $sql = "SELECT * FROM master_berita WHERE id_berita = '$id'";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $id = $row['id_berita'];
        $judul = $row['judul'];
        $deskripsi = $row['deskripsi'];
        $gambar = $row['image'];
    }
} else {
    // Membuat ID baru jika mode tambah
    $sqlGetLastId = "SELECT id_berita FROM master_berita ORDER BY id_berita DESC LIMIT 1";
    $result = mysqli_query($conn, $sqlGetLastId);
    $lastId = $result && mysqli_num_rows($result) > 0 ? mysqli_fetch_assoc($result)['id_berita'] : null;
    $id = $lastId ? 'NEWS' . str_pad(intval(substr($lastId, 4)) + 1, 4, '0', STR_PAD_LEFT) : 'NEWS0001';
}

// Ketika tombol simpan ditekan
if (isset($_POST['simpan'])) {
    $id = $_POST['id_berita'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $gambar = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];
    $gambar_org = $gambar; // Capture original image name

    // Check if image was uploaded and move it
    if (move_uploaded_file($tmp_name, "../img/" . $gambar_org)) {
        $op = isset($_GET['op']) ? $_GET['op'] : '';

        if ($op == 'edit') {
            // Proses update data
            $sql = "UPDATE master_berita SET 
                    judul = '$judul', 
                    deskripsi = '$deskripsi', 
                    image = '$gambar' 
                    WHERE id_berita = '$id'";

            if ($conn->query($sql) === TRUE) {
                echo "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil edit data',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = 'berita.php'; 
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
                            title: 'Gagal edit data',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>";
            }
        } else {
            // Proses insert data baru
            $sql = "INSERT INTO master_berita (id_berita, judul, deskripsi";
            if ($gambar) {
            $sql .= ", image";
                }
                $sql .= ") VALUES ('$id', '$judul', '$deskripsi'";
                if ($gambar) {
                    $sql .= ", '$gambar'";
                }
                $sql .= ")";

            if ($conn->query($sql) === TRUE) {
                echo "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil tambah data',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = 'berita.php'; 
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
                            title: 'Gagal tambah data',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>";
            }
        }
    }
}

// Jika tombol Delete diklik
if (isset($_GET['deleteData'])) {
    $id = $_GET['id']; // Id untuk data yang akan dihapus

    $sql = "DELETE FROM master_berita WHERE id_berita='$id'";

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
                            window.location = 'berita.php'; 
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main p-3">
            <div class="card">
                <div class="card-body">
                    <h1 class="mb-5">Berita</h1>
                    <div class="d-flex flex-column align-items-end" style="width: 100%;">
                        <button id="addDataBtn" class="btn btn-primary mb-2" data-bs-toggle="modal"
                            data-bs-target="#addDataModal">
                            <i class="bi bi-plus-lg"></i> Tambah Berita
                        </button>
                        <div class="input-group mb-3" style="max-width: 400px; border: none;">
                            <input type="text" class="form-control" placeholder="Search..." aria-label="Recipient's username"
                                aria-describedby="basic-addon2">
                        </div>
                    </div>

                    <table class="table table-bordered border-light">
                        <thead class="table-secondary" >
                            <tr>
                                <td>No</td>
                                <td>Judul</td>
                                <td>Tanggal</td>
                                <td>Deskripsi</td>
                                <td>Gambar</td>
                                <td>Aksi</td>
                            </tr>
                        </thead>
                        <tbody style="text-align: left;">
                           <?php 
                            $sql2   = "SELECT * FROM master_berita";
                            $q2     = mysqli_query($conn, $sql2);
                            $urut   = 1;
                            while ($r2 = mysqli_fetch_array($q2)) {
                                $nomer      = $r2['id_berita'];
                                $irahirah     = $r2['judul'];
                                $woconan  = $r2['deskripsi'];
                                $gwambar     = $r2['image'];
                                $tanggale = $r2['tanggal'];
                            ?>
                            <tr>
                                <td style="text-align: center;"><?= $urut++ ?></td>
                                <td><?= htmlspecialchars($irahirah) ?></td>
                                <td><?= htmlspecialchars($tanggale) ?></td>
                                <td><?= htmlspecialchars($woconan) ?></td>
                                <td scope="row"><img src="../img/<?= htmlspecialchars($gwambar); ?>" width="auto" height="125"> </td>
                                <td>
                                    <a href="?op=edit&id_berita=<?= $nomer ?>" class="btn btn-secondary" style="width: 50px;">
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
                            <h1><?= isset($_GET['op']) && $_GET['op'] == 'edit' ? 'Edit Berita' : 'Tambah Berita' ?></h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addDataForm" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <!-- <label for="id_berita" class="form-label">Kode Berita</label> -->
                                    <input type="hidden" class="form-control" id="id_berita" name="id_berita" 
                                        value="<?= isset($_GET['op']) && $_GET['op'] == 'edit' ? $id : $id ?>" 
                                        readonly>

                                    </div>
                                <div class="mb-3">
                                    <label for="judul" class="form-label">Judul Berita</label>
                                    <input type="text" class="form-control" id="judul" name="judul" value="<?= $judul ?>" required>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col">
                                        <label for="deskripsi" class="form-label">Deskripsi Berita</label>
                                        <textarea id="deskripsi" name="deskripsi" class="form-control" required style="font-size: 12px; height: 200px; width: 100%;"><?= $deskripsi ?></textarea>
                                    </div>
                                </div>
                                <!-- <div class="mb-3">
                                <label for="image" class="form-label">Gambar Berita</label>
                                    <input type="file" class="form-control" name="image" 
                                </div> -->
                                <div class="mb-3">
                                    <label for="image" class="form-label">Gambar Berita</label>
                                    <input type="file" class="form-control" id="image" name="image" required>
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

    <script>
    $(document).ready(function() {
        // Jika URL memiliki parameter 'op=edit', buka modal
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('op') === 'edit') {
            $('#addDataModal').modal('show');
        }

        // Ketika modal ditutup, hapus parameter 'op' dan 'id_berita' dari URL
        $('#addDataModal').on('hidden.bs.modal', function () {
            if (urlParams.get('op') === 'edit') {
                urlParams.delete('op');
                urlParams.delete('id_berita');
                window.location.search = urlParams.toString();
            }
        });

        // Konfirmasi hapus data
        $('.delete-btn').on('click', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = '?deleteData=true&id=' + id;
                }
            });
        });
    });
</script>

        </div>
    </div>
</body>

</html>