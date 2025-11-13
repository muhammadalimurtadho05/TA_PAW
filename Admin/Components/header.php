<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="Asset/Css/style.css">
</head>
<body>
<header>
    <div class="logo">Admin | PPDB Online</div>
    <div class="menu">
        <a href="index.php">Dashboard</a>
        <a href="index.php?page=siswa">Siswa</a>
        <a href="index.php?page=pendaftar">Pendaftar</a>
        <a href="index.php?page=jurusan">Jurusan</a>
        <a href="index.php?page=kamar">Kamar</a>
        <a href="index.php?page=logout" class="logout">Logout</a>
    </div>
    <div class="top-kanan">
        <a href="index.php?page=profil" class="prof"><?= $_SESSION['nama']?></a>
        <img src="Asset/Img/profile.png" alt="" class="profile-photo">
    </div>
</header>
<main class="content">
