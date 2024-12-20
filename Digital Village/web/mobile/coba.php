<?php

// Connect to the database
require_once 'koneksi.php';

$target_dir = "uploads/fotoProfil/";

if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if (isset($_POST['nik']) && !empty($_POST['nik'])) {
    $nik = $_POST['nik'];

    if (isset($_FILES['foto_profil']['name'])) {
        $file_name = basename($_FILES["foto_profil"]["name"]);
        $file_tmp = $_FILES["foto_profil"]["tmp_name"];
        $file_size = $_FILES["foto_profil"]["size"];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $new_file_name = $nik . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $new_file_name;

        $allowed_extensions = array("jpg", "jpeg", "png");

        if (in_array($file_ext, $allowed_extensions)) {
            if ($file_size < 5000000) {
                if (move_uploaded_file($file_tmp, $target_file)) {
                    $sql = "UPDATE master_akun SET foto_profil='$new_file_name' WHERE nik='$nik'";

                    if (mysqli_query($conn, $sql)) {
                        $image_url = "http://" . $_SERVER['SERVER_NAME'] . "/CRUDVolley/" . $target_file;
                        echo json_encode(["status" => "success", "message" => "Foto profil berhasil diperbarui.", "file_name" => $new_file_name, "url" => $image_url]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Query gagal: " . mysqli_error($conn)]);
                    }
                } else {
                    echo json_encode(["status" => "error", "message" => "Gagal mengunggah file."]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Ukuran file lebih dari 5MB."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Format file tidak valid. Hanya JPG, JPEG, PNG yang diperbolehkan."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Tidak ada file yang diunggah."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "NIK diperlukan."]);
}

mysqli_close($conn);
?>
