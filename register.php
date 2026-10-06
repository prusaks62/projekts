<?php
require 'db.php';

$kludas = [];
$lietotajvards = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    parbaudit_csrf();
    $lietotajvards = trim($_POST['lietotajvards'] ?? '');
    $parole = $_POST['parole'] ?? '';
    $parole2 = $_POST['parole2'] ?? '';

    // Validācija
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $lietotajvards)) {
        $kludas[] = 'Lietotājvārdam jābūt 3-20 simboliem (burti, cipari, _).';
    }
    if (strlen($parole) < 8) {
        $kludas[] = 'Parolei jābūt vismaz 8 simbolus garai.';
    }
    if ($parole !== $parole2) {
        $kludas[] = 'Paroles nesakrīt.';
    }

    if (!$kludas) {
        $st = $pdo->prepare('SELECT id FROM lietotaji WHERE lietotajvards = ?');
        $st->execute([$lietotajvards]);
        if ($st->fetch()) {
            $kludas[] = 'Šāds lietotājvārds jau ir aizņemts.';
        } else {
            $st = $pdo->prepare('INSERT INTO lietotaji (lietotajvards, parole_hash) VALUES (?, ?)');
            $st->execute([$lietotajvards, password_hash($parole, PASSWORD_DEFAULT)]);
            zurnals("Reģistrēts jauns lietotājs: $lietotajvards");
            header('Location: login.php?registrets=1');
            exit;
        }
    }
}

galva('Reģistrācija');
?>
<h1>Reģistrācija</h1>
<?php foreach ($kludas as $k): ?><p><?= e($k) ?></p><?php endforeach; ?>
<form method="post">
    <?= csrf_lauks() ?>
    <label>Lietotājvārds <input name="lietotajvards" value="<?= e($lietotajvards) ?>" required maxlength="20"></label><br>
    <label>Parole <input type="password" name="parole" required minlength="8"></label><br>
    <label>Parole atkārtoti <input type="password" name="parole2" required minlength="8"></label><br>
    <button>Reģistrēties</button>
</form>
<p>Jau ir konts? <a href="login.php">Pieteikties</a></p>
</body></html>
