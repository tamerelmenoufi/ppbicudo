<?php

    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");


    if($_POST['filtro'] == 'filtrar'){
        $_SESSION['dashboardDataInicial'] = $_POST['dashboardDataInicial'];
        $_SESSION['dashboardDataFinal'] = $_POST['dashboardDataFinal'];
      }elseif($_POST['filtro']){
        $_SESSION['dashboardDataInicial'] = false;
        $_SESSION['dashboardDataFinal'] = false;
      }
  
      if($_SESSION['dashboardDataInicial'] and $_SESSION['dashboardDataFinal']){
        $where = " and dataCriacao between '{$_SESSION['dashboardDataInicial']} 00:00:00' and '{$_SESSION['dashboardDataFinal']} 23:59:59' ";

      }


    // echo $query = "update produtos set 
    //                                 valor = '3.44'
    //                         where categoria = 2
    // ";
    // mysqli_query($con,$query);


    $q = "select 
                (select sum(ValorPedidoXquantidade) from relatorio where deletado != '1' and devolucao != '1' {$where} ) as pagamento_produto,   
                (select sum(CustoEnvio) from relatorio where deletado != '1' and devolucao != '1' {$where} ) as pagamento_frete,   
                (select sum(PrecoCusto) from relatorio where deletado != '1' and devolucao != '1' {$where} ) as custo_produto,   
                (select sum(CustoEnvioSeller) from relatorio where deletado != '1' and devolucao != '1' {$where} ) as custo_frete,
                (select sum(TarifaGatwayPagamento + TarifaMarketplace) from relatorio where deletado != '1' and devolucao != '1' {$where} ) as comissão,   
                (select sum(ValorPedidoXquantidade - PrecoCusto - CustoEnvioSeller - TarifaGatwayPagamento - TarifaMarketplace) from relatorio where deletado != '1' and devolucao != '1' {$where} ) as lucro,
                (select sum(ValorPedidoXquantidade) from relatorio where deletado != '1' and devolucao = '1' {$where} ) as pagamento_devolucao,
                (select count(*) from relatorio where deletado != '1' and devolucao = '1' {$where} ) as devolucao,
                (select count(*) from planilhas) as planilhas,
                (select count(*) from relatorio where 1 {$where} ) as vendas
        ";
    $r = mysqli_query($con, $q);
    $v = mysqli_fetch_object($r);
    
?>
<style>
    td, th{
    font-size:12px;
    white-space: nowrap;
  }
</style>
</style>
<div class="m-3">

    <div class="row g-0 mb-3 mt-3">
        <div class="col-md-6"></div>
        <div class="col-md-6">
            <div class="input-group">
                <label class="input-group-text">Filtro por Período </label>
                <label class="input-group-text" for="data_inicial"> De </label>
                <input type="date" id="data_inicial" class="form-control" <?=$busca_disabled?> value="<?=$_SESSION['dashboardDataInicial']?>" >
                <label class="input-group-text" for="data_final"> A </label>
                <input type="date" id="data_final" class="form-control" value="<?=$_SESSION['dashboardDataFinal']?>" >
                <button filtro="filtrar" class="btn btn-outline-secondary" type="button">Buscar</button>
                <button filtro="limpar" class="btn btn-outline-danger" type="button">limpar</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-0">
        <div class="col-md-12 p-2">
            <h6>Resumo <?=(($_SESSION['dashboardDataInicial'] and $_SESSION['dashboardDataFinal'])? "de ".dataBr($_SESSION['dashboardDataInicial'])." a ".dataBr($_SESSION['dashboardDataFinal']):'Geral')?></h6>
        </div>
        <div class="col-md-2 p-2">
            <div class="alert alert-secondary" role="alert">
                <span>Planilhas Importadas</span>
                <h1><?=$v->planilhas?></h1>
            </div>
        </div>
        <div class="col-md-2 p-2">
            <div class="alert alert-primary" role="alert">
                <span>Quantidade Vendas</span>
                <h1><?=$v->vendas?></h1>
            </div>
        </div>

        <div class="col-md-2 p-2">
            <div class="alert alert-warning" role="alert">
                <span>Quantidade Devolução</span>
                <h1><?=$v->devolucao?></h1>
            </div>
        </div>

        <div class="col-md-3 p-2">
            <div class="alert alert-danger" role="alert">
                <span>Total Devolução</span>
                <h1>R$ <?=number_format($v->pagamento_devolucao,2,',','.')?></h1>
            </div>
        </div>


        <div class="col-md-3 p-2">
            <div class="alert alert-success" role="alert">
                <span>Total Vendas</span>
                <h1>R$ <?=number_format($v->pagamento_produto,2,',','.')?></h1>
            </div>
        </div>


        
    </div>

    <div class="row g-0">
        <div class="col-md-12 p-2">
            <h6>Resumo Financeiro <?=(($_SESSION['dashboardDataInicial'] and $_SESSION['dashboardDataFinal'])? "de ".dataBr($_SESSION['dashboardDataInicial'])." a ".dataBr($_SESSION['dashboardDataFinal']):'Geral')?></h6>
        </div>
        <div class="col-md-2 p-2">
            <div class="alert alert-secondary" role="alert">
                <span>Pagamento Produto</span>
                <h3>R$ <?=number_format($v->pagamento_produto,2,',','.')?></h3>
            </div>
        </div>
        <div class="col-md-2 p-2">
            <div class="alert alert-secondary" role="alert">
                <span>Pagamento Frete</span>
                <h3>R$ <?=number_format($v->pagamento_frete,2,',','.')?></h3>
            </div>
        </div>
        <div class="col-md-2 p-2">
            <div class="alert alert-warning" role="alert">
                <span>Custo Produto</span>
                <h3>R$ <?=number_format($v->custo_produto,2,',','.')?></h3>
            </div>
        </div>
        <div class="col-md-2 p-2">
            <div class="alert alert-secondary" role="alert">
                <span>Custo Frete</span>
                <h3>R$ <?=number_format($v->custo_frete,2,',','.')?></h3>
            </div>
        </div>
        <div class="col-md-2 p-2">
            <div class="alert alert-primary" role="alert">
                <span>Comissão</span>
                <h3>R$ <?=number_format($v->comissão,2,',','.')?></h3>
            </div>
        </div>
        <div class="col-md-2 p-2">
            <div class="alert alert-success" role="alert">
                <span>Lucro</span>
                <h3>R$ <?=number_format($v->lucro,2,',','.')?></h3>
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
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Origem</th>
                            <th class="text-center">Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $q = "select a.*, (select count(*) from relatorio where origem = a.codigo {$where} ) as qt from origens a order by a.nome";
                    $r = mysqli_query($con, $q);
                    while($s = mysqli_fetch_object($r)){
                    ?>
                    <tr>
                        <td><?=$s->nome?></td>
                        <td class="text-center"><?=$s->qt?></td>
                    </tr>                
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-8 p-2">
            <div class="table-responsive">
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
                                (select sum(ValorPedidoXquantidade) from relatorio where origem = a.codigo and deletado != '1' and devolucao != '1' {$where} ) as pagamento_produto,   
                                (select sum(CustoEnvio) from relatorio where origem = a.codigo and deletado != '1' and devolucao != '1' {$where}) as pagamento_frete,   
                                (select sum(PrecoCusto) from relatorio where origem = a.codigo and deletado != '1' and devolucao != '1' {$where}) as custo_produto,   
                                (select sum(CustoEnvioSeller) from relatorio where origem = a.codigo and deletado != '1' and devolucao != '1' {$where}) as custo_frete,   
                                (select sum(TarifaGatwayPagamento + TarifaMarketplace) from relatorio where origem = a.codigo and deletado != '1' {$where}) as comissão,   
                                (select sum(ValorPedidoXquantidade - PrecoCusto - CustoEnvioSeller - TarifaGatwayPagamento - TarifaMarketplace) from relatorio where origem = a.codigo and deletado != '1' and devolucao != '1' {$where}) as lucro   
                            from origens a order by a.nome";
                    $r = mysqli_query($con, $q);
                    while($s = mysqli_fetch_object($r)){
                    ?>
                    <tr>
                        <td><?=$s->nome?></td>
                        <td>R$ <?=number_format($s->pagamento_produto,2,',','.')?></td>
                        <td>R$ <?=number_format($s->pagamento_frete,2,',','.')?></td>
                        <td>R$ <?=number_format($s->custo_produto,2,',','.')?></td>
                        <td>R$ <?=number_format($s->custo_frete,2,',','.')?></td>
                        <td>R$ <?=number_format($s->comissão,2,',','.')?></td>
                        <td>R$ <?=number_format($s->lucro,2,',','.')?></td>
                    </tr>                
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
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

        $("button[filtro]").click(function(){
          filtro = $(this).attr("filtro");
          dashboardDataInicial = $("#data_inicial").val();
          dashboardDataFinal = $("#data_final").val();
          Carregando()
          $.ajax({
              url:"src/dashboard/index.php",
              type:"POST",
              data:{
                  filtro,
                  dashboardDataInicial,
                  dashboardDataFinal
              },
              success:function(dados){
                  $("#paginaHome").html(dados);
              }
          })
        })

        $("button[limpar]").click(function(){
          Carregando()
          $.ajax({
              url:"src/dashboard/index.php",
              type:"POST",
              data:{
                  filtro:'limpar',
              },
              success:function(dados){
                  $("#paginaHome").html(dados);
              }
          })
        })
        
    })
</script>