<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

    if($_POST['delete']){
      // $query = "delete from relatorio where codigo = '{$_POST['delete']}'";
      $query = "update relatorio set deletado = '1' where codigo = '{$_POST['delete']}'";
      mysqli_query($con,$query);
    }

    if($_POST['situacao']){
      $query = "update relatorio set status = '{$_POST['opc']}' where codigo = '{$_POST['situacao']}'";
      mysqli_query($con,$query);
      exit();
    }


    if($_POST['filtro'] == 'filtrar'){
      $_SESSION['buscaOrigem'] = $_POST['buscaOrigem'];
      $_SESSION['buscaDataInicial'] = $_POST['buscaDataInicial'];
      $_SESSION['buscaDataFinal'] = $_POST['buscaDataFinal'];
    }elseif($_POST['filtro']){
      $_SESSION['buscaOrigem'] = false;
      $_SESSION['buscaDataInicial'] = false;
      $_SESSION['buscaDataFinal'] = false;
    }

    if($_SESSION['buscaOrigem'] and $_SESSION['buscaDataInicial'] and $_SESSION['buscaDataFinal']){
      // $cpf = str_replace( '.', '', str_replace('-', '', $_SESSION['usuarioBusca']));
      $where = " and origem = '{$_SESSION['buscaOrigem']}' and dataCriacao between '{$_SESSION['buscaDataInicial']} 00:00:00' and '{$_SESSION['buscaDataFinal']} 23:59:59' ";
    }



    $opcoes = [];

?>
<style>
  .btn-perfil{
    padding:5px;
    border-radius:8px;
    color:#fff;
    background-color:#a1a1a1;
    cursor: pointer;
  }
  td, th{
    font-size:12px;
  }
  .marcar_todos{
    cursor:pointer;
  }

</style>
<div class="col">
  <div class="m-3">

    <div class="row">
      <div class="col">
        <div class="card">
          <h5 class="card-header">Tela de Consultas</h5>
          <div class="card-body">
              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="input-group">
                    <label class="input-group-text" for="inputGroupFile01">Buscar por </label>
                    <select id="origem" class="form-select">
                      <option value="">:: Selecione Origem ::</option>
                      <?php
                      $q = "select * from origens where status = '1' order by nome";
                      $r = mysqli_query($con, $q);
                      while($s = mysqli_fetch_object($r)){
                      ?>
                      <option value="<?=$s->codigo?>" <?=(($s->codigo == $_SESSION['buscaOrigem'])?'selected':false)?>><?=$s->nome?></option>
                      <?php
                      }
                      ?>
                    </select>
                    <label class="input-group-text" for="inputGroupFile01"> De </label>
                    <input type="date" id="data_inicial" class="form-control" value="<?=$_SESSION['buscaDataInicial']?>" >
                    <label class="input-group-text" for="inputGroupFile01"> A </label>
                    <input type="date" id="data_final" class="form-control" value="<?=$_SESSION['buscaDataFinal']?>" >
                    <button filtro="filtrar" class="btn btn-outline-secondary" type="button">Buscar</button>
                    <button filtro="limpar" class="btn btn-outline-danger" type="button">limpar</button>
                    <a class="btn btn-outline-success" type="button" href='./print.php' target="_blank"><i class="fa-solid fa-print"></i></a>
                  </div>
                </div>


                <div class="col-md-6">
                  <div class="input-group">
                    <label class="input-group-text" for="inputGroupFile01">Relatório</label>
                    <input type="text" id="nome_relatorio" class="form-control" value="<?=$rel->nome?>" >
                    <button id="salvar_relatorio" class="btn btn-outline-success" type="button"><i class="fa-regular fa-floppy-disk"></i></button>
                    <button id="abrir_relatorio" class="btn btn-outline-primary" type="button"><i class="fa-solid fa-folder-tree"></i></button>
                  </div>
                </div>


            </div>

            <?php
              if($where){

            ?>

              <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th scope="col" colspan="14"><i 
                                                    class="fa-solid fa-turn-down me-2"
                                                    style = "-moz-transform: scaleX(-1); -o-transform: scaleX(-1); -webkit-transform: scaleX(-1); transform: scaleX(-1);"
                                                ></i> <span class="marcar_todos">Marcar Todos</span></th>
                  </tr>
                  <tr>
                    <th scope="col"><input type="checkbox" class="marcar_todos"></th>
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
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $query = "select * from relatorio where 1 {$where} order by dataCriacao asc";
                    $result = mysqli_query($con,$query);
                    
                    while($d = mysqli_fetch_object($result)){
                  ?>
                  <tr 
                    <?=(($d->deletado and !$d->observacoes)?'style="text-decoration: line-through; color:red"':false)?>
                    <?=(($d->observacoes and !$d->deletado)?'style="background-color:yellow;"':false)?>
                    <?=(($d->observacoes and $d->deletado)?'style="background-color:yellow; text-decoration: line-through; color:red"':false)?>
                  >
                    <td><input type="checkbox" class="opcoes" <?=((in_array($d->codigo, $opcoes))?'checked':false)?> value="<?=$d->codigo?>"></td>
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
                    <td class="text-nowrap">
                      <i 
                          editar="<?=$d->codigo?>" 
                          style="cursor:pointer;" 
                          class="fa-solid fa-pen-to-square text-primary"
                          data-bs-toggle="offcanvas"
                          href="#offcanvasDireita"
                          role="button"
                          aria-controls="offcanvasDireita"
                      ></i>
                    </td>
                  </tr>
                  <?php
                      if(!$d->deletado){
                        $totalValorPedidoXquantidade = ($totalValorPedidoXquantidade + $d->ValorPedidoXquantidade);
                        $totalCustoEnvio = ($totalCustoEnvio + $d->CustoEnvio);
                        $totalPrecoCusto = ($totalPrecoCusto + $d->PrecoCusto);
                        $totalCustoEnvioSeller = ($totalCustoEnvioSeller + $d->CustoEnvioSeller);
                        $totalComissao = ($totalComissao + ($d->TarifaGatwayPagamento + $d->TarifaMarketplace));
                        $totalLucro = ($totalLucro + ($d->ValorPedidoXquantidade - $d->PrecoCusto - $d->CustoEnvioSeller - $d->TarifaGatwayPagamento - $d->TarifaMarketplace));
                      }
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
                    <th class="text-nowrap"></th>
                  </tr>  
                </tbody>
              </table>
              <?php
              }
              ?>

          </div>
        </div>
      </div>
    </div>

  </div>
</div>


<script>
    $(function(){
        Carregando('none');

        $("button[filtro]").click(function(){
          filtro = $(this).attr("filtro");
          buscaOrigem = $("#origem").val();
          buscaDataInicial = $("#data_inicial").val();
          buscaDataFinal = $("#data_final").val();
          $.ajax({
              url:"src/relatorio/index.php",
              type:"POST",
              data:{
                  filtro,
                  buscaOrigem,
                  buscaDataInicial,
                  buscaDataFinal
              },
              success:function(dados){
                  $("#paginaHome").html(dados);
              }
          })
        })

        $("button[limpar]").click(function(){
          $.ajax({
              url:"src/relatorio/index.php",
              type:"POST",
              data:{
                  filtro:'limpar',
              },
              success:function(dados){
                  $("#paginaHome").html(dados);
              }
          })
        })

        $("i[editar]").click(function(){
          editar = $(this).attr("editar");
          $.ajax({
              url:"src/relatorio/form.php",
              type:"POST",
              data:{
                  editar,
              },
              success:function(dados){
                $(".LateralDireita").html(dados);
              }
          })          
        })



    })
</script>