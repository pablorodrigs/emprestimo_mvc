<div class="container">
    <div class="card">
        <h2 class="card-title">Máquinas Disponíveis para Empréstimo</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Patrimônio</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($maquinasDisponiveis)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">Nenhuma máquina disponível no momento.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($maquinasDisponiveis as $maquina): ?>
                    <tr>
                        <td><?= htmlspecialchars($maquina['marca']) ?></td>
                        <td><?= htmlspecialchars($maquina['modelo']) ?></td>
                        <td><?= htmlspecialchars($maquina['patrimonio']) ?></td>
                        <td><?= htmlspecialchars($maquina['tipo']) ?></td>
                        <td class="actions">
                            <a href="index.php?page=registrar_emprestimo&id=<?= $maquina['id'] ?>" class="btn btn-primary">Emprestar</a>
                            <a href="#" class="btn btn-secondary">Detalhes</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>