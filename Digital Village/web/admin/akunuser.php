<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Akun User</title>
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

// Query untuk mengambil data dari view
$sql = "SELECT nik, nama, rt, rw, tanggal_lahir FROM view_penduduk_data";
$result = $conn->query($sql);
?>
<?php
include '../koneksi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nik'])) {
    $nik = $_POST['nik'];
    $sql = "DELETE FROM users WHERE nik = '$nik'";
    if ($conn->query($sql) === TRUE) {
        echo "Success";
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>
<?php
include '../koneksi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik = $_POST['nik'];
    $nama = $_POST['nama'];
    $rt = $_POST['rt'];
    $rw = $_POST['rw'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    
    $sql = "UPDATE users SET nama='$nama', rt='$rt', rw='$rw', tanggal_lahir='$tanggal_lahir' WHERE nik='$nik'";
    if ($conn->query($sql) === TRUE) {
        echo "Success";
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>        
        <div class="main p-3">
            <div class="card">
                <div class="card-body">
                    <h1>Master Akun User</h1>
                    
                    <div class="d-flex flex-column align-items-end" style="width: 100%;">
                        <div class="input-group mb-3" style="max-width: 400px; border: none;">
                            <input type="text" class="form-control" placeholder="Search..." aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </div>
                    </div>

                    <table class="table table-bordered border-light">
                        <thead class="table-secondary">
                            <tr>
                                <td>No</td>
                                <td>Nama Lengkap</td>
                                <td>NIK</td>
                                <td>RT</td>
                                <td>RW</td>
                                <td>Tanggal Lahir</td>
                                <td>Aksi</td>
                            </tr>
                        </thead>
                        <tbody style="text-align: left;">
                            <?php
                            if ($result->num_rows > 0) {
                                $no = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $no++ . "</td>";
                                    echo "<td>" . $row["nama"] . "</td>";
                                    echo "<td>" . $row["nik"] . "</td>";
                                    echo "<td>" . $row["rt"] . "</td>";
                                    echo "<td>" . $row["rw"] . "</td>";
                                    echo "<td>" . date("d F Y", strtotime($row["tanggal_lahir"])) . "</td>";
                                    echo "<td>
                                            <button class='btn btn-warning btn-sm edit-btn'><i class='bi bi-pencil'></i> Edit</button>
                                            <button class='btn btn-danger btn-sm delete-btn'><i class='bi bi-trash'></i> Hapus</button>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>Tidak ada data</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Data User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editNik" name="nik">
                        <div class="mb-3">
                            <label for="editNama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="editNama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="editRt" class="form-label">RT</label>
                            <input type="text" class="form-control" id="editRt" name="rt" required>
                        </div>
                        <div class="mb-3">
                            <label for="editRw" class="form-label">RW</label>
                            <input type="text" class="form-control" id="editRw" name="rw" required>
                        </div>
                        <div class="mb-3">
                            <label for="editTanggalLahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="editTanggalLahir" name="tanggal_lahir" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function () {
        // Handle the Edit button click
        $('.edit-btn').on('click', function () {
            const row = $(this).closest('tr');
            const nik = row.find('td:eq(2)').text();
            
            // Open an edit modal
            $('#editModal').modal('show');
            
            // Populate modal fields with row data
            $('#editNik').val(nik);
            $('#editNama').val(row.find('td:eq(1)').text());
            $('#editRt').val(row.find('td:eq(3)').text());
            $('#editRw').val(row.find('td:eq(4)').text());
            $('#editTanggalLahir').val(row.find('td:eq(5)').text());
        });

        // Handle the Delete button click
        $('.delete-btn').on('click', function () {
            const nik = $(this).closest('tr').find('td:eq(2)').text();
            
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data tidak dapat dikembalikan setelah dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'delete_user.php',
                        type: 'POST',
                        data: { nik: nik },
                        success: function () {
                            Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success').then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        });

        // Handle the form submission for editing data
        $('#editForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: 'update_user.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function () {
                    $('#editModal').modal('hide');
                    Swal.fire('Updated!', 'Data berhasil diupdate.', 'success').then(() => {
                        location.reload();
                    });
                }
            });
        });
    });
    </script>

</body>
</html>