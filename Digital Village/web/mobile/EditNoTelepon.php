<?php
// Debugging error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "digital_village");

if ($conn->connect_error) {
    echo json_encode(array("error" => "Koneksi gagal: " . $conn->connect_error));
    exit;
}

// Memastikan metode permintaan adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nik']) && isset($_POST['no_hp'])) {
        $nik = $_POST['nik'];
        $no_hp = $_POST['no_hp'];

        // Validasi sederhana untuk memastikan data tidak kosong
        if (!empty($nik) && !empty($no_hp)) {
            // Query untuk mengupdate no_hp berdasarkan nik
            $sql = "UPDATE master_akun SET no_hp = ? WHERE nik = ?";

            // Menggunakan prepared statement untuk menghindari SQL injection
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                die(json_encode(array("error" => "Statement gagal disiapkan: " . $conn->error)));
            }

            // Bind parameter (no_hp, nik)
            $stmt->bind_param("ss", $no_hp, $nik);

            // Eksekusi query
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(array("message" => "Nomor telepon berhasil diperbarui."));
                } else {
                    echo json_encode(array("error" => "NIK tidak ditemukan atau data tidak berubah."));
                }
            } else {
                echo json_encode(array("error" => "Terjadi kesalahan saat memperbarui data: " . $stmt->error));
            }

            $stmt->close();
        } else {
            echo json_encode(array("error" => "Semua field harus diisi."));
        }
    } else {
        echo json_encode(array("error" => "Parameter nik atau no_hp tidak ada dalam permintaan POST."));
    }
} else {
    echo json_encode(array("error" => "Hanya menerima permintaan POST."));
}

$conn->close();
?>
