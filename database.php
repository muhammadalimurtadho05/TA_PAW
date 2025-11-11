<?php


require_once 'conn.php';
require_once 'base.php';


// User Login
function login()
{
    $username = $_POST['username'];
    $passwd   = $_POST['password'];

    $user = DBC->prepare("SELECT * FROM USERS WHERE USERNAME = :username AND PASSWORD = :pass");
    $user->execute([
        ':username' => $username,
        ':pass'     => $passwd
    ]);

    if ($user->rowCount() == 1) {
        $data = $user->fetch();

        session_start();
        $_SESSION['username'] = $data['USERNAME'];
        $_SESSION['nama']     = $data['NAMA'];
        $_SESSION['role']     = $data['ROLE'];

        if ($data['ROLE'] == '1') {
            header("Location: Admin/index.php");
        } else {
            header("Location: Siswa/index.php");
        }
        exit;
    } else {
        echo "<script>alert('Username atau password salah!'); window.location='login.php';</script>";
    }
}

// Register
function register($array)
{
    // Cek apakah username sudah terdaftar
    $cek = cekUsername($array['username']);
    if ($cek == 0) {
        $register = DBC->prepare("
            INSERT INTO USERS (USERNAME, PASSWORD, NAMA, FOTO_SISWA, ROLE)
            VALUES (:username, :pass, :nama, NULL, '0')
        ");
        $register->execute([
            ':username' => $array['username'],
            ':pass'     => $array['password'],
            ':nama'     => $array['nama']
        ]);

        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Username sudah terdaftar!'); window.location='register.php';</script>";
    }
}

// Cek Ketersediaan USERNAME
function cekUsername($username)
{
    $cek = DBC->prepare("SELECT USERNAME FROM USERS WHERE USERNAME = :user");
    $cek->execute([':user' => $username]);
    return $cek->rowCount();
}
