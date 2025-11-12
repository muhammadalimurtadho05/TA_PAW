<?php
require_once 'base.php';
define("HOST","localhost");
define("USER","root");
define("PASS","");
define("DB_NAME","ta_paw");


const OPTIONS = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    define("DBC",new PDO("mysql:host=".HOST.";dbname=".DB_NAME, USER, PASS, OPTIONS));
    // echo "Connected";
} catch (PDOException $e) {
    echo $e->getMessage();
    die();
}
