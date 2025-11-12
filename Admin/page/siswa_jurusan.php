<?php
if(!defined('APP_SECURE')){
    require_once 'error.php';
    die();
}
$siswa = getSiswaJurusan();
$jurusan = getJurusanName();

?>

<div class="page"><a href="">Dashboard</a> / <a href="index.php?page=jurusan">Jurusan </a>/ Siswa</div>
<?php if(!$siswa):?>
    <h1>Tidak Ada Siswa Pada Jurusan Ini</h1>
    <?php else:?>
        <h1>Daftar Siswa | <?= $jurusan['NAMA_JURUSAN']?> - <?= $jurusan['DETAIL_JURUSAN']?></h1>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NISN</th>
                <th>Nama</th>
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
                    <td><?= $sw['KAMAR'] ?></td>
                    <td><?= $sw['ALAMAT'] ?></td>
                    <td><?= $sw['TELP'] ?></td>
                </tr>
            <?php endforeach?>
        </tbody>
    </table>
<?php endif?>
