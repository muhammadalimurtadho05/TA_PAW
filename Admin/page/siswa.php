<?php
if(!defined('APP_SECURE')){
    require_once 'error.php';
    die();
}

$siswa = getAllSiswa();
?>

<div class="page"><a href="index.php">Dashboard</a> / Siswa</div>

        <h1>Daftar Semua Siswa</h1>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NISN</th>
                <th>Nama</th>
                <th>Jurusan</th>
                <th>Kamar</th>
                <th>Alamat</th>
                <th>Telp</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; foreach( $siswa as $sw ):?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $sw['NISN'] ?></td>
                    <td><?= $sw['NAMA'] ?></td>
                    <td><?= $sw['NAMA_JURUSAN'] ?></td>
                    <td><?= $sw['KAMAR'] ?></td>
                    <td><?= $sw['ALAMAT'] ?></td>
                    <td><?= $sw['TELP'] ?></td>
                </tr>
            <?php endforeach?>
        </tbody>
    </table>
