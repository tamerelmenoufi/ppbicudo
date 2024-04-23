<?php

    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");


    // echo $query = "update produtos set 
    //                                 valor = '3.44'
    //                         where categoria = 2
    // ";
    // mysqli_query($con,$query);
    
?>
<style>

</style>
<div class="m-3">
    
    <div class="row g-0">
        <div class="col-md-12 p-2">
            <h6>Resumo Geral</h6>
        </div>
        <div class="col-md-4 p-2">
            <div class="alert alert-secondary" role="alert">
                <span>Planilhas Importadas</span>
                <h1>136</h1>
            </div>
        </div>
        <div class="col-md-4 p-2">
            <div class="alert alert-primary" role="alert">
                <span>Total de Vendas</span>
                <h1>2693</h1>
            </div>
        </div>
        <div class="col-md-4 p-2">
            <div class="alert alert-success" role="alert">
                <span>Total Arrecadado</span>
                <h1>R$ 126.851,97</h1>
            </div>
        </div>
    </div>


    <div class="row g-0">
        <div class="col-md-12 p-2">
            <h6>Resumo Financeiro Geral</h6>
        </div>
        <div class="col-md-2 p-2">
            <div class="alert alert-secondary" role="alert">
                <span>Pagamento Produto</span>
                <h3>136</h3>
            </div>
        </div>
        <div class="col-md-2 p-2">
            <div class="alert alert-secondary" role="alert">
                <span>Pagamento Frete</span>
                <h3>2693</h3>
            </div>
        </div>
        <div class="col-md-2 p-2">
            <div class="alert alert-warning" role="alert">
                <span>Custo Produto</span>
                <h3>R$ 126.851,97</h3>
            </div>
        </div>
        <div class="col-md-2 p-2">
            <div class="alert alert-secondary" role="alert">
                <span>Custo Frete</span>
                <h3>136</h3>
            </div>
        </div>
        <div class="col-md-2 p-2">
            <div class="alert alert-primary" role="alert">
                <span>Comissão</span>
                <h3>2693</h3>
            </div>
        </div>
        <div class="col-md-2 p-2">
            <div class="alert alert-success" role="alert">
                <span>Lucro</span>
                <h3>R$ 126.851,97</h3>
            </div>
        </div>
    </div>

    <div class="row g-0">
        <div class="col-md-4 p-2">
            <h6>Importação por Origem</h6>
        </div>
        <div class="col-md-8 p-2">
            <h6>Arrecadação por Origem</h6>
        </div>
        <div class="col-md-4 p-2">
            <table class="table table-hover">
                <?php
                $q = "select a.*, (select count(*) from relatorio where origem = a.codigo ) as qt from origens a order by a.nome";
                $r = mysqli_query($con, $q);
                while($s = mysqli_fetch_object($r)){
                ?>
                <tr>
                    <td><?=$s->nome?></td>
                    <td><?=$s->qt?></td>
                </tr>                
                <?php
                }
                ?>
            </table>
        </div>
        <div class="col-md-8 p-2">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Origem</th>
                        <th>Pagamento Produto</th>
                        <th>Pagamento Frete</th>
                        <th>Custo Produto</th>
                        <th>Custo Frete</th>
                        <th>Comissão</th>
                        <th>Lucro</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $q = "select 
                            a.*,
                            (select sum(ValorPedidoXquantidade) from relatorio where origem = a.codigo) as pagamento_produto,   
                            (select sum(CustoEnvio) from relatorio where origem = a.codigo) as pagamento_frete,   
                            (select sum(PrecoCusto) from relatorio where origem = a.codigo) as custo_produto,   
                            (select sum(CustoEnvioSeller) from relatorio where origem = a.codigo) as custo_frete,   
                            (select sum(TarifaGatwayPagamento + TarifaMarketplace) from relatorio where origem = a.codigo) as comissão,   
                            (select sum(ValorPedidoXquantidade - PrecoCusto - CustoEnvioSeller - TarifaGatwayPagamento - TarifaMarketplace) from relatorio where origem = a.codigo) as lucro   
                        from origens a order by a.nome";
                $r = mysqli_query($con, $q);
                while($s = mysqli_fetch_object($r)){
                ?>
                <tr>
                    <td><?=$s->nome?></td>
                    <td><?=$s->pagamento_produto?></td>
                    <td><?=$s->pagamento_frete?></td>
                    <td><?=$s->custo_produto?></td>
                    <td><?=$s->custo_frete?></td>
                    <td><?=$s->comissão?></td>
                    <td><?=$s->lucro?></td>
                </tr>                
                <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>



    <?php
    /*
    ?>

    <div class="row g-0">
        <div class="col-md-12 p-2">
            <h6>Arrecadação Mensal</h6>
        </div>
        <?php
        for($i=0;$i<12;$i++){
        ?>
        <div class="col-md-1 p-2">
            <div class="alert alert-light" style="border:solid 1px #a1a1a1" role="alert">
                <span style="color:#a1a1a1; font-size:12px;">Mês <?=$i+1?></span>
                <div style="font-size:13; font-weight:bold">R$ 154.999,64</div>
            </div>
        </div>
        <?php
        }
        ?>
    </div>
    
    <div class="row g-0">
        <div class="col-md-12 p-2">
            <h6>Lucro Mensal</h6>
        </div>
        <?php
        for($i=0;$i<12;$i++){
        ?>
        <div class="col-md-1 p-2">
            <div class="alert alert-success" role="alert">
                <span style="color:#a1a1a1; font-size:12px;">Mês <?=$i+1?></span>
                <div style="font-size:13; font-weight:bold">R$ 154.999,64</div>
            </div>
        </div>
        <?php
        }
        ?>
    </div>
    <?php
    //*/
    ?>
</div>


<script>
    $(function(){
        Carregando('none')
        
    })
</script>