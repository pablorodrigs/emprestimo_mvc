<?php
// app/model/EmprestarModel.php

class EmprestarModel // <-- NOME DA CLASSE ALTERADO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function buscarMaquinaPorId($id_maquina)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM maquinas WHERE id = :id");
        $stmt->bindValue(':id', $id_maquina);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvar(array $dados)
    {
        $this->pdo->beginTransaction();
        try {
            $sql_emprestimo = "INSERT INTO emprestimos 
                (id_maquina, nome_completo, email, cpf, data_emprestimo, data_devolucao_prevista, observacao) 
                VALUES (:id_maquina, :nome_completo, :email, :cpf, :data_emprestimo, :data_devolucao_prevista, :observacao)";
            
            $stmt_emprestimo = $this->pdo->prepare($sql_emprestimo);
            $stmt_emprestimo->execute([
                ':id_maquina' => $dados['id_maquina'],
                ':nome_completo' => $dados['nome_completo'],
                ':email' => $dados['email'],
                ':cpf' => $dados['cpf'],
                ':data_emprestimo' => $dados['data_emprestimo'],
                ':data_devolucao_prevista' => $dados['data_devolucao_prevista'],
                ':observacao' => $dados['observacao']
            ]);

            $sql_maquina = "UPDATE maquinas SET status = 'EMPRESTADO' WHERE id = :id_maquina";
            $stmt_maquina = $this->pdo->prepare($sql_maquina);
            $stmt_maquina->bindValue(':id_maquina', $dados['id_maquina']);
            $stmt_maquina->execute();

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erro ao salvar empréstimo: " . $e->getMessage());
            return false;
        }
    }
}
?>