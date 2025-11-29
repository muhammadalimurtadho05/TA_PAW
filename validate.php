<?php 
function requiredCheck($field){
	$field = trim($field);
	return empty($field);
}

function cekNISN($field,&$errors){
	if(requiredCheck($field)){
		$errors['nisn'] = 'NISN wajib di isi';
	}elseif(!preg_match('/^[0-9]{10}+$/',$field)){
		$errors['nisn'] = 'NISN tidak valid, harus angka dan harus 10 digit';
	}
}

function cekNama($field, &$errors, $key, $label){
    if (requiredCheck($field)) {
        $errors[$key] = "Kolom $label wajib diisi";
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $field)) {
        $errors[$key] = "Kolom $label hanya boleh huruf";
    }
}

function cekAlamat($field, &$errors, $label, $key){
    if (requiredCheck($field)) {
        $errors[$key] = "Kolom $label wajib diisi";
    }
}

function cekTanggal($field, &$errors){
    $today = date('Y-m-d');
    $d = DateTime::createFromFormat('m-d-Y', $field);
    if(requiredCheck($field)){
        $errors['tanggal_lahir'] = "Wajib di isi";
    }elseif($field >$today ){
        $errors['tanggal_lahir'] = "Tanggal lahir tidak boleh melebihi hari ini"; 
    }
}

function cekTelepon($field, &$errors, $key, $label){
    if (requiredCheck($field)) {
        $errors[$key] = "Kolom $label wajib diisi";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $field)) {
        $errors[$key] = "$label harus angka 10-15 digit";
    }
}

function cekJurusan($field, &$errors){
    if (requiredCheck($field)) {
        $errors['id_jurusan'] = 'Silakan pilih jurusan';
    }
}

function cekPDF($file, &$errors, $key, $label){

    if ($file['error'] !== 0) {
        $errors[$key] = "$label wajib diupload";
        return;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        $errors[$key] = "$label harus berformat PDF";
        return;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        $errors[$key] = "$label maksimal 5MB";
        return;
    }
}


function cekNamaDaftar($field, &$errors) {
    if (requiredCheck($field)) {
        $errors['nama'] = 'Nama wajib diisi';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $field)) {
        $errors['nama'] = 'Nama hanya boleh huruf';
    }
}

function cekUsernameDaftar($field, &$errors) {
    if (requiredCheck($field)) {
        $errors['username'] = 'Username wajib diisi';
    } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*[0-9])[A-Za-z0-9]+$/',$field)){
		$errors['username'] = 'Kolom username kombinasi alfabet dan numerik';
	}
}

function cekPasswordDaftar($field, &$errors) {
    if (requiredCheck($field)) {
        $errors['password'] = 'Password wajib diisi';
    } elseif (!preg_match('/^[a-zA-Z0-9]{8,}+$/',$field)){
		$errors['password'] = 'Kolom password minimal 8 karakter';
	}
}

function cekJenisKelamin($field, &$errors) {
    if (requiredCheck($field)) {
        $errors['jenis_kelamin'] = 'Silakan pilih jenis kelamin';
    } elseif (!in_array($field, ['L', 'P'])) {
        $errors['jenis_kelamin'] = 'Pilihan jenis kelamin tidak valid';
    }
}

?>