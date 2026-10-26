<?php
require_once 'funktionen.php';
header('Location: ' . (eingeloggt() ? 'bibliothek.php' : 'login.php'));
exit;
