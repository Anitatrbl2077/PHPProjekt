<?php

require_once 'funktionen.php';
$fehler = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    csrf_pruefen();
    $name  = trim($_POST['benutzername'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pw    = $_POST['passwort'] ?? '';
    $pw2   = $_POST['passwort2'] ?? '';

    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $name)) $fehler[] = 'Benutzername: 3–30 Zeichen, nur Buchstaben, Zahlen und _.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) $fehler[] = 'Bitte eine gültige E-Mail-Adresse eingeben.';
    if (strlen($pw) < 8) $fehler[] = 'Das Passwort muss mindestens 8 Zeichen haben.';
    if ($pw !== $pw2) $fehler[] = 'Die Passwörter stimmen nicht überein.';

    if (!$fehler) 
    {
        $st = $pdo->prepare('SELECT COUNT(*) FROM benutzer WHERE benutzername = :n OR email = :e');
        $st->execute(['n' => $name, 'e' => $email]);
        if ($st->fetchColumn() > 0) 
        {
            $fehler[] = 'Benutzername oder E-Mail ist schon vergeben.';
        } 
        else 
        {
            $st = $pdo->prepare('INSERT INTO benutzer (benutzername, email, passwort_hash) VALUES (:n, :e, :h)');
            $st->execute(['n' => $name, 'e' => $email, 'h' => password_hash($pw, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['benutzer_id'] = (int)$pdo->lastInsertId();
            $_SESSION['benutzername'] = $name;
            header('Location: bibliothek.php');
            exit;
        }
    }
}

$seitentitel = 'Registrieren';
require 'header.php';
?>

<section class="karte schmal">
  <h1>Konto erstellen</h1>
  <?php foreach ($fehler as $f): ?><p class="fehler"><?= e($f) ?></p><?php endforeach; ?>
  <form method="post" class="form">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Benutzername <input name="benutzername" value="<?= e($name) ?>" required maxlength="30"></label>
    <label>E-Mail <input type="email" name="email" value="<?= e($email) ?>" required maxlength="100"></label>
    <label>Passwort (mind. 8 Zeichen) <input type="password" name="passwort" required></label>
    <label>Passwort wiederholen <input type="password" name="passwort2" required></label>
    <button class="knopf" type="submit">Registrieren</button>
  </form>
  <p class="klein">Schon ein Konto? <a href="login.php">Zur Anmeldung</a></p>
</section>

<?php require 'footer.php'; ?>
