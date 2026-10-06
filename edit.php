<?php

require 'db.php';
vajag_pieteikties();

$id = (int)($_GET['id'] ?? 0);

// Lietotājs edito tikai savas piezīmes
$st = $pdo->prepare('SELECT * FROM piezimes WHERE id = ? AND lietotaja_id = ?');
$st->execute([$id, $_SESSION['lietotaja_id']]);
$p = $st->fetch();
if (!$p) {
    http_response_code(404);
    exit('Piezīme nav atrasta');
}

$kludas = [];
$virsraksts = $p['virsraksts'];
$teksts = $p['teksts'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    parbaudit_csrf();
    $virsraksts = trim($_POST['virsraksts'] ?? '');
    $teksts = trim($_POST['teksts'] ?? '');

    if ($virsraksts === '' || mb_strlen($virsraksts) > 100) $kludas[] = 'Virsrakstam jābūt 1-100 simboliem.';
    if ($teksts === '' || mb_strlen($teksts) > 2000) $kludas[] = 'Tekstam jābūt 1-2000 simboliem.';

    if (!$kludas) {
        $st = $pdo->prepare('UPDATE piezimes SET virsraksts = ?, teksts = ? WHERE id = ? AND lietotaja_id = ?');
        $st->execute([$virsraksts, $teksts, $id, $_SESSION['lietotaja_id']]);
        zurnals("Rediģēja piezīmi ID $id");
        header('Location: piezimes.php');
        exit;
    }
}

galva('Rediģēt piezīmi');
?>
<h1>Rediģēt piezīmi</h1>
<?php foreach ($kludas as $k): ?><p><?= e($k) ?></p><?php endforeach; ?>
<form method="post">
    <?= csrf_lauks() ?>
    <label>Virsraksts <input name="virsraksts" value="<?= e($virsraksts) ?>" required maxlength="100"></label><br>
    <label>Teksts <textarea name="teksts" rows="6" required maxlength="2000"><?= e($teksts) ?></textarea></label><br>
    <button>Saglabāt</button> <a href="piezimes.php">Atcelt</a>
</form>
</body></html>
