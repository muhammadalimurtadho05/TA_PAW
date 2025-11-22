<?php


require_once 'conn.php';

// User Login
function login()
{
    global $pdo;
    session_start();

    $username = $_POST['username'];
    $passwd   = md5($_POST['password']);

    $user = $pdo->prepare("SELECT * FROM USERS WHERE USERNAME = :username AND PASSWORD = :pass");
    $user->execute([
        ':username' => $username,
        ':pass'     => $passwd
    ]);

    if ($user->rowCount() == 1) {
        $data = $user->fetch();

        $_SESSION['username'] = $data['USERNAME'];
        $_SESSION['nama']     = $data['NAMA'];
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
    require_once 'validate.inc';

    $errors = [];

    $nama     = trim($array['nama']);
    $username = trim($array['username']);
    $password = trim($array['password']);

    // Validasi
    cekNamaDaftar($nama, $errors);
    cekUsernameDaftar($username, $errors);
    cekPasswordDaftar($password, $errors);

    if (cekUsername($username) > 0) {
        $errors['username'] = "Username sudah terdaftar!";
    }

    if (!empty($errors)) {
        return [
            'status' => 'error',
            'errors' => $errors,
            'old'    => [
                'nama'     => $nama,
                'username' => $username
            ]
        ];
    }

    // Insert DB
    $register = $pdo->prepare("
        INSERT INTO USERS (USERNAME, PASSWORD, NAMA, FOTO, ROLE)
        VALUES (:username, md5(:pass), :nama, NULL, '0')
    ");

    $register->execute([
        ':username' => $username,
        ':pass'     => $password,
        ':nama'     => $nama
    ]);

    $_SESSION['pesan'] = [
        'tipe' => 'sukses',
        'teks' => 'Registrasi berhasil! Silakan login.'
    ];

    header("Location: login.php");
    exit;
}

// Cek Ketersediaan USERNAME
function cekUsername($username)
{
    global $pdo;
    $cek = $pdo->prepare("SELECT USERNAME FROM USERS WHERE USERNAME = :user");
    $cek->execute([':user' => $username]);
    return $cek->rowCount();
}
