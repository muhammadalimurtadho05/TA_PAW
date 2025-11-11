<?php
require_once 'conn.php';

// User Login
function login()
{
    session_start();

    $username = $_POST['username'];
    $passwd   = $_POST['password'];

    $user = DBC->prepare("SELECT * FROM USERS WHERE USERNAME = :username AND PASSWORD = :pass");
    $user->execute([
        ':username' => $username,
        ':pass'     => $passwd
    ]);

    if ($user->rowCount() == 1) {
        $data = $user->fetch();

        $_SESSION['username'] = $data['USERNAME'];
        $_SESSION['nama']     = $data['NAMA'];
        $_SESSION['role']     = $data['ROLE'];

        $_SESSION['role']     = $data['ROLE'];



        $_SESSION['pesan'] = [
            'tipe' => 'sukses',
            'teks' => '✅ Selamat datang, ' . $data['NAMA'] . '!'
        ];

        if ($data['ROLE'] == '1') {
            header("Location: Admin/index.php");
        } else {
            header("Location: Siswa/index.php");
        }
        exit;
    } else {
        // ❌ Pesan error
        $_SESSION['pesan'] = [
            'tipe' => 'error',
            'teks' => '❌ Username atau password salah!'
        ];
        header("Location: login.php");
        exit;
    }
}


function register($array)
{
    session_start();

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


        $_SESSION['pesan'] = [
            'tipe' => 'sukses',
            'teks' => '✅ Registrasi berhasil! Silakan login.'
        ];
        header("Location: login.php");
        exit;
    } else {

        $_SESSION['pesan'] = [
            'tipe' => 'error',
            'teks' => '⚠️ Username sudah terdaftar!'
        ];
        header("Location: register.php");
        exit;
    }
}


// Cek Ketersediaan USERNAME
function cekUsername($username)
{
    $cek = DBC->prepare("SELECT USERNAME FROM USERS WHERE USERNAME = :user");
    $cek->execute([':user' => $username]);
    return $cek->rowCount();
}