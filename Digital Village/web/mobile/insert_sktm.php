<?php
include "koneksi.php";

$sql = "SELECT * FROM master_penduduk";

$r = mysqli_query($conn,$sql);

$result = array();

while($row = mysqli_fetch_array($r)){
    array_push($result,array(
        "no_kk"=>$row['no_kk'],
        "nik"=>$row['nik'],
        "nama_lengkap"=>$row['nama_lengkap']
    ));
}

echo json_encode(array('result'=> $result));

mysqli_close($conn);
?>
