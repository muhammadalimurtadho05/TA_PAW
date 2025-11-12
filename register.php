<?php
session_start();
require_once 'conn.php';
require_once 'database.php';



if (isset($_SESSION['pesan'])) {
    $tipe = $_SESSION['pesan']['tipe'];
    $teks = $_SESSION['pesan']['teks'];

    $warnaBg  = ($tipe === 'sukses') ? '#d4edda' : '#f8d7da';
    $warnaTxt = ($tipe === 'sukses') ? '#155724' : '#721c24';

    echo "<div class='alert-message {$tipe}'>{$teks}</div>";
    unset($_SESSION['pesan']);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'username' => $_POST['username'],
        'nama'     => $_POST['nama'],
        'password' => $_POST['password']
    ];
    register($data);
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
            <p>Sudah punya akun? <a href="login.php">Login</a></p>

            <form method="POST" action="" autocomplete="off">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Nama Lengkap" required>

                <label>Username</label>
                <input type="text" name="username" placeholder="Isi dengan NISN anda" required>

                <label>Password</label>
                <input type="password" name="password" placeholder="Password" required>

                <button type="submit">Register</button>
            </form>
        </div>

        <div class="bg-box">
            <img src="assets/image/bg.jpg" alt="bg" />
        </div>
    </div>
</body>

</html>