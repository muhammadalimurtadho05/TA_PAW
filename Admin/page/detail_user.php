<?php
if(!defined('APP_SECURE')){
    require_once 'error.php';
    die();
}
$user = getDetailUser();
// var_dump($user);
if(isset($_GET['terima'])){
    terimaSiswa();
    header("Location:index.php?page=detail&user=".$_GET['user']);
}elseif(isset($_GET['tolak'])){
    tolakSiswa();
    header("Location:index.php?page=detail&user=".$_GET['user']);
}elseif(isset($_GET['pending'])){
    pendingSiswa();
    header("Location:index.php?page=detail&user=".$_GET['user']);
}
?>
<h1></h1>    
<div class="profile-card">
    <div class="avatar">A</div>
    <h2><?= $user['NAMA'] ?></h2>
    <p style="color: rgb(93, 199, 187);"><?= $user['USERNAME'] ?></p>

    <div class="profile-info">
        <div>
            <strong>NISN</strong>
            <span><?= $user['NISN'] ?></span>
        </div>
        <div>
            <strong>Jurusan</strong>
            <span><?= $user['NAMA_JURUSAN'] ?></span>
        </div>
        <div>
            <strong>Tempat, Tanggal Lahir</strong>
            <span><?= $user['TEMPAT_LAHIR'].', '.$user['TANGGAL_LAHIR'] ?></span>
        </div>
        <div>
            <strong>Kamar</strong>
            <span><?= $user['KAMAR'] ?></span>
        </div>
        <div>
            <strong>Alamat</strong>
            <span><?= $user['ALAMAT'] ?></span>
        </div>
        <div>
            <strong>Nama Ayah</strong>
            <span><?= $user['NAMA_AYAH'] ?></span>
        </div>
        <div>
            <strong>Nama Ibu</strong>
            <span><?= $user['NAMA_IBU'] ?></span>
        </div>
        <div>
            <strong>Jenis Kelamin</strong>
            <span><?= $user['JENIS_KELAMIN'] ?></span>
        </div>
        <div>
            <strong>Asal Sekolah</strong>
            <span><?= $user['ASAL_SEKOLAH'] ?></span>
        </div>
        <div>
            <strong>Kartu Keluarga</strong>
            <span><a href="" class="show">Lihat</a></span>
        </div>
        <div>
            <strong>Akta Kelahiran</strong>
            <span><a href="" class="show">Lihat</a></span>
        </div>
        <div>
            <strong>Ijazah</strong>
            <span><a href="" class="show">Lihat</a></span>
        </div>
        <div>
            <strong>Surat Sehat</strong>
            <span><a href="" class="show">Lihat</a></span>
        </div>
        <div>
            <strong>Surat Pernyataan</strong>
            <span><a href="" class="show">Lihat</a></span>
        </div>
        <div>
            <strong>Status Daftar</strong>
            <?php if($user['STATUS_DAFTAR'] == 0){?>
                <span class="pending">Pending</span>
            <?php }else if($user['STATUS_DAFTAR'] == 1){?>
                <span class="pending">Diterima</span>
            <?php
            }else{
            ?>
            <span class="tolak">Ditolak</span>
            <?php }?>
        </div>
    </div>
    <?php if($user['STATUS_DAFTAR'] == 0):?>
        <div class="tombol-bawah">
            <a class="dec" href="index.php?page=detail&user=<?= $user['USERNAME'] ?>&tolak">Tolak!</a>
            <a class="acc" href="index.php?page=detail&user=<?= $user['USERNAME'] ?>&terima">Terima!</a>
        </div>
    <?php endif?>
    <?php if($user['STATUS_DAFTAR'] == 1):?>
        <div class="tombol-bawah">
            <a class="dec" href="index.php?page=detail&user=<?= $user['USERNAME'] ?>&tolak">Tolak!</a>
            <a class="acc" href="index.php?page=detail&user=<?= $user['USERNAME'] ?>&pending">Pending!</a>
        </div>
        <?php else:?>
            <div class="tombol-bawah">
                <a class="dec" href="index.php?page=detail&user=<?= $user['USERNAME'] ?>&pending">Pending!</a>
                <a class="acc" href="index.php?page=detail&user=<?= $user['USERNAME'] ?>&terima">Terima!</a>
            </div>
    <?php endif?>
</div>