<?php

session_start();

// MySQL 
$pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


$pdo->exec(file_get_contents(__DIR__ . '/datubaze.sql'));
$pdo->exec('USE projekts');

// Žurnālfails
function zurnals($teksts) {
    $dir = __DIR__ . '/logs';
    if (!is_dir($dir)) mkdir($dir);
    $lietotajs = $_SESSION['lietotajvards'] ?? 'viesis';
    $rinda = date('Y-m-d H:i:s') . " [{$_SERVER['REMOTE_ADDR']}] [$lietotajs] $teksts\n";
    file_put_contents("$dir/app.log", $rinda, FILE_APPEND | LOCK_EX);
}


function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_lauks() {
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}
function parbaudit_csrf() {
    if (!hash_equals(csrf_token(), $_POST['csrf'] ?? '')) {
        zurnals('CSRF pārbaude neizdevās');
        http_response_code(403);
        exit('Nederīgs CSRF tokens');
    }
}

function vajag_pieteikties() {
    if (empty($_SESSION['lietotaja_id'])) {
        header('Location: login.php');
        exit;
    }
}

function e($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function galva($virsraksts) {
    echo '<!doctype html><html lang="lv"><head><meta charset="utf-8"><title>' . e($virsraksts) . '</title></head><body>';
}
