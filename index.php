<?php
session_start();
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
            <h2>Login</h2>
            <p>Belum punya akun? <a href="register.php">Register</a></p>

            <form method="POST" autocomplete="off">
                <label>Username</label>
                <input type="text" name="username" placeholder="masukkan username anda">

                <label>Password</label>
                <input type="password" name="password" placeholder="Password">
                <select name="role" id="">
                    <option value="siswa">Calon Siswa</option>
                    <option value="admin">Admin</option>
                </select>

                <button type="submit">Login</button>
            </form>
        </div>

        <div class="bg-box">
            <img src="assets/image/bg.jpg" alt="bg" />
        </div>
    </div>
</body>

</html>