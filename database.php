<?php


require_once 'conn.php';

// User Login
function login()
{
    global $pdo;
    session_start();

    $username = $_POST['username'];
    $passwd   = md5($_POST['password']);
    $role = $_POST['role'];
    if($role == 'siswa'){
        $user = $pdo->prepare("SELECT * FROM USERS WHERE USERNAME = :username AND PASSWORD = :pass");
        $user->execute([
            ':username' => $username,
            ':pass'     => $passwd
        ]);
    
        if ($user->rowCount() == 1) {
            $data = $user->fetch();
    
            $_SESSION['username'] = $data['USERNAME'];
            $_SESSION['nama']     = $data['NAMA'];
    
            $_SESSION['pesan'] = [
                'tipe' => 'sukses',
                'teks' => '✅ Selamat datang, ' . $data['NAMA'] . '!'
            ];
            header("Location: Siswa/index.php");
            exit;
        } else {
            // ❌ Pesan error
            $_SESSION['pesan'] = [
                'tipe' => 'error',
                'teks' => '❌ Username atau password salah!'
            ];
            header("Location: index.php");
            exit;
        }
    }else{
        $user = $pdo->prepare("SELECT * FROM ADMIN WHERE USERNAME_ADMIN = :username AND PASSWORD_ADMIN = :pass");
        $user->execute([
            ':username' => $username,
            ':pass'     => $passwd
        ]);
        if ($user->rowCount() == 1) {
            $data = $user->fetch();
    
            $_SESSION['username'] = $data['USERNAME_ADMIN'];
            $_SESSION['nama']     = $data['NAMA_ADMIN'];
            $_SESSION['role']     = 'Admin';
            
            $_SESSION['pesan'] = [
                'tipe' => 'sukses',
                'teks' => '✅ Selamat datang, ' . $data['NAMA_ADMIN'] . '!'
            ];
    
            header("Location: Admin/index.php");
            exit;
        } else {
            // ❌ Pesan error
            $_SESSION['pesan'] = [
                'tipe' => 'error',
                'teks' => '❌ Username atau password salah!'
            ];
            header("Location: index.php");
            exit;
        }
    }
    
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

    header("Location: index.php");
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
