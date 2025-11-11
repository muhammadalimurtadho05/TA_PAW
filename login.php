<?php
session_start();

if (isset($_SESSION['pesan'])) {
    $tipe = $_SESSION['pesan']['tipe'];
    $teks = $_SESSION['pesan']['teks'];

    $warnaBg  = ($tipe === 'sukses') ? '#d4edda' : '#f8d7da';
    $warnaTxt = ($tipe === 'sukses') ? '#155724' : '#721c24';

    echo "<div style='
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: {$warnaBg};
            color: {$warnaTxt};
            padding: 12px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            z-index: 9999;
            font-weight: 500;
            text-align: center;
            width: fit-content;
            max-width: 90%;
          '>
            {$teks}
          </div>";
    unset($_SESSION['pesan']);
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require_once 'database.php';
    login($_POST);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Siswa</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container">
        <div class="form-box">
            <h2>Login Siswa</h2>
            <p>Belum punya akun? <a href="register.php">Register</a></p>

            <form method="POST" action="" autocomplete="off">
                <label>Username</label>
                <input type="text" name="username" placeholder="isi dengan NISN anda">

                <label>Password</label>
                <input type="password" name="password" placeholder="Password">

                <button type="submit">Login</button>
            </form>
        </div>

        <div class="bg-box">
            <img src="assets/image/bg.jpg" alt="bg" />
        </div>
    </div>
</body>

</html>