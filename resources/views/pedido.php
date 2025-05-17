<div>
    <?php foreach ($pedidos as $pedido): ?>
        <?php
            $status = strtolower($pedido['status']);
            $statusClasses = [
                'pendente' => 'bg-warning text-dark',
                'aprovado' => 'bg-success',
                'cancelado' => 'bg-danger'
            ];
            $badgeClass = $statusClasses[$status] ?? 'bg-secondary';
        ?>

        <div class="my-4 card card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Pedido #<?= $pedido['id'] ?></h5>
                <span class="badge <?= $badgeClass ?>"><?= ucfirst($status) ?></span>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Nome</label>
                    <input type="text" value="<?= $pedido['nome'] ?>" class="form-control" readonly>
                </div>
                <div class="col">
                    <label class="form-label">Email</label>
                    <input type="email" value="<?= $pedido['email'] ?>" class="form-control" readonly>
                </div>
                <div class="col">
                    <label class="form-label">Status</label>
                    <input type="text" value="<?= ucfirst($pedido['status']) ?>" class="form-control" readonly>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Endereço</label>
                    <input type="text" value="<?= $pedido['endereco'] ?>" class="form-control" readonly>
                </div>
                <div class="col">
                    <label class="form-label">Bairro</label>
                    <input type="text" value="<?= $pedido['bairro'] ?>" class="form-control" readonly>
                </div>
                <div class="col">
                    <label class="form-label">Cidade</label>
                    <input type="text" value="<?= $pedido['cidade'] ?>" class="form-control" readonly>
                </div>
                <div class="col">
                    <label class="form-label">Estado</label>
                    <input type="text" value="<?= $pedido['estado'] ?>" class="form-control" readonly>
                </div>
                <div class="col">
                    <label class="form-label">CEP</label>
                    <input type="text" value="<?= $pedido['cep'] ?>" class="form-control" readonly>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Total</label>
                    <input type="text" value="R$ <?= number_format($pedido['total'], 2, ',', '.') ?>" class="form-control" readonly>
                </div>
                <div class="col">
                    <label class="form-label">Frete</label>
                    <input type="text" value="R$ <?= number_format($pedido['frete'], 2, ',', '.') ?>" class="form-control" readonly>
                </div>
                <div class="col">
                    <label class="form-label">Desconto</label>
                    <input type="text" value="R$ <?= number_format($pedido['desconto'], 2, ',', '.') ?>" class="form-control" readonly>
                </div>
                <div class="col">
                    <label class="form-label">Cupom</label>
                    <input type="text" value="<?= $pedido['cupom'] ?>" class="form-control" readonly>
                </div>
            </div>

            <h5>Produtos do Pedido (<?= $pedido['qtd_produtos'] ?>)</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>Variações</th>
                            <th>Quantidade</th>
                            <th>Preço Unitário</th>
                            <th>Preço Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedido['produtos'] as $produto): ?>
                            <tr>
                                <td><?= $produto['nome'] ?></td>
                                <td><?= $produto['variacoes'] ?></td>
                                <td><?= $produto['qtd'] ?></td>
                                <td>R$ <?= number_format($produto['preco_un'], 2, ',', '.') ?></td>
                                <td>R$ <?= number_format($produto['preco_total'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach ?>
</div>
