<?php
/**
 * Caminhos baseados na localização deste arquivo (app/view/home.php)
 */

// Inclui os arquivos necessários com os caminhos corretos
require_once __DIR__ . '/../../lib/Database.php';
require_once __DIR__ . '/../model/HomeModel.php';
require_once __DIR__ . '/../controllers/HomeController.php';

// Obtém a conexão PDO usando a sua classe estática
$pdo = Database::getConnection();

// Instancia o Model (passando a conexão obtida) e o Controller
$model = new HomeModel($pdo);
$controller = new HomeController($model);

// Processa as ações de cadastrar e deletar
$controller->cadastrar();
$controller->deletar();

// Busca a lista de máquinas para exibir na tabela
$maquinas = $controller->listarMaquinas();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Máquinas - Facens</title>
    <style>
        /* CSS Geral */
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f0f2f5; margin: 0; }
        .container { max-width: 1200px; margin: 30px auto; padding: 20px; }
        .card { background-color: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 30px; }
        .card-title { font-size: 1.8rem; margin-top: 0; margin-bottom: 25px; color: #333; }
        .form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; }
        label { margin-bottom: 8px; color: #555; font-weight: bold; }
        input[type="text"], select { padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 1rem; }
        .btn { padding: 12px 20px; border: none; border-radius: 5px; color: white; font-size: 1rem; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; }
        .btn-primary, .btn-success { background-color: #007bff; }
        .btn-edit { background-color: #ffc107; }
        .btn-delete { background-color: #dc3545; }
        a.btn-secondary { background-color: #6c757d; margin-left: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { padding: 12px; border: 1px solid #dee2e6; text-align: left; }
        th { background-color: #343a40; color: white; }
        tbody tr:nth-child(odd) { background-color: #f8f9fa; }
        .status-disponivel { color: green; font-weight: bold; }
        .actions a { margin-right: 5px; }

        /* CSS da Navbar Atualizado */
        .navbar {
            background-color: #343a40;
            color: white;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 60px;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .navbar-nav {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }
        .nav-item {
            margin-left: 20px;
        }
        .nav-link {
            color: #f8f9fa;
            text-decoration: none;
            font-size: 1rem;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .nav-link:hover {
            background-color: #495057;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="navbar-brand">
            <span>Facens</span>
        </div>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="home.php" class="nav-link">Empréstimo</a>
            </li>
            <li class="nav-item">
                <a href="historico.php" class="nav-link">Histórico</a>
            </li>
            <li class="nav-item">
                <a href="usuarios.php" class="nav-link">Usuários</a>
            </li>
        </ul>
    </nav>

    <div class="container">
        <div class="card">
            <h2 class="card-title">Cadastro de Máquinas</h2>
            <form action="home.php" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="marca">Marca</label>
                        <input type="text" id="marca" name="marca" required>
                    </div>
                    <div class="form-group">
                        <label for="modelo">Modelo</label>
                        <input type="text" id="modelo" name="modelo" required>
                    </div>
                    <div class="form-group">
                        <label for="memoria">Memória</label>
                        <input type="text" id="memoria" name="memoria">
                    </div>
                    <div class="form-group">
                        <label for="armazenamento">Armazenamento</label>
                        <input type="text" id="armazenamento" name="armazenamento">
                    </div>
                    <div class="form-group">
                        <label for="patrimonio">Patrimônio</label>
                        <input type="text" id="patrimonio" name="patrimonio">
                    </div>
                    <div class="form-group">
                        <label for="servicetag">Service Tag</label>
                        <input type="text" id="servicetag" name="servicetag">
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo</label>
                        <select id="tipo" name="tipo">
                            <option value="Desktop">Desktop</option>
                            <option value="Notebook">Notebook</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="DISPONÍVEL">DISPONÍVEL</option>
                            <option value="EM MANUTENÇÃO">EM MANUTENÇÃO</option>
                            <option value="EMPRESTADO">EMPRESTADO</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="cadastrar" class="btn btn-primary">Cadastrar</button>
            </form>

            <h2 class="card-title" style="margin-top: 40px;">Máquinas Cadastradas</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>Marca</th><th>Modelo</th><th>Memória</th><th>Armazenamento</th><th>Patrimônio</th><th>Servicetag</th><th>Tipo</th><th>Status</th><th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($maquinas)): ?>
                        <tr>
                            <td colspan="9" style="text-align:center;">Nenhuma máquina cadastrada.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($maquinas as $maquina): ?>
                        <tr>
                            <td><?= htmlspecialchars($maquina['marca']) ?></td>
                            <td><?= htmlspecialchars($maquina['modelo']) ?></td>
                            <td><?= htmlspecialchars($maquina['memoria']) ?></td>
                            <td><?= htmlspecialchars($maquina['armazenamento']) ?></td>
                            <td><?= htmlspecialchars($maquina['patrimonio']) ?></td>
                            <td><?= htmlspecialchars($maquina['servicetag']) ?></td>
                            <td><?= htmlspecialchars($maquina['tipo']) ?></td>
                            <td class="status-disponivel"><?= htmlspecialchars($maquina['status']) ?></td>
                            <td class="actions">
                                <a href="editar.php?id=<?= $maquina['id'] ?>" class="btn btn-edit">Editar</a>
                                <a href="home.php?action=delete&id=<?= $maquina['id'] ?>" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja deletar esta máquina?');">Deletar</a>
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