<?php

class Database {
    private static $host = "localhost";
    private static $dbname = "emprestimofacens";
    private static $user = "postgres";
    private static $password = "P@blo2712";
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    "pgsql:host=" . self::$host . ";dbname=" . self::$dbname,
                    self::$user,
                    self::$password
                );
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erro de conexão: " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
