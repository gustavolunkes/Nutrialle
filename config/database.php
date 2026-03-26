<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'nutrialle');
define('DB_USER', 'root');
define('DB_PASS', '123456');
define('DB_CHARSET', 'utf8mb4');

// Classe de conexão com o banco
class Database {
    private static $instance = null;
    private $conn;
    
    /**
     * Construtor privado (Singleton)
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false
            ];
            
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch(PDOException $e) {
            die("Erro na conexão com o banco de dados: " . $e->getMessage());
        }
    }
    
    /**
     * Retorna a instância única da conexão (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Retorna a conexão PDO
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Previne clonagem
     */
    private function __clone() {}
    
    /**
     * Previne deserialização
     */
    public function __wakeup() {
        throw new Exception("Não é possível deserializar singleton");
    }
}

// Função helper para obter a conexão facilmente
function getDB() {
    return Database::getInstance()->getConnection();
}
?>