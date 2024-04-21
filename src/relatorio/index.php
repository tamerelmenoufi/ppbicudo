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
      $_SESSION['usuarioBusca'] = $_POST['campo'];
    }elseif($_POST['filtro']){
      $_SESSION['usuarioBusca'] = false;
    }

    if($_SESSION['usuarioBusca']){
      $cpf = str_replace( '.', '', str_replace('-', '', $_SESSION['usuarioBusca']));
      $where = " and nome like '%{$_SESSION['usuarioBusca']}%' or REPLACE( REPLACE( cpf, '.', '' ), '-', '' ) = '{$cpf}' ";
    }



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

</style>
<div class="col">
  <div class="m-3">

    <div class="row">
      <div class="col">
        <div class="card">
          <h5 class="card-header">Tela de Consultas</h5>
          <div class="card-body">
              <div class="d-flex justify-content-between mb-3">
                <div class="input-group">
                  <label class="input-group-text" for="inputGroupFile01">Buscar por </label>
                  <input campoBusca type="text" class="form-control" value="<?=$_SESSION['usuarioBusca']?>" aria-label="Digite a informação para a busca">
                  <button filtro="filtrar" class="btn btn-outline-secondary" type="button">Buscar</button>
                  <button filtro="limpar" class="btn btn-outline-danger" type="button">limpar</button>
                </div>
            </div>

              <table class="table table-striped table-hover">
                <thead>
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
                    <td class="text-nowrap"><?=number_format($d->frete,2,',','.')?>%</td>
                    <td class="text-nowrap"><?=$d->Porcentagem?></td>
                    <td class="text-nowrap"><?=$d->codigoPedido?></td>
                  </tr>
                  <?php
                    }
                  ?>
                </tbody>
              </table>

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
          campo = $("input[campoBusca]").val();
          $.ajax({
              url:"src/relatorio/index.php",
              type:"POST",
              data:{
                  filtro,
                  campo
              },
              success:function(dados){
                  $("#paginaHome").html(dados);
              }
          })
        })



    })
</script>