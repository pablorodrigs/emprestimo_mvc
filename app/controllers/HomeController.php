<?php
class HomeController {
    private $model;

    public function __construct(HomeModel $model) {
        $this->model = $model;
    }

    /**
     * Processa a requisição de cadastro de máquina.
     */
    public function cadastrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar'])) {
            // Coleta os dados do formulário
            $data = [
                'marca' => $_POST['marca'] ?? '',
                'modelo' => $_POST['modelo'] ?? '',
                'memoria' => $_POST['memoria'] ?? '',
                'armazenamento' => $_POST['armazenamento'] ?? '',
                'patrimonio' => $_POST['patrimonio'] ?? '',
                'servicetag' => $_POST['servicetag'] ?? '',
                'tipo' => $_POST['tipo'] ?? 'Desktop',
                'status' => $_POST['status'] ?? 'DISPONÍVEL'
            ];

            // Validação simples (pode ser aprimorada)
            if (!empty($data['marca']) && !empty($data['modelo'])) {
                if ($this->model->addMaquina($data)) {
                    header("Location: home.php?status=success");
                    exit();
                } else {
                    header("Location: home.php?status=error");
                    exit();
                }
            }
        }
    }
    
    /**
     * Processa a requisição para deletar uma máquina.
     */
    public function deletar() {
        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
            if ($id) {
                if($this->model->deleteMaquina($id)) {
                    header("Location: home.php?status=deleted");
                    exit();
                } else {
                    header("Location: home.php?status=delete_error");
                    exit();
                }
            }
        }
    }

    /**
     * Retorna a lista de todas as máquinas.
     * @return array
     */
    public function listarMaquinas() {
        return $this->model->getAllMaquinas();
    }

    
    /**
     * Processa a requisição de atualização de máquina. (Editar.php)
     */
    public function atualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
            // Coleta os dados do formulário de edição
            $data = [
                'id' => $_POST['id'],
                'marca' => $_POST['marca'] ?? '',
                'modelo' => $_POST['modelo'] ?? '',
                'memoria' => $_POST['memoria'] ?? '',
                'armazenamento' => $_POST['armazenamento'] ?? '',
                'patrimonio' => $_POST['patrimonio'] ?? '',
                'servicetag' => $_POST['servicetag'] ?? '',
                'tipo' => $_POST['tipo'] ?? 'Desktop',
                'status' => $_POST['status'] ?? 'DISPONÍVEL'
            ];

            if (!empty($data['id'])) {
                if ($this->model->updateMaquina($data)) {
                    // Redireciona para a página inicial com mensagem de sucesso
                    header("Location: home.php?status=updated");
                    exit();
                }
            }
            // Se falhar, redireciona de volta para a página de edição com erro
            header("Location: editar.php?id=" . $data['id'] . "&status=error");
            exit();
        }
    }
}
?>