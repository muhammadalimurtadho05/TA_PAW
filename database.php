<?php


require_once 'conn.php';

// User Login
function login()
{
    global $pdo;
    session_start();

    $username = $_POST['username'];
    $passwd   = md5($_POST['password']);

    // ======= CEK ADMIN DULU =======
    $cekAdmin = $pdo->prepare("SELECT * FROM ADMIN WHERE USERNAME_ADMIN = :username");
    $cekAdmin->execute([':username' => $username]);

    if ($cekAdmin->rowCount() == 1) {
        $admin = $cekAdmin->fetch();

        if ($admin['PASSWORD_ADMIN'] === $passwd) {
            // LOGIN ADMIN
            $_SESSION['username'] = $admin['USERNAME_ADMIN'];
            $_SESSION['nama']     = $admin['NAMA_ADMIN'];
            $_SESSION['role']     = 'Admin';

            $_SESSION['pesan'] = [
                'tipe' => 'sukses',
                'teks' => '👨‍💼 Selamat datang, Admin ' . $admin['NAMA_ADMIN'] . '!'
            ];

            header("Location: Admin/index.php");
            exit;
        }
    }

    // ======= CEK SISWA JIKA BUKAN ADMIN =======
    $cekUser = $pdo->prepare("SELECT * FROM USERS WHERE USERNAME = :username");
    $cekUser->execute([':username' => $username]);

    if ($cekUser->rowCount() == 1) {
        $user = $cekUser->fetch();
        if ($user['PASSWORD'] === $passwd) {

            $_SESSION['username'] = $user['USERNAME'];
            $_SESSION['nama']     = $user['NAMA'];
            $_SESSION['role']     = 'Siswa';

            $_SESSION['pesan'] = [
                'tipe' => 'sukses',
                'teks' => '🎉 Selamat datang, ' . $user['NAMA']
            ];

            header("Location: Siswa/index.php");
            exit;
        }
    }

    // ======= GAGAL LOGIN =======
    $_SESSION['pesan'] = [
        'tipe' => 'error',
        'teks' => '❌ Username atau Password salah!'
    ];
    header("Location: index.php");
    exit;
}

function register($array)
{
    require_once 'validate.php';

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
    global $pdo;
    // Insert DB
    $register = $pdo->prepare("
        INSERT INTO USERS (USERNAME, PASSWORD, NAMA, FOTO)
        VALUES (:username, md5(:pass), :nama, NULL)
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

    header("Location: index.php");
    exit;
}

// Cek Ketersediaan USERNAME
function cekUsername($username)
{
    global $pdo;

    // Cek di tabel ADMIN
    $cekAdmin = $pdo->prepare("SELECT USERNAME_ADMIN FROM ADMIN WHERE USERNAME_ADMIN = :user");
    $cekAdmin->execute([':user' => $username]);

    // Cek di tabel USERS
    $cekUser = $pdo->prepare("SELECT USERNAME FROM USERS WHERE USERNAME = :user");
    $cekUser->execute([':user' => $username]);



    return  $cekAdmin->rowCount() + $cekUser->rowCount();
}
