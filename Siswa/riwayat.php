<?php
session_start();
require_once 'database.php';
if (isset($_SESSION['pesan'])) {
    $tipe = $_SESSION['pesan']['tipe'] ?? 'info';
    $teks = $_SESSION['pesan']['teks'] ?? '';

    echo "<div class='alert-message {$tipe}'>{$teks}</div>";
    unset($_SESSION['pesan']);
}

require_once '../conn.php';
require_once 'database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
}
$username = $_SESSION['username'];

// Ambil data pendaftaran + berkas
$data = getPendaftaranByUser($username);
$berkas = $data ? getBerkasByPendaftaran($data['ID_DAFTAR']) : [];


$username = $_SESSION['username'];
$user = getUserByUsername($username);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Pendaftaran - PPDB Online</title>
    <link rel="stylesheet" href="../assets/css/siswa.css">
    <link rel="stylesheet" href="../assets/css/riwayat.css">
</head>

<body>

    <header class="navbar">
        <div class="logo">
            <span><strong>PPDB</strong> Online <span>Madrasah Aliyah </span> <strong>Al Hikmah</strong></span>
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

    <div class="riwayat-container">
        <div class="riwayat-box">
            <h2>Riwayat Pendaftaran</h2>

            <?php if (!$data): ?>
                <p class="belum-daftar">Anda belum melakukan pendaftaran.</p>
            <?php else: ?>

                <!-- Tabs Navigasi -->
                <div class="tabs">
                    <input type="radio" id="tab1" name="tab" checked>
                    <label for="tab1">Status Pendaftaran</label>
                    <input type="radio" id="tab2" name="tab">
                    <label for="tab2">Data Pendaftaran</label>

                    <!-- TAB 1: STATUS -->
                    <div class="tab-content" id="content1">
                        <?php
                        if ($data['STATUS_DAFTAR'] == '1') {
                            echo "<div class='status status-diterima'>🎉 Selamat! Anda <strong>Diterima</strong> di Pesantren.</div>";
                        } elseif ($data['STATUS_DAFTAR'] == '2') {
                            echo "<div class='status status-ditolak'>❌ Maaf, Anda <strong>Belum Diterima</strong>. Silakan hubungi panitia untuk informasi lebih lanjut.</div>";
                        } else {
                            echo "<div class='status status-menunggu'>⌛ Pendaftaran Anda masih dalam proses verifikasi.</div>";
                        }
                        ?>

                        <div class="status-info">
                            <p><strong>Nama:</strong> <?= htmlspecialchars($data['NAMA']); ?></p>
                            <?php if ($data['STATUS_DAFTAR'] == '1'): ?>
                                <p><strong>Jurusan:</strong> <?= htmlspecialchars($data['NAMA_JURUSAN']); ?></p>
                                <p><strong>Kamar:</strong> <?= htmlspecialchars($data['KAMAR']); ?></p>
                            <?php elseif ($data['STATUS_DAFTAR'] == '0'): ?>
                                <p><strong>Jurusan:</strong> <?= htmlspecialchars($data['NAMA_JURUSAN']); ?></p>
                                <p><strong>Kamar:</strong> -</p>
                            <?php else: ?>
                                <p><strong>Jurusan:</strong> -</p>
                                <p><strong>Kamar:</strong> -</p>
                            <?php endif; ?>
                            <p><strong>Tanggal Daftar:</strong> <?= htmlspecialchars($data['CREATED_AT']); ?></p>
                        </div>
                    </div>

                    <!-- TAB 2: DATA PENDAFTARAN -->
                    <div class="tab-content" id="content2">
                        <div class="foto-siswa">
                            <?php if (!empty($data['FOTO'])): ?>
                                <img src="../assets/uploads/<?= htmlspecialchars($data['FOTO']); ?>" alt="Foto Profil">
                            <?php else: ?>
                                <img src="../assets/image/default.jpeg" alt="Foto Default">
                            <?php endif; ?>
                        </div>

                        <div class="data-grid">
                            <div><span class="label">Nama Lengkap</span><strong class="value"><?= htmlspecialchars($data['NAMA']); ?></strong></div>
                            <div><span class="label">Username</span><span class="value"><?= htmlspecialchars($data['USERNAME']); ?></span></div>
                            <div><span class="label">NISN</span><span class="value"><?= htmlspecialchars($data['NISN']); ?></span></div>
                            <div><span class="label">Jurusan</span><span class="value"><?= htmlspecialchars($data['NAMA_JURUSAN']); ?></span></div>
                            <div><span class="label">Tempat, Tanggal Lahir</span><span class="value"><?= htmlspecialchars($data['TEMPAT_LAHIR']); ?>, <?= ($data['TANGGAL_LAHIR']); ?></span></div>
                            <div><span class="label">Alamat</span><span class="value"><?= htmlspecialchars($data['ALAMAT']); ?></span></div>
                            <div><span class="label">Nama Ayah</span><span class="value"><?= htmlspecialchars($data['NAMA_AYAH']); ?></span></div>
                            <div><span class="label">Nama Ibu</span><span class="value"><?= htmlspecialchars($data['NAMA_IBU']); ?></span></div>
                            <div><span class="label">Jenis Kelamin</span><span class="value"><?= htmlspecialchars($data['JENIS_KELAMIN'] == 'L' ? 'Laki-laki' : 'Perempuan'); ?></span></div>
                            <div><span class="label">Asal Sekolah</span><span class="value"><?= htmlspecialchars($data['ASAL_SEKOLAH']); ?></span></div>
                            <h3>📋 Data Pendaftaran</h3>
                            <?php if ($berkas): ?>
                                <?php foreach ($berkas as $b): ?>
                                    <div>
                                        <span class="label"><?= ($b['NAMA_BERKAS']); ?></span>
                                        <span class="value"><a href="../assets/uploads/<?= htmlspecialchars($b['BERKAS']); ?>" target="_blank" class="lihat-btn">Lihat</a></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>



</html>