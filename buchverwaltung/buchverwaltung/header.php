
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($seitentitel ?? 'Buchverwaltung') ?> – Buchverwaltung</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/design.js"></script>
</head>
<body>
<header class="kopf">
  <div class="inhalt kopf-innen">
    <a class="logo" href="bibliothek.php">Meine Bibliothek</a>
    <nav>
      <?php if (eingeloggt()): ?>
        <a href="bibliothek.php">Bibliothek</a>
        <a href="buch_neu.php">+ Buch hinzufügen</a>
        <span class="nutzer"><?= e($_SESSION['benutzername']) ?></span>
        <a href="logout.php">Abmelden</a>
      <?php else: ?>
        <a href="login.php">Anmelden</a>
        <a href="registrieren.php">Registrieren</a>
      <?php endif; ?>
      <select id="designwahl" aria-label="Design wählen">
        <option value="hell">Hell</option>
        <option value="dunkel">Dunkel</option>
        <option value="pink">Pink</option>
      </select>
    </nav>
  </div>
</header>
<main class="inhalt">
