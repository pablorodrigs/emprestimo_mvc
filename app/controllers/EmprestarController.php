<?php
// app/controllers/EmprestarController.php

require_once __DIR__ . '/../../lib/Database.php';
require_once __DIR__ . '/../model/EmprestarModel.php';

class EmprestarController // <-- NOME DA CLASSE ALTERADO
{
    public function novo()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ../../index.php?error=acesso_negado');
            exit;
        }

        $id_maquina = $_GET['id_maquina'] ?? 0;

        $model = new EmprestarModel();
        $maquina = $model->buscarMaquinaPorId($id_maquina);

        if (!$maquina || $maquina['status'] !== 'DISPONÍVEL') {
            header('Location: default.php?pagina=maquina&metodo=listar&error=maquina_invalida');
            exit;
        }

        require_once __DIR__ . '/../view/realizar_emprestimo.php';
    }

    public function salvar()
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ../../index.php?error=acesso_negado');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = [
                'id_maquina' => $_POST['id_maquina'],
                'nome_completo' => $_POST['nome_completo'],
                'email' => $_POST['email'],
                'cpf' => $_POST['cpf'],
                'data_emprestimo' => $_POST['data_emprestimo'],
                'data_devolucao_prevista' => $_POST['data_devolucao_prevista'],
                'observacao' => $_POST['observacao']
            ];

            $model = new EmprestarModel();
            $sucesso = $model->salvar($dados);

            if ($sucesso) {
                header('Location: default.php?pagina=maquina&metodo=listar&status=emprestimo_sucesso');
                exit;
            } else {
                header('Location: default.php?pagina=emprestar&metodo=novo&id_maquina=' . $dados['id_maquina'] . '&error=salvar_falhou'); // <-- ATUALIZADO
                exit;
            }
        }
    }
}
?>