<?php
require_once __DIR__ . '/../../lib/Database.php';

class LoginModel {
    public static function autenticar($usuario, $senha) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM login WHERE usuario = :usuario");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        $usuarioDB = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuarioDB && password_verify($senha, $usuarioDB['senha'])) {
            return true;
        }

        return false;
    }
}
