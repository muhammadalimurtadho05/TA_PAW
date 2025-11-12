<?php
if(!defined('APP_SECURE')){
    require_once 'error.php';
    die();
}
$jurusan = getAllJurusan();
if($_SERVER['REQUEST_METHOD']=='POST'){
    tambahJurusan($_POST);
}
if(isset($_GET['hapus'])){
    hapusJurusan();
}
?>
<div class="top">
    <div class="kiri">
        <div class="page"><a href="index.php">Dashboard</a> / Jurusan</div>
        <h1>Daftar Jurusan</h1><br>
    </div>
    <?php if(isset($_SESSION['msg'])):?>
        <div class="kanan">
            <span class="success-alert"><?= $_SESSION['msg']?> </span>
        </div>
        <?php
        unset($_SESSION['msg']);
        ?>
    <?php endif?>
</div>
<a href="index.php?page=jurusan&add" class="show">Tambah Jurusan Baru</a>
<?php if(isset($_GET['add'])):?>
<form action="" method="POST" class="frkamar">
    <label for="nama">Nama Jurusan</label>
    <input type="text" id="nama" name="jurusan"><br>
    <label for="kapasitas">Detail Jurusan</label>
    <input type="text" id="kapasitas" name="dtl"><br>
    <button type="submit" class="btn-tambah">Tambah</button>
</form>
<?php endif?>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Jurusan</th>
            <th>Detail Jurusan</th>
            <th>Jumlah Siswa</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1; 
        foreach( $jurusan as $km ):?>
        <tr>
            <td><?= $no++?>.</td>
            <td><?= $km['NAMA_JURUSAN'] ?></td>
            <td><?= $km['DETAIL_JURUSAN'] ?></td>
            <td><?= $km['jumlah'] ?></td>
            <td>
                <?php if($km['jumlah']>0):?>
                <a href="index.php?page=siswa_jurusan&id=<?= $km['ID_JURUSAN'] ?>" class="lihat">Lihat</a>
                <?php endif?>
                <a href="index.php?page=jurusan&id=<?= $km['ID_JURUSAN'] ?>&hapus" class="hps">Hapus</a>
            </td>
        </tr>
        <?php endforeach?>
        
    </tbody>
</table>