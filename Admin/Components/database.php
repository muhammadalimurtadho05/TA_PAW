<?php
require_once BASE_PATH . '/Admin/Components/validate.inc';

function getAllUsers()
{
    global $pdo;
    $users = $pdo->prepare("SELECT * FROM users WHERE ROLE = 0");
    $users->execute();
    return $users->fetchAll();
}

function getAllPendaftar()
{
    global $pdo;
    $pendaftar = $pdo->prepare("
    SELECT pendaftaran.NISN,pendaftaran.STATUS_DAFTAR,jurusan.NAMA_JURUSAN, jurusan.DETAIL_JURUSAN, users.NAMA,users.USERNAME 
    FROM pendaftaran JOIN jurusan ON pendaftaran.ID_JURUSAN = jurusan.ID_JURUSAN
    JOIN users ON pendaftaran.USERNAME = users.USERNAME ");
    $pendaftar->execute();
    return $pendaftar->fetchAll();
}

function getDetailUser()
{
    global $pdo;
    $user = $pdo->prepare("SELECT pendaftaran.*, users.NAMA,users.FOTO, jurusan.*,kamar.KAMAR FROM pendaftaran JOIN users ON pendaftaran.USERNAME = users.USERNAME JOIN jurusan ON jurusan.ID_JURUSAN = pendaftaran.ID_JURUSAN JOIN kamar ON pendaftaran.ID_KAMAR = kamar.ID_KAMAR WHERE pendaftaran.USERNAME = :username");
    $user->execute([':username' => $_GET['user']]);
    return $user->fetch();
}

// Cek kamar mana yang kosong
function cek_kamar()
{
    global $pdo;
    $penghuni = $pdo->prepare("SELECT 
            kamar.KAMAR,
            kamar.ID_KAMAR,
            kamar.KAPASITAS,
            COUNT(pendaftaran.ID_KAMAR) AS penghuni
        FROM kamar
        LEFT JOIN pendaftaran 
            ON kamar.ID_KAMAR = pendaftaran.ID_KAMAR
        GROUP BY 
            kamar.ID_KAMAR, kamar.KAMAR 
        HAVING kamar.KAPASITAS > penghuni LIMIT 1");
    $penghuni->execute();
    if ($penghuni->rowCount() > 0) {
        $kamar_kosong = $penghuni->fetch();
        return $kamar_kosong['ID_KAMAR'];
    } else {
        return false;
    }
}

// Siswa diterima
function terimaSiswa()
{
    global $pdo;
    $kamar = cek_kamar();
    if ($kamar) {
        $terima = $pdo->prepare("UPDATE pendaftaran SET STATUS_DAFTAR = 1, ID_KAMAR = :id WHERE USERNAME = :user");
        $terima->execute([
            ':id' => $kamar,
            ':user' => $_GET['user']
        ]);
    } else {
        $_SESSION['msg_err'] = 'Semua Kamar Telah Penuh!';
    }
    header("Location:index.php?page=detail&user=" . $_GET['user']);
    exit;
}

// Siswa Ditolak
function tolakSiswa()
{
    global $pdo;
    $tolak = $pdo->prepare("UPDATE pendaftaran SET STATUS_DAFTAR = 2,ID_KAMAR = 1 WHERE USERNAME = :user");
    $tolak->execute([
        ':user' => $_GET['user']
    ]);
}
function pendingSiswa()
{
    global $pdo;
    $tolak = $pdo->prepare("UPDATE pendaftaran SET STATUS_DAFTAR = 0 WHERE USERNAME = :user");
    $tolak->execute([
        ':user' => $_GET['user']
    ]);
}

// Daftar Kamar
function getAllKamar()
{
    global $pdo;
    $kamar = $pdo->prepare("SELECT kamar.*, COUNT(pendaftaran.ID_KAMAR) AS jumlah FROM kamar LEFT JOIN pendaftaran ON kamar.ID_KAMAR = pendaftaran.ID_KAMAR WHERE kamar.ID_KAMAR != 1 GROUP BY kamar.ID_KAMAR;
    ");
    $kamar->execute();
    return $kamar->fetchAll();
}

// Tambah Kamar
function tambahKamar($array)
{
    global $pdo;
    $reNama = "/^[a-zA-Z0-9\s]+$/";
    $reAngka = "/^[0-9]*$/";
    $errors = [];
    validate($errors, $array, 'kamar', $reNama, "Nama Kamar Hanya Mengandung Alfabet", "Kamar");
    validate($errors, $array, 'kapasitas', $reAngka, "Kapasitas Merupakan Angka", "Kapasitas");
    if (!$errors) {
        $ins = $pdo->prepare("INSERT INTO kamar VALUES(NULL, :kamar, :kapasitas)");
        $ins->execute([
            ':kamar' => $array['kamar'],
            ':kapasitas' => $array['kapasitas']
        ]);
        $_SESSION['msg_sc'] = 'Kamar Berhasil Ditambah';

        header("Location:index.php?page=kamar");
        die;
    }
    return $errors;
}

// Penghuni Kamar
function getSiswaKamar()
{
    global $pdo;
    $kamar = $pdo->prepare("SELECT pendaftaran.*, users.NAMA FROM pendaftaran JOIN users ON pendaftaran.USERNAME = users.USERNAME WHERE pendaftaran.ID_KAMAR = :id");
    $kamar->execute([':id' => $_GET['id']]);
    return $kamar->fetchAll();
}

// Daftar Jurusan
function getAllJurusan()
{
    global $pdo;
    $jurusan = $pdo->prepare("SELECT jurusan.*, COUNT(pendaftaran.ID_JURUSAN) AS jumlah FROM jurusan LEFT JOIN pendaftaran ON jurusan.ID_JURUSAN = pendaftaran.ID_JURUSAN AND pendaftaran.STATUS_DAFTAR = 1 GROUP BY jurusan.ID_JURUSAN ");
    $jurusan->execute();
    return $jurusan->fetchAll();
}

// Tambah Jurusan
function tambahJurusan(&$errors, $array)
{
    global $pdo;
    $reNama = "/^[a-zA-Z\s]+$/";
    //  = [];
    validate($errors, $array, 'jurusan', $reNama, "Nama Jurusan Hanya Mengandung Alfabet", "Jurusan");
    validate($errors, $array, 'dtl', $reNama, "Detail Jurusan Hanya Mengandung Alfabet", "Detail Jurusan");
    if (!$errors) {
        $jurusan = $pdo->prepare("INSERT INTO jurusan VALUES (NULL, :nama, :detail)");
        $jurusan->execute([':nama' => $array['jurusan'], ':detail' => $array['dtl']]);
        if ($jurusan->rowCount() > 0) {
            $_SESSION['msg_sc'] = 'Jurusan Berhasil Ditambah';
            header('Location:index.php?page=jurusan');
            die;
        }
    }
    return $errors;
}

// Edit jurusan
function editJurusan(&$errors, $array)
{
    global $pdo;
    $reNama = "/^[a-zA-Z\s]+$/";
    validate($errors, $array, 'jurusan', $reNama, "Nama Jurusan Hanya Mengandung Alfabet", "Jurusan");
    validate($errors, $array, 'dtl', $reNama, "Detail Jurusan Hanya Mengandung Alfabet", "Detail Jurusan");
    if (!$errors) {
        $update = $pdo->prepare("UPDATE jurusan SET NAMA_JURUSAN = :nama, DETAIL_JURUSAN = :detail WHERE ID_JURUSAN = :id");
        $update->execute([
            ':nama' => $array['jurusan'],
            ':detail' => $array['dtl'],
            ':id' => $array['id']
        ]);
        $_SESSION['msg_sc'] = 'Jurusan Berhasil Diupdate';
        header("Location:index.php?page=jurusan");
        exit;
    }
    return $errors;
}
function editKamar($array)
{
    global $pdo;
    $errors = [];
    $reNama = "/^[a-zA-Z0-9\s]+$/";
    $reAngka = "/^[0-9]*$/";
    validate($errors, $array, 'kamar', $reNama, "Nama Kamar Hanya Mengandung Alfabet", "Kamar");
    validate($errors, $array, 'kapasitas', $reAngka, "Kapasitas Merupakan Angka", "Kapasitas");
    if (!$errors) {
        $update = $pdo->prepare("UPDATE kamar SET KAMAR = :nama, KAPASITAS = :kapas WHERE ID_KAMAR = :id");
        $update->execute([
            ':nama' => $array['kamar'],
            ':kapas' => $array['kapasitas'],
            ':id' => $array['id']
        ]);
        $_SESSION['msg_sc'] = 'Kamar Berhasil Diupdate';
        header("Location:index.php?page=kamar");
        exit;
    }
    return $errors;
}

function getJurusanName()
{
    global $pdo;
    $jurusan = $pdo->prepare("SELECT * FROM jurusan WHERE ID_JURUSAN = :id");
    $jurusan->execute([':id' => $_GET['id']]);
    return $jurusan->fetch();
}

function getKamarName($id)
{
    global $pdo;
    $kamar = $pdo->prepare("SELECT * FROM KAMAR WHERE ID_KAMAR = :id");
    $kamar->execute([':id' => $id]);
    return $kamar->fetch();
}

// Hapus Jurusan
function hapusJurusan()
{
    global $pdo;
    $cekJurusan = $pdo->prepare('SELECT ID_DAFTAR FROM pendaftaran WHERE ID_JURUSAN = :id');
    $cekJurusan->execute([':id' => $_GET['id']]);
    if ($cekJurusan->rowCount() == 0) {
        $hapus = $pdo->prepare("DELETE FROM jurusan WHERE ID_JURUSAN = :id");
        $hapus->execute([':id' => $_GET['id']]);
        if ($hapus->rowCount() > 0) {
            $_SESSION['msg_sc'] = 'Jurusan Berhasil Dihapus';
            header("Location:index.php?page=jurusan");
            exit;
        }
    } else {
        $_SESSION['msg_err'] = ['Jurusan Gagal Dihapus'];
        header("Location:index.php?page=jurusan");
        exit;
    }
}
function hapusKamar()
{
    global $pdo;
    $cekKamar = $pdo->prepare('SELECT ID_DAFTAR FROM pendaftaran WHERE ID_KAMAR = :id');
    $cekKamar->execute([':id' => $_GET['id_km']]);
    if ($cekKamar->rowCount() == 0) {
        $hapus = $pdo->prepare("DELETE FROM KAMAR WHERE ID_KAMAR = :id");
        $hapus->execute([':id' => $_GET['id_km']]);
        if ($hapus->rowCount() > 0) {
            $_SESSION['msg_sc'] = 'Kamar Berhasil Dihapus!';
            header("Location:index.php?page=kamar");
            exit;
        }
    } else {
        $_SESSION['msg_err'] = ['Kamar Gagal Dihapus'];
        header("Location:index.php?page=kamar");
        exit;
    }
}

// Menampilkan Siswa Berdasarkan Jurusan
function getSiswaJurusan()
{
    global $pdo;
    $siswa = $pdo->prepare("SELECT pendaftaran.*, users.NAMA,kamar.KAMAR FROM pendaftaran JOIN users ON pendaftaran.USERNAME = users.USERNAME JOIN kamar ON pendaftaran.ID_KAMAR = kamar.ID_KAMAR WHERE pendaftaran.ID_JURUSAN = :id AND pendaftaran.STATUS_DAFTAR = 1");
    $siswa->execute([':id' => $_GET['id']]);
    if ($siswa->rowCount() > 0) {
        return $siswa->fetchAll();
    } else {
        return false;
    }
}

// Update Profile Admin
function updateProfileAdmin($array)
{
    global $pdo;
    // Cek username
    $errors = [];
    $rePass = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9])\S+$/";
    $reNama = "/^[a-zA-Z\s]+$/";
    validate($errors, $array, 'nama', $reNama, "Nama Hanya Mengandung Alfabet", "Nama");
    if (!empty($array['pass'])) {
        validasi_jumlah($errors, $array, 'pass', 8, "Password minimal berjumlah 8 karakter");
        validate($errors, $array, 'pass', $rePass, "Password Kombinasi huruf kecil,huruf besar, angka dan karakter khusus", "Password");
        if (!$errors) {
            $update = $pdo->prepare("UPDATE users SET NAMA = :nama, PASSWORD = :pass WHERE username = :username");
            $update->execute([
                ':nama' => $array['nama'],
                ':pass' => md5($array['pass']),
                ':username' => $_SESSION['username']
            ]);
            if ($update->rowCount() > 0) {
                $_SESSION['nama']     = $array['nama'];
            }
        }
    } else {
        if (!$errors) {
            $update = $pdo->prepare("UPDATE users SET NAMA = :nama WHERE username = :username");
            $update->execute([
                ':nama' => $array['nama'],
                ':username' => $_SESSION['username']
            ]);
            if ($update->rowCount() > 0) {
                $_SESSION['nama']     = $array['nama'];
            }
        }
    }
    return $errors;
}
function unsetErr()
{
    if (isset($_SESSION['msg_err'])) {
        unset($_SESSION['msg_err']);
    }
}

// Logout
function logout()
{
    session_start();
    session_unset();
    session_destroy();
    header("Location: ../index.php");
}

function pendaftarTerima()
{
    global $pdo;
    $terima = $pdo->prepare("SELECT COUNT(USERNAME) AS jumlah FROM pendaftaran WHERE STATUS_DAFTAR = 1");
    $terima->execute();
    $temp = $terima->fetch();
    return $temp['jumlah'];
}
function pendaftarOnline()
{
    global $pdo;
    $online = $pdo->prepare("SELECT COUNT(USERNAME) AS jumlah FROM pendaftaran");
    $online->execute();
    $temp = $online->fetch();
    return $temp['jumlah'];
}
function jumlahJurusan()
{
    global $pdo;
    $online = $pdo->prepare("SELECT COUNT(ID_JURUSAN) AS jumlah FROM jurusan");
    $online->execute();
    $temp = $online->fetch();
    return $temp['jumlah'];
}

function getAllSiswa()
{
    global $pdo;
    $siswa = $pdo->prepare("SELECT pendaftaran.*, users.NAMA, jurusan.NAMA_JURUSAN, kamar.KAMAR FROM pendaftaran JOIN users ON pendaftaran.USERNAME = users.USERNAME JOIN jurusan ON pendaftaran.ID_JURUSAN = jurusan.ID_JURUSAN JOIN kamar ON pendaftaran.ID_KAMAR = kamar.ID_KAMAR WHERE pendaftaran.STATUS_DAFTAR = 1");
    $siswa->execute();
    return $siswa->fetchAll();
}
function getBerkasByPendaftaran($id_daftar)
{
    global $pdo;
    $bq = $pdo->prepare("SELECT NAMA_BERKAS, BERKAS FROM BERKAS_SISWA WHERE ID_DAFTAR = :id");
    $bq->execute([':id' => $id_daftar]);
    return $bq->fetchAll();
}
