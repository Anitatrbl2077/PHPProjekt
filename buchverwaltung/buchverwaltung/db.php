<?php

// Datenbankverbindung mit PDO
$host = 'localhost';
$db   = 'buchverwaltung';
$user = 'root';   // XAMPP-Standard
$pass = '';       // XAMPP-Standard: kein Passwort

try 
{
    $pdo = new PDO
    (
        "mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //Fehler abfangen
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       //Liste
            PDO::ATTR_EMULATE_PREPARES   => false,                   //Prepared statement
        ]
    );
} 
catch (PDOException $e) 
{
    die('Datenbankverbindung fehlgeschlagen.');   // keine Details anzeigen
}
