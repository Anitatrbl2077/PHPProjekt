<?php

require_once 'funktionen.php';
$_SESSION = [];

session_destroy();

header('Location: login.php');

exit;
