<?php
/**
 * Database — PDO Singleton
 * Usage: $pdo = DB::connection();
 */

require_once __DIR__ . '/../../config/database.php';

class DB
{
    private static ?PDO $instance = null;

    public static function connection(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
            );
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Never expose DB credentials in production
                http_response_code(500);
                die('<h2>Database connection failed. Please check config/database.php.</h2>');
            }
        }
        return self::$instance;
    }

    // Prevent cloning / unserialization
    private function __construct() {}
    private function __clone() {}
}
