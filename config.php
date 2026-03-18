<?php
// ============================================
// CONFIGURACIÓN DE BASE DE DATOS - EJEMPLO
// ============================================
// 1. Copia este archivo: cp config.example.php config.php
// 2. Edita config.php con tus datos locales
// 3. NUNCA subas config.php al repositorio
// ============================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'ejercicio_git');
define('DB_USER', 'root');
define('DB_PASS', '1234');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
    }

    return $pdo;
}
