<?php

// Koneksi ke database
include 'koneksi.php'; // Pastikan koneksi ke database

// Cek apakah request adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dari request POST
    $nik = isset($_POST['nik']) ? $_POST['nik'] : null;
    $no_hp = isset($_POST['no_hp']) ? $_POST['no_hp'] : null;

    // Validasi data
    if (!empty($nik) && !empty($no_hp)) {
        // Query untuk update nomor telepon
        $query = "UPDATE master_akun SET no_hp = '$no_hp' WHERE nik = '$nik'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Nomor telepon berhasil diperbarui']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui nomor telepon']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    }
} else {
    // Tangani selain metode POST
    echo json_encode(['status' => 'failed', 'message' => 'Hanya metode POST yang diterima.']);
}

?>
