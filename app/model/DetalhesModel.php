<?php
// app/model/DetalhesModel.php

class DetalhesModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Busca os dados principais de uma máquina específica pelo ID.
     * @param int $id_maquina
     * @return array|false
     */
    public function getMaquinaById($id_maquina) {
        $stmt = $this->pdo->prepare("SELECT * FROM maquinas WHERE id = ?");
        $stmt->execute([$id_maquina]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca o histórico completo de empréstimos e devoluções para uma máquina.
     * @param int $id_maquina
     * @return array
     */
    public function getHistoricoByMaquinaId($id_maquina) {
        $sql = "
            SELECT 
                e.nome_completo AS pessoa_que_pegou,
                e.data_emprestimo,
                e.data_devolucao_efetiva,
                e.condicao_devolucao,
                admin_emprestou.usuario AS quem_emprestou,
                admin_devolveu.usuario AS quem_recebeu
            FROM 
                emprestimos e
            LEFT JOIN 
                login admin_emprestou ON e.id_usuario_emprestimo = admin_emprestou.id
            LEFT JOIN 
                login admin_devolveu ON e.id_usuario_devolucao = admin_devolveu.id
            WHERE 
                e.id_maquina = ?
            ORDER BY 
                e.data_emprestimo DESC, e.data_devolucao_efetiva DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_maquina]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>