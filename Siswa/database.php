<?php
require_once '../conn.php';

/**
 * Ambil data user berdasarkan username
 */
function getUserByUsername($username)
{
    $stmt = DBC->prepare("SELECT * FROM USERS WHERE USERNAME = :username");
    $stmt->execute([':username' => $username]);
    return $stmt->fetch();
}

/**
 * Ambil data lengkap pendaftaran siswa (user, jurusan, kamar, dan status)
 */
function getPendaftaranByUser($username)
{
    $stmt = DBC->prepare("
        SELECT 
            U.NAMA, U.FOTO_SISWA, U.USERNAME,
            P.ID_DAFTAR, P.NISN, P.ALAMAT, P.NAMA_AYAH, P.NAMA_IBU,
            P.TELP, P.TELP_ORTU, P.JENIS_KELAMIN, P.ASAL_SEKOLAH, 
            P.TEMPAT_LAHIR, P.TANGGAL_LAHIR, P.STATUS_DAFTAR, P.CREATED_AT,
            J.NAMA_JURUSAN, J.DETAIL_JURUSAN,
            K.KAMAR
        FROM PENDAFTARAN P
        LEFT JOIN USERS U ON U.USERNAME = P.USERNAME
        LEFT JOIN JURUSAN J ON J.ID_JURUSAN = P.ID_JURUSAN
        LEFT JOIN KAMAR K ON K.ID_KAMAR = P.ID_KAMAR
        WHERE P.USERNAME = :user
        ORDER BY P.ID_DAFTAR DESC
        LIMIT 1
    ");
    $stmt->execute([':user' => $username]);
    return $stmt->fetch();
}

/**
 * Ambil semua berkas milik siswa berdasarkan ID_DAFTAR
 */
function getBerkasByPendaftaran($id_daftar)
{
    $bq = DBC->prepare("SELECT NAMA_BERKAS, BERKAS FROM BERKAS_SISWA WHERE ID_DAFTAR = :id");
    $bq->execute([':id' => $id_daftar]);
    return $bq->fetchAll();
}
