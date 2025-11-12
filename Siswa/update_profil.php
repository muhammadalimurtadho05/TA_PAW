<?php
session_start();
require_once '../conn.php';

$username = $_SESSION['username'];
$nama = $_POST['nama'];
$password = $_POST['password'];

$fotoBaru = $_FILES['foto']['name'];
$fotoTmp  = $_FILES['foto']['tmp_name'];

if (!empty($fotoBaru)) {

    $namaFile = time() . "_" . basename($fotoBaru);
    $targetDir = "../assets/uploads/" . $namaFile;
    move_uploaded_file($fotoTmp, $targetDir);

    if (!empty($password)) {
        $stmt = DBC->prepare("UPDATE USERS SET NAMA=:nama, PASSWORD=:pass, FOTO_SISWA=:foto WHERE USERNAME=:user");
        $stmt->execute([
            ':nama' => $nama,
            ':pass' => $password,
            ':foto' => $namaFile,
            ':user' => $username
        ]);
    } else {
        $stmt = DBC->prepare("UPDATE USERS SET NAMA=:nama, FOTO_SISWA=:foto WHERE USERNAME=:user");
        $stmt->execute([
            ':nama' => $nama,
            ':foto' => $namaFile,
            ':user' => $username
        ]);
    }
} else {

    if (!empty($password)) {
        $stmt = DBC->prepare("UPDATE USERS SET NAMA=:nama, PASSWORD=:pass WHERE USERNAME=:user");
        $stmt->execute([
            ':nama' => $nama,
            ':pass' => $password,
            ':user' => $username
        ]);
    } else {
        $stmt = DBC->prepare("UPDATE USERS SET NAMA=:nama WHERE USERNAME=:user");
        $stmt->execute([
            ':nama' => $nama,
            ':user' => $username
        ]);
    }
}


$_SESSION['nama'] = $nama;


$_SESSION['pesan'] = [
    'tipe' => 'sukses',
    'teks' => '✅ Profil berhasil diperbarui!'
];


header("Location: index.php");

exit;

exit;


header("Location: index.php");

