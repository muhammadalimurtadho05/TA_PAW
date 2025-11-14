<?php
session_start();
require_once 'database.php';
if (isset($_SESSION['pesan'])) {
    $tipe = $_SESSION['pesan']['tipe'] ?? 'info';
    $teks = $_SESSION['pesan']['teks'] ?? '';

    echo "<div class='alert-message {$tipe}'>{$teks}</div>";
    unset($_SESSION['pesan']);
}


$db = constant("DBC");
$username = $_SESSION['username'];
$user = getUserByUsername($username);
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}



require_once '../conn.php';

// Ambil daftar jurusan dari database untuk dropdown
$jurusan_query = $db->prepare("SELECT * FROM jurusan");
$jurusan_query->execute();
$jurusan_list = $jurusan_query->fetchAll();

// Fungsi untuk mencari kamar dengan kapasitas tersedia
function getAvailableKamar($db)
{
    $query = $db->query("
        SELECT k.ID_KAMAR, k.KAMAR, k.KAPASITAS, 
               COUNT(p.ID_DAFTAR) AS total_penghuni
        FROM kamar k
        LEFT JOIN pendaftaran p ON p.ID_KAMAR = k.ID_KAMAR
        GROUP BY k.ID_KAMAR
        HAVING total_penghuni < k.KAPASITAS
        ORDER BY k.ID_KAMAR ASC
        LIMIT 1
    ");
    return $query->fetch(PDO::FETCH_ASSOC);
}

// 🚫 Cek apakah user sudah pernah daftar
$cek_daftar = $db->prepare("SELECT * FROM pendaftaran WHERE USERNAME = ?");
$cek_daftar->execute([$username]);
$sudah_daftar = $cek_daftar->fetch();

if ($sudah_daftar) {
    $_SESSION['pesan'] = [
        'tipe' => 'sukses',
        'teks' => 'Anda Sudah Mendaftar !'
    ];
    header("Location: ../Siswa/riwayat.php");
    exit;
}


if (isset($_POST['submit'])) {
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
    $id_jurusan = $_POST['id_jurusan'];

    // ====== 1️⃣ Tentukan kamar otomatis ======
    $kamar = getAvailableKamar($db);
    $id_kamar = $kamar ? $kamar['ID_KAMAR'] : null;

    // ====== 1️⃣ Simpan ke tabel pendaftaran ======
    $query = $db->prepare("INSERT INTO pendaftaran 
        (USERNAME,ID_JURUSAN,ID_KAMAR,NISN,NAMA_AYAH, NAMA_IBU, ALAMAT, ASAL_SEKOLAH, TEMPAT_LAHIR, TANGGAL_LAHIR, JENIS_KELAMIN, TELP, TELP_ORTU, STATUS_DAFTAR)
        VALUES (:username,:id_jurusan,:id_kamar,:nisn,:nama_ayah,:nama_ibu,:alamat,:asal_sekolah,:tempat_lahir,:tanggal_lahir,:jenis_kelamin,:telp,:telp_ortu, 0)");

    // $query->bindValue(':username', $username);
    $query->bindValue(':username', $_SESSION['username']);
    $query->bindValue(':nisn', $nisn);
    $query->bindValue(':nama_ayah', $nama_ayah);
    $query->bindValue(':nama_ibu', $nama_ibu);
    $query->bindValue(':alamat', $alamat);
    $query->bindValue(':asal_sekolah', $asal_sekolah);
    $query->bindValue(':tempat_lahir', $tempat_lahir);
    $query->bindValue(':tanggal_lahir', $tanggal_lahir);
    $query->bindValue(':jenis_kelamin', $jenis_kelamin);
    $query->bindValue(':telp', $telp);
    $query->bindValue(':telp_ortu', $telp_ortu);
    $query->bindValue(':id_jurusan', $id_jurusan);
    $query->bindValue(':id_kamar', $id_kamar);
    $query->execute();

    if ($query->rowCount() > 0) {
        $id_daftar = $db->lastInsertId();

        // ====== 2️⃣ Upload 5 berkas PDF ======
        $berkas_labels = [
            'Ijazah',
            'Surat Sehat',
            'Kartu Keluarga',
            'Akte Lahir',
            'Surat Pernyataan'

        ];

        $upload_dir = '../assets/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($berkas_labels as $key => $label) {
            $input_name = 'berkas' . ($key + 1);
            if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === 0) {
                $file_name = $_FILES[$input_name]['name'];
                $file_tmp = $_FILES[$input_name]['tmp_name'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                if ($file_ext !== 'pdf') {
                    echo "<script>alert('Semua berkas harus dalam format PDF!');</script>";
                    exit;
                }

                $new_name = time() . '_' . $label . '.pdf';
                $target_path = $upload_dir . $new_name;

                if (move_uploaded_file($file_tmp, $target_path)) {
                    $stmt = $db->prepare("INSERT INTO berkas_siswa (ID_DAFTAR, NAMA_BERKAS, BERKAS) VALUES (?, ?, ?)");
                    $stmt->execute([$id_daftar, $label, $new_name]);
                }
            }
        }

        $_SESSION['pesan'] = [
            'tipe' => 'sukses',
            'teks' => '✅ selamat anda berhasil daftar !'
        ];
        header("Location: ../Siswa/index.php");
        exit;
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
            <span><strong>PPDB</strong> Online <span>Madrasah Aliyah </span> <strong>Al Hikmah</strong></span>
        </div>
        <nav class="menu">
            <a href="index.php">Home</a>
            <a href="riwayat.php">Riwayat Pendaftaran</a>
            <a href="pendaftaran.php" class="active">Pendaftaran</a>
            <a href="edit_profil.php" class="btn">Edit Profil</a>
            <a href="logout.php" class="logout">Logout</a>
        </nav>
        <div class="user">
            <a href="profil.php">
                <?php if (!empty($user['FOTO_SISWA'])): ?>
                    <img src="../assets/uploads/<?= $user['FOTO_SISWA']; ?>" alt="Foto Profil">
                <?php else: ?>
                    <img src="../assets/image/default.jpeg" alt="Foto Default">
                <?php endif; ?>
            </a>
        </div>
    </header>

    <div class="container-form">
        <h2>Formulir Pendaftaran Siswa Baru</h2>
        <form method="POST" enctype="multipart/form-data">

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
                <input type="radio" name="jenis_kelamin" value="L"> Laki-laki
                <input type="radio" name="jenis_kelamin" value="P"> Perempuan
            </div>

            <label>No. Telp:</label>
            <input type="text" name="telp">

            <label>No. Telp Orang Tua:</label>
            <input type="text" name="telp_ortu">

            <label>Jurusan:</label>
            <select name="id_jurusan">
                <option value="">-- Pilih Jurusan --</option>
                <?php foreach ($jurusan_list as $jur): ?>
                    <option value="<?= $jur['ID_JURUSAN']; ?>">
                        <?= $jur['NAMA_JURUSAN']; ?> - <?= $jur['DETAIL_JURUSAN']; ?>
                    </option>
                <?php endforeach; ?>
            </select>


            <h3>Unggah Berkas (PDF Saja)</h3>
            <label>1. Ijazah:</label>
            <input type="file" name="berkas1" accept="application/pdf">

            <label>2. Akte Kelahiran:</label>
            <input type="file" name="berkas2" accept="application/pdf">

            <label>3. Kartu Keluarga:</label>
            <input type="file" name="berkas3" accept="application/pdf">

            <label>4. Surat sehat:</label>
            <input type="file" name="berkas4" accept="application/pdf">

            <label>5. Surat Pernyataan:</label>
            <input type="file" name="berkas5" accept="application/pdf">

            <button type="submit" name="submit">Daftar & Upload</button>
        </form>
    </div>
</body>

</html>