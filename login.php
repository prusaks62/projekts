<?php
require 'db.php';

const MAX_MEGINAJUMI = 5;
const BLOKESANAS_LAIKS = 5; 

$kluda = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    parbaudit_csrf();
    $lietotajvards = trim($_POST['lietotajvards'] ?? '');
    $parole = $_POST['parole'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'];

    //  defense pret paroļu minēšanu
    $st = $pdo->prepare('SELECT COUNT(*) FROM pieteiksanas_meginajumi WHERE (lietotajvards = ? OR ip = ?) AND laiks > NOW() - INTERVAL ' . BLOKESANAS_LAIKS . ' MINUTE');
    $st->execute([$lietotajvards, $ip]);

    if ($st->fetchColumn() >= MAX_MEGINAJUMI) {
        $kluda = 'Pārāk daudz neveiksmīgu mēģinājumu. Mēģini vēlreiz pēc 5 minūtēm.';
        zurnals("Bloķēts pieteikšanās mēģinājums: $lietotajvards");
    } elseif ($lietotajvards === '' || $parole === '') {
        $kluda = 'Aizpildi visus laukus.';
    } else {
        $st = $pdo->prepare('SELECT * FROM lietotaji WHERE lietotajvards = ?');
        $st->execute([$lietotajvards]);
        $lietotajs = $st->fetch();

        if ($lietotajs && password_verify($parole, $lietotajs['parole_hash'])) {
            $pdo->prepare('DELETE FROM pieteiksanas_meginajumi WHERE lietotajvards = ?')->execute([$lietotajvards]);
            session_regenerate_id(true);
            $_SESSION['lietotaja_id'] = $lietotajs['id'];
            $_SESSION['lietotajvards'] = $lietotajs['lietotajvards'];
            zurnals('Pieteicās sistēmā');
            header('Location: piezimes.php');
            exit;
        }

        $pdo->prepare('INSERT INTO pieteiksanas_meginajumi (lietotajvards, ip) VALUES (?, ?)')
            ->execute([$lietotajvards, $ip]);
        zurnals("Neveiksmīga pieteikšanās: $lietotajvards");
        $kluda = 'Nepareizs lietotājvārds vai parole.';
    }
}

galva('Pieteikšanās');
?>
<h1>Pieteikšanās</h1>
<?php if (isset($_GET['registrets'])): ?><p>Reģistrācija veiksmīga! Tagad piesakies.</p><?php endif; ?>
<?php if ($kluda): ?><p><?= e($kluda) ?></p><?php endif; ?>
<form method="post">
    <?= csrf_lauks() ?>
    <label>Lietotājvārds <input name="lietotajvards" required></label><br>
    <label>Parole <input type="password" name="parole" required></label><br>
    <button>Pieteikties</button>
</form>
<p>Nav konta? <a href="register.php">Reģistrēties</a></p>
</body></html>
