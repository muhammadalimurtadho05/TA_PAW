<?php
session_start();
require_once 'database.php';

if (isset($_SESSION['pesan'])) {
    $tipe = $_SESSION['pesan']['tipe'] ?? 'info';
    $teks = $_SESSION['pesan']['teks'] ?? '';

    echo "<div class='alert-message {$tipe}'>{$teks}</div>";
    unset($_SESSION['pesan']);
}
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../conn.php';

$username = $_SESSION['username'];
$user = getUserByUsername($username);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Siswa - PPDB Online</title>
    <link rel="stylesheet" href="../assets/css/siswa.css">

</head>

<body>
    <header class="navbar">
        <div class="logo">
            <span><strong>PPDB</strong> Online <strong>Pesantren</strong></span>
        </div>
        <nav class="menu">


            <a href="index.php" class="active">Home</a>
            <a href="riwayat.php">Riwayat Pendaftaran</a>
            <a href="pendaftaran.php">Pendaftaran</a>
            <a href="edit_profil.php" class="btn">Edit Profil</a>
            <a href="logout.php" class="logout">Logout</a>
        </nav>
        <div class="user">
            <a href="profil.php">
                <?php if (!empty($user['FOTO_SISWA'])): ?>
                    <img src="../assets/uploads/<?= htmlspecialchars($user['FOTO_SISWA']); ?>" alt="Foto Profil">
                <?php else: ?>
                    <img src="../assets/image/default.jpeg" alt="Foto Default">
                <?php endif; ?>
            </a>
        </div>

    </header>

    <section class="hero">

        <div class="hero-text">
            <h1>Selamat Datang<br>Di PPDB Online</h1>
            <p class="sub">Pesantren</p>
            <p>Akses dan cari tahu prosedur serta ketentuan informasi pendaftaran Pesantren.</p>
            <a href="pendaftaran.php" class="btn">Mendaftar</a>
        </div>
        <div class="hero-img">
            <img src="../assets/image/bg.jpg" alt="Gedung Pesantren">
        </div>
    </section>
</body>

</html>