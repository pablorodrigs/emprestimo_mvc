<?php
/**
 * Página para processar a devolução de uma máquina (versão organizada).
 */

require_once __DIR__ . '/../../lib/Database.php';
require_once __DIR__ . '/../model/DevolverModel.php'; // Incluindo nosso novo Model
session_start();

$emprestimo = null;
$error_message = '';

try {
    $devolverModel = new DevolverModel();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Se o formulário foi enviado, chama o método para processar
        $sucesso = $devolverModel->processarDevolucao($_POST);
        if ($sucesso) {
            header("Location: emprestimo.php?status=devolucao_sucesso");
            exit;
        }
    } else {
        // Se a página foi carregada, busca os dados para exibir o formulário
        $id_emprestimo = $_GET['id_emprestimo'] ?? 0;
        if (!$id_emprestimo) {
            throw new Exception("ID do empréstimo não fornecido.");
        }
        $emprestimo = $devolverModel->getEmprestimoAtivo($id_emprestimo);
        if (!$emprestimo) {
            throw new Exception("Empréstimo não encontrado ou já foi finalizado.");
        }
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devolução de Objeto - Facens</title>
    <link rel="stylesheet" href="/EMPRESTIMO/emprestimo/app/template/css/style.css">
</head>
<body>
    <div class="container devolucao-container">
        <div class="card">
            <h1 class="card-title">Devolução de Objeto</h1>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <strong>Erro:</strong> <?= htmlspecialchars($error_message) ?>
                    <br><br><a href="emprestimo.php" class="btn btn-primary">Voltar para a lista</a>
                </div>
            <?php elseif ($emprestimo): ?>
                <form action="devolver_maquina.php" method="POST">
                    <input type="hidden" name="id_emprestimo" value="<?= $emprestimo['id'] ?>">

                    <div class="auth-row">
                        <div class="form-group">
                            <input type="text" name="usuario_admin" placeholder="Usuário que recebe" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="senha_admin" placeholder="Senha" required>
                        </div>
                    </div>

                    <div class="info-maquina">
                        <p><?= htmlspecialchars($emprestimo['marca'] . ' ' . $emprestimo['modelo']) ?></p>
                        <div class="details-grid">
                            <strong>TIPO:</strong> <span><?= htmlspecialchars($emprestimo['tipo']) ?></span>
                            <strong>SERVICE TAG:</strong> <span><?= htmlspecialchars($emprestimo['servicetag']) ?></span>
                            <strong>PATRIMÔNIO:</strong> <span><?= htmlspecialchars($emprestimo['patrimonio']) ?></span>
                            <strong>CONDIÇÕES:</strong>
                            <select id="condicao" name="condicao" required>
                                <option value="OK">OK</option>
                                <option value="COM DEFEITO">COM DEFEITO</option>
                                <option value="AVARIADO">AVARIADO</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Assinar e Devolver</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>