<?php


session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../conn.php';

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $nisn = $_POST['nisn'];
    $nama_ayah = $_POST['nama_ayah'];
    $nama_ibu = $_POST['nama_ibu'];
    $alamat = $_POST['alamat'];
    $asal_sekolah = $_POST['asal_sekolah'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $telp = $_POST['telp'];
    $telp_ortu = $_POST['telp_ortu'];


    $db = constant("DBC");

    $query = $db->prepare("INSERT INTO pendaftaran 
            (NISN, NAMA_AYAH, NAMA_IBU, ALAMAT, ASAL_SEKOLAH, TEMPAT_LAHIR, TANGGAL_LAHIR, JENIS_KELAMIN, TELP, TELP_ORTU )
            VALUES (:nisn,:nama_ayah,:nama_ibu,:alamat,:asal_sekolah,:tempat_lahir,:tanggal_lahir,:jenis_kelamin,:telp,:telp_ortu)");

    // $query -> bindValue(':username',$_POST['username']);
    $query->bindValue(':nisn', $_POST['nisn']);
    $query->bindValue(':nama_ayah', $_POST['nama_ayah']);
    $query->bindValue(':nama_ibu', $_POST['nama_ibu']);
    $query->bindValue(':alamat', $_POST['alamat']);
    $query->bindValue(':asal_sekolah', $_POST['asal_sekolah']);
    $query->bindValue(':tempat_lahir', $_POST['tempat_lahir']);
    $query->bindValue(':tanggal_lahir', $_POST['tanggal_lahir']);
    $query->bindValue(':jenis_kelamin', $_POST['jenis_kelamin']);
    $query->bindValue(':telp', $_POST['telp']);
    $query->bindValue(':telp_ortu', $_POST['telp_ortu']);
    $query->execute();

    if ($query->rowCount() > 0) {
        echo "<script>alert('Pendaftaran berhasil!'); window.location='index.php'</script>";
    } else {
        echo "<script>alert('Pendaftaran gagal!');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pendaftaran Siswa Baru</title>
    <link rel="stylesheet" href="../assets/css/daftar.css">
    <link rel="stylesheet" href="../assets/css/siswa.css">
</head>

<body>

    <header class="navbar">
        <div class="logo">
            <span><strong>PPDB</strong> Online <strong>Pesantren</strong></span>
        </div>
        <nav class="menu">
            <a href="index.php">Home</a>
            <a href="#">Riwayat Pendaftaran</a>
            <a href="pendaftaran.php" class="active">Pendaftaran</a>
            <a href="edit_profil.php" class="btn">Edit Profil</a>
        </nav>
        <div class="user">
            <a href="profil.php" class="nama-user">
                <?= htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']) ?>
            </a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </header>

    <!-- Isi Halaman -->
    <div class="container">
        <h2>Formulir Pendaftaran Siswa Baru</h2>
        <form method="POST">

            <label>NISN:</label>
            <input type="text" name="nisn">

            <label>Nama Ayah:</label>
            <input type="text" name="nama_ayah">

            <label>Nama Ibu:</label>
            <input type="text" name="nama_ibu">

            <label>Alamat:</label>
            <textarea name="alamat"></textarea>

            <label>Asal Sekolah:</label>
            <input type="text" name="asal_sekolah">

            <label>Tempat Lahir:</label>
            <input type="text" name="tempat_lahir">

            <label>Tanggal Lahir:</label>
            <input type="date" name="tanggal_lahir">

            <label>Jenis Kelamin:</label>
            <div class="radio-group">
                <input type="radio" name="jenis_kelamin" value="Laki-laki"> Laki-laki
                <input type="radio" name="jenis_kelamin" value="Perempuan"> Perempuan
            </div>

            <label>No. Telp:</label>
            <input type="text" name="telp">

            <label>No. Telp Orang Tua:</label>
            <input type="text" name="telp_ortu">

            <button type="submit" name="submit">Daftar</button>
        </form>
    </div>
    </form>
    </div>
    </div>
</body>

</html>