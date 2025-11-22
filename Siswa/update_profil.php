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
        $stmt = $pdo->prepare("UPDATE USERS SET NAMA=:nama, PASSWORD=:pass, FOTO=:foto WHERE USERNAME=:user");
        $stmt->execute([
            ':nama' => $nama,
            ':pass' => md5($password),
            ':foto' => $namaFile,
            ':user' => $username
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE USERS SET NAMA=:nama, FOTO=:foto WHERE USERNAME=:user");
        $stmt->execute([
            ':nama' => $nama,
            ':foto' => $namaFile,
            ':user' => $username
        ]);
    }
} else {

    if (!empty($password)) {
        $stmt = $pdo->prepare("UPDATE USERS SET NAMA=:nama, PASSWORD=:pass WHERE USERNAME=:user");
        $stmt->execute([
            ':nama' => $nama,
            ':pass' => md5($password),
            ':user' => $username
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE USERS SET NAMA=:nama WHERE USERNAME=:user");
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
