<?php

use App\Components\Session;

$carrinho = Session::all();

$codigoCupomErro = $carrinho['codigo_cupom_erro'] ?? null;
$cupom = $carrinho['cupom'] ?? [];
$carrinhoProduto = $carrinho['carrinhoProduto'] ?? [];
?>
<table class="table table-hover mt-4 text-center align-middle border" style="width: 750px !important;">
    <thead>
        <tr>
            <th scope="col">Produto</th>
            <th scope="col">Variação</th>
            <th scope="col">Qtd</th>
            <th scope="col">Preço Un</th>
            <th scope="col">Preço Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $subTotal = 0;
        $total = 0;
        $frete = 0;
        $desconto = $cupom['desconto'] ?? 0;
        ?>
        <!-- Produto -->
        <?php foreach ($carrinhoProduto as $item): ?>
            <tr>
                <td><?= $item['nome'] ?></td>
                <td><?= $item['variacoes'] ?></td>
                <td><?= $item['qtd'] ?></td>
                <td>R$ <?= $item['preco_un'] ?></td>
                <td>R$ <?= $item['preco_total'] ?></td>
            </tr>
            <?php $subTotal += $item['preco_total']; ?>
        <?php endforeach; ?>
        <!-- End Produto -->

        <!-- Cupom -->
        <tr>
            <td colspan="5">
                <form action="<?= CUPOM ?>/adicionar" method="POST" class="d-flex">
                    <input type="text" name="codigo" id="codigo" class="form-control" placeholder="Informe o Código do Cupom" style="margin-right: 10px;">
                    <button class="btn btn-primary">Adicionar</button>
                </form>
                <?php if ($codigoCupomErro) { ?>
                    <script>
                        alert('<?= $codigoCupomErro ?>')
                    </script>
                    <?php Session::unset('codigo_cupom_erro') ?>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                Código do Cupom: <?= isset($cupom['codigo']) ? '<span class="border px-2 py-1">' . $cupom['codigo'] . '</span>' : '' ?>
            </td>
            <td colspan="1">
                Validade: <?= isset($cupom['validade']) ? (new DateTime($cupom['validade']))->format('d/m/Y') : '' ?>
            </td>
            <td colspan="1">
                Desconto: <?= isset($cupom['desconto']) ? $cupom['desconto'] . '%' : '' ?>
            </td>
        </tr>
        <!-- End Cupom -->

        <!-- Subtotal -->
        <tr class="fw-semibold">
            <td colspan="4">SubTotal</td>
            <td>R$ <?= $subTotal ?></td>
        </tr>
        <!-- End Subtotal -->

        <!-- Frete -->
        <?php
        if ($subTotal >= 52 and $subTotal <= 166.59) {
            $frete = 15;
        } elseif ($subTotal > 200) {
            $frete = 0;
        } elseif ($subTotal == 0) {
            $frete = 0;
        } else {
            $frete = 20;
        }
        ?>
        <tr class="fw-semibold">
            <td colspan="4">
                Frete
            </td>
            <td>
                <?php if ($frete > 0) { ?>
                    R$ <?= $frete ?>
                <?php } elseif ($frete == 0 and $subTotal == 0) { ?>
                    R$ <?= $frete ?>
                <?php } else { ?>
                    Frete Grátis
                <?php } ?>
            </td>
        </tr>
        <!-- End Frete -->

        <?php $total = $subTotal + $frete; ?>

        <!-- Desconto -->
        <?php
        $descontoValor = 0;

        if ($desconto) {
            $descontoValor = ($total * $desconto) / 100;
        }
        ?>
        <tr class="fw-semibold">
            <td colspan="4">
                Desconto
            </td>
            <td>R$ <?= $descontoValor ?></td>
        </tr>
        <!-- End Desconto -->

        <!-- Total -->
        <?php
        if ($descontoValor) {
            $total -= $descontoValor;
        }
        ?>
        <tr class="fw-semibold">
            <td colspan="4">
                Total
            </td>
            <td>R$ <?= $total ?></td>
        </tr>
        <!-- End Total -->
    </tbody>
</table>

<table class="table mt-4 text-center align-middle border" style="width: 750px !important;">
    <thead>
        <tr>
            <th colspan="4">
                <div class="d-flex justify-content-between">
                    <input type="text" name="cep" id="cep" placeholder="Informe seu CEP" class="form-control" style="margin-right: 10px;">
                    <button type="button" class="btn btn-primary" onclick="buscarEndereco()">Buscar</button>
                </div>
            </th>
        </tr>
        <tr>
            <th scope="col">Cidade</th>
            <th scope="col">Bairro</th>
            <th scope="col">Estado</th>
            <th scope="col">Endereço</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <span id="cidade">-</span>
                <input type="hidden" name="cidade" class="form-control">
            </td>
            <td>
                <span id="bairro">-</span>
                <input type="hidden" name="bairro" class="form-control">
            </td>
            <td>
                <span id="estado">-</span>
                <input type="hidden" name="estado" class="form-control">
            </td>
            <td>
                <span id="endereco">-</span>
                <input type="hidden" name="endereco" class="form-control">
            </td>
        </tr>
    </tbody>
</table>

<script>
    function buscarEndereco() {
        const cep = document.querySelector('#cep').value.replace(/\D/g, '')

        if (cep.length !== 8) {
            alert('CEP inválido. Deve conter 8 dígitos.')
            return
        }

        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    alert('Erro ao consultar o CEP.')
                    console.error('Erro na requisição:', data.erro)
                    return
                }

                document.querySelector('#cidade').innerHTML = data.localidade
                document.querySelector('input[name="cidade"]').value = data.localidade

                document.querySelector('#bairro').innerHTML = data.bairro
                document.querySelector('input[name="bairro"]').value = data.bairro

                document.querySelector('#estado').innerHTML = data.estado
                document.querySelector('input[name="estado"]').value = data.estado

                document.querySelector('#endereco').innerHTML = data.logradouro
                document.querySelector('input[name="endereco"]').value = data.logradouro
            })
            .catch(error => {
                alert('Erro ao consultar o CEP.')
                console.error('Erro na requisição:', error)
            })
    }
</script>