<?php
require_once 'funktionen.php';
login_pflicht();
$id = $_GET['id'] ?? '';
$uid = $_SESSION['benutzer_id'];

// Nur eigene Bücher laden
$st = $pdo->prepare('SELECT * FROM buch WHERE id = :id AND benutzer_id = :uid');
$st->execute(['id' => $id, 'uid' => $uid]);
$buch = $st->fetch();

if (!$buch) 
{
    http_response_code(404);
    die('Buch nicht gefunden.');
}

$fehler = [];
$w = $buch;

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    csrf_pruefen();
    [$d, $fehler] = buch_pruefen($pdo, $_POST);
    $w = $_POST;
    if (!$fehler) 
    {
        $d['id'] = $buch['id'];
        $d['uid'] = $uid;
        $st = $pdo->prepare('UPDATE buch SET genre_id=:genre_id, titel=:titel, autor=:autor, erscheinungsjahr=:erscheinungsjahr,
            beschreibung=:beschreibung, cover=:cover, status=:status, bewertung=:bewertung, gemocht=:gemocht,
            rezension=:rezension, startdatum=:startdatum, enddatum=:enddatum WHERE id=:id AND benutzer_id=:uid');
        $st->execute($d);
        header('Location: buch.php?id=' . $buch['id']);
        exit;
    }
}

$seitentitel = 'Buch bearbeiten';
$genres = alle_genres($pdo);
$knopf = 'Änderungen speichern';
require 'header.php';
?>
<section class="karte">
  <h1>Buch bearbeiten</h1>
  <?php require 'buch_form.php'; ?>
</section>
<?php require 'footer.php'; ?>
