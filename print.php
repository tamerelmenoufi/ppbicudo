<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

    if($_SESSION['buscaOrigem'] and $_SESSION['buscaDataInicial'] and $_SESSION['buscaDataFinal']){

      $query = "select * from origens where codigo = '{$_SESSION['buscaOrigem']}'";
      $result = mysqli_query($con, $query);
      $d = mysqli_fetch_object($result);
      // $cpf = str_replace( '.', '', str_replace('-', '', $_SESSION['usuarioBusca']));
      $where = " and relatorio = '0' and origem = '{$_SESSION['buscaOrigem']}' and dataCriacao between '{$_SESSION['buscaDataInicial']} 00:00:00' and '{$_SESSION['buscaDataFinal']} 23:59:59' ";

    }else if($_SESSION['modelo_relatorio']){

      $q = "select * from relatorio_modelos where codigo = '{$_SESSION['modelo_relatorio']}'";
      $rel = mysqli_fetch_object(mysqli_query($con, $q));

      $registros = json_decode($rel->registros);
      $opcoes = $registros;
      $registros = implode(", ", $registros); 
      $where = " and codigo in ({$registros})";

      $query = "select * from origens where codigo = '{$rel->origem}'";
      $result = mysqli_query($con, $query);
      $d = mysqli_fetch_object($result);      

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
    .text-nowrap{
      white-space: nowrap;
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
        .page-break {
              page-break-before: always;
              break-before: page;
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
                        <?php
                        if($_SESSION['buscaOrigem']){
                        ?>
                        <i class="fa-solid fa-calendar-days text-secondary" style="font-size:50px; margin-right:10px;"></i>
                        <h5 class="text-secondary">
                          <i class="fa-solid fa-arrow-down"></i> <?=dataBr($_SESSION['buscaDataInicial'])?><br>
                          <i class="fa-solid fa-arrow-up"></i> <?=dataBr($_SESSION['buscaDataFinal'])?>
                        </h5>
                        <?php
                        }else{
                        ?>
                        <h5 class="text-secondary mt-3">
                          <i class="fa-solid fa-caret-right"></i> <?=$rel->nome?>
                        </h5>                        
                        <?php
                        }
                        ?>

                    </div>
                  </div>
                  <div class="col">
                    <div class="d-flex justify-content-center align-items-center">
                      <img src="img/logo.png?1" height="60" alt="">
                    </div>
                  </div>
                  <div class="col">
                    <div class="d-flex justify-content-end align-items-center">
                        <h5 class="text-secondary me-1">
                          <?=$d->nome?>
                        </h5>
                      <img src="<?=$urlPainel?>src/volume/origens/<?=$d->imagem?>" height="60" alt="">
                    </div>
                  </div>
                </div>
              </th>
            </tr>
            <tr>
            <th scope="col text-nowrap">Data</th>
            <th scope="col">Anúncios</th>
            <th scope="col text-nowrap">Pagamento Produto</th>
            <th scope="col text-nowrap">Pagamento Frete</th>
            <th scope="col text-nowrap">Custo Produto</th>
            <th scope="col text-nowrap">Custo Frete</th>
            <th scope="col text-nowrap">Comissão</th> 
            <th scope="col text-nowrap">Lucro</th>
            <th scope="col text-nowrap">Frete</th>
            <th scope="col text-nowrap">Porcentagem</th>
            <th scope="col text-nowrap">Código do Produto</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "select * from relatorio where deletado != '1' {$where} order by dataCriacao asc";
            $result = mysqli_query($con,$query);
            
            while($d = mysqli_fetch_object($result)){
            ?>
            <tr>
            <td class="text-nowrap"><?=dataBr($d->dataCriacao)?></td>
            <td class=""><?=$d->tituloItem?></td>
            <td class="text-nowrap">R$ <?=number_format($d->ValorPedidoXquantidade,2,',','.')?></td>
            <td class="text-nowrap">R$ <?=number_format($d->CustoEnvio,2,',','.')?></td>
            <td class="text-nowrap">R$ <?=number_format($d->PrecoCusto,2,',','.')?></td>
            <td class="text-nowrap">R$ <?=number_format($d->CustoEnvioSeller,2,',','.')?></td>
            <td class="text-nowrap">R$ <?=number_format(($d->TarifaGatwayPagamento + $d->TarifaMarketplace),2,',','.')?></td>
            <td class="text-nowrap">R$ <?=number_format(($d->ValorPedidoXquantidade - $d->PrecoCusto - $d->CustoEnvioSeller - $d->TarifaGatwayPagamento - $d->TarifaMarketplace),2,',','.')?></td>
            <td class="text-nowrap"><?=$d->frete?></td>
            <td class="text-nowrap"><?=number_format((($d->ValorPedidoXquantidade - $d->PrecoCusto - $d->CustoEnvioSeller - $d->TarifaGatwayPagamento - $d->TarifaMarketplace)/($d->PrecoCusto + $d->CustoEnvioSeller + ($d->TarifaGatwayPagamento + $d->TarifaMarketplace)))*100,2,',','.')?>%</td>
            <td class="text-nowrap"><?=$d->codigoPedido?></td>
            </tr>
            <?php
              $totalValorPedidoXquantidade = ($totalValorPedidoXquantidade + $d->ValorPedidoXquantidade);
              $totalCustoEnvio = ($totalCustoEnvio + $d->CustoEnvio);
              $totalPrecoCusto = ($totalPrecoCusto + $d->PrecoCusto);
              $totalCustoEnvioSeller = ($totalCustoEnvioSeller + $d->CustoEnvioSeller);
              $totalComissao = ($totalComissao + ($d->TarifaGatwayPagamento + $d->TarifaMarketplace));
              $totalLucro = ($totalLucro + ($d->ValorPedidoXquantidade - $d->PrecoCusto - $d->CustoEnvioSeller - $d->TarifaGatwayPagamento - $d->TarifaMarketplace));
            }
            ?>
            <tr>
            <th class="text-nowrap"></th>
            <th class=""></th>
            <th class="text-nowrap">R$ <?=number_format($totalValorPedidoXquantidade,2,',','.')?></th>
            <th class="text-nowrap">R$ <?=number_format($totalCustoEnvio,2,',','.')?></th>
            <th class="text-nowrap">R$ <?=number_format($totalPrecoCusto,2,',','.')?></th>
            <th class="text-nowrap">R$ <?=number_format($totalCustoEnvioSeller,2,',','.')?></th>
            <th class="text-nowrap">R$ <?=number_format(($totalComissao),2,',','.')?></th>
            <th class="text-nowrap">R$ <?=number_format(($totalLucro),2,',','.')?></th>
            <th class="text-nowrap"></th>
            <th class="text-nowrap"></th>
            <th class="text-nowrap"></th>
            </tr>            
        </tbody>
    </table>




    <?php
              if($_SESSION['modelo_relatorio']){

                $query = "select * from relatorio where devolucao = '1' and devolucao_relatorio = '{$_SESSION['modelo_relatorio']}'";
                $result = mysqli_query($con,$query);
                if(mysqli_num_rows($result)){
              ?>
              <div class="page-break"></div>
              <h5>Devoluções</h5>
              <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th scope="col text-nowrap">Data</th>
                    <th scope="col">Anúncios</th>
                    <th scope="col text-nowrap">Pagamento Produto</th>
                    <th scope="col text-nowrap">Pagamento Frete</th>
                    <th scope="col text-nowrap">Custo Produto</th>
                    <th scope="col text-nowrap">Custo Frete</th>
                    <th scope="col text-nowrap">Comissão</th> 
                    <th scope="col text-nowrap">Lucro</th>
                    <th scope="col text-nowrap">Frete</th>
                    <th scope="col text-nowrap">Porcentagem</th>
                    <th scope="col text-nowrap">Código do Produto</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                   
                    while($d = mysqli_fetch_object($result)){
                  ?>
                  <tr>
                    <td class="text-nowrap"><?=dataBr($d->dataCriacao)?></td>
                    <td class=""><?=$d->tituloItem?></td>
                    <td class="text-nowrap">R$ <?=number_format($d->ValorPedidoXquantidade,2,',','.')?></td>
                    <td class="text-nowrap">R$ <?=number_format($d->CustoEnvio,2,',','.')?></td>
                    <td class="text-nowrap">R$ <?=number_format($d->PrecoCusto,2,',','.')?></td>
                    <td class="text-nowrap">R$ <?=number_format($d->CustoEnvioSeller,2,',','.')?></td>
                    <td class="text-nowrap">R$ <?=number_format(($d->TarifaGatwayPagamento + $d->TarifaMarketplace),2,',','.')?></td>
                    <td class="text-nowrap">R$ <?=number_format(($d->ValorPedidoXquantidade - $d->PrecoCusto - $d->CustoEnvioSeller - $d->TarifaGatwayPagamento - $d->TarifaMarketplace),2,',','.')?></td>
                    <td class="text-nowrap"><?=$d->frete?></td>
                    <td class="text-nowrap"><?=number_format((($d->ValorPedidoXquantidade - $d->PrecoCusto - $d->CustoEnvioSeller - $d->TarifaGatwayPagamento - $d->TarifaMarketplace)/(($d->PrecoCusto + $d->CustoEnvioSeller + ($d->TarifaGatwayPagamento + $d->TarifaMarketplace))?:1))*100,2,',','.')?>%</td>
                    <td class="text-nowrap"><?=$d->codigoPedido?></td>
                  </tr>
                  <?php
                      if(!$d->deletado){
                        $devolucaoValorPedidoXquantidade = ($devolucaoValorPedidoXquantidade + $d->ValorPedidoXquantidade);
                        $devolucaoCustoEnvio = ($devolucaoCustoEnvio + $d->CustoEnvio);
                        $devolucaoPrecoCusto = ($devolucaoPrecoCusto + $d->PrecoCusto);
                        $devolucaoCustoEnvioSeller = ($devolucaoCustoEnvioSeller + $d->CustoEnvioSeller);
                        $devolucaoComissao = ($devolucaoComissao + ($d->TarifaGatwayPagamento + $d->TarifaMarketplace));
                        $devolucaoLucro = ($devolucaoLucro + ($d->ValorPedidoXquantidade - $d->PrecoCusto - $d->CustoEnvioSeller - $d->TarifaGatwayPagamento - $d->TarifaMarketplace));
                      }
                    }
                  ?>
                  <tr>
                    <th class=""></th>
                    <th class=""></th>
                    <th class="text-nowrap" valor="<?=$devolucaoValorPedidoXquantidade?>" campo="ValorPedidoXquantidade">R$ <?=number_format($devolucaoValorPedidoXquantidade,2,',','.')?></th>
                    <th class="text-nowrap" valor="<?=$devolucaoCustoEnvio?>" campo="CustoEnvio">R$ <?=number_format($devolucaoCustoEnvio,2,',','.')?></th>
                    <th class="text-nowrap" valor="<?=$devolucaoPrecoCusto?>" campo="PrecoCusto">R$ <?=number_format($devolucaoPrecoCusto,2,',','.')?></th>
                    <th class="text-nowrap" valor="<?=$devolucaoCustoEnvioSeller?>" campo="CustoEnvioSeller">R$ <?=number_format($devolucaoCustoEnvioSeller,2,',','.')?></th>
                    <th class="text-nowrap" valor="<?=$devolucaoComissao?>" campo="Comissao">R$ <?=number_format(($devolucaoComissao),2,',','.')?></th>
                    <th class="text-nowrap" valor="<?=$devolucaoLucro?>" campo="Lucro">R$ <?=number_format(($devolucaoLucro),2,',','.')?></th>
                    <th class="text-nowrap"></th>
                    <th class="text-nowrap"></th>
                    <th class="text-nowrap"></th>
                  </tr>  
                </tbody>
              </table>


              <div class="row g-0">
                <div class="col">
                  <div class="card m-2">
                    <table class="table table-hover">
                      <tr>
                        <th>Bruto:</th><td>R$ <?=(number_format($totalValorPedidoXquantidade, 2,',','.'))?></td>
                      </tr>
                      <tr>
                        <th>Deconto Devolução:</th><td>R$ <?=(number_format($devolucaoValorPedidoXquantidade, 2,',','.'))?>  (<?=(number_format($devolucaoValorPedidoXquantidade/$totalValorPedidoXquantidade*100, 0,false,false))?>%) </td>
                      </tr>
                      <tr>
                        <th>Valor Final:</th><td>R$ <?=(number_format($totalValorPedidoXquantidade-$devolucaoValorPedidoXquantidade, 2,',','.'))?></td>
                      </tr>
                    </table>
                  </div>
                </div>


                <div class="col">
                  <div class="card m-2">
                    <table class="table table-hover">
                      <tr>
                        <th>Custo:</th><td>R$ <?=(number_format($totalPrecoCusto, 2,',','.'))?></td>
                      </tr>
                      <tr>
                        <th>Deconto:</th><td>R$ <?=(number_format($devolucaoPrecoCusto, 2,',','.'))?></td>
                      </tr>
                      <tr>
                        <th>Valor Final:</th><td>R$ <?=(number_format($totalPrecoCusto-$devolucaoPrecoCusto, 2,',','.'))?></td>
                      </tr>
                    </table>
                  </div>
                </div>


                <div class="col">
                  <div class="card m-2">
                    <table class="table table-hover">
                      <tr>
                        <th>Lucro:</th><td>R$ <?=(number_format($totalLucro, 2,',','.'))?> (<?=(number_format($totalLucro/$totalValorPedidoXquantidade*100, 0,false,false))?>%)</td>
                      </tr>
                      <tr>
                        <th>Deconto:</th><td>R$ <?=(number_format($devolucaoLucro, 2,',','.'))?></td>
                      </tr>
                      <tr>
                        <th>Valor Final:</th><td>R$ <?=(number_format($totalLucro-$devolucaoLucro, 2,',','.'))?></td>
                      </tr>
                    </table>
                  </div>
                </div>

              </div>

              <?php
                }
              } // final da condição de exibir apenas em homologação
              ?>


    <?php
    include("lib/footer.php");
    ?>

    <script>
 

    </script>

  </body>
</html>