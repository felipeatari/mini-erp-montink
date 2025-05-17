<?php
use App\Components\Session;

$carrinho = Session::all();

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
            $desconto = 0;
        ?>
        <!-- Produto -->
        <?php foreach($carrinhoProduto as $item): ?>
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
            }
            elseif ($subTotal > 200) {
                $frete = 0;
            }
            else {
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
                <?php } else { ?>
                    Frete Grátis
                <?php } ?>
            </td>
        </tr>
        <!-- End Frete -->

        <!-- Desconto -->
        <?php $total = $subTotal + $frete; ?>

        <tr class="fw-semibold">
            <td colspan="4">
                Desconto
            </td>
            <td>R$ <?= $desconto ?></td>
        </tr>
        <!-- End Desconto -->

        <!-- Total -->
        <?php $total -= $desconto; ?>
        <tr class="fw-semibold">
            <td colspan="4">
                Total
            </td>
            <td>R$ <?= $total ?></td>
        </tr>
        <!-- End Total -->
    </tbody>
</table>