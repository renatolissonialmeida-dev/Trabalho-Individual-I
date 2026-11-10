<?php
// Carrega as variáveis de ambiente do arquivo .env localizado na raiz do projeto
$envPath = __DIR__ . '/../../.env';
$env = parse_ini_file($envPath);

// Configurações de Conexão usando as variáveis do .env
define('DB_HOST', $env['DB_HOST']);
define('DB_USER', $env['DB_USER']);
define('DB_PASS', $env['DB_PASS'] ?? ''); // O ?? '' garante que funcione se a senha for vazia
define('DB_NAME', $env['DB_NAME']);

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
                http_response_code(500); // <-- Adicionado para o Requisito 7 (Status HTTP)
                header('Content-Type: application/json');
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Indisponibilidade temporária nos servidores do banco de dados.'
                ]);
                exit;
            }
        }
        return self::$instancia;
    }
}