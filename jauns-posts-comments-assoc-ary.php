<!-- plans
1. Izveidot datubāzes savienojumu, izmantojot PDO
2. Izpildīt LEFT JOIN vaicājumu, lai iegūtu plakanu masīvu
3. Pārveidot plakano masīvu uz hierarhisku asociatīvo masīvu
4. Ģenerēt HTML sarakstu no asociatīvā masīva
5. Aizvērt PDO savienojumu (nav obligāti, bet ieteicams) -->

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Definējam savienojuma parametrus
define('DB_SERVER', 'localhost');
define('DB_NAME', 'posts_db');
define('DB_USERNAME', 'usserr');
define('DB_PASSWORD', 'password');

try {
    $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME;
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Savienojuma kļūda: " . $e->getMessage());
}
