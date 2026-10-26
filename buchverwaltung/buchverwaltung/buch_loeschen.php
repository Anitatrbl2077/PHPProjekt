<?php
require_once 'funktionen.php';
login_pflicht();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
{   // Löschen nur per POST
    header('Location: bibliothek.php');
    exit;
}
csrf_pruefen();
$st = $pdo->prepare('DELETE FROM buch WHERE id = :id AND benutzer_id = :uid');
$st->execute(['id' => $_POST['id'] ?? '', 'uid' => $_SESSION['benutzer_id']]);
header('Location: bibliothek.php');
exit;
