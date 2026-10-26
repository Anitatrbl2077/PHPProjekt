<?php

require_once 'funktionen.php';
if (eingeloggt()) { header('Location: bibliothek.php'); exit; }
$fehler = '';
$name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    csrf_pruefen();
    $name = trim($_POST['benutzername'] ?? '');
    $pw   = $_POST['passwort'] ?? '';

    // Anmeldung mit Benutzername oder E-Mail
    $st = $pdo->prepare('SELECT id, benutzername, passwort_hash FROM benutzer WHERE benutzername = :n OR email = :e');
    $st->execute(['n' => $name, 'e' => $name]);
    $nutzer = $st->fetch();

    if ($nutzer && password_verify($pw, $nutzer['passwort_hash'])) 
    {
        session_regenerate_id(true);   // Schutz vor Session-Fixation
        $_SESSION['benutzer_id'] = (int)$nutzer['id'];
        $_SESSION['benutzername'] = $nutzer['benutzername'];
        header('Location: bibliothek.php');
        exit;
    }
    $fehler = 'Benutzername oder Passwort ist falsch.';   
}

$seitentitel = 'Anmelden';
require 'header.php';
?>

<section class="karte schmal">
  <h1>Anmelden</h1>
  
  <?php if ($fehler): ?><p class="fehler"><?= e($fehler) ?></p><?php endif; ?>

  <form method="post" class="form">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Benutzername oder E-Mail <input name="benutzername" value="<?= e($name) ?>" required></label>
    <label>Passwort <input type="password" name="passwort" required></label>
    <button class="knopf" type="submit">Anmelden</button>
  </form>
  <p class="klein">Noch kein Konto? <a href="registrieren.php">Jetzt registrieren</a></p>
</section>

<?php require 'footer.php'; ?>
