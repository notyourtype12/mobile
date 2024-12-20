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

// Query untuk menghitung jumlah baris dengan status 'Diajukan'
$query = "SELECT COUNT(*) AS total FROM `view_pengajuan_diajukan` WHERE status = 'Disetujui RT' AND rw = '$rw'";
$result = $conn->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    $total = $row['total']; // Ambil jumlah baris
} else {
    echo "Terjadi kesalahan: " . $conn->error;
}

// Query untuk menghitung jumlah baris dengan status 'Selesai'
$query1 = "SELECT COUNT(*) AS total FROM `view_pengajuan_diajukan` WHERE status = 'Disetujui RW' AND rw = '$rw'";
$result1 = $conn->query($query1);

if ($result1) {
    $row1 = $result1->fetch_assoc();
    $total1 = $row1['total']; // Ambil jumlah baris
} else {
    echo "Terjadi kesalahan: " . $conn->error;
}

// Query untuk mengambil data jenis pengajuan surat
$query2 = "SELECT * FROM master_surat";
$result2 = $conn->query($query2);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main p-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-end" style="width: 100%;">
                        <div class="input-group mb-3" style="max-width: 400px; border: none;">
                            <!-- Input Group can be added here if needed -->
                        </div>
                    </div>
                    <div class="text">
                        <h1>Dashboard</h1>
                        <h6>Selamat Datang Ketua RW <?= $rw ?></h6>
                    </div>

                    <!-- Cards Section -->
                    <div class="row mt-4">
                        <div class="col-auto custom-col">
                            <div class="card">
                                <div class="card-body d-flex align-items-center">
                                    <div class="me-2">
                                        <h5 class="card-title">Surat Masuk</h5>
                                        <p class="card-text ms-2"><?= $total ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-auto custom-col">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Surat Selesai</h5>
                                    <p class="card-text"><?= $total1 ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jenis Pengajuan Surat Section -->
                    <h3 class="mt-4">Jenis Pengajuan Surat</h3>
                    <h6>Menampilkan Data Jenis Pengajuan</h6>

                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Jenis Pengajuan Surat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Menampilkan data jenis pengajuan surat
                            if ($result2->num_rows > 0) {
                                $no = 1;
                                while ($row2 = $result2->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<th scope='row'>" . $no++ . "</th>";
                                    echo "<td>" . $row2['nama_surat'] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='2'>Tidak ada data jenis pengajuan surat.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div> <!-- End of card-body -->
            </div> <!-- End of card -->
        </div> <!-- End of main -->
    </div> <!-- End of wrapper -->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-..." crossorigin="anonymous"></script>
</body>

</html>
