<?php
session_start();
require_once 'database.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
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
    <title>Edit Profil - PPDB Online</title>
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
            <a href="pendaftaran.php">Pendaftaran</a>
            <a href="edit_profil.php" class="btn">Edit Profil</a>
            <a href="logout.php" class="logout">Logout</a>
        </nav>
        <div class="user">
            <a href="profil.php">
                <?php if (!empty($user['FOTO_SISWA'])): ?>
                    <img src="../assets/uploads/<?= htmlspecialchars($user['FOTO_SISWA']); ?>" alt="Foto Profil">
                <?php else: ?>
                    <img src="../assets/default.jpg" alt="Foto Default">
                <?php endif; ?>
            </a>
        </div>
    </header>
    <div class="container-form">
        <h2>Edit Profil</h2>
        <form action="update_profil.php" method="POST" enctype="multipart/form-data">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" value="<?= ($user['NAMA']); ?>">

            <label>Password (kosongkan jika tidak diubah)</label>
            <input type="password" name="password" placeholder="Password baru">

            <label>Foto Profil</label>
            <input type="file" name="foto" accept=".jpg,.jpeg,.png">

            <?php if ($user['FOTO_SISWA']): ?>
                <p>Foto saat ini:</p>
                <img src="../assets/uploads/<?= ($user['FOTO_SISWA']); ?>" width="100">
            <?php endif; ?>

            <button type="submit" name="update">Simpan Perubahan</button>
        </form>
    </div>
</body>

</html>