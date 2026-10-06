<?php

require 'db.php';
vajag_pieteikties();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Atļauta tikai POST metode');
}
parbaudit_csrf();

$id = (int)($_POST['id'] ?? 0);
$st = $pdo->prepare('DELETE FROM piezimes WHERE id = ? AND lietotaja_id = ?');
$st->execute([$id, $_SESSION['lietotaja_id']]);
if ($st->rowCount()) zurnals("Izdzēsa piezīmi ID $id");

header('Location: piezimes.php');
