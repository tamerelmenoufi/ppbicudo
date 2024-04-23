<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

    $query = "select * from origens where codigo = '{$_SESSION['buscaOrigem']}'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);


    if($_SESSION['buscaOrigem'] and $_SESSION['buscaDataInicial'] and $_SESSION['buscaDataFinal']){
      // $cpf = str_replace( '.', '', str_replace('-', '', $_SESSION['usuarioBusca']));
      $where = " and origem = '{$_SESSION['buscaOrigem']}' and dataCriacao between '{$_SESSION['buscaDataInicial']} 00:00:00' and '{$_SESSION['buscaDataFinal']} 23:59:59' ";
    }
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="img/icone.png">
    <title>PPBICUDO - Painel de Controle</title>
    <?php
    include("lib/header.php");
    ?>

<style>
    td, th{
    font-size:12px;
  }

  thead {
        display: table-header-group;
    }
    tfoot {
        display: table-footer-group;
    }
    @media print {
        thead {
            display: table-header-group !important;
        }
        tfoot {
            display: table-footer-group !important;
        }
    }
</style>

  </head>
  <body translate="no">

    <?php
    if(!$where){
    ?>
    <div class="d-flex justify-content-center align-items-center" style="color:#a1a1a1; position:fixed; left:0; right:0; top:0; bottom:0">
        <h1>RELATÓRIO NÃO DEFINIDO</h1>
    </div>
    <?php
    exit();
    }
    ?>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
              <th colspan = "11" >
                <div class="row g-0">
                  <div class="col">
                    <div class="d-flex justify-content-start align-items-center">
                      <h4>
                        <i class="fa-solid fa-arrow-down"></i> <?=dataBr($_SESSION['buscaDataInicial'])?><br>
                        <i class="fa-solid fa-arrow-up"></i> <?=dataBr($_SESSION['buscaDataFinal'])?>
                      </h4>
                    </div>
                  </div>
                  <div class="col">
                    <div class="d-flex justify-content-center align-items-center">
                      <img src="img/logo.png?1" height="60" alt="">
                    </div>
                  </div>
                  <div class="col">
                    <div class="d-flex justify-content-end align-items-center">
                      <img src="<?=$urlPainel?>src/volume/origens/<?=$d->imagem?>" height="60" alt="">
                    </div>
                  </div>
                </div>
              </th>
            </tr>
            <tr>
            <th scope="col">Data</th>
            <th scope="col">Anúncios</th>
            <th scope="col">Pagamento Produto</th>
            <th scope="col">Pagamento Frete</th>
            <th scope="col">Custo Produto</th>
            <th scope="col">Custo Frete</th>
            <th scope="col">Comissão</th> 
            <th scope="col">Lucro</th>
            <th scope="col">Frete</th>
            <th scope="col">Porcentagem</th>
            <th scope="col">Código do Produto</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "select * from relatorio where 1 {$where} order by dataCriacao desc";
            $result = mysqli_query($con,$query);
            
            while($d = mysqli_fetch_object($result)){
            ?>
            <tr>
            <td class="text-nowrap"><?=dataBr($d->dataCriacao)?></td>
            <td class=""><?=$d->tituloItem?></td>
            <td class="text-nowrap">R$<?=number_format($d->ValorPedidoXquantidade,2,',','.')?></td>
            <td class="text-nowrap">R$<?=number_format($d->CustoEnvio,2,',','.')?></td>
            <td class="text-nowrap">R$<?=number_format($d->PrecoCusto,2,',','.')?></td>
            <td class="text-nowrap">R$<?=number_format($d->CustoEnvioSeller,2,',','.')?></td>
            <td class="text-nowrap">R$<?=number_format(($d->TarifaGatwayPagamento + $d->TarifaMarketplace),2,',','.')?></td>
            <td class="text-nowrap">R$<?=number_format(($d->ValorPedidoXquantidade - $d->PrecoCusto - $d->CustoEnvioSeller - $d->TarifaGatwayPagamento - $d->TarifaMarketplace),2,',','.')?></td>
            <td class="text-nowrap"><?=$d->frete?></td>
            <td class="text-nowrap"><?=number_format($d->Porcentagem,2,',','.')?>%</td>
            <td class="text-nowrap"><?=$d->codigoPedido?></td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>




    <?php
    include("lib/footer.php");
    ?>

    <script>
 

    </script>

  </body>
</html>