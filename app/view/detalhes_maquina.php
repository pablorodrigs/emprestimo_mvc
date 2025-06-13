<?php
/**
 * Página para exibir os detalhes e o histórico de uma máquina.
 * Lógica de banco de dados movida para o DetalhesModel.
 */

require_once __DIR__ . '/../../lib/Database.php';
require_once __DIR__ . '/../model/DetalhesModel.php'; // Incluindo nosso novo Model
session_start();

// Inicializa variáveis
$maquina = null;
$historico = [];
$error_message = '';
$id_maquina = $_GET['id'] ?? 0;

if (!$id_maquina) {
    $error_message = "ID da máquina não fornecido.";
} else {
    try {
        // 1. Cria uma instância do Model
        $detalhesModel = new DetalhesModel();

        // 2. Usa o Model para buscar os dados
        $maquina = $detalhesModel->getMaquinaById($id_maquina);
        if (!$maquina) {
            throw new Exception("Máquina não encontrada.");
        }
        $historico = $detalhesModel->getHistoricoByMaquinaId($id_maquina);

    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Máquina - Facens</title>
    
    <!-- Link para o seu arquivo CSS centralizado -->
    <link rel="stylesheet" href="/EMPRESTIMO/emprestimo/app/template/css/style.css">

</head>
<body>

    <nav class="navbar">
        <div class="navbar-brand"><span>Facens</span></div>
        <ul class="navbar-nav">
            <li class="nav-item"><a href="emprestimo.php" class="nav-link">Empréstimo</a></li>
            <li class="nav-item"><a href="historico.php" class="nav-link">Histórico</a></li>
            <li class="nav-item"><a href="home.php" class="nav-link">Cadastrar Máquina</a></li>
            <li class="nav-item"><a href="logout.php" class="nav-link">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="card">
            <?php if ($error_message): ?>
                <div style="color: red; padding: 15px; text-align: center;">
                    <strong>Erro:</strong> <?= htmlspecialchars($error_message) ?>
                    <br><br><a href="emprestimo.php" class="btn btn-primary">Voltar para a lista</a>
                </div>
            <?php elseif ($maquina): ?>
                <div class="machine-header">
                    <strong>DESCRIÇÃO:</strong> <?= htmlspecialchars($maquina['marca'] . ' ' . $maquina['modelo']) ?> &nbsp;|&nbsp;
                    <strong>TIPO:</strong> <?= htmlspecialchars($maquina['tipo']) ?> &nbsp;|&nbsp;
                    <strong>SERVICE TAG:</strong> <?= htmlspecialchars($maquina['servicetag']) ?> &nbsp;|&nbsp;
                    <strong>PATRIMÔNIO:</strong> <?= htmlspecialchars($maquina['patrimonio']) ?>
                </div>

                <table class="history-log">
                    <thead>
                        <tr>
                            <th>INFORMAÇÃO</th>
                            <th>DATA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($historico)): ?>
                            <tr>
                                <td colspan="2" style="text-align:center;">Nenhum histórico de empréstimo para esta máquina.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($historico as $evento): ?>
                                <?php // Se tem data de devolução, mostra o evento de devolução ?>
                                <?php if ($evento['data_devolucao_efetiva']): ?>
                                <tr>
                                    <td>Devolvido e conferido por <?= htmlspecialchars($evento['quem_recebeu'] ?? 'N/A') ?>. Condição: <?= htmlspecialchars($evento['condicao_devolucao'] ?? 'N/A') ?>.</td>
                                    <td><?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($evento['data_devolucao_efetiva']))) ?></td>
                                </tr>
                                <?php endif; ?>

                                <?php // Mostra sempre o evento do empréstimo ?>
                                <tr>
                                    <td>Emprestado e conferido por <?= htmlspecialchars($evento['quem_emprestou'] ?? 'N/A') ?> para <?= htmlspecialchars($evento['pessoa_que_pegou']) ?>.</td>
                                    <td><?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($evento['data_emprestimo']))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>