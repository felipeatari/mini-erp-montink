<div>
    <h1 class="h1 my-4">Gerenciar Produto</h1>
    <form action="<?= PRODUTO ?>/criar" method="POST">
        <div class="row">
            <div class="col">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" name="nome" id="nome" class="form-control">
            </div>
            <div class="col">
                <label for="preco" class="form-label">Preço</label>
                <input type="number" name="preco" id="preco" class="form-control">
            </div>
            <div class="col">
                <label for="variacao_1" class="form-label">Variação 1</label>
                <input type="text" name="variacao_1" id="variacao_1" class="form-control">
            </div>
            <div class="col">
                <label for="variacao_2" class="form-label">Variação 2</label>
                <input type="text" name="variacao_2" id="variacao_2" class="form-control">
            </div>
            <div class="col">
                <label for="estoque_quantidade" class="form-label">Estoque</label>
                <input type="number" name="estoque_quantidade" id="estoque_quantidade" class="form-control">
            </div>
            <div class="col d-flex flex-column justify-content-end">
                <button class="btn btn-primary">Criar</button>
            </div>
        </div>
    </form>

    <?php foreach ($produtos as $produto): ?>
        <form action="<?= PRODUTO ?>/editar/<?= $produto['id'] ?>" method="POST" class="my-4">
            <div class="row">
                <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                <input type="hidden" name="estoque_id" value="<?= $produto['estoque_id'] ?>">
                <div class="col">
                    <label for="nome" class="form-label">Nome</label>
                    <input required type="text" name="nome" id="nome" value="<?= $produto['nome'] ?>" class="form-control">
                </div>
                <div class="col">
                    <label for="preco" class="form-label">Preço</label>
                    <input required type="number" name="preco" id="preco" value="<?= $produto['preco'] ?>" class="form-control">
                </div>
                <div class="col">
                    <label for="variacao_1" class="form-label">Variação 1</label>
                    <input required type="text" name="variacao_1" id="variacao_1" value="<?= $produto['variacao_1'] ?>" class="form-control">
                </div>
                <div class="col">
                    <label for="variacao_2" class="form-label">Variação 2</label>
                    <input required type="text" name="variacao_2" id="variacao_2" value="<?= $produto['variacao_2'] ?>" class="form-control">
                </div>
                <div class="col">
                    <label for="estoque_quantidade" class="form-label">Estoque</label>
                    <input required type="number" name="estoque_quantidade" id="estoque_quantidade" value="<?= $produto['estoque_quantidade'] ?>" class="form-control">
                </div>
                <div class="col w-100 d-flex flex-column justify-content-end">
                    <div class="d-flex">
                        <button class="w-100 btn btn-warning" style="margin-right: 5px;" name="editar_produto">Editar</button>
                        <button class="w-100 btn btn-primary" name="adicionar_carrinho">Comprar</button>
                    </div>
                </div>
            </div>
        </form>
    <?php endforeach ?>
</div>