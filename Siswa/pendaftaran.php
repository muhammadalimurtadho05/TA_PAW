<?php
session_start();
require_once 'database.php';

$username = $_SESSION['username'];
$user = getUserByUsername($username);
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../conn.php';

if (isset($_POST['submit'])) {
    // $username = $_SESSION['username'];
    $nisn = $_POST['nisn'];
    $nama_ayah = $_POST['nama_ayah'];
    $nama_ibu = $_POST['nama_ibu'];
    $alamat = $_POST['alamat'];
    $asal_sekolah = $_POST['asal_sekolah'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $telp = $_POST['telp'];
    $telp_ortu = $_POST['telp_ortu'];

    $db = constant("DBC");

    // ====== 1️⃣ Simpan ke tabel pendaftaran ======
    $query = $db->prepare("INSERT INTO pendaftaran 
        (NISN, NAMA_AYAH, NAMA_IBU, ALAMAT, ASAL_SEKOLAH, TEMPAT_LAHIR, TANGGAL_LAHIR, JENIS_KELAMIN, TELP, TELP_ORTU, STATUS_DAFTAR)
        VALUES (:nisn,:nama_ayah,:nama_ibu,:alamat,:asal_sekolah,:tempat_lahir,:tanggal_lahir,:jenis_kelamin,:telp,:telp_ortu, 'P')");

    // $query->bindValue(':username', $username);
    $query->bindValue(':nisn', $nisn);
    $query->bindValue(':nama_ayah', $nama_ayah);
    $query->bindValue(':nama_ibu', $nama_ibu);
    $query->bindValue(':alamat', $alamat);
    $query->bindValue(':asal_sekolah', $asal_sekolah);
    $query->bindValue(':tempat_lahir', $tempat_lahir);
    $query->bindValue(':tanggal_lahir', $tanggal_lahir);
    $query->bindValue(':jenis_kelamin', $jenis_kelamin);
    $query->bindValue(':telp', $telp);
    $query->bindValue(':telp_ortu', $telp_ortu);
    $query->execute();

    if ($query->rowCount() > 0) {
        $id_daftar = $db->lastInsertId();

        // ====== 2️⃣ Upload 5 berkas PDF ======
        $berkas_labels = [
            'Ijazah', 
            'Raport', 
            'Kartu Keluarga', 
            'Akte Lahir', 
            'KTP Orang Tua'
        ];

        $upload_dir = '../assets/uploads_berkas/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($berkas_labels as $key => $label) {
            $input_name = 'berkas' . ($key + 1);
            if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === 0) {
                $file_name = $_FILES[$input_name]['name'];
                $file_tmp = $_FILES[$input_name]['tmp_name'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                if ($file_ext !== 'pdf') {
                    echo "<script>alert('Semua berkas harus dalam format PDF!');</script>";
                    exit;
                }

                $new_name = time() . '_' . $label . '.pdf';
                $target_path = $upload_dir . $new_name;

                if (move_uploaded_file($file_tmp, $target_path)) {
                    $stmt = $db->prepare("INSERT INTO berkas_siswa (ID_DAFTAR, NAMA_BERKAS, BERKAS) VALUES (?, ?, ?)");
                    $stmt->execute([$id_daftar, $label, $target_path]);
                }
            }
        }

        echo "<script>alert('Pendaftaran dan unggah 5 berkas PDF berhasil!'); window.location='index.php'</script>";
    } else {
        echo "<script>alert('Pendaftaran gagal disimpan.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pendaftaran Siswa Baru</title>
    <link rel="stylesheet" href="../assets/css/daftar.css">
    <link rel="stylesheet" href="../assets/css/siswa.css">
</head>
<body>
    <header class="navbar">
        <div class="logo">
            <span><strong>PPDB</strong> Online <strong>Pesantren</strong></span>
        </div>
        <nav class="menu">
            <a href="index.php">Home</a>
            <a href="riwayat.php">Riwayat Pendaftaran</a>
            <a href="pendaftaran.php" class="active">Pendaftaran</a>
            <a href="edit_profil.php" class="btn">Edit Profil</a>
            <a href="logout.php" class="logout">Logout</a>
        </nav>
        <div class="user">
            <a href="profil.php">
                <?php if (!empty($user['FOTO_SISWA'])): ?>
                    <img src="../assets/uploads/<?= $user['FOTO_SISWA']; ?>" alt="Foto Profil">
                <?php else: ?>
                    <img src="../assets/default.jpg" alt="Foto Default">
                <?php endif; ?>
            </a>
        </div>
    </header>

    <div class="container-form">
        <h2>Formulir Pendaftaran Siswa Baru</h2>
        <form method="POST" enctype="multipart/form-data">

            <label>NISN:</label>
            <input type="text" name="nisn" >

            <label>Nama Ayah:</label>
            <input type="text" name="nama_ayah" >

            <label>Nama Ibu:</label>
            <input type="text" name="nama_ibu" >

            <label>Alamat:</label>
            <textarea name="alamat" ></textarea>

            <label>Asal Sekolah:</label>
            <input type="text" name="asal_sekolah" >

            <label>Tempat Lahir:</label>
            <input type="text" name="tempat_lahir" >

            <label>Tanggal Lahir:</label>
            <input type="date" name="tanggal_lahir" >

            <label>Jenis Kelamin:</label>
            <div class="radio-group">
                <input type="radio" name="jenis_kelamin" value="L" > Laki-laki
                <input type="radio" name="jenis_kelamin" value="P" > Perempuan
            </div>

            <label>No. Telp:</label>
            <input type="text" name="telp" >

            <label>No. Telp Orang Tua:</label>
            <input type="text" name="telp_ortu" >

            <h3>Unggah Berkas (PDF Saja)</h3>
            <label>1. Ijazah:</label>
            <input type="file" name="berkas1" accept="application/pdf" >

            <label>2. Akte Kelahiran:</label>
            <input type="file" name="berkas2" accept="application/pdf" >

            <label>3. Kartu Keluarga:</label>
            <input type="file" name="berkas3" accept="application/pdf" >

            <label>4. Surat sehat:</label>
            <input type="file" name="berkas4" accept="application/pdf" >

            <label>5. Surat Pernyataan:</label>
            <input type="file" name="berkas5" accept="application/pdf" >

            <button type="submit" name="submit">Daftar & Upload</button>
        </form>
    </div>
</body>
</html>
