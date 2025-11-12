<?php
if(!defined('APP_SECURE')){
    require_once 'error.php';
    die();
}
$users = getAllUsers();
?>
<h1>Daftar Akun Terdaftar</h1>
<p>Ini adalah area konten utama. Anda bisa menambahkan tabel, grafik, atau komponen lainnya di sini.</p>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1; 
        foreach( $users as $user ):?>
        <tr>
            <td><?= $no++?>.</td>
            <td><?= $user['NAMA'] ?></td>
        </tr>
        <?php endforeach?>
        
    </tbody>
</table>