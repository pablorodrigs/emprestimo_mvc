<?php
/**
 * Página para exibir o histórico de todos os empréstimos.
 * A verificação de login foi removida.
 */

require_once __DIR__ . '/../../lib/Database.php';
session_start(); // Mantido para o funcionamento do Logout e outras funcionalidades de sessão.

// O bloco que verificava se o usuário estava logado foi REMOVIDO daqui.

// Função para buscar o histórico completo de empréstimos
function buscarHistoricoCompleto() {
    try {
        $pdo = Database::getConnection();
        // Esta consulta SQL une as tabelas 'emprestimos' e 'maquinas'
        $sql = "
            SELECT 
                m.marca, m.modelo, m.armazenamento, m.memoria, 
                e.data_emprestimo AS inicio, 
                e.data_devolucao_efetiva AS fim, 
                e.status,
                e.nome_completo AS usuario,
                e.cpf,
                e.condicao_devolucao
            FROM 
                emprestimos e
            JOIN 
                maquinas m ON e.id_maquina = m.id
            WHERE
                e.status = 'FINALIZADO'
            ORDER BY 
                e.data_devolucao_efetiva DESC
        ";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erro ao buscar o histórico: " . $e->getMessage());
        return [];
    }
}

// Chama a função para obter os dados do histórico
$historico = buscarHistoricoCompleto();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Empréstimos - Facens</title>
    
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