<?php
if (!defined('APP_SECURE')) {
    require_once 'error.php';
    die();
}
$kamar = getAllKamar();
if (isset($_POST['tambah'])) {
    $tambah = tambahKamar($_POST);
}
if (isset($_POST['edit'])) {
    $edit =  editKamar($_POST);
}
if (isset($_GET['hapus'])) {
    hapusKamar();
}
?>
<div class="top">
    <div class="kiri">
        <div class="page"><a href="index.php">Dashboard</a> / Kamar</div>
        <h1>Daftar Kamar</h1><br>
    </div>
    <?php if (isset($_SESSION['msg_sc'])): ?>
        <div class="kanan">
            <span class="success-alert"><?= $_SESSION['msg_sc'] ?> </span>
        </div>
        <?php
        unset($_SESSION['msg_sc']);
        ?>
    <?php endif ?>
    <?php if (isset($_SESSION['msg_err'])): ?>
        <div class="kanan">
            <?php
            ?>
            <?php foreach ($_SESSION['msg_err'] as $err): ?>
                <div class="danger-alert"><?= $err ?></div>
            <?php endforeach ?>
        </div>
        <?php
        unset($_SESSION['msg_err']);
        ?>
    <?php endif ?>
    <?php if (isset($_GET['edit'])): ?>
        <?php
        $dtl_kamar = getKamarName($_GET['id_km']);
        ?>
        <form action="" method="POST" class="frkamar editjurusan">
            <label for="nama">Nama Kamar</label>
            <input type="hidden" name="id" value="<?= $dtl_kamar['ID_KAMAR'] ?>">
            <input type="text" id="nama" value="<?= $_POST['kamar'] ?? $dtl_kamar['KAMAR'] ?>" name="kamar"><br>
            <span class="errspan knn"><?= $edit['kamar'] ?? '' ?></span>
            <label for="kapasitas">Kapasitas</label>
            <input type="text" id="kapasitas" name="kapasitas" value="<?= $_POST['kapasitas'] ?? $dtl_kamar['KAPASITAS'] ?>"><br>
            <span class="errspan knn"><?= $edit['kapasitas'] ?? '' ?></span>
            <div class="btn">
                <button type="submit" class="btn-tambah" name="edit">Edit</button>
            </div>
        </form>
    <?php endif ?>
</div>
<a href="index.php?page=kamar&add" class="show">Tambah Kamar Baru</a>
<?php if (isset($_GET['add'])): ?>
    <form action="" method="POST" class="frkamar">
        <label for="nama">Nama Kamar</label>
        <input type="text" id="nama" value="<?= $_POST['kamar'] ?? '' ?>" name="kamar"><br>
        <span class="errspan"><?= $tambah['kamar'] ?? '' ?></span>
        <label for="kapasitas">Kapasitas</label>
        <input type="text" id="kapasitas" value="<?= $_POST['kapasitas'] ?? '' ?>" name="kapasitas"><br>
        <span class="errspan"><?= $tambah['kapasitas'] ?? '' ?></span>
        <button type="submit" name="tambah" class="btn-tambah">Tambah</button>
    </form>
<?php endif ?>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kamar</th>
            <th>Jumlah</th>
            <th>Kapasitas</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($kamar as $km): ?>
            <tr>
                <td><?= $no++ ?>.</td>
                <td><?= $km['KAMAR'] ?></td>
                <td><?= $km['jumlah'] ?></td>
                <td><?= $km['KAPASITAS'] ?></td>
                <td>
                    <a href="index.php?page=kamar&id=<?= $km['ID_KAMAR'] ?>" class="lihat">Lihat</a>
                    <a href="index.php?page=kamar&id_km=<?= $km['ID_KAMAR'] ?>&edit" class="show-edit">Edit</a>
                    <a href="index.php?page=kamar&id_km=<?= $km['ID_KAMAR'] ?>&hapus" class="hps">Hapus</a>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>