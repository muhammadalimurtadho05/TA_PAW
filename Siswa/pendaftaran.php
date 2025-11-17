<?php
session_start();
require_once '../validate.inc';
require_once 'database.php';

// *** Pastikan user login sebelum menggunakan $_SESSION['username']
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$db = constant("DBC");
$username = $_SESSION['username'];

// ambil data user (fungsi getUserByUsername harus ada di 'database.php')
$user = getUserByUsername($username);

// tampilkan pesan session jika ada
if (isset($_SESSION['pesan'])) {
    $tipe = $_SESSION['pesan']['tipe'] ?? 'info';
    $teks = $_SESSION['pesan']['teks'] ?? '';

    echo "<div class='alert-message {$tipe}'>" . htmlspecialchars($teks) . "</div>";
    unset($_SESSION['pesan']);
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

// Cek apakah user sudah pernah daftar
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

// variabel untuk repopulate
$nisn = $nama_ayah = $nama_ibu = $alamat = $asal_sekolah = $tempat_lahir = "";
$tanggal_lahir = $jenis_kelamin = $telp = $telp_ortu = $id_jurusan = "";

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ambil aman dengan null coalescing untuk mencegah warning undefined index
    $nisn = $_POST['nisn'] ?? "";
    $nama_ayah = $_POST['nama_ayah'] ?? "";
    $nama_ibu = $_POST['nama_ibu'] ?? "";
    $alamat = $_POST['alamat'] ?? "";
    $asal_sekolah = $_POST['asal_sekolah'] ?? "";
    $tempat_lahir = $_POST['tempat_lahir'] ?? "";
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? "";
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? "";
    $telp = $_POST['telp'] ?? "";
    $telp_ortu = $_POST['telp_ortu'] ?? "";
    $id_jurusan = $_POST['id_jurusan'] ?? "";

    // Panggil fungsi validasi di validate.inc
    cekNISN($nisn, $errors);
    cekNama($nama_ayah, $errors, 'nama_ayah', 'Nama Ayah');
    cekNama($nama_ibu, $errors, 'nama_ibu', 'Nama Ibu');
    cekAlamat($alamat, $errors);
    cekTelepon($telp, $errors, 'telp', 'Nomor Telepon');
    cekTelepon($telp_ortu, $errors, 'telp_ortu', 'Nomor Telepon Orang Tua');

  

    cekJurusan($id_jurusan, $errors);

    // cek jurusan
    cekPDF($_FILES['berkas1'] ?? ['error'=>4], $errors, 'berkas1', 'Ijazah');
    cekPDF($_FILES['berkas2'] ?? ['error'=>4], $errors, 'berkas2', 'Akte Kelahiran');
    cekPDF($_FILES['berkas3'] ?? ['error'=>4], $errors, 'berkas3', 'Kartu Keluarga');
    cekPDF($_FILES['berkas4'] ?? ['error'=>4], $errors, 'berkas4', 'Surat Sehat');
    cekPDF($_FILES['berkas5'] ?? ['error'=>4], $errors, 'berkas5', 'Surat Pernyataan');

    if (empty($errors)) {
        // Tentukan kamar otomatis
        $kamar = getAvailableKamar($db);
        $id_kamar = $kamar ? $kamar['ID_KAMAR'] : null;

        // Simpan ke tabel pendaftaran
        $query = $db->prepare("INSERT INTO pendaftaran 
        (USERNAME,ID_JURUSAN,ID_KAMAR,NISN,NAMA_AYAH, NAMA_IBU, ALAMAT, ASAL_SEKOLAH, TEMPAT_LAHIR, TANGGAL_LAHIR, JENIS_KELAMIN, TELP, TELP_ORTU, STATUS_DAFTAR)
        VALUES (:username,:id_jurusan,:id_kamar,:nisn,:nama_ayah,:nama_ibu,:alamat,:asal_sekolah,:tempat_lahir,:tanggal_lahir,:jenis_kelamin,:telp,:telp_ortu, 0)");

        $query->bindValue(':username', $username);
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

        // bind id_kamar dengan tipe NULL kalau null
        // if ($id_kamar === null) {
        //     $query->bindValue(':id_kamar', null, PDO::PARAM_NULL);
        // } else {
        // }
        $query->bindValue(':id_kamar', '1', PDO::PARAM_INT);

        $query->execute();

        if ($query->rowCount() > 0) {
            $id_daftar = $db->lastInsertId();

            // Upload berkas PDF
            $berkas_labels = [
                'berkas1' => 'Ijazah',
                'berkas2' => 'Akte Kelahiran',
                'berkas3' => 'Kartu Keluarga',
                'berkas4' => 'Surat Sehat',
                'berkas5' => 'Surat Pernyataan'
            ];

            $upload_dir = '../assets/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            foreach ($berkas_labels as $input_name => $label) {
                if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === 0) {
                    $file_name = $_FILES[$input_name]['name'];
                    $file_tmp = $_FILES[$input_name]['tmp_name'];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    if ($file_ext !== 'pdf') {
                        // hapus data pendaftaran jika perlu (opsional)
                        echo "<script>alert('Semua berkas harus dalam format PDF!');</script>";
                        exit;
                    }

                    $new_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $label) . '.pdf';
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
                <?php if (!empty($user['FOTO'])): ?>
                    <img src="../assets/uploads/<?= htmlspecialchars($user['FOTO']); ?>" alt="Foto Profil">
                <?php else: ?>
                    <img src="../assets/image/default.jpeg" alt="Foto Default">
                <?php endif; ?>
            </a>
        </div>
    </header>

    <div class="container-form">
        <h2>Formulir Pendaftaran Siswa Baru</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Masukkan NISN</label>
            <input type="text" name="nisn" value="<?= htmlspecialchars($nisn) ?>">
            <span class="errors"><?= $errors['nisn'] ?? "" ?></span>

            <label>Nama Ayah:</label>
            <input type="text" name="nama_ayah" value="<?= htmlspecialchars($nama_ayah) ?>">
            <span class="errors"><?= $errors['nama_ayah'] ?? "" ?></span>

            <label>Nama Ibu:</label>
            <input type="text" name="nama_ibu" value="<?= htmlspecialchars($nama_ibu) ?>">
            <span class="errors"><?= $errors['nama_ibu'] ?? "" ?></span>

            <label>Alamat:</label>
            <textarea name="alamat"><?= htmlspecialchars($alamat) ?></textarea>
            <span class="errors"><?= $errors['alamat'] ?? "" ?></span>

            <label>Asal Sekolah:</label>
            <input type="text" name="asal_sekolah" value="<?= htmlspecialchars($asal_sekolah) ?>">

            <label>Tempat Lahir:</label>
            <input type="text" name="tempat_lahir" value="<?= htmlspecialchars($tempat_lahir) ?>">

            <label>Tanggal Lahir:</label>
            <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($tanggal_lahir) ?>">

            <label>Jenis Kelamin:</label>
            <div class="radio-group">
                <input type="radio" name="jenis_kelamin" value="L" <?= $jenis_kelamin === 'L' ? 'checked' : '' ?>> Laki-laki
                <input type="radio" name="jenis_kelamin" value="P" <?= $jenis_kelamin === 'P' ? 'checked' : '' ?>> Perempuan
            </div>
            <span class="errors"><?= $errors['jenis_kelamin'] ?? "" ?></span>

            <label>No. Telp:</label>
            <input type="text" name="telp" value="<?= htmlspecialchars($telp) ?>">
            <span class="errors"><?= $errors['telp'] ?? "" ?></span>

            <label>No. Telp Orang Tua:</label>
            <input type="text" name="telp_ortu" value="<?= htmlspecialchars($telp_ortu) ?>">
            <span class="errors"><?= $errors['telp_ortu'] ?? "" ?></span>

            <label>Jurusan:</label>
            <select name="id_jurusan">
                <option value="">-- Pilih Jurusan --</option>
                <?php foreach ($jurusan_list as $jur): ?>
                    <option value="<?= htmlspecialchars($jur['ID_JURUSAN']); ?>" <?= $id_jurusan == $jur['ID_JURUSAN'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($jur['NAMA_JURUSAN']); ?> - <?= htmlspecialchars($jur['DETAIL_JURUSAN']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="errors"><?= $errors['id_jurusan'] ?? "" ?></span>

            <h3>Unggah Berkas (PDF Saja)</h3>
            <label>1. Ijazah:</label>
            <input type="file" name="berkas1">
            <span class="errors"><?= $errors['berkas1'] ?? "" ?></span>

            <label>2. Akte Kelahiran:</label>
            <input type="file" name="berkas2">
            <span class="errors"><?= $errors['berkas2'] ?? "" ?></span>

            <label>3. Kartu Keluarga:</label>
            <input type="file" name="berkas3">
            <span class="errors"><?= $errors['berkas3'] ?? "" ?></span>

            <label>4. Surat Sehat:</label>
            <input type="file" name="berkas4">
            <span class="errors"><?= $errors['berkas4'] ?? "" ?></span>

            <label>5. Surat Pernyataan:</label>
            <input type="file" name="berkas5">
            <span class="errors"><?= $errors['berkas5'] ?? "" ?></span>

            <button type="submit" name="submit">Daftar & Upload</button>
        </form>
    </div>
</body>
</html>
