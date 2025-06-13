<?php
/**
 * Página para exibir o histórico de todos os empréstimos.
 * Lógica de banco de dados movida para o HistoricoModel.
 */

// Inclui os arquivos necessários
require_once __DIR__ . '/../../lib/Database.php';      // Para a conexão
require_once __DIR__ . '/../model/HistoricoModel.php'; // Inclui o nosso novo Model
session_start();

// 1. Cria uma instância do Model
$historicoModel = new HistoricoModel();

// 2. Usa o Model para buscar o histórico
$historico = $historicoModel->buscarHistoricoCompleto();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Empréstimos - Facens</title>
    
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
            <h2 class="card-title">Histórico de Empréstimos</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>DESCRIÇÃO</th>
                        <th>INICIO</th>
                        <th>FIM</th>
                        <th>STATUS</th>
                        <th>USUÁRIO</th>
                        <th>CPF</th>
                        <th>CONDIÇÕES DE DEVOLUÇÃO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($historico)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;">Nenhum histórico de empréstimo encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($historico as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['marca'] . ' ' . $item['modelo'] . ' - ' . $item['memoria'] . ' - ' . $item['armazenamento']) ?></td>
                            <td><?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($item['inicio']))) ?></td>
                            <td><?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($item['fim']))) ?></td>
                            <td><span class="status-finalizado"><?= htmlspecialchars($item['status']) ?></span></td>
                            <td><?= htmlspecialchars($item['usuario']) ?></td>
                            <td><?= htmlspecialchars($item['cpf']) ?></td>
                            <td><?= htmlspecialchars($item['condicao_devolucao']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>