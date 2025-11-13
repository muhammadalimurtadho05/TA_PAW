<?php
if(!defined('APP_SECURE')){
    require_once 'error.php';
    die();
}
$terima = pendaftarTerima();
$online = pendaftarOnline();
$jurusan = jumlahJurusan();
?>
<h1>Dashboard</h1>
<div class="dashboard">
    <div class="dashboard-bawah">
        <h2 class="ttl">Selamat datang di Dashboard Admin PPDB!</h2>
        <p class="ttl">Kelola data pendaftaran, verifikasi berkas, dan pantau proses seleksi peserta didik baru dengan mudah dan efisien. Pastikan seluruh informasi calon siswa tercatat dengan benar untuk kelancaran proses PPDB.</p>
    </div>
    <div class="dashboard-atas">
        <a href="index.php?page=pendaftar" class="card">
            <div class="in-card">
                <img src="Asset/Img/statistik.png" class="icon" alt="">
                <div class="card-content">
                    <h2>Pendaftar Online</h2>
                    <h1 class="jumlah"><?= $online?></h1>
                </div>
            </div>
        </a>
        <a href="index.php?page=siswa" class="card">

        <div class="in-card">
            <img src="Asset/Img/people.png" class="icon" alt="">
            <div class="card-content">
                <h2>Siswa Diterima</h2>
                <h1 class="jumlah"><?= $terima?></h1>
            </div>
        </div>
        </a>
        <a href="index.php?page=jurusan" class="card">

        <div class="in-card">
            <img src="Asset/Img/major.png" class="icon" alt="">
            <div class="card-content">
                <h2>Jurusan Tersedia</h2>
                <h1 class="jumlah"><?= $jurusan?></h1>
            </div>
        </div>
        </a>
    </div>
    
</div>