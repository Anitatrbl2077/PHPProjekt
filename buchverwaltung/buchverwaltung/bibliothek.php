<?php
require_once 'funktionen.php';

login_pflicht();

$uid = $_SESSION['benutzer_id'];

// Filter aus der URL lesen
$q       = trim($_GET['q'] ?? '');
$status  = $_GET['status'] ?? '';
$genreId = $_GET['genre'] ?? '';
$gemocht = $_GET['gemocht'] ?? '';

$sql    = 'SELECT b.*, g.name AS genre_name FROM buch b LEFT JOIN genre g ON g.id = b.genre_id WHERE b.benutzer_id = :uid';
$params = ['uid' => $uid];

if ($q !== '') 
{
    // % und _ im Suchtext entschärfen
    $like = '%' . addcslashes($q, '%_\\') . '%';
    $sql .= ' AND (b.titel LIKE :q1 OR b.autor LIKE :q2)';
    $params['q1'] = $like;
    $params['q2'] = $like;
}

if (array_key_exists($status, STATUS)) 
{
    $sql .= ' AND b.status = :status';
    $params['status'] = $status;
} else {
    $status = '';
}
if ($genreId !== '' && ctype_digit($genreId)) 
{
    $sql .= ' AND b.genre_id = :genre';
    $params['genre'] = $genreId;
} else {
    $genreId = '';
}

if ($gemocht === '1' || $gemocht === '0') 
{
    $sql .= ' AND b.gemocht = :gemocht';
    $params['gemocht'] = $gemocht;
} else {
    $gemocht = '';
}
$sql .= ' ORDER BY b.titel';

$st = $pdo->prepare($sql);
$st->execute($params);
$buecher = $st->fetchAll();

// Anzahl pro Status für die Reiter
$st = $pdo->prepare('SELECT status, COUNT(*) AS n FROM buch WHERE benutzer_id = :uid GROUP BY status');
$st->execute(['uid' => $uid]);
$anzahl = array_column($st->fetchAll(), 'n', 'status');
$gesamt = array_sum($anzahl);

// Link-Helfer: behält Suche/Filter bei und ändert nur einen Wert
function filter_link(array $aendern): string 
{
    $aktuell = array_filter([
        'q' => $_GET['q'] ?? '', 'status' => $_GET['status'] ?? '',
        'genre' => $_GET['genre'] ?? '', 'gemocht' => $_GET['gemocht'] ?? '',
    ], fn($v) => $v !== '');
    return 'bibliothek.php?' . http_build_query(array_filter(array_merge($aktuell, $aendern), fn($v) => $v !== ''));
}

$seitentitel = 'Bibliothek';
require 'header.php';
?>


<h1>Meine Bibliothek <span class="zahl"><?= $gesamt ?> Bücher</span></h1>

<nav class="reiter">
  <a href="<?= e(filter_link(['status' => ''])) ?>" class="<?= $status === '' ? 'aktiv' : '' ?>">Alle (<?= $gesamt ?>)</a>
  <?php foreach (STATUS as $key => $label): ?>
    <a href="<?= e(filter_link(['status' => $key])) ?>" class="<?= $status === $key ? 'aktiv' : '' ?>">
      <?= e($label) ?> (<?= (int)($anzahl[$key] ?? 0) ?>)
    </a>
  <?php endforeach; ?>
</nav>

<form method="get" class="suche">
  <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?>
  <input type="search" name="q" value="<?= e($q) ?>" placeholder="Titel oder Autor suchen …">
  <select name="genre">
    <option value="">Alle Genres</option>
    <?php foreach (alle_genres($pdo) as $g): ?>
      <option value="<?= (int)$g['id'] ?>" <?= (string)$g['id'] === $genreId ? 'selected' : '' ?>><?= e($g['name']) ?></option>
    <?php endforeach; ?>
  </select>
  <select name="gemocht">
    <option value="">Gemocht: egal</option>
    <option value="1" <?= $gemocht === '1' ? 'selected' : '' ?>>Gemocht</option>
    <option value="0" <?= $gemocht === '0' ? 'selected' : '' ?>>Nicht gemocht</option>
  </select>
  <button class="knopf" type="submit">Suchen</button>
  <?php if ($q !== '' || $genreId !== '' || $gemocht !== ''): ?>
    <a class="zuruecksetzen" href="<?= e(filter_link(['q' => '', 'genre' => '', 'gemocht' => ''])) ?>">Zurücksetzen</a>
  <?php endif; ?>
</form>



<?php if (!$buecher): ?>
  <div class="karte leer">
    <?php if ($gesamt === 0): ?>
      <p>Deine Bibliothek ist noch leer.</p>
      <a class="knopf" href="buch_neu.php">Erstes Buch hinzufügen</a>
    <?php else: ?>
      <p>Keine Bücher gefunden. Passe Suche oder Filter an.</p>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="raster">
    <?php foreach ($buecher as $b): ?>
      <a class="buchkarte" href="buch.php?id=<?= (int)$b['id'] ?>">
        <?php if ($b['cover']): ?>
          <img class="cover" src="<?= e($b['cover']) ?>" alt="Cover von <?= e($b['titel']) ?>" loading="lazy">
        <?php else: ?>
          <div class="cover platzhalter"><?= e(mb_substr($b['titel'], 0, 1)) ?></div>
        <?php endif; ?>
        <div class="buchinfo">
          <strong class="titel"><?= e($b['titel']) ?></strong>
          <span class="autor"><?= e($b['autor']) ?></span>
          <span class="sterne <?= $b['bewertung'] === null ? 'unbewertet' : '' ?>"><?= e(sterne($b['bewertung'])) ?></span>
          <span class="badge status-<?= e($b['status']) ?>"><?= e(STATUS[$b['status']]) ?></span>
          <?php if ($b['gemocht'] !== null): ?>
            <span class="gemocht"><?= $b['gemocht'] ? '♥ Gemocht' : '✕ Nicht gemocht' ?></span>
          <?php endif; ?>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php require 'footer.php'; ?>
