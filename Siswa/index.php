<?php
session_start();


if (isset($_SESSION['pesan'])) {
    $tipe = $_SESSION['pesan']['tipe'] ?? 'info';
    $teks = $_SESSION['pesan']['teks'] ?? '';


    $warnaBg  = ($tipe === 'sukses') ? '#d4edda' : (($tipe === 'error') ? '#f8d7da' : '#cce5ff');
    $warnaTxt = ($tipe === 'sukses') ? '#155724' : (($tipe === 'error') ? '#721c24' : '#004085');


    echo "
    <style>
        @keyframes fadeOut {
            0% { opacity: 1; }
            80% { opacity: 1; }
            100% { opacity: 0; display: none; }
        }
        .alert-fade {
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            background: {$warnaBg};
            color: {$warnaTxt};
            padding: 12px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            z-index: 9999;
            font-weight: 500;
            text-align: center;
            width: fit-content;
            max-width: 90%;
            animation: fadeOut 3s ease forwards;
        }
    </style>

    <div class='alert-fade'>{$teks}</div>
    ";
    unset($_SESSION['pesan']);
}
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once '../conn.php';

$username = $_SESSION['username'];
$query = DBC->prepare("SELECT * FROM USERS WHERE USERNAME = :username");
$query->execute([':username' => $username]);
$user = $query->fetch();
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
            <a href="index.php">Home</a>
            <a href="#">riwayat Pendaftaran</a>
            <a href="pendaftaran.php">Pendaftaran</a>
            <a href="edit_profil.php" class="btn">Edit Profil</a>
        </nav>
        <div class="user">
            <a href="profil.php" class="nama-user">
                <?= ($_SESSION['nama']) ?>
            </a>
            <a href="logout.php" class="logout">Logout</a>
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