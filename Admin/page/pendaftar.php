<?php
if(!defined('APP_SECURE')){
    require_once 'error.php';
    die();
}
$pendaftar = getAllPendaftar();
?>
<h1>Pendaftar Online</h1>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>NISN</th>
            <th>Nama</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1; 
        foreach( $pendaftar as $user ):?>
        <tr>
            <td><?= $no++?>.</td>
            <td><?= $user['NISN'] ?></td>
            <td><?= $user['NAMA'] ?></td>
            <td>
                <?php if($user['STATUS_DAFTAR'] == 0){?>
                <span>Pending</span>
            <?php }else if($user['STATUS_DAFTAR'] == 1){?>
                <span>Diterima</span>
            <?php
            }else{
            ?>
            <span>Ditolak</span>
            <?php }?>
            </td>
            <td>
                <a href="index.php?page=detail&user=<?= $user['USERNAME'] ?>" class="acc">Info</a>
            </td>
        </tr>
        <?php endforeach?>
    </tbody>
</table>