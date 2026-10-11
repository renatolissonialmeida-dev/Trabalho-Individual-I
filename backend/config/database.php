<?php
// Configurações de Conexão com o MySQL do XAMPP
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'fazbear_db');

class Database {
    private static $instancia = null;

    public static function getConnection() {
        if (self::$instancia === null) {
            try {
                self::$instancia = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                header('Content-Type: application/json');
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Erro de conexão com o banco de dados: ' . $e->getMessage()
                ]);
                exit;
            }
        }
        return self::$instancia;
    }
}