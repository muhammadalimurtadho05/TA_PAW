<?php
session_start();
require_once 'conn.php';
require_once 'database.php';
require_once 'validate.php';

// Jika form disubmit → proses register()
$hasil  = [];
$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hasil = register($_POST);
    $errors = $hasil['errors'] ?? [];
    $old    = $hasil['old']    ?? [];
}

// Tampilkan pesan sukses (bukan error validasi)
if (isset($_SESSION['pesan'])) {
    $tipe = $_SESSION['pesan']['tipe'] ?? 'info';
    $teks = $_SESSION['pesan']['teks'] ?? '';

    echo "<div class='alert-message {$tipe}'>{$teks}</div>";
    unset($_SESSION['pesan']);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Registrasi Siswa</title>
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>
    <div class="container">
        <div class="form-box">
            <h2>Registrasi Siswa</h2>
            <p>Sudah punya akun? <a href="index.php">Login</a></p>

            <form method="POST" action="" autocomplete="off">
                <label>Nama Lengkap</label>
                <input type="text"
                       name="nama"
                       placeholder="Nama Lengkap"
                       value="<?= htmlspecialchars($old['nama'] ?? '') ?>">
                <span class="errors"><?= $errors['nama'] ?? '' ?></span>


                <label>Username</label>
                <input type="text"
                       name="username"
                       placeholder="isi username anda"
                       value="<?= htmlspecialchars($old['username'] ?? '') ?>">
                <span class="errors"><?= $errors['username'] ?? '' ?></span>


                <label>Password</label>
                <input type="password"
                       name="password"
                       placeholder="Password">
                <span class="errors"><?= $errors['password'] ?? '' ?></span>

                <button type="submit">Register</button>
            </form>
        </div>

        <div class="bg-box">
            <img src="assets/image/bg.jpg" alt="bg" />
        </div>
    </div>
</body>

</html>
