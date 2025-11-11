<?php
session_start();

require_once '../conn.php';
require_once BASE_PATH.'/Admin/Components/database.php';
require_once BASE_PATH.'/Admin/Components/header.php';
?>

<!-- <h1>Daftar Akun Terdaftar</h1> -->
<!-- <p>Ini adalah area konten utama. Anda bisa menambahkan tabel, grafik, atau komponen lainnya di sini.</p> -->
<?php
if(isset($_GET['page'])){
    $page = $_GET['page'];
    if($page == 'users'){
        require_once 'page/users.php';
    }else if($page == 'pendaftar'){
        require_once 'page/pendaftar.php';
    }else if($page == 'detail'){
        require_once 'page/detail_user.php';
    }else if($page == 'kamar'){
        if(isset($_GET['id'])){
            require_once 'page/kamar-dtl.php';
        }else{
            require_once 'page/kamar.php';
        }
    }else if($page == 'jurusan'){
        require_once 'page/jurusan.php';
    }else if($page == 'siswa_jurusan'){
        require_once 'page/siswa_jurusan.php';
    }else if($page == 'profil'){
        require_once 'page/profile.php';
    }
    else if($page == 'logout'){
        logout();
    }
    
    
    
    else{
        require_once 'page/not-found.php';
    }
}else{
    include 'page/dashboard.php';
}
?>
<?php
require_once 'Components/footer.php';

?>
   