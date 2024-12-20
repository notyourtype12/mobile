<?php
// Buat koneksi ke database
$conn = new mysqli("localhost", "root", "", "digital_village");

if ($conn->connect_error) {
    // Kembalikan error dalam format JSON
    echo json_encode(array("error" => "Koneksi gagal: " . $conn->connect_error));
    exit;
}

// Tangkap NIK dari parameter URL
$NIK = $_GET['nik'];

// Query untuk mengambil data profil berdasarkan NIK
$query = "SELECT master_penduduk.no_kk, 
                 master_penduduk.nik, 
                 master_penduduk.nama_lengkap, 
                 master_penduduk.tempat_lahir,
                 master_penduduk.tanggal_lahir,
                 master_penduduk.jenis_kelamin,
                 master_penduduk.golongan_darah,
                 master_kartukeluarga.dusun,
                 master_kartukeluarga.rt,
                 master_kartukeluarga.rw,
                 master_kartukeluarga.desa,
                 master_kartukeluarga.kecamatan,
                 master_penduduk.agama,
                 master_penduduk.status_perkawinan,
                 master_penduduk.pekerjaan,
                 master_penduduk.kewarganegaraan,
          FROM master_penduduk 
          LEFT JOIN master_kartukeluarga ON master_penduduk.nik = master_akun.nik 
          WHERE master_penduduk.nik = '$NIK'";

$result = $conn->query($query);

// Periksa apakah data ditemukan
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Kembalikan data dalam format JSON
    echo json_encode($row);
} else {
    // Jika data tidak ditemukan, kembalikan pesan error dalam JSON
    echo json_encode(array("error" => "Data tidak ditemukan"));
}

$conn->close();
?>