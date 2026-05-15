<?php
// =============================================
// includes/db.php - Konekcija na bazu
// =============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // promijeni za Railway/produkciju
define('DB_PASS', '');           // promijeni za Railway/produkciju
define('DB_NAME', 'netflix_lv4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('<div style="color:red;padding:2rem;font-family:sans-serif;">
                <h2>Greška pri spajanju na bazu!</h2>
                <p>' . htmlspecialchars($e->getMessage()) . '</p>
                <p>Provjeri DB_HOST, DB_USER, DB_PASS, DB_NAME u includes/db.php</p>
            </div>');
        }
    }
    return $pdo;
}
