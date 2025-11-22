<?php
require_once BASE_PATH.'/Admin/Components/validate.inc';

function getAllUsers(){
    global $pdo;
    $users = $pdo->prepare("SELECT * FROM USERS WHERE ROLE = 0");
    $users->execute();
    return $users->fetchAll();
}

function getAllPendaftar(){
    global $pdo;
    $pendaftar = $pdo->prepare("
    SELECT PENDAFTARAN.NISN,PENDAFTARAN.STATUS_DAFTAR,JURUSAN.NAMA_JURUSAN, JURUSAN.DETAIL_JURUSAN, USERS.NAMA,USERS.USERNAME 
    FROM PENDAFTARAN 
    JOIN JURUSAN ON PENDAFTARAN.ID_JURUSAN = JURUSAN.ID_JURUSAN
    JOIN USERS ON PENDAFTARAN.USERNAME = USERS.USERNAME ");
    $pendaftar->execute();
    return $pendaftar->fetchAll();
}
function logout(){
    session_start();
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit;
}

function getDetailUser(){
    global $pdo;
    $user = $pdo->prepare("SELECT PENDAFTARAN.*, USERS.NAMA,USERS.FOTO, JURUSAN.*,KAMAR.KAMAR FROM PENDAFTARAN JOIN USERS ON PENDAFTARAN.USERNAME = USERS.USERNAME JOIN JURUSAN ON JURUSAN.ID_JURUSAN = PENDAFTARAN.ID_JURUSAN JOIN KAMAR ON PENDAFTARAN.ID_KAMAR = KAMAR.ID_KAMAR WHERE PENDAFTARAN.USERNAME = :username");
    $user->execute([':username' => $_GET['user']]);
    return $user->fetch();
}

function cek_kamar(){
    global $pdo;
    $penghuni = $pdo->prepare("SELECT 
            KAMAR.KAMAR,
            KAMAR.ID_KAMAR,
            KAMAR.KAPASITAS,
            COUNT(PENDAFTARAN.ID_KAMAR) AS penghuni
        FROM KAMAR
        LEFT JOIN PENDAFTARAN 
            ON KAMAR.ID_KAMAR = PENDAFTARAN.ID_KAMAR
        GROUP BY 
            KAMAR.ID_KAMAR, KAMAR.KAMAR 
        HAVING KAMAR.KAPASITAS > penghuni LIMIT 1");
    $penghuni->execute();
    if($penghuni->rowCount()>0){
        $kamar_kosong = $penghuni->fetch();
        return $kamar_kosong['ID_KAMAR'];
    }else{
        return false;
    }
}

function terimaSiswa(){
    global $pdo;
    $kamar = cek_kamar();
    if($kamar){
        $terima = $pdo->prepare("UPDATE PENDAFTARAN SET STATUS_DAFTAR = 1, ID_KAMAR = :id WHERE USERNAME = :user");
        $terima->execute([
            ':id' => $kamar,
            ':user'=>$_GET['user']
        ]);
    }else{
        $_SESSION['msg_err'] = 'Semua Kamar Telah Penuh!';
    }
    header("Location:index.php?page=detail&user=".$_GET['user']);
    exit;
}

function tolakSiswa(){
    global $pdo;
    $tolak = $pdo->prepare("UPDATE PENDAFTARAN SET STATUS_DAFTAR = 2,ID_KAMAR = 1 WHERE USERNAME = :user");
    $tolak->execute([
        ':user'=>$_GET['user']
    ]);
}
function pendingSiswa(){
    global $pdo;
    $tolak = $pdo->prepare("UPDATE PENDAFTARAN SET STATUS_DAFTAR = 0 WHERE USERNAME = :user");
    $tolak->execute([
        ':user'=>$_GET['user']
    ]);
}

function getAllKamar(){
    global $pdo;
    $kamar = $pdo->prepare("SELECT KAMAR.*, COUNT(PENDAFTARAN.ID_KAMAR) AS jumlah FROM KAMAR LEFT JOIN PENDAFTARAN ON KAMAR.ID_KAMAR = PENDAFTARAN.ID_KAMAR WHERE KAMAR.ID_KAMAR != 1 GROUP BY KAMAR.ID_KAMAR;");
    $kamar->execute();
    return $kamar->fetchAll();
}

function tambahKamar($array){
    global $pdo;
    $reNama = "/^[a-zA-Z0-9\s]+$/";
    $reAngka = "/^[0-9]*$/";
    $errors = [];
    validate($errors,$array,'kamar',$reNama,"Nama Kamar Hanya Mengandung Alfabet","Kamar");
    validate($errors,$array,'kapasitas',$reAngka,"Kapasitas Merupakan Angka","Kapasitas");
    if(!$errors){
        $ins = $pdo->prepare("INSERT INTO KAMAR VALUES(NULL, :kamar, :kapasitas)");
        $ins->execute([
            ':kamar' => htmlspecialchars($array['kamar']),
            ':kapasitas' => htmlspecialchars($array['kapasitas'])
        ]);
        $_SESSION['msg_sc'] = 'Kamar Berhasil Ditambah';
        header("Location:index.php?page=kamar");
        die;
    }
    return $errors;
}

function getSiswaKamar(){
    global $pdo;
    $kamar = $pdo->prepare("SELECT PENDAFTARAN.*, USERS.NAMA FROM PENDAFTARAN JOIN USERS ON PENDAFTARAN.USERNAME = USERS.USERNAME WHERE PENDAFTARAN.ID_KAMAR = :id");
    $kamar->execute([':id' => $_GET['id']]);
    return $kamar->fetchAll(); 
}

function getAllJurusan(){
    global $pdo;
    $jurusan = $pdo->prepare("SELECT JURUSAN.*, COUNT(PENDAFTARAN.ID_JURUSAN) AS jumlah FROM JURUSAN LEFT JOIN PENDAFTARAN ON JURUSAN.ID_JURUSAN = PENDAFTARAN.ID_JURUSAN AND PENDAFTARAN.STATUS_DAFTAR = 1 GROUP BY JURUSAN.ID_JURUSAN ");
    $jurusan->execute();
    return $jurusan->fetchAll();
}

function tambahJurusan(&$errors,$array){
    global $pdo;
    $reNama = "/^[a-zA-Z\s]+$/";
    validate($errors,$array,'jurusan',$reNama,"Nama Jurusan Hanya Mengandung Alfabet","Jurusan");
    validate($errors,$array,'dtl',$reNama,"Detail Jurusan Hanya Mengandung Alfabet","Detail Jurusan");
    if(!$errors){
        $jurusan = $pdo->prepare("INSERT INTO JURUSAN VALUES (NULL, :nama, :detail)");
        $jurusan->execute([':nama' => htmlspecialchars($array['jurusan']),':detail'=>htmlspecialchars($array['dtl'])]);
        if($jurusan->rowCount()>0){
            $_SESSION['msg_sc'] = 'Jurusan Berhasil Ditambah';    
            header('Location:index.php?page=jurusan');
            die;  
        }
    }
    return $errors;
}

function editJurusan(&$errors,$array){
    global $pdo;
    $reNama = "/^[a-zA-Z\s]+$/";
    validate($errors,$array,'jurusan',$reNama,"Nama Jurusan Hanya Mengandung Alfabet","Jurusan");
    validate($errors,$array,'dtl',$reNama,"Detail Jurusan Hanya Mengandung Alfabet","Detail Jurusan");
    if(!$errors){
        $update = $pdo->prepare("UPDATE JURUSAN SET NAMA_JURUSAN = :nama, DETAIL_JURUSAN = :detail WHERE ID_JURUSAN = :id");
        $update->execute([
            ':nama' => $array['jurusan'],
            ':detail'=>$array['dtl'],
            ':id' => $array['id']
        ]);
        $_SESSION['msg_sc'] = 'Jurusan Berhasil Diupdate';
        header("Location:index.php?page=jurusan");
        exit;
    }
    return $errors;
}

function getJurusanName(){
    global $pdo;
    $jurusan = $pdo->prepare("SELECT * FROM JURUSAN WHERE ID_JURUSAN = :id");
    $jurusan->execute([':id' =>$_GET['id']]);
    return $jurusan->fetch();
}

function editKamar($array){
    global $pdo;
    $errors = [];
    $reNama = "/^[a-zA-Z0-9\s]+$/";
    $reAngka = "/^[0-9]*$/";
    validate($errors,$array,'kamar',$reNama,"Nama Kamar Hanya Mengandung Alfabet","Kamar");
    validate($errors,$array,'kapasitas',$reAngka,"Kapasitas Merupakan Angka","Kapasitas");
    if(!$errors){
        $update = $pdo->prepare("UPDATE KAMAR SET KAMAR = :nama, KAPASITAS = :kapas WHERE ID_KAMAR = :id");
        $update->execute([
            ':nama' => $array['kamar'],
            ':kapas'=>$array['kapasitas'],
            ':id' => $array['id']
        ]);
        $_SESSION['msg_sc'] = 'Kamar Berhasil Diupdate';
        header("Location:index.php?page=kamar");
        exit;
    }
    return $errors;
}

function getKamarName($id){
    global $pdo;
    $kamar = $pdo->prepare("SELECT * FROM KAMAR WHERE ID_KAMAR = :id");
    $kamar->execute([':id' =>$id]);
    return $kamar->fetch();
}

function hapusJurusan(){
    global $pdo;
    $cekJurusan = $pdo->prepare('SELECT ID_DAFTAR FROM PENDAFTARAN WHERE ID_JURUSAN = :id');
    $cekJurusan->execute([':id'=>$_GET['id']]);
    if($cekJurusan->rowCount()==0){
        $hapus = $pdo->prepare("DELETE FROM JURUSAN WHERE ID_JURUSAN = :id");
        $hapus->execute([':id' => $_GET['id']]);
        if($hapus->rowCount()>0){
            $_SESSION['msg_sc'] = 'Jurusan Berhasil Dihapus';
            header("Location:index.php?page=jurusan");
            exit;
        }
    }else{
        $_SESSION['msg_err'] = ['Jurusan Gagal Dihapus'];
        header("Location:index.php?page=jurusan");
        exit;
    }
}

function hapusKamar(){
    global $pdo;
    $cekKamar = $pdo->prepare('SELECT ID_DAFTAR FROM PENDAFTARAN WHERE ID_KAMAR = :id');
    $cekKamar->execute([':id'=>$_GET['id_km']]);
    if($cekKamar->rowCount()==0){
        $hapus = $pdo->prepare("DELETE FROM KAMAR WHERE ID_KAMAR = :id");
        $hapus->execute([':id' => $_GET['id_km']]);
        if($hapus->rowCount()>0){
            $_SESSION['msg_sc'] = 'Kamar Berhasil Dihapus!';
            header("Location:index.php?page=kamar");
            exit;
        }
    }else{
        $_SESSION['msg_err'] = ['Kamar Gagal Dihapus'];
        header("Location:index.php?page=kamar");
        exit;
    }
}

function getSiswaJurusan(){
    global $pdo;
    $siswa = $pdo->prepare("SELECT PENDAFTARAN.*, USERS.NAMA,KAMAR.KAMAR FROM PENDAFTARAN JOIN USERS ON PENDAFTARAN.USERNAME = USERS.USERNAME JOIN KAMAR ON PENDAFTARAN.ID_KAMAR = KAMAR.ID_KAMAR WHERE PENDAFTARAN.ID_JURUSAN = :id AND PENDAFTARAN.STATUS_DAFTAR = 1");
    $siswa->execute([':id' => $_GET['id']]);
    if($siswa->rowCount()>0){
        return $siswa->fetchAll();
    }else{
        return false;
    }
}

function updateProfileAdmin($array){
    global $pdo;
    $errors = [];
    $rePass = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9])\S+$/";
    $reNama = "/^[a-zA-Z\s]+$/";
    validate($errors,$array,'nama',$reNama,"Nama Hanya Mengandung Alfabet","Nama");
    if(!empty($array['pass'])){
        validasi_jumlah($errors,$array,'pass',8,"Password minimal berjumlah 8 karakter");
        validate($errors,$array,'pass',$rePass,"Password Kombinasi huruf kecil,huruf besar, angka dan karakter khusus","Password");
        if(!$errors){
            $update = $pdo->prepare("UPDATE USERS SET NAMA = :nama, PASSWORD = :pass WHERE USERNAME = :username");
            $update->execute([
                ':nama' => $array['nama'],
                ':pass' => md5($array['pass']),
                ':username' => $_SESSION['username']
            ]);
            if($update->rowCount()>0){
                $_SESSION['nama']     = $array['nama'];
            }
        }
    }else{
        if(!$errors){
            $update = $pdo->prepare("UPDATE USERS SET NAMA = :nama WHERE USERNAME = :username");
            $update->execute([
                ':nama' => $array['nama'],
                ':username' => $_SESSION['username']
            ]);
            if($update->rowCount()>0){
                $_SESSION['nama']     = $array['nama'];
            }
        }
    }
    return $errors;
}

function pendaftarTerima(){
    global $pdo;
    $terima = $pdo->prepare("SELECT COUNT(USERNAME) AS jumlah FROM PENDAFTARAN WHERE STATUS_DAFTAR = 1");
    $terima->execute();
    $temp = $terima->fetch();
    return $temp['jumlah'];
}
function pendaftarOnline(){
    global $pdo;
    $online = $pdo->prepare("SELECT COUNT(USERNAME) AS jumlah FROM PENDAFTARAN");
    $online->execute();
    $temp = $online->fetch();
    return $temp['jumlah'];
}
function jumlahJurusan(){
    global $pdo;
    $online = $pdo->prepare("SELECT COUNT(ID_JURUSAN) AS jumlah FROM JURUSAN");
    $online->execute();
    $temp = $online->fetch();
    return $temp['jumlah'];
}

function getAllSiswa(){
    global $pdo;
    $siswa = $pdo->prepare("SELECT PENDAFTARAN.*, USERS.NAMA, JURUSAN.NAMA_JURUSAN, KAMAR.KAMAR FROM PENDAFTARAN JOIN USERS ON PENDAFTARAN.USERNAME = USERS.USERNAME JOIN JURUSAN ON PENDAFTARAN.ID_JURUSAN = JURUSAN.ID_JURUSAN JOIN KAMAR ON PENDAFTARAN.ID_KAMAR = KAMAR.ID_KAMAR WHERE PENDAFTARAN.STATUS_DAFTAR = 1");
    $siswa->execute();
    return $siswa->fetchAll();
}

function getBerkasByPendaftaran($id_daftar){
    global $pdo;
    $bq = $pdo->prepare("SELECT NAMA_BERKAS, BERKAS FROM BERKAS_SISWA WHERE ID_DAFTAR = :id");
    $bq->execute([':id' => $id_daftar]);
    return $bq->fetchAll();
}
