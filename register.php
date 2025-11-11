<?php
session_start();
require_once 'conn.php';
require_once 'database.php';

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
            <p>Sudah punya akun? <a href="login.php">login</a></p>

            <form method="POST" action="">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Nama Lengkap">

                <label>Username</label>
                <input type="text" name="username" placeholder="isi dengan NISN anda">

                <label>Password</label>
                <input type="password" name="password" placeholder="password">

                <button type="submit">Register</button>
            </form>
        </div>

        <div class="bg-box">
            <img src="assets/image/bg.jpg" alt="bg" />
        </div>
    </div>
</body>

</html>