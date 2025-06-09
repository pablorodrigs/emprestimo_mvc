<?php
class EmprestimoModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Registra um novo empréstimo e atualiza o status da máquina (usando transação).
     * @param array $data
     * @return bool
     */
    public function createEmprestimo($data) {
        $sqlInsert = "INSERT INTO emprestimos (maquina_id, nome_pessoa, data_devolucao_prevista, observacoes) 
                      VALUES (:maquina_id, :nome_pessoa, :data_devolucao_prevista, :observacoes)";
        
        $sqlUpdate = "UPDATE maquinas SET status = 'EMPRESTADO' WHERE id = :maquina_id";

        try {
            $this->pdo->beginTransaction();

            $stmtInsert = $this->pdo->prepare($sqlInsert);
            $stmtInsert->bindParam(':maquina_id', $data['maquina_id'], PDO::PARAM_INT);
            $stmtInsert->bindParam(':nome_pessoa', $data['nome_pessoa']);
            $stmtInsert->bindParam(':data_devolucao_prevista', $data['data_devolucao_prevista']);
            $stmtInsert->bindParam(':observacoes', $data['observacoes']);
            $stmtInsert->execute();

            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':maquina_id', $data['maquina_id'], PDO::PARAM_INT);
            $stmtUpdate->execute();

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Busca todos os empréstimos, juntando com dados da máquina.
     * @return array
     */
    public function getAllEmprestimos() {
        // O JOIN é usado para pegar informações da máquina (marca, modelo) junto com o empréstimo
        $sql = "SELECT 
                    e.*, 
                    m.marca, 
                    m.modelo, 
                    m.patrimonio 
                FROM 
                    emprestimos e
                LEFT JOIN 
                    maquinas m ON e.maquina_id = m.id
                ORDER BY 
                    e.data_emprestimo DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>