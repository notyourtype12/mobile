<?php
// Mengatur header untuk respons dalam format JSON
header('Content-Type: application/json');

// Menyertakan file koneksi ke database
include_once 'koneksi.php';

// Mendapatkan data POST yang dikirim dari aplikasi Android
$nik = $_POST['nik'];
$no_hp = $_POST['no_hp'];
$password = $_POST['password'];

// Memeriksa apakah NIK ada dalam tabel master_penduduk
$query_penduduk = "SELECT * FROM master_penduduk WHERE nik = '$nik' LIMIT 1";
$result_penduduk = mysqli_query($conn, $query_penduduk);

if (mysqli_num_rows($result_penduduk) > 0) {
    // Memeriksa apakah NIK sudah ada dalam tabel master_akun (akun sudah diaktifkan)
    $query_akun = "SELECT * FROM master_akun WHERE nik = '$nik' LIMIT 1";
    $result_akun = mysqli_query($conn, $query_akun);

    if (mysqli_num_rows($result_akun) > 0) {
        // Jika NIK sudah ada di tabel master_akun, akun sudah diaktivasi
        echo json_encode([
            'error' => true,
            'message' => 'Akun sudah diaktivasi, silahkan melakukan login.'
        ]);
    } else {
        // Jika NIK belum ada di tabel master_akun, maka insert data akun baru
        $insert_query = "INSERT INTO master_akun (nik, no_hp, password) VALUES ('$nik', '$no_hp', '$password')";

        if (mysqli_query($conn, $insert_query)) {
            // Mengirimkan respons sukses jika data berhasil dimasukkan
            echo json_encode([
                'error' => false,
                'message' => 'Akun berhasil diaktivasi.'
            ]);
        } else {
            // Mengirimkan respons error jika terjadi masalah pada proses insert
            echo json_encode([
                'error' => true,
                'message' => 'Gagal mengaktivasi akun, coba lagi.'
            ]);
        }
    }
} else {
    // Mengirimkan respons error jika NIK tidak ditemukan di tabel master_penduduk
    echo json_encode([
        'error' => true,
        'message' => 'NIK tidak ditemukan.'
    ]);
}
?>
