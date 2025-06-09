<?php
// A primeira coisa em um arquivo protegido é iniciar a sessão
session_start();

// PASSO 1: PORTÃO DE SEGURANÇA
if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    header("Location: index.php?error=login_required");
    exit();
}

// PASSO 2: SETUP INICIAL
require_once __DIR__ . '/lib/Database.php';
require_once __DIR__ . '/app/model/HomeModel.php';
require_once __DIR__ . '/app/model/EmprestimoModel.php';
require_once __DIR__ . '/app/controllers/HomeController.php';
require_once __DIR__ . '/app/controllers/EmprestimoController.php';

$pdo = Database::getConnection();
$homeModel = new HomeModel($pdo);
$emprestimoModel = new EmprestimoModel($pdo);
$homeController = new HomeController($homeModel);
$emprestimoController = new EmprestimoController($emprestimoModel, $homeModel);

// PASSO 3: PROCESSAMENTO DE AÇÕES
$action = $_GET['action'] ?? null;
switch ($action) {
    case 'cadastrar':
        $homeController->cadastrar();
        break;
    case 'delete':
        $homeController->deletar();
        break;
    case 'atualizar':
        $homeController->atualizar();
        break;
    case 'registrar_emprestimo':
        $emprestimoController->registrar();
        break;
}

// PASSO 4: CARREGAMENTO DA PÁGINA
$page = $_GET['page'] ?? 'home';

// O Roteador agora só chama o método do controller,
// que por sua vez vai carregar o arquivo de view completo.
switch ($page) {
    case 'home':
        $homeController->showHomePage();
        break;
    case 'editar':
        $homeController->showEditarPage();
        break;
    case 'emprestimo':
        $emprestimoController->showEmprestimoPage();
        break;
    case 'registrar_emprestimo':
        $emprestimoController->showRegistrarForm();
        break;
    default:
        http_response_code(404);
        // Você pode criar uma view de 404 ou apenas mostrar uma mensagem
        echo "<h1>Erro 404: Página não encontrada.</h1><a href='default.php'>Voltar</a>";
        break;
}
?>