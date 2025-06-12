<?php
/**
 * Página para realizar um novo empréstimo.
 * A verificação de login foi REMOVIDA.
 */

// A sessão ainda é iniciada para tentar pegar o ID do usuário
session_start();

// O bloco que verificava se o usuário estava logado foi removido daqui.

// Inclui o arquivo do banco de dados para poder fazer a conexão
require_once __DIR__ . '/../../lib/Database.php';

// Inicializa variáveis que serão usadas no HTML
$maquina = null;
$error_message = '';

try {
    $pdo = Database::getConnection();

    // --- LÓGICA PARA PROCESSAR O ENVIO DO FORMULÁRIO (MÉTODO POST) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Coleta os dados do formulário
        $id_maquina = $_POST['id_maquina'];
        $nome_completo = $_POST['nome_completo'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
        $data_emprestimo = $_POST['data_emprestimo'];

        // MODIFICAÇÃO: Se não houver usuário logado, usa 0 (ou null) como padrão para evitar erros.
        $id_usuario_logado = $_SESSION['usuario_id'] ?? 0; 

        $pdo->beginTransaction();
        
        $sql_emprestimo = "INSERT INTO emprestimos 
            (id_maquina, nome_completo, email, cpf, data_emprestimo, id_usuario_emprestimo) 
            VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt_emprestimo = $pdo->prepare($sql_emprestimo);
        $stmt_emprestimo->execute([$id_maquina, $nome_completo, $email, $cpf, $data_emprestimo, $id_usuario_logado]);

        $sql_maquina = "UPDATE maquinas SET status = 'EMPRESTADO' WHERE id = ?";
        $stmt_maquina = $pdo->prepare($sql_maquina);
        $stmt_maquina->execute([$id_maquina]);

        $pdo->commit();
        
        header("Location: emprestimo.php?status=sucesso");
        exit;

    } else {
        // --- LÓGICA PARA EXIBIR A PÁGINA (MÉTODO GET) ---
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            throw new Exception("ID da máquina não fornecido.");
        }
        $id_maquina = $_GET['id'];

        $stmt = $pdo->prepare("SELECT * FROM maquinas WHERE id = ? AND status = 'DISPONÍVEL'");
        $stmt->execute([$id_maquina]);
        $maquina = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$maquina) {
            throw new Exception("Máquina não encontrada ou não está disponível para empréstimo.");
        }
    }

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $error_message = "Erro: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Empréstimo - Facens</title>
    
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
            <h2 class="card-title">Formulário de Empréstimo</h2>

            <?php if (!empty($error_message)): ?>
                <div style="color: red; background-color: #fdd; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    <?= htmlspecialchars($error_message) ?>
                    <br><br><a href="emprestimo.php" class="btn btn-primary">Voltar para a lista</a>
                </div>
            <?php elseif ($maquina): ?>
                <div class="info-maquina">
                    <h5>Você está emprestando a máquina:</h5>
                    <ul>
                        <li><strong>Marca:</strong> <?= htmlspecialchars($maquina['marca']) ?></li>
                        <li><strong>Modelo:</strong> <?= htmlspecialchars($maquina['modelo']) ?></li>
                        <li><strong>Patrimônio:</strong> <?= htmlspecialchars($maquina['patrimonio']) ?></li>
                    </ul>
                </div>

                <form action="realizar_emprestimo.php" method="POST">
                    <input type="hidden" name="id_maquina" value="<?= htmlspecialchars($maquina['id']) ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome_completo">Nome Completo</label>
                            <input type="text" id="nome_completo" name="nome_completo" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email">
                        </div>
                    </div>
                    
                    <div class="form-row" style="margin-bottom: 20px;">
                        <div class="form-group">
                            <label for="cpf">CPF</label>
                            <input type="text" id="cpf" name="cpf">
                        </div>
                        <div class="form-group">
                            <label for="data_emprestimo">Data do Empréstimo</label>
                            <input type="date" id="data_emprestimo" name="data_emprestimo" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Salvar Empréstimo</button>
                    <a href="emprestimo.php" class="btn btn-secondary">Cancelar</a>
                </form>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>