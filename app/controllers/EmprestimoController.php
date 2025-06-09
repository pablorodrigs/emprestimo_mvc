<?php
class EmprestimoController {
    private $emprestimoModel;
    private $homeModel;

    // Ele recebe os dois models para poder trabalhar com máquinas e empréstimos
    public function __construct(EmprestimoModel $emprestimoModel, HomeModel $homeModel) {
        $this->emprestimoModel = $emprestimoModel;
        $this->homeModel = $homeModel;
    }

    /**
     * Processa o registro de um novo empréstimo.
     */
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_emprestimo'])) {
            $data = [
                'maquina_id' => $_POST['maquina_id'],
                'nome_pessoa' => $_POST['nome_pessoa'] ?? '',
                'data_devolucao_prevista' => $_POST['data_devolucao_prevista'] ?? null,
                'observacoes' => $_POST['observacoes'] ?? ''
            ];

            if (!empty($data['maquina_id']) && !empty($data['nome_pessoa'])) {
                if ($this->emprestimoModel->createEmprestimo($data)) {
                    header("Location: index.php?page=emprestimo&status=loan_success");
                    exit();
                }
            }
            header("Location: index.php?page=registrar_emprestimo&id=" . $data['maquina_id'] . "&status=loan_error");
            exit();
        }
    }

    /**
     * Prepara os dados e exibe a página com a lista de máquinas disponíveis.
     */
    public function showEmprestimoPage() {
        $todasMaquinas = $this->homeModel->getAllMaquinas();
        $maquinasDisponiveis = array_filter($todasMaquinas, function($m) {
            return $m['status'] === 'DISPONÍVEL';
        });

        // Carrega a view passando a variável necessária
        require_once __DIR__ . '/../view/emprestimo.php';
    }

    /**
     * Prepara os dados e exibe o formulário para registrar um empréstimo.
     */
    public function showRegistrarForm() {
        $maquina_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $maquina = $maquina_id ? $this->homeModel->getMaquinaById($maquina_id) : null;

        if (!$maquina || $maquina['status'] !== 'DISPONÍVEL') {
            header("Location: index.php?page=emprestimo&status=not_available");
            exit();
        }

        // Carrega a view passando a variável necessária
        require_once __DIR__ . '/../view/registrar_emprestimo.php';
    }
}
?>