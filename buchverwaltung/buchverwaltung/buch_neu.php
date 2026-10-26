<?php
require_once 'funktionen.php';
login_pflicht();
$fehler = [];
$w = ['status' => 'moechte_lesen'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    csrf_pruefen();
    [$d, $fehler] = buch_pruefen($pdo, $_POST);
    $w = $_POST;
    if (!$fehler) 
    {
        $d['benutzer_id'] = $_SESSION['benutzer_id'];
        $st = $pdo->prepare('INSERT INTO buch
            (benutzer_id, genre_id, titel, autor, erscheinungsjahr, beschreibung, cover, status, bewertung, gemocht, rezension, startdatum, enddatum)
            VALUES (:benutzer_id, :genre_id, :titel, :autor, :erscheinungsjahr, :beschreibung, :cover, :status, :bewertung, :gemocht, :rezension, :startdatum, :enddatum)');
        $st->execute($d);
        header('Location: buch.php?id=' . $pdo->lastInsertId());
        exit;
    }
}

$seitentitel = 'Buch hinzufügen';
$genres = alle_genres($pdo);
$knopf = 'Buch speichern';
require 'header.php';
?>
<section class="karte">
  <h1>Buch hinzufügen</h1>
  <?php require 'buch_form.php'; ?>
</section>
<?php require 'footer.php'; ?>
