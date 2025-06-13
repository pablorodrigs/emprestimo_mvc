<?php
// app/model/DevolverModel.php

class DevolverModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Busca os dados de um empréstimo ativo para exibir no formulário de devolução.
     * @param int $id_emprestimo
     * @return array|false
     */
    public function getEmprestimoAtivo($id_emprestimo) {
        $sql = "SELECT e.*, m.marca, m.modelo, m.servicetag, m.patrimonio, m.tipo
                FROM emprestimos e
                JOIN maquinas m ON e.id_maquina = m.id
                WHERE e.id = ? AND e.status = 'ATIVO'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_emprestimo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Processa a devolução: autentica o admin, atualiza as tabelas e usa uma transação.
     * @param array $dados Os dados do formulário de devolução.
     * @return bool True em caso de sucesso.
     * @throws Exception Em caso de falha.
     */
    public function processarDevolucao(array $dados) {
        // 1. Autenticar o usuário que está recebendo
        $stmt_admin = $this->pdo->prepare("SELECT id, senha FROM login WHERE usuario = ?");
        $stmt_admin->execute([$dados['usuario_admin']]);
        $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($dados['senha_admin'], $admin['senha'])) {
            throw new Exception("Usuário ou senha do recebedor inválidos.");
        }
        $id_usuario_devolucao = $admin['id'];

        // 2. Iniciar a transação
        $this->pdo->beginTransaction();
        
        try {
            // 3. Pegar o id_maquina
            $stmt_get_maquina = $this->pdo->prepare("SELECT id_maquina FROM emprestimos WHERE id = ?");
            $stmt_get_maquina->execute([$dados['id_emprestimo']]);
            $id_maquina = $stmt_get_maquina->fetchColumn();

            if (!$id_maquina) {
                throw new Exception("Empréstimo não encontrado na transação.");
            }

            // 4. Atualizar o registro do empréstimo
            $sql_emprestimo = "UPDATE emprestimos SET 
                                status = 'FINALIZADO', 
                                data_devolucao_efetiva = CURRENT_DATE,
                                id_usuario_devolucao = ?,
                                condicao_devolucao = ?
                              WHERE id = ?";
            $stmt_emprestimo = $this->pdo->prepare($sql_emprestimo);
            $stmt_emprestimo->execute([$id_usuario_devolucao, $dados['condicao'], $dados['id_emprestimo']]);

            // 5. Atualizar o status da máquina
            $novo_status_maquina = ($dados['condicao'] === 'COM DEFEITO') ? 'EM MANUTENÇÃO' : 'DISPONÍVEL';
            $sql_maquina = "UPDATE maquinas SET status = ? WHERE id = ?";
            $stmt_maquina = $this->pdo->prepare($sql_maquina);
            $stmt_maquina->execute([$novo_status_maquina, $id_maquina]);
            
            // Se tudo deu certo, confirma
            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            // Se algo deu errado, desfaz
            $this->pdo->rollBack();
            // Re-lança a exceção para que a página principal possa tratá-la
            throw $e;
        }
    }
}
?>