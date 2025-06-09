<div class="container">
    <div class="card">
        <h2 class="card-title">Registrar Empréstimo</h2>
        <p>
            <strong>Máquina:</strong> <?= htmlspecialchars($maquina['marca'] . ' ' . $maquina['modelo']) ?> <br>
            <strong>Patrimônio:</strong> <?= htmlspecialchars($maquina['patrimonio']) ?>
        </p>
        <hr>
        <form action="index.php?action=registrar_emprestimo" method="POST">
            <input type="hidden" name="maquina_id" value="<?= htmlspecialchars($maquina['id']) ?>">
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="nome_pessoa">Nome da Pessoa que está Retirando</label>
                <input type="text" id="nome_pessoa" name="nome_pessoa" class="form-control" required>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="data_devolucao_prevista">Data de Devolução Prevista</label>
                <input type="date" id="data_devolucao_prevista" name="data_devolucao_prevista" class="form-control" required>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="observacoes">Observações (Opcional)</label>
                <textarea id="observacoes" name="observacoes" class="form-control" rows="3"></textarea>
            </div>
            
            <button type="submit" name="registrar_emprestimo" class="btn btn-success">Confirmar Empréstimo</button>
            <a href="index.php?page=emprestimo" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>