<?php
session_start();
require_once __DIR__ . '/../model/LoginModel.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (LoginModel::autenticar($usuario, $senha)) {
        
        // --- SUCESSO ---
        $_SESSION['usuario_logado'] = true;
        $_SESSION['usuario_nome'] = $usuario;

        // A MUDANÇA ESTÁ AQUI: Redireciona direto para o arquivo da view
        header("Location: /EMPRESTIMO/emprestimo/app/view/home.php");
        exit;

    } else {
        // --- FALHA ---
        header("Location: ../../index.php?error=invalid");
        exit;
    }
} else {
    // Se não for um POST, volta para a página inicial
    header("Location: /EMPRESTIMO/emprestimo/index.php");
    exit;
}
?>