<?php
/**
 * Clase Database (Singleton)
 * Gestiona la conexión única a la base de datos mediante el patrón Singleton.
 */
class Database {
    private static $instance = null;
    private $pdo;

    /**
     * El constructor es privado para evitar instanciación externa.
     */
    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    /**
     * Retorna la instancia única de la clase.
     * Si no existe, la crea.
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retorna el objeto PDO de la conexión.
     */
    public function getConnection(): PDO {
        return $this->pdo;
    }

    /**
     * Prevenir la clonación del objeto.
     */
    private function __clone() {}

    /**
     * Prevenir la deserialización.
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
