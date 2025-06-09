<?php
class HomeModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Busca todas as máquinas cadastradas no banco de dados.
     * @return array
     */
    public function getAllMaquinas() {
        $sql = "SELECT * FROM maquinas ORDER BY id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Adiciona uma nova máquina ao banco de dados.
     * @param array $data - Dados da máquina.
     * @return bool
     */
    public function addMaquina($data) {
        $sql = "INSERT INTO maquinas (marca, modelo, memoria, armazenamento, patrimonio, servicetag, tipo, status) 
                VALUES (:marca, :modelo, :memoria, :armazenamento, :patrimonio, :servicetag, :tipo, :status)";
        
        $stmt = $this->pdo->prepare($sql);

        // Bind dos parâmetros para evitar SQL Injection
        $stmt->bindParam(':marca', $data['marca']);
        $stmt->bindParam(':modelo', $data['modelo']);
        $stmt->bindParam(':memoria', $data['memoria']);
        $stmt->bindParam(':armazenamento', $data['armazenamento']);
        $stmt->bindParam(':patrimonio', $data['patrimonio']);
        $stmt->bindParam(':servicetag', $data['servicetag']);
        $stmt->bindParam(':tipo', $data['tipo']);
        $stmt->bindParam(':status', $data['status']);

        return $stmt->execute();
    }
    
    /**
     * Deleta uma máquina pelo seu ID.
     * @param int $id
     * @return bool
     */
    public function deleteMaquina($id) {
        $sql = "DELETE FROM maquinas WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    
    /**
     * Busca uma única máquina pelo seu ID.
     * @param int $id
     * @return array|false
     */
    public function getMaquinaById($id) {
        $sql = "SELECT * FROM maquinas WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // fetch() para um único resultado
    }

   
    /**
     * Atualiza os dados de uma máquina no banco de dados.
     * @param array $data - Dados da máquina, incluindo o id.
     * @return bool
     */
    public function updateMaquina($data) {
        $sql = "UPDATE maquinas SET 
                    marca = :marca, 
                    modelo = :modelo, 
                    memoria = :memoria, 
                    armazenamento = :armazenamento, 
                    patrimonio = :patrimonio, 
                    servicetag = :servicetag, 
                    tipo = :tipo, 
                    status = :status 
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);

        // Bind dos parâmetros
        $stmt->bindParam(':marca', $data['marca']);
        $stmt->bindParam(':modelo', $data['modelo']);
        $stmt->bindParam(':memoria', $data['memoria']);
        $stmt->bindParam(':armazenamento', $data['armazenamento']);
        $stmt->bindParam(':patrimonio', $data['patrimonio']);
        $stmt->bindParam(':servicetag', $data['servicetag']);
        $stmt->bindParam(':tipo', $data['tipo']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>