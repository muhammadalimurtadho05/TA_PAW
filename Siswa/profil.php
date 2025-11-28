<?php
session_start();
require_once '../conn.php';
require_once 'database.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];
$user = getUserByUsername($username);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Profil Saya - PPDB Online</title>
    <link rel="stylesheet" href="../assets/css/siswa.css">
</head>

<body>
    <header class="navbar">
        <div class="logo"><span><strong>PPDB</strong> Online <span>Madrasah Aliyah </span> <strong>Al Hikmah</strong></span>
        </div>
        <nav class="menu">
            <a href="index.php">Home</a>
            <a href="riwayat.php" class="active">Riwayat Pendaftaran</a>
            <a href="pendaftaran.php">Pendaftaran</a>
            <a href="edit_profil.php" class="btn">Edit Profil</a>
            <a href="logout.php" class="logout">Logout</a>
        </nav>
        <div class="user">
            <a href="profil.php">
                <?php if (!empty($user['FOTO'])): ?>
                    <img src="../assets/uploads/<?= htmlspecialchars($user['FOTO']); ?>" alt="Foto Profil">
                <?php else: ?>
                    <img src="../assets/image/default.jpeg" alt="Foto Default">
                <?php endif; ?>
            </a>
        </div>
    </header>

    <div class="container-form" style="margin-top:120px;">
        <h2>Profil Saya</h2>

        <div class="profil-info">
            <div class="foto-box">
                <?php if ($user['FOTO']): ?>
                    <img src="../assets/uploads/<?= htmlspecialchars($user['FOTO']); ?>" alt="Foto Profil">
                <?php else: ?>
                    <img src="../assets/image/default.jpeg" alt="Foto Default">
                <?php endif; ?>
            </div>

            <div class="data-grid">
                <div><span class="label">Username</span><span class="value"><?= htmlspecialchars($user['USERNAME']); ?></span></div>
                <div><span class="label">Nama Lengkap</span><strong class="value"><?= htmlspecialchars($user['NAMA']); ?></strong></div>
                <div><span class="label">password</span><strong class="value">*******</strong></div>
                 <a href="edit_profil.php" class="edit">Edit Profil</a>
            </div>
        </div>
    </div>

</body>

</html>