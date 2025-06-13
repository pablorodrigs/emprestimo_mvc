<?php
// app/model/HistoricoModel.php

class HistoricoModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Busca o histórico completo de empréstimos finalizados.
     * @return array
     */
    public function buscarHistoricoCompleto() {
        try {
            // Esta é a mesma função que estava no seu historico.php
            $sql = "
                SELECT 
                    m.marca, m.modelo, m.armazenamento, m.memoria, 
                    e.data_emprestimo AS inicio, 
                    e.data_devolucao_efetiva AS fim, 
                    e.status,
                    e.nome_completo AS usuario,
                    e.cpf,
                    e.condicao_devolucao
                FROM 
                    emprestimos e
                JOIN 
                    maquinas m ON e.id_maquina = m.id
                WHERE
                    e.status = 'FINALIZADO'
                ORDER BY 
                    e.data_devolucao_efetiva DESC
            ";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erro ao buscar o histórico: " . $e->getMessage());
            return [];
        }
    }
}
?>