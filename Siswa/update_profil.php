<?php
session_start();
require_once '../conn.php';

$username = $_SESSION['username'];
$nama = $_POST['nama'];
$password = $_POST['password'];

// Ambil file foto
$fotoBaru = $_FILES['foto']['name'];
$fotoTmp = $_FILES['foto']['tmp_name'];

if (!empty($fotoBaru)) {
    // Buat nama unik biar tidak tabrakan
    $namaFile = time() . "_" . basename($fotoBaru);
    $targetDir = "../assets/uploads/" . $namaFile;

    move_uploaded_file($fotoTmp, $targetDir);

    // Update data dengan foto baru
    if (!empty($password)) {
        $stmt = DBC->prepare("UPDATE USERS SET NAMA=:nama, PASSWORD=:pass, FOTO_SISWA=:foto WHERE USERNAME=:user");
        $stmt->execute([':nama' => $nama, ':pass' => $password, ':foto' => $namaFile, ':user' => $username]);
    } else {
        $stmt = DBC->prepare("UPDATE USERS SET NAMA=:nama, FOTO_SISWA=:foto WHERE USERNAME=:user");
        $stmt->execute([':nama' => $nama, ':foto' => $namaFile, ':user' => $username]);
    }
} else {
    // Tidak upload foto
    if (!empty($password)) {
        $stmt = DBC->prepare("UPDATE USERS SET NAMA=:nama, PASSWORD=:pass WHERE USERNAME=:user");
        $stmt->execute([':nama' => $nama, ':pass' => $password, ':user' => $username]);
    } else {
        $stmt = DBC->prepare("UPDATE USERS SET NAMA=:nama WHERE USERNAME=:user");
        $stmt->execute([':nama' => $nama, ':user' => $username]);
    }
}

// update session nama
$_SESSION['nama'] = $nama;

echo "<script>alert('Profil berhasil diperbarui!'); window.location='index.php';</script>";
