<?php
include "koneksi.php";

// Menetapkan header untuk respons JSON
// header('Content-Type: application/json');

$sql = "SELECT * FROM master_penduduk";
$r = mysqli_query($conn, $sql);

$result = array();

// Periksa apakah query berhasil
if ($r) {
    while ($row = mysqli_fetch_assoc($r)) { 
        array_push($result, array(
            "nik" => $row['nik'],
            "nama_lengkap" => $row['nama_lengkap']
        ));
    }
} else {
    // eror gayaaa
    echo json_encode(array("error" => "Query failed: " . mysqli_error($conn)));
}

// Mengembalikan data formast json
echo json_encode($result);

// Menutup koneksi database
mysqli_close($conn);
?>
