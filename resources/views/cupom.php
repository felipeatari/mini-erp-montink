<div>
    <h1 class="h1 my-4">Gerenciar Cupons</h1>
    <form action="<?= CUPOM ?>/criar" method="POST">
        <div class="row mb-3">
            <div class="col">
                <label for="codigo" class="form-label">Código</label>
                <input type="text" name="codigo" id="codigo" class="form-control">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="desconto" class="form-label">Desconto (%)</label>
                <input type="number" name="desconto" id="desconto" class="form-control">
            </div>
            <div class="col">
                <label for="validade" class="form-label">Validade</label>
                <input type="date" name="validade" id="validade" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col d-flex flex-column justify-content-end">
                <button class="btn btn-primary">Criar</button>
            </div>
        </div>
    </form>

    <div class="mt-4">
        <?php foreach ($cupons as $cupom): ?>
            <form action="<?= CUPOM ?>/apagar/<?= $cupom['id'] ?>" method="POST" class="my-4 card card-body">
                <div class="row">
                    <input type="hidden" name="id" value="<?= $cupom['id'] ?>">
                    <div class="col">
                        <label for="codigo" class="form-label">Código</label>
                        <input readonly type="text" name="codigo" id="codigo" value="<?= $cupom['codigo'] ?>" class="form-control">
                    </div>
                    <div class="col">
                        <label for="desconto" class="form-label">Desconto (%)</label>
                        <input readonly type="number" name="desconto" id="desconto" value="<?= $cupom['desconto'] ?>" class="form-control">
                    </div>
                    <div class="col">
                        <label for="validade" class="form-label">Validade</label>
                        <input readonly type="date" name="validade" id="validade" value="<?= $cupom['validade'] ?>" class="form-control">
                    </div>
                    <div class="col w-100 d-flex flex-column justify-content-end">
                        <div class="d-flex">
                            <button class="w-100 btn btn-danger" style="margin-right: 5px;" name="apagar_cupom">Remover</button>
                        </div>
                    </div>
                </div>
            </form>
        <?php endforeach ?>
    </div>
</div>