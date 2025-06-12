<?php
// Includes e instanciação dos objetos
require_once __DIR__ . '/../../lib/Database.php';
// ATENÇÃO: Os arquivos HomeModel e HomeController precisam existir para este código funcionar.
require_once __DIR__ . '/../model/HomeModel.php';
require_once __DIR__ . '/../controllers/HomeController.php';

$pdo = Database::getConnection();
$model = new HomeModel($pdo);
$controller = new HomeController($model);

// Processa a atualização se o formulário for enviado
$controller->atualizar();

// Pega o ID da URL e busca os dados da máquina
$maquina_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$maquina = null;

if ($maquina_id) {
    $maquina = $model->getMaquinaById($maquina_id);
}

// Se não encontrou a máquina, redireciona para a home
if (!$maquina) {
    header("Location: home.php?status=notfound");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Máquina - Facens</title>
    
    <link rel="stylesheet" href="/EMPRESTIMO/emprestimo/app/template/css/style.css">

</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <span>Facens - Edição</span>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h2 class="card-title">Editar Máquina (ID: <?= htmlspecialchars($maquina['id']) ?>)</h2>
            
            <form action="editar.php?id=<?= htmlspecialchars($maquina['id']) ?>" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($maquina['id']) ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="marca">Marca</label>
                        <input type="text" id="marca" name="marca" value="<?= htmlspecialchars($maquina['marca']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="modelo">Modelo</label>
                        <input type="text" id="modelo" name="modelo" value="<?= htmlspecialchars($maquina['modelo']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="memoria">Memória</label>
                        <input type="text" id="memoria" name="memoria" value="<?= htmlspecialchars($maquina['memoria']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="armazenamento">Armazenamento</label>
                        <input type="text" id="armazenamento" name="armazenamento" value="<?= htmlspecialchars($maquina['armazenamento']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="patrimonio">Patrimônio</label>
                        <input type="text" id="patrimonio" name="patrimonio" value="<?= htmlspecialchars($maquina['patrimonio']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="servicetag">Service Tag</label>
                        <input type="text" id="servicetag" name="servicetag" value="<?= htmlspecialchars($maquina['servicetag']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo</label>
                        <select id="tipo" name="tipo">
                            <option value="Desktop" <?= $maquina['tipo'] == 'Desktop' ? 'selected' : '' ?>>Desktop</option>
                            <option value="Notebook" <?= $maquina['tipo'] == 'Notebook' ? 'selected' : '' ?>>Notebook</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="DISPONÍVEL" <?= $maquina['status'] == 'DISPONÍVEL' ? 'selected' : '' ?>>DISPONÍVEL</option>
                            <option value="EM MANUTENÇÃO" <?= $maquina['status'] == 'EM MANUTENÇÃO' ? 'selected' : '' ?>>EM MANUTENÇÃO</option>
                            <option value="EMPRESTADO" <?= $maquina['status'] == 'EMPRESTADO' ? 'selected' : '' ?>>EMPRESTADO</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="atualizar" class="btn btn-success">Salvar Alterações</button>
                <a href="home.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>