<?php
// app/model/MaquinaModel.php

class MaquinaModel {
    private $pdo;

    public function __construct() {
        // A conexão com o banco é feita aqui
        $this->pdo = Database::getConnection();
    }

    /**
     * Busca todas as máquinas e o status do empréstimo ativo.
     * @return array
     */
    public function listarMaquinasComStatus() {
        try {
            // Esta é a mesma função que estava no seu emprestimo.php
            $sql = "
                SELECT 
                    m.id, m.marca, m.modelo, m.memoria, m.armazenamento, m.patrimonio, m.servicetag, m.tipo, m.status, 
                    e.id AS emprestimo_id 
                FROM 
                    maquinas m
                LEFT JOIN 
                    emprestimos e ON m.id = e.id_maquina AND e.status = 'ATIVO'
                ORDER BY 
                    m.id ASC
            ";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erro ao buscar máquinas: " . $e->getMessage());
            return [];
        }
    }
}
?>