<?php
// Gemeinsame Hilfsfunktionen, wird am Anfang jeder Seite eingebunden
session_start();
require_once __DIR__ . '/db.php';

const STATUS = 
[
    'moechte_lesen' => 'Möchte ich lesen',
    'lese_gerade'   => 'Lese ich gerade',
    'gelesen'       => 'Gelesen',
    'abgebrochen'   => 'Abgebrochen',
];

// Text sicher ausgeben (Schutz vor XSS)
function e($text): string 
{
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

function eingeloggt(): bool 
{
    return isset($_SESSION['benutzer_id']);
}

function login_pflicht(): void 
{
    if (!eingeloggt()) 
    {
        header('Location: login.php');
        exit;
    }
}

// CSRF-Schutz: jedes Formular bekommt einen geheimen Token
function csrf_token(): string 
{

    if (empty($_SESSION['csrf'])) 
    {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_pruefen(): void 
{
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) 
    {
        http_response_code(400);
        die('Ungültige Anfrage.');
    }
}

function sterne($n): string 
{
    if ($n === null || $n === '') return 'Nicht bewertet';
    return str_repeat('★', (int)$n) . str_repeat('☆', 5 - (int)$n);
}

function datum_de($d): string 
{
    return $d ? date('d.m.Y', strtotime($d)) : '–';
}

// Alle Genres für Dropdowns
function alle_genres(PDO $pdo): array 
{
    return $pdo->query('SELECT id, name FROM genre ORDER BY name')->fetchAll();
}

// Prüft die Formulardaten eines Buchs. Gibt [saubere Daten, Fehlerliste] zurück
function buch_pruefen(PDO $pdo, array $p): array 
{
    $f = [];
    $d = [];

    $d['titel'] = trim($p['titel'] ?? '');
    if ($d['titel'] === '' || mb_strlen($d['titel']) > 150) $f[] = 'Titel ist Pflicht (max. 150 Zeichen).';

    $d['autor'] = trim($p['autor'] ?? '');
    if ($d['autor'] === '' || mb_strlen($d['autor']) > 100) $f[] = 'Autor ist Pflicht (max. 100 Zeichen).';

    $j = trim($p['erscheinungsjahr'] ?? '');
    $d['erscheinungsjahr'] = null;
    if ($j !== '') 
    {
        if (ctype_digit($j) && (int)$j >= 1000 && (int)$j <= (int)date('Y') + 1) $d['erscheinungsjahr'] = (int)$j;
        else $f[] = 'Erscheinungsjahr ist ungültig.';
    }

    $g = trim($p['genre_id'] ?? '');
    $d['genre_id'] = null;
    if ($g !== '') 
    {
        $st = $pdo->prepare('SELECT COUNT(*) FROM genre WHERE id = :id');
        $st->execute(['id' => $g]);
        if (ctype_digit($g) && $st->fetchColumn() > 0) $d['genre_id'] = (int)$g;
        else $f[] = 'Genre ist ungültig.';
    }

    $d['status'] = $p['status'] ?? '';
    if (!array_key_exists($d['status'], STATUS)) $f[] = 'Status ist ungültig.';

    $b = trim($p['bewertung'] ?? '');
    $d['bewertung'] = null;
    if ($b !== '') 
    {
        if (ctype_digit($b) && (int)$b <= 5) $d['bewertung'] = (int)$b;
        else $f[] = 'Bewertung muss zwischen 0 und 5 liegen.';
    }

    $m = trim($p['gemocht'] ?? '');
    $d['gemocht'] = null;
    if ($m !== '') 
    {
        if ($m === '0' || $m === '1') $d['gemocht'] = (int)$m;
        else $f[] = 'Angabe zu „gemocht“ ist ungültig.';
    }

    $d['beschreibung'] = trim($p['beschreibung'] ?? '') ?: null;
    $d['rezension']    = trim($p['rezension'] ?? '') ?: null;
    if (mb_strlen((string)$d['beschreibung']) > 5000 || mb_strlen((string)$d['rezension']) > 5000)
    {
        $f[] = 'Beschreibung und Rezension dürfen max. 5000 Zeichen haben.';
    }

    $c = trim($p['cover'] ?? '');
    $d['cover'] = null;
    if ($c !== '') 
    {
        if (filter_var($c, FILTER_VALIDATE_URL) && preg_match('~^https?://~i', $c) && strlen($c) <= 255) $d['cover'] = $c;
        else $f[] = 'Cover muss ein Link sein, der mit http:// oder https:// beginnt.';
    }

    foreach (['startdatum', 'enddatum'] as $feld) 
    {
        $v = trim($p[$feld] ?? '');
        $d[$feld] = null;
        if ($v !== '') 
        {
            $dt = DateTime::createFromFormat('Y-m-d', $v);
            if ($dt && $dt->format('Y-m-d') === $v) $d[$feld] = $v;
            else $f[] = ucfirst($feld) . ' ist ungültig.';
        }
    }
    if ($d['startdatum'] && $d['enddatum'] && $d['enddatum'] < $d['startdatum']) 
    {
        $f[] = 'Das Enddatum darf nicht vor dem Startdatum liegen.';
    }

    return [$d, $f];
}
