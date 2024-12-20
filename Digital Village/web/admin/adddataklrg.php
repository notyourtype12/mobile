<?php
session_start();

if (!isset($_SESSION['NIK'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: ../index.php");
    exit;
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anggota Kartu Keluarga</title>
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

// Initialize variables for the form
$no_kk = isset($_GET['no_kk']) ? $_GET['no_kk'] : '';
$nik = $nama = $kelamin = $tempat = $tgllahir = $agama = $pendidikan = $pekerjaan = $goldrah = $perkawinan = $tglnikah = $statuskeluarga = $kewarganegaraan = $nopaspor = $nokitap = $nmayah = $nmibu = "";

// Retrieve no_kk from URL if available
if (isset($_GET['no_kk'])) {
    $no_kk = $_GET['no_kk'];
}

// Check if in edit mode
if (isset($_GET['op']) && $_GET['op'] === 'edit' && isset($_GET['nik'])) {
    $nik = $_GET['nik'];
    
    // Query to fetch data from master_penduduk
    $sql1 = "SELECT * FROM master_penduduk WHERE nik='$nik'";
    $q1 = mysqli_query($conn, $sql1);

    if ($q1 && mysqli_num_rows($q1) > 0) {
        $r1              = mysqli_fetch_array($q1);
        $nik             = $r1['nik'];
        $nama            = $r1['nama_lengkap'];
        $kelamin         = $r1['jenis_kelamin'];
        $tempat          = $r1['tempat_lahir'];
        $tgllahir        = $r1['tanggal_lahir'];
        $agama           = $r1['agama'];
        $pendidikan      = $r1['pendidikan'];
        $pekerjaan       = $r1['pekerjaan'];
        $goldrah         = $r1['golongan_darah'];
        $perkawinan      = $r1['status_perkawinan'];
        $tglnikah        = $r1['tanggal_perkawinan'];
        $statuskeluarga  = $r1['status_keluarga'];
        $kewarganegaraan = $r1['kewarganegaraan'];
        $nopaspor        = $r1['no_paspor'];
        $nokitap         = $r1['no_kitap'];
        $nmayah          = $r1['nama_ayah'];
        $nmibu           = $r1['nama_ibu'];
        $no_kk           = $r1['no_kk'];
    }
}

// When the save button is clicked
if (isset($_POST['simpan'])) {
    $nik             = $_POST['nik'];
    $nama            = $_POST['namalengkap'];
    $kelamin         = $_POST['jeniskelamin'];
    $tempat          = $_POST['tempatlahir'];
    $tgllahir        = $_POST['tgllahir'];
    $agama           = $_POST['agama'];
    $pendidikan      = $_POST['pendidikan'];
    $pekerjaan       = $_POST['pekerjaan'];
    $goldrah         = $_POST['goldarah'];
    $perkawinan      = $_POST['statusperkawinan'];
    // $tglnikah        = $_POST['tanggalnikah'];
    $tglnikah = isset($_POST['tanggalnikah']) ? $_POST['tanggalnikah'] : null;
    $nokitap = isset($_POST['nokitap']) ? $_POST['nokitap'] : null;
    $statuskeluarga  = $_POST['hubungan_keluarga'];
    $kewarganegaraan = $_POST['kewarganegaraan'];
    $nopaspor        = isset($_POST['nopaspor']) ? $_POST['nopaspor'] : null;
    // $nokitap         = $_POST['nokitap'];
    $nmayah          = $_POST['namaayah'];
    $nmibu           = $_POST['namaibu'];
    $no_kk           = $_POST['no_kk'];

    $op = isset($_GET['op']) ? $_GET['op'] : '';

    if ($op == 'edit') {
        // Update data
        try {
            $conn->begin_transaction();

            $query_update_penduduk = "UPDATE master_penduduk SET 
                    nama_lengkap = '$nama', 
                    jenis_kelamin = '$kelamin',
                    tempat_lahir = '$tempat',
                    tanggal_lahir = '$tgllahir',
                    agama = '$agama',
                    pendidikan = '$pendidikan',
                    pekerjaan = '$pekerjaan',
                    golongan_darah = '$goldrah',
                    status_perkawinan = '$perkawinan',
                    tanggal_perkawinan = '$tglnikah',
                    status_keluarga = '$statuskeluarga',
                    kewarganegaraan = '$kewarganegaraan',
                    no_paspor = '$nopaspor',
                    no_kitap = '$nokitap',
                    nama_ayah = '$nmayah',
                    nama_ibu = '$nmibu',
                    no_kk = '$no_kk'
                    WHERE nik = '$nik'";

            if ($conn->query($query_update_penduduk) === TRUE) {
                $conn->commit();
                echo "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Menutup modal terlebih dahulu jika masih terbuka
                        var modalElement = document.getElementById('addDataModal');
                        var modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) modal.hide(); // Menutup modal

                        // Menampilkan pesan sukses dengan SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil edit data'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Mengarahkan kembali ke halaman dengan nomor KK yang baru saja diedit
                                window.location = 'adddataklrg.php?no_kk=" . htmlspecialchars($no_kk, ENT_QUOTES, 'UTF-8') . "';
                            }
                        });
                    });
                </script>";
            }


        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            $errorMessage = $e->getMessage();
            echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal edit data',
                        html: '<b>Error:</b> " . addslashes($errorMessage) . "',
                        confirmButtonText: 'OK'
                    });
                });
            </script>";
        }
    } else {
        // Insert new data
        try {
            $conn->begin_transaction();

            $query_penduduk = "INSERT INTO master_penduduk (nik, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, agama, pendidikan, pekerjaan, golongan_darah, status_perkawinan, tanggal_perkawinan, status_keluarga, kewarganegaraan, no_paspor, no_kitap, nama_ayah, nama_ibu, no_kk)
            VALUES ('$nik', '$nama', '$kelamin', '$tempat', '$tgllahir', '$agama', '$pendidikan', '$pekerjaan', '$goldrah', '$perkawinan', '$tglnikah', '$statuskeluarga', '$kewarganegaraan', '$nopaspor', '$nokitap', '$nmayah', '$nmibu', '$no_kk')";

            if ($conn->query($query_penduduk) === TRUE) {
                $conn->commit();
                echo "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil tambah data'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = 'adddataklrg.php?no_kk=" . htmlspecialchars($no_kk, ENT_QUOTES, 'UTF-8') . "';
                            }
                        });
                    });
                </script>";
            }
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            $errorMessage = $e->getMessage();
            echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal tambah data',
                        html: '<b>Error:</b> " . addslashes($errorMessage) . "',
                        confirmButtonText: 'OK'
                    });
                });
            </script>";
        }
    }
}

// If the Delete button is clicked
if (isset($_GET['deleteData'])) {
    $id = $_GET['nik']; 

    // Query untuk menghapus data dari tabel master_penduduk berdasarkan no_kk
    $query_delete_penduduk = "DELETE FROM master_penduduk WHERE nik = '$id'";

        if ($conn->query($query_delete_penduduk) === TRUE) {
            echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil hapus data',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect kembali dengan no_kk
                            window.location = 'adddataklrg.php?no_kk=" . htmlspecialchars($no_kk, ENT_QUOTES, 'UTF-8') . "';
                        }
                    });
                });
            </script>";
        } else {
            echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal hapus data',
                        confirmButtonText: 'OK'
                    });
                });
            </script>";
}

}
?>

<body>
    <div class="wrapper">
         <?php include 'sidebar.php'; ?>
        <div class="main p-3">
            <div class="card">
                <div class="card-body">
                    <h1>Anggota Kartu Keluarga</h1>

                    <div class="d-flex flex-column align-items-end" style="width: 100%;">
                        <button id="addDataBtn" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addDataModal">
                            <i class="bi bi-plus-lg"></i> Tambah Data
                        </button>
                        <div class="input-group mb-3" style="max-width: 300px;">
                            <input type="text" class="form-control" placeholder="Search..." aria-label="Search" aria-describedby="basic-addon2">
                        </div>
                    </div>

                    <table class="table table-bordered border-light">
                        <thead class="table-secondary" style="text-align: left;">
                            <tr>
                                <td>No</td>
                                <td>No Kartu Keluarga</td>
                                <td>NIK</td>
                                <td>Nama Lengkap</td>
                                <td>Status Keluarga</td>
                                <td>Tempat Tanggal Lahir</td>
                                <td>Aksi</td>
                            </tr>
                        </thead>
                        <tbody style="text-align: left;">
                            <?php 
                            $sql2   = "SELECT *, CONCAT(tempat_lahir, ', ', DATE_FORMAT(tanggal_lahir, '%d-%m-%Y')) AS tempat_tanggal_lahir FROM master_penduduk WHERE no_kk='$no_kk'";
                            $q2     = mysqli_query($conn, $sql2);
                            $urut   = 1;
                            while ($r2 = mysqli_fetch_array($q2)) {
                                $kk       = $r2['no_kk'];
                                $nonik    = $r2['nik'];
                                $juweneng = $r2['nama_lengkap'];
                                $setatus     = $r2['status_keluarga'];
                                $tuwempat     = $r2['tempat_tanggal_lahir'];
                            ?>
                            <tr>
                                <td style="text-align: center;"><?= $urut++ ?></td>
                                <td><?= $kk ?></td>
                                <td><?= $nonik ?></td>
                                <td><?= $juweneng ?></td>
                                <td><?= $setatus ?></td>
                                <td><?= $tuwempat ?></td>
                                <td>
                                    <a href="?op=edit&nik=<?= $nonik ?>" class="btn btn-secondary" style="width: 50px;" data-bs-target="#addDataForm">
                                    <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="javascript:void(0)" data-nik="<?= $nonik ?>" class="btn btn-danger delete-btn" style="width: 50px;">
                                        <i class="bi bi-trash-fill"></i>
                                    <script>
                                        $(document).ready(function() {
                                            $('.delete-btn').on('click', function() {
                                                var nik = $(this).data('nik');  // Ambil NIK dari data-nik
                                                var no_kk = "<?= $no_kk ?>";  // Pastikan no_kk sudah ada di sini

                                                // Tampilkan SweetAlert untuk konfirmasi penghapusan
                                                Swal.fire({
                                                    title: 'Apakah Anda yakin?',
                                                    text: "Data ini akan dihapus secara permanen!",
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Ya, hapus!',
                                                    cancelButtonText: 'Batal'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        // Redirect ke URL untuk menghapus data, tetap pertahankan no_kk
                                                        window.location.href = "?deleteData=true&nik=" + nik + "&no_kk=" + no_kk;
                                                    }
                                                });
                                            });
                                        });
                                    </script>

                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Modal for adding data -->
            <div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="addDataModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6><?= isset($_GET['op']) && $_GET['op'] == 'edit' ? 'Edit Data Anggota Kartu Keluarga' : 'Tambah Data Anggota Kartu Keluarga' ?></h6>
                            <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x"></i></button>
                        </div>
                        <div class="modal-body">
                            <form id="addDataForm" method="POST" action="">
                                <input type="hidden" name="no_kk" value="<?= $no_kk ?>">
                                <div class="mb-3">
                                    <label for="nik" class="form-label">NIK</label>
                                    <input type="text" class="form-control" id="nik" name="nik" value="<?= $nik ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="namalengkap" name="namalengkap" value="<?= $nama ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="jeniskelamin" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" name="jeniskelamin" id="jeniskelamin" required>
                                        <option value=""></option>
                                        <option value="Laki-laki" <?php if ($kelamin == "Laki-laki") echo "selected"; ?>>Laki-laki</option>
                                        <option value="Perempuan" <?php if ($kelamin == "Perempuan") echo "selected"; ?>>Perempuan</option>
                                    </select>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col">
                                        <label for="tempatlahir" class="form-label">Tempat Lahir</label>
                                        <input type="text" class="form-control" id="tempatlahir" name="tempatlahir" value="<?= $tempat ?>" required>
                                    </div>
                                    <div class="col">
                                        <label for="tgllahir" class="form-label">Tanggal Lahir</label>
                                        <input type="date" class="form-control" id="tgllahir" name="tgllahir" value="<?= $tgllahir ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col">
                                        <label for="agama" class="form-label">Agama</label>
                                        <select class="form-select" name="agama" id="agama" required>
                                            <option value=""></option>
                                            <option value="Islam" <?php if ($agama == "Islam") echo "selected"; ?>>Islam</option>
                                            <option value="Kristen" <?php if ($agama == "Kristen") echo "selected"; ?>>Kristen</option>
                                            <option value="Katholik" <?php if ($agama == "Katholik") echo "selected"; ?>>Katholik</option>
                                            <option value="Hindu" <?php if ($agama == "Hindu") echo "selected"; ?>>Hindu</option>
                                            <option value="Budha" <?php if ($agama == "Budha") echo "selected"; ?>>Budha</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label for="pendidikan" class="form-label">Pendidikan</label>
                                        <select class="form-select" name="pendidikan" id="pendidikan" required>
                                            <option value=""></option>
                                            <option value="TIDAK / BELUM SEKOLAH" <?php if ($pendidikan == "TIDAK / BELUM SEKOLAH") echo "selected"; ?>>TIDAK / BELUM SEKOLAH</option>
                                            <option value="BELUM TAMAT SD/SEDERAJAT" <?php if ($pendidikan == "BELUM TAMAT SD/SEDERAJAT") echo "selected"; ?>>BELUM TAMAT SD/SEDERAJAT</option>
                                            <option value="TAMAT SD / SEDERAJAT" <?php if ($pendidikan == "TAMAT SD / SEDERAJAT") echo "selected"; ?>>TAMAT SD / SEDERAJAT</option>
                                            <option value="SLTP / SEDERAJAT" <?php if ($pendidikan == "SLTP / SEDERAJAT") echo "selected"; ?>>SLTP / SEDERAJAT</option>
                                            <option value="SLTA / SEDERAJAT" <?php if ($pendidikan == "SLTA / SEDERAJAT") echo "selected"; ?>>SLTA / SEDERAJAT</option>
                                            <option value="DIPLOMA I / II" <?php if ($pendidikan == "DIPLOMA I / II") echo "selected"; ?>>DIPLOMA I / II</option>
                                            <option value="AKADEMI / DIPLOMA III / S. MUDA" <?php if ($pendidikan == "AKADEMI / DIPLOMA III / S. MUDA") echo "selected"; ?>>AKADEMI / DIPLOMA III / S. MUDA</option>
                                            <option value="DIPLOMA IV / STRATA I" <?php if ($pendidikan == "DIPLOMA IV / STRATA I") echo "selected"; ?>>DIPLOMA IV / STRATA I</option>
                                            <option value="STRATA II" <?php if ($pendidikan == "STRATA II") echo "selected"; ?>>STRATA II</option>
                                            <option value="STRATA III" <?php if ($pendidikan == "STRATA III") echo "selected"; ?>>STRATA III</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="pekerjaan" class="form-label">Pekerjaan</label>
                                    <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" value="<?= $pekerjaan ?>" required>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col">
                                        <label for="goldarah" class="form-label">Golongan Darah</label>
                                        <select class="form-select" name="goldarah" id="goldarah" >
                                            <option value=""></option>
                                            <option value="A" <?php if ($goldrah == "A") echo "selected"; ?>>A</option>
                                            <option value="B" <?php if ($goldrah == "B") echo "selected"; ?>>B</option>
                                            <option value="AB" <?php if ($goldrah == "AB") echo "selected"; ?>>AB</option>
                                            <option value="O" <?php if ($goldrah == "O") echo "selected"; ?>>O</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label for="statusperkawinan" class="form-label">Status Perkawinan</label>
                                        <select class="form-select" name="statusperkawinan" id="statusperkawinan" required onchange="toggleTanggalNikah()">
                                            <option value=""></option>
                                            <option value="Belum kawin" <?php if ($perkawinan == "Belum kawin") echo "selected"; ?>>Belum kawin</option>
                                            <option value="Kawin" <?php if ($perkawinan == "Kawin") echo "selected"; ?>>Kawin</option>
                                            <option value="Cerai hidup" <?php if ($perkawinan == "Cerai hidup") echo "selected"; ?>>Cerai hidup</option>
                                            <option value="Cerai mati" <?php if ($perkawinan == "Cerai mati") echo "selected"; ?>>Cerai mati</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggalnikah" class="form-label">Tanggal Perkawinan</label>
                                    <input type="date" class="form-control" id="tanggalnikah" name="tanggalnikah" value="<?= $tglnikah ?>" >
                                </div>
                                <div class="mb-3 row">
                                    <div class="col">
                                        <label for="hubungan_keluarga" class="form-label">Status Hubungan Keluarga</label>
                                        <select class="form-select" name="hubungan_keluarga" id="hubungan_keluarga" required>
                                            <option value=""></option>
                                            <option value="Kepala Keluarga" <?php if ($statuskeluarga == "Kepala Keluarga") echo "selected"; ?>>Kepala Keluarga</option>
                                            <option value="Suami" <?php if ($statuskeluarga == "Suami") echo "selected"; ?>>Suami</option>
                                            <option value="Istri" <?php if ($statuskeluarga == "Istri") echo "selected"; ?>>Istri</option>
                                            <option value="Anak" <?php if ($statuskeluarga == "Anak") echo "selected"; ?>>Anak</option>
                                            <option value="Menantu" <?php if ($statuskeluarga == "Menantu") echo "selected"; ?>>Menantu</option>
                                            <option value="Orang Tua" <?php if ($statuskeluarga == "Orang Tua") echo "selected"; ?>>Orang Tua</option>
                                            <option value="Mertua" <?php if ($statuskeluarga == "Mertua") echo "selected"; ?>>Mertua</option>
                                            <option value="Pembantu" <?php if ($statuskeluarga == "Pembantu") echo "selected"; ?>>Pembantu</option>
                                            <option value="Famili Lain" <?php if ($statuskeluarga == "Famili Lain") echo "selected"; ?>>Famili Lain</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label for="kewarganegaraan" class="form-label">Kewarganegaraan</label>
                                        <select class="form-select" name="kewarganegaraan" id="kewarganegaraan" required onchange="togglenokitap()">
                                            <option value=""></option>
                                            <option value="WNI" <?php if ($kewarganegaraan == "WNI") echo "selected"; ?>>WNI</option>
                                            <option value="WNA" <?php if ($kewarganegaraan == "WNA") echo "selected"; ?>>WNA</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col">
                                        <label for="nopaspor" class="form-label">No Paspor</label>
                                        <input type="text" class="form-control" id="nopaspor" name="nopaspor" value="<?= $nopaspor ?>" >
                                    </div>
                                    <div class="col">
                                        <label for="nokitap" class="form-label">No KITAP</label>
                                        <input type="text" class="form-control" id="nokitap" name="nokitap" value="<?= $nokitap ?>" >
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col">
                                        <label for="namaayah" class="form-label">Nama Ayah</label>
                                        <input type="text" class="form-control" id="namaayah" name="namaayah" value="<?= $nmayah ?>" required>
                                    </div>
                                    <div class="col">
                                        <label for="namaibu" class="form-label">Nama Ibu</label>
                                        <input type="text" class="form-control" id="namaibu" name="namaibu" value="<?= $nmibu ?>" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary" name="simpan">
                                    <?= isset($_GET['op']) && $_GET['op'] == 'edit' ? 'Update' : 'Simpan' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
   <script>
    function togglenokitap(){
        const kewarganegaraan = document.getElementById("kewarganegaraan").value;
        const nokitap = document.getElementById("nokitap");

        // Nonaktifkan input nokitap jika kewarganegaraan adalah WNI
        nokitap.disabled = (kewarganegaraan === "WNI");
        
        // Kosongkan kolom KITAP jika WNI untuk menghindari pengisian yang tidak diinginkan
        if (kewarganegaraan === "WNI") {
            nokitap.value = '';
        }
    }

    function toggleTanggalNikah() {
        const statusPerkawinan = document.getElementById("statusperkawinan").value;
        const tanggalNikah = document.getElementById("tanggalnikah");

        if (statusPerkawinan === "Kawin") {
            tanggalNikah.disabled = false;
        } else {
            tanggalNikah.disabled = true;
            tanggalNikah.value = ""; // Menghapus nilai jika status bukan "Kawin"
        }
    }

    // Panggil fungsi saat halaman dimuat untuk memastikan status yang benar
   
    document.addEventListener("DOMContentLoaded", () => {
        togglenokitap();
        toggleTanggalNikah();
    });

    $(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);

    // Ketika modal ditutup
    $("#addDataModal").on("hidden.bs.modal", function () {
        // Periksa apakah URL memiliki parameter op=edit
        if (urlParams.get("op") === "edit") {
            // Ambil no_kk dari PHP
            const no_kk = <?= json_encode(htmlspecialchars($no_kk, ENT_QUOTES, 'UTF-8')) ?>;

            // Redirect ke URL awal dengan parameter no_kk
            window.location.href = "//localhost/web/admin/adddataklrg.php?no_kk=" + no_kk;
        }
    });
});

    $(document).ready(function() {
        // Jika URL mengandung parameter 'op=edit', maka buka modal
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('op') === 'edit') {
            $('#addDataModal').modal('show');
        }

    });
    
</script>

</body>

</html>