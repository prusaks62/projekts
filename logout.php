<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Atļauta tikai POST metode');
}
parbaudit_csrf();
zurnals('Atteicās no sistēmas');
$_SESSION = [];
session_destroy();
header('Location: login.php');
