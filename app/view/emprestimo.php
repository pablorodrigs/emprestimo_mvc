<?php
/**
 * Página principal para listar todas as máquinas.
 * A lógica de banco de dados foi movida para o MaquinaModel.
 */

// Inclui os arquivos necessários
require_once __DIR__ . '/../../lib/Database.php';      // Para a conexão
require_once __DIR__ . '/../model/MaquinaModel.php'; // Inclui o nosso novo Model

// 1. Cria uma instância do Model
$maquinaModel = new MaquinaModel();

// 2. Usa o Model para buscar as máquinas
$maquinas = $maquinaModel->listarMaquinasComStatus();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empréstimo de Máquinas - Facens</title>
    
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
            <h2 class="card-title">Máquinas para Empréstimo</h2>
            
            <?php if (isset($_GET['status']) && $_GET['status'] === 'devolucao_sucesso'): ?>
                <div class="alert-success">Máquina devolvida com sucesso!</div>
            <?php endif; ?>
            <?php if (isset($_GET['status']) && $_GET['status'] === 'sucesso'): ?>
                <div class="alert-success">Empréstimo realizado com sucesso!</div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Memória</th>
                        <th>Armazenamento</th>
                        <th>Patrimônio</th>
                        <th>Service Tag</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($maquinas)): ?>
                        <tr><td colspan="10" style="text-align:center;">Nenhuma máquina cadastrada.</td></tr>
                    <?php else: ?>
                        <?php foreach ($maquinas as $maquina): ?>
                        <tr>
                            <td><?= htmlspecialchars($maquina['id']) ?></td>
                            <td><?= htmlspecialchars($maquina['marca']) ?></td>
                            <td><?= htmlspecialchars($maquina['modelo']) ?></td>
                            <td><?= htmlspecialchars($maquina['memoria']) ?></td>
                            <td><?= htmlspecialchars($maquina['armazenamento']) ?></td>
                            <td><?= htmlspecialchars($maquina['patrimonio']) ?></td>
                            <td><?= htmlspecialchars($maquina['servicetag']) ?></td>
                            <td><?= htmlspecialchars($maquina['tipo']) ?></td>
                            <td>
                                <?php if ($maquina['status'] === 'DISPONÍVEL'): ?>
                                    <span class="status-disponivel">DISPONÍVEL</span>
                                <?php else: ?>
                                    <span class="status-emprestado">EMPRESTADO</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <?php if ($maquina['status'] === 'DISPONÍVEL'): ?>
                                    <a href="realizar_emprestimo.php?id=<?= $maquina['id'] ?>" class="btn btn-success">Emprestar</a>
                                <?php else: ?>
                                    <a href="devolver_maquina.php?id_emprestimo=<?= $maquina['emprestimo_id'] ?>" class="btn btn-warning" onclick="return confirm('Tem certeza que deseja registrar a devolução desta máquina?');">Devolver</a>
                                <?php endif; ?>
                                
                                <a href="detalhes_maquina.php?id=<?= $maquina['id'] ?>" class="btn btn-primary">Detalhes</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>