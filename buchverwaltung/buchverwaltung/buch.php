<?php
require_once 'funktionen.php';
login_pflicht();

$st = $pdo->prepare('SELECT b.*, g.name AS genre_name FROM buch b LEFT JOIN genre g ON g.id = b.genre_id
                     WHERE b.id = :id AND b.benutzer_id = :uid');
$st->execute(['id' => $_GET['id'] ?? '', 'uid' => $_SESSION['benutzer_id']]);
$b = $st->fetch();

if (!$b) 
{
    http_response_code(404);
    die('Buch nicht gefunden.');
}
$seitentitel = $b['titel'];
require 'header.php';
?>

<p><a href="bibliothek.php">← Zurück zur Bibliothek</a></p>
<section class="karte detail">
  <?php if ($b['cover']): ?>
    <img class="cover gross" src="<?= e($b['cover']) ?>" alt="Cover von <?= e($b['titel']) ?>">
  <?php else: ?>
    <div class="cover gross platzhalter"><?= e(mb_substr($b['titel'], 0, 1)) ?></div>
  <?php endif; ?>
  <div>
    <h1><?= e($b['titel']) ?></h1>
    <p class="autor">von <?= e($b['autor']) ?></p>
    <p>
      <span class="badge status-<?= e($b['status']) ?>"><?= e(STATUS[$b['status']]) ?></span>
      <?php if ($b['gemocht'] !== null): ?>
        <span class="gemocht"><?= $b['gemocht'] ? '♥ Gemocht' : '✕ Nicht gemocht' ?></span>
      <?php endif; ?>
    </p>
    <p class="sterne gross <?= $b['bewertung'] === null ? 'unbewertet' : '' ?>"><?= e(sterne($b['bewertung'])) ?></p>
    <dl class="daten">
      <dt>Genre</dt><dd><?= e($b['genre_name'] ?? '–') ?></dd>
      <dt>Erschienen</dt><dd><?= e($b['erscheinungsjahr'] ?? '–') ?></dd>
      <dt>Angefangen</dt><dd><?= e(datum_de($b['startdatum'])) ?></dd>
      <dt>Beendet</dt><dd><?= e(datum_de($b['enddatum'])) ?></dd>
    </dl>
    <?php if ($b['beschreibung']): ?>
      <h2>Beschreibung</h2>
      <p class="text"><?= nl2br(e($b['beschreibung'])) ?></p>
    <?php endif; ?>
    <h2>Meine Rezension</h2>
    <p class="text"><?= $b['rezension'] ? nl2br(e($b['rezension'])) : 'Noch keine Rezension geschrieben.' ?></p>
    <div class="aktionen">
      <a class="knopf" href="buch_bearbeiten.php?id=<?= (int)$b['id'] ?>">Bearbeiten</a>
      <form method="post" action="buch_loeschen.php" onsubmit="return confirm('Dieses Buch wirklich löschen?');">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
        <button class="knopf gefahr" type="submit">Löschen</button>
      </form>
    </div>
  </div>
</section>
<?php require 'footer.php'; ?>
