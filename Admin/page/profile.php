<?php
if(!defined('APP_SECURE')){
    require_once 'error.php';
    die();
}
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $err = updateProfileAdmin($_POST);
}
?>
<div class="alert-msg-prof">
    <?php if(isset($err) && empty($err)):?>
        <div class="success-alert">Profil Berhasil Diupdate!</div>
    <?php endif?>
</div>
<div class="profile-card">
    <div class="avatar">
        <img src="<?=BASE_URL.'Admin/Asset/Img/profile.png'?>" alt="">
    </div>
    <h2><?= $_SESSION['nama'] ?></h2>
    <p style="color: rgb(93, 199, 187);"><?= $_SESSION['username'] ?></p>
    <form method="POST">
        <div class="profile-info">
            <div>
                <strong>Username</strong>
                <span>
                    <input type="text" name="username" disabled class="profile-form" value="<?= $_SESSION['username'] ?>"><br>
                </span>
            </div>
            <div>
                <strong>Nama</strong>
                <span class="knn">
                    <input type="text" name="nama" class="profile-form" value="<?= $_SESSION['nama'] ?>"><br>
                    <span class="errspan"><?= $err['nama'] ?? "" ?></span>
                </span>
            </div>
            <div>
                <span><strong>Password Baru</strong><br>(Kosongkan jika tidak ingin diganti)</span>
                <span class="knn">
                    <input type="password" name="pass" class="profile-form"><br>
                    <span class="errspan"><?= $err['pass'] ?? "" ?></span>
                </span>
            </div>
            <div>
                <span></span>
                <button class="btn-tambah">Update!</button>
            </div>
        </div>
    </form>
</div>  
