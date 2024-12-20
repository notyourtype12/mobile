<?php
include "koneksi.php";

$sql = "SELECT * FROM penduduk";

$r = mysqli_query($conn,$sql);

$result = array();

while($row = mysqli_fetch_array($r)){
    array_push($result,array(
        "Nomer_KK"=>$row['Nomer_KK'],
        "NIK"=>$row['NIK'],
        "nama"=>$row['nama']
    ));
}

echo json_encode(array('result'=> $result));

mysqli_close($conn);
?>