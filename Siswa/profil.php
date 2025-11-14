<?php
session_start();
require_once '../conn.php';
require_once 'database.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
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
        <div class="logo">
            <span><strong>PPDB</strong> Online <strong>Pesantren</strong></span>
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
                <?php if (!empty($user['FOTO_SISWA'])): ?>
                    <img src="../assets/uploads/<?= htmlspecialchars($user['FOTO_SISWA']); ?>" alt="Foto Profil">
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
                <?php if ($user['FOTO_SISWA']): ?>
                    <img src="../assets/uploads/<?= ($user['FOTO_SISWA']); ?>" alt="Foto Profil">
                <?php else: ?>
                    <img src="../assets/image/default.jpeg" alt="Foto Default">
                <?php endif; ?>
            </div>

            <div class="data-box">
                <label>Username</label>
                <input type="text" name="nama" readonly value="<?= ($user['USERNAME']); ?>">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" readonly value="<?= ($user['NAMA']); ?>">
                <label>Password</label>
                <input type="password" name="password" readonly placeholder="*******">
                <br></br>
                <button type="submit"> <a href="edit_profil.php" class="edit">Edit Profil</a></button>
            </div>
        </div>
    </div>

</body>

</html>