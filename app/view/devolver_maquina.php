<?php
/**
 * Página para processar a devolução de uma máquina.
 */

require_once __DIR__ . '/../../lib/Database.php';
session_start();

$emprestimo = null;
$error_message = '';
$id_emprestimo = $_GET['id_emprestimo'] ?? 0;

try {
    $pdo = Database::getConnection();

    // --- LÓGICA PARA PROCESSAR O FORMULÁRIO (MÉTODO POST) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $usuario_admin = $_POST['usuario_admin'];
        $senha_admin = $_POST['senha_admin'];
        $id_emprestimo_form = $_POST['id_emprestimo'];
        $condicao_devolucao = $_POST['condicao'];
        
        $stmt_admin = $pdo->prepare("SELECT id, senha FROM login WHERE usuario = ?");
        $stmt_admin->execute([$usuario_admin]);
        $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($senha_admin, $admin['senha'])) {
            throw new Exception("Usuário ou senha do recebedor inválidos.");
        }
        $id_usuario_devolucao = $admin['id'];
        
        $pdo->beginTransaction();
        
        $stmt_get_maquina = $pdo->prepare("SELECT id_maquina FROM emprestimos WHERE id = ?");
        $stmt_get_maquina->execute([$id_emprestimo_form]);
        $id_maquina = $stmt_get_maquina->fetchColumn();

        if (!$id_maquina) {
            throw new Exception("Empréstimo não encontrado.");
        }

        $sql_emprestimo = "UPDATE emprestimos SET 
                            status = 'FINALIZADO', 
                            data_devolucao_efetiva = CURRENT_DATE,
                            id_usuario_devolucao = ?,
                            condicao_devolucao = ?
                          WHERE id = ?";
        $stmt_emprestimo = $pdo->prepare($sql_emprestimo);
        $stmt_emprestimo->execute([$id_usuario_devolucao, $condicao_devolucao, $id_emprestimo_form]);

        $novo_status_maquina = ($condicao_devolucao === 'COM DEFEITO') ? 'EM MANUTENÇÃO' : 'DISPONÍVEL';
        $sql_maquina = "UPDATE maquinas SET status = ? WHERE id = ?";
        $stmt_maquina = $pdo->prepare($sql_maquina);
        $stmt_maquina->execute([$novo_status_maquina, $id_maquina]);
        
        $pdo->commit();

        header("Location: emprestimo.php?status=devolucao_sucesso");
        exit;
    }
    
    // --- LÓGICA PARA EXIBIR A PÁGINA (MÉTODO GET) ---
    if (!$id_emprestimo) throw new Exception("ID do empréstimo não fornecido.");
    
    $sql_dados = "SELECT e.*, m.marca, m.modelo, m.servicetag, m.patrimonio, m.tipo
                  FROM emprestimos e
                  JOIN maquinas m ON e.id_maquina = m.id
                  WHERE e.id = ?";
    $stmt_dados = $pdo->prepare($sql_dados);
    $stmt_dados->execute([$id_emprestimo]);
    $emprestimo = $stmt_dados->fetch(PDO::FETCH_ASSOC);

    if (!$emprestimo || $emprestimo['status'] !== 'ATIVO') {
        throw new Exception("Empréstimo não encontrado ou já foi finalizado.");
    }

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
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