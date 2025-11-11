<?php
$siswa = getSiswaKamar();
?>

<div class="page"><a href="index.php">Admin</a> / <a href="index.php?page=kamar">Kamar </a>/ <a href="">Siswa</a></div>
<?php if(!$siswa):?>
    <h1>Tidak Ada Siswa Pada Kamar Ini</h1>
    <?php else:?>
        <h1>Daftar Siswa Jurusan </h1>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NISN</th>
                <th>Nama</th>
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
                    <td><?= $sw['ALAMAT'] ?></td>
                    <td><?= $sw['TELP'] ?></td>
                </tr>
            <?php endforeach?>
        </tbody>
    </table>
<?php endif?>
