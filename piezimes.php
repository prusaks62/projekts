<?php

require 'db.php';
vajag_pieteikties();

$kludas = [];
$virsraksts = $teksts = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    parbaudit_csrf();
    $virsraksts = trim($_POST['virsraksts'] ?? '');
    $teksts = trim($_POST['teksts'] ?? '');

    if ($virsraksts === '' || mb_strlen($virsraksts) > 100) $kludas[] = 'Virsrakstam jābūt 1-100 simboliem.';
    if ($teksts === '' || mb_strlen($teksts) > 2000) $kludas[] = 'Tekstam jābūt 1-2000 simboliem.';

    if (!$kludas) {
        $st = $pdo->prepare('INSERT INTO piezimes (lietotaja_id, virsraksts, teksts) VALUES (?, ?, ?)');
        $st->execute([$_SESSION['lietotaja_id'], $virsraksts, $teksts]);
        zurnals('Izveidoja piezīmi ID ' . $pdo->lastInsertId());
        header('Location: piezimes.php');
        exit;
    }
}

$st = $pdo->prepare('SELECT * FROM piezimes WHERE lietotaja_id = ? ORDER BY id DESC');
$st->execute([$_SESSION['lietotaja_id']]);
$piezimes = $st->fetchAll();

galva('Manas piezīmes');
?>
<p>Sveiks, <b><?= e($_SESSION['lietotajvards']) ?></b>!
<form method="post" action="logout.php"><?= csrf_lauks() ?><button>Iziet</button></form></p>

<h2>Jauna piezīme</h2>
<?php foreach ($kludas as $k): ?><p><?= e($k) ?></p><?php endforeach; ?>
<form method="post">
    <?= csrf_lauks() ?>
    <label>Virsraksts <input name="virsraksts" value="<?= e($virsraksts) ?>" required maxlength="100"></label><br>
    <label>Teksts <textarea name="teksts" rows="4" required maxlength="2000"><?= e($teksts) ?></textarea></label><br>
    <button>Pievienot</button>
</form>

<h2>Manas piezīmes (<?= count($piezimes) ?>)</h2>
<?php foreach ($piezimes as $p): ?>
<div>
    <h3><?= e($p['virsraksts']) ?></h3>
    <p><?= nl2br(e($p['teksts'])) ?></p>
    <small><?= e($p['izveidots']) ?></small><br>
    <a href="edit.php?id=<?= $p['id'] ?>">Rediģēt</a>
    <form method="post" action="delete.php" onsubmit="return confirm('Dzēst piezīmi?')">
        <?= csrf_lauks() ?>
        <input type="hidden" name="id" value="<?= $p['id'] ?>">
        <button>Dzēst</button>
    </form>
</div>
<hr>
<?php endforeach; ?>
</body></html>
