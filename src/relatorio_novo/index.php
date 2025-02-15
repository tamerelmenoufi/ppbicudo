<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

    // devolucao,
    // devolucao_data,
    // devolucao_relatorio,
    // codigo_pedido,
    // acao:'devolucao'

    if($_POST['sair_relatorio']){
      $_SESSION['modelo_relatorio'] = false;
    }

    if($_POST['modelo']){
      $_SESSION['modelo_relatorio'] = $_POST['modelo'];
      $_SESSION['modelo_campo'] = $_POST['campo'];
    }




    if($_POST['acao'] == 'devolucao'){
      $query = "update relatorio set 
                                    devolucao = '1',
                                    devolucao_data = '{$_POST['devolucao_data']}',
                                    devolucao_relatorio = '{$_POST['devolucao_relatorio']}'
                where codigoPedido = '{$_POST['codigo_pedido']}'";
      mysqli_query($con, $query);

    }

    if($_POST['acao'] == 'devolucaoDesfazer'){
      $query = "update relatorio set 
                                    devolucao = '0',
                                    devolucao_data = NULL,
                                    devolucao_relatorio = 0
                where codigo = '{$_POST['devolucaoDesfazer']}'";
      mysqli_query($con, $query);

    }

    if($_POST['acao'] == 'atualizaCampo'){

      $query = "UPDATE relatorio set {$_POST['campo']} = '{$_POST['valor']}' where codigo = '{$_POST['codigo']}'";
      mysqli_query($con, $query);
      // echo $query;

      //exit();
    }

    if($_POST['deletar']){

      $query = "DELETE FROM relatorio where codigo = '{$_POST['deletar']}'";
      mysqli_query($con, $query);

      if($_SESSION['modelo_relatorio']){
        $q = "UPDATE relatorio_modelos set 
                                          {$_SESSION['modelo_campo']} = JSON_REMOVE({$_SESSION['modelo_campo']}, JSON_UNQUOTE(JSON_SEARCH({$_SESSION['modelo_campo']}, 'one', '{$_POST['deletar']}')))              where codigo = '{$_SESSION['modelo_relatorio']}'";
        mysqli_query($con, $q);
      }
      // echo $query;

      //exit();
    }


    function editarValores($d){
      //*
      if(!$d['deletado']){
?>
    <div class="d-flex justify-content-start">
      R$ 
      <!-- <span opc="<?=$d['codigo']?>"><?=number_format($d['valor'],2,',',false)?></span> -->
      <input opc-<?=$d['campo']?>-<?=$d['codigo']?> type="text" class="moeda" campo="<?=$d['campo']?>" valor="<?=$d['valor']?>" codigo="<?=$d['codigo']?>" value="<?=number_format($d['valor'],2,',',false)?>" inputmode="numeric" >
      <!--<i opc-<?=$d['campo']?>-<?=$d['codigo']?> class="fa-solid fa-arrow-rotate-left desfazer" campo="<?=$d['campo']?>" valor="<?=$d['valor']?>" codigo="<?=$d['codigo']?>"></i>-->
    </div>
<?php
  //*/
      }else{
?>
R$ <?=number_format($d['valor'],2,',',false)?>
<?php
      }
    }

    function listarValores($d){
      //*
      if(!$d['deletado']){
?>
      <input opc-<?=$d['campo']?>-<?=$d['codigo']?> type="hidden" valor="<?=$d['valor']?>" >
<?php
  //*/
      }
?>
R$ <?=number_format($d['valor'],2,',',false)?>
<?php
    }    


    $opcoes = [];

    if($_POST['acao'] == 'relatorio'){

      //data	nome	registros
      $registros = json_encode($_POST['lista']);
      if($_POST['codigo_relatorio']){
        $query = "UPDATE relatorio_modelos set nome = '{$_POST['nome_relatorio']}', data = '{$_POST['data_relatorio']}', {$_SESSION['modelo_campo']} = '{$registros}' where codigo = '{$_POST['codigo_relatorio']}'";
        mysqli_query($con, $query);
        $_SESSION['modelo_relatorio'] = $_POST['codigo_relatorio'];
      }else{
        $query = "INSERT INTO relatorio_modelos set nome = '{$_POST['nome_relatorio']}', data = '{$_POST['data_relatorio']}', {$_SESSION['modelo_campo']} = '{$registros}', origem = '{$_POST['origem']}'";
        mysqli_query($con, $query);
        $_SESSION['modelo_relatorio'] = mysqli_insert_id($con);
      }

      mysqli_query($con, "UPDATE relatorio set relatorio = '0' where relatorio = '{$_SESSION['modelo_relatorio']}'");
      mysqli_query($con, "UPDATE relatorio set relatorio = '{$_SESSION['modelo_relatorio']}' where codigo in (".implode(", ", $_POST['lista']).")");

    }


    if($_POST['acao'] == 'anexar_relatorio'){

      // codigo_relatorio,
      // lista,
      // acao:'anexar_relatorio'

      $lista1 = mysqli_fetch_object(mysqli_query($con, "select {$_SESSION['modelo_campo']} from relatorio_modelos where codigo = '{$_POST['codigo_relatorio']}'"));
      $lista_completa = array_merge(json_decode($lista1->$_SESSION['modelo_campo']), $_POST['lista']);

      $registros = json_encode($lista_completa);

      $query = "UPDATE relatorio_modelos set {$_SESSION['modelo_campo']} = '{$registros}' where codigo = '{$_POST['codigo_relatorio']}'";
      mysqli_query($con, $query);
      $_SESSION['modelo_relatorio'] = $_POST['codigo_relatorio'];


      mysqli_query($con, "UPDATE relatorio set relatorio = '0' where relatorio = '{$_SESSION['modelo_relatorio']}'");
      mysqli_query($con, "UPDATE relatorio set relatorio = '{$_SESSION['modelo_relatorio']}' where codigo in (".implode(", ", $lista_completa).")");

    }    

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


    print_r($_SESSION);

    // exit();


    if($_SESSION['modelo_relatorio']){
      $busca_disabled = 'disabled';
      $_SESSION['buscaOrigem'] = false;
      $_SESSION['buscaDataInicial'] = false;
      $_SESSION['buscaDataFinal'] = false;

      $q = "select * from relatorio_modelos where codigo = '{$_SESSION['modelo_relatorio']}'";
      $rel = mysqli_fetch_object(mysqli_query($con, $q));

      if($_SESSION['modelo_campo']){
        $registros = json_decode($rel->$_SESSION['modelo_campo']);
        $opcoes = $registros;
        $registros = implode(", ", $registros); 
      }
      if($registros) $where = " and codigo in ({$registros})";

      // if($rel->registros){
      //   $registros = json_decode($rel->registros);
      //   $registros = implode(", ", $registros); 
      //   $where = " and codigo in ({$registros})";
      // }
      
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
      $where = " and a.relatorio = '0' and a.origem = '{$_SESSION['buscaOrigem']}' and a.dataCriacao between '{$_SESSION['buscaDataInicial']} 00:00:00' and '{$_SESSION['buscaDataFinal']} 23:59:59' ";
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
  .text-nowrap{
    white-space: nowrap;
  }
  .marcar_todos{
    cursor:pointer;
  }
  i[info]{
    cursor:pointer;
  }

  .moeda{
    width:100%;
    border:0;
    margin:0;
    padding:0;
    margin-left:5px;
    margin-right:5px;
    background-color:transparent;
  }
  .desfazer{
    cursor:pointer;
    opacity:0;
  }
  .atualizacao{
    opacity: 0;
  }

  .calculaTitulos{
    padding:0;
    margin:0;
  }  

  .calculaTitulos th{
    color:#a1a1a1;
    font-size:10px;
    padding:5px;
    margin:0;
  }

  .calculaTitulos td{
    color:#333333;
    font-size:12px;
    padding:5px;
    margin:0;
  }

</style>
<div class="col">
  <div class="m-3">

    <div class="row">
      <div class="col">
        <div class="card" style="margin-bottom:50px;">
          <h5 class="card-header">Tela de Consultas</h5>
          <div class="card-body">
              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="input-group">
                    <label class="input-group-text" for="inputGroupFile01">Buscar por </label>
                    <select id="origem" class="form-select" <?=$busca_disabled?>>
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
                    <input type="date" id="data_inicial" class="form-control" <?=$busca_disabled?> value="<?=$_SESSION['buscaDataInicial']?>" >
                    <label class="input-group-text" for="inputGroupFile01"> A </label>
                    <input type="date" id="data_final" class="form-control" <?=$busca_disabled?> value="<?=$_SESSION['buscaDataFinal']?>" >
                    <button filtro="filtrar" class="btn btn-outline-secondary" <?=$busca_disabled?> type="button">Buscar</button>
                    <button filtro="limpar" class="btn btn-outline-danger" <?=$busca_disabled?> type="button">limpar</button>
                    <a class="btn btn-warning" type="button" href='./print.php' target="_blank"><i class="fa-solid fa-print"></i></a>
                  </div>
                </div>


                <div class="col-md-6">
                  
                  <div class="input-group">
                    <label class="input-group-text" for="nome_relatorio">Relatório</label>
                    <input type="text" id="nome_relatorio" class="form-control" value="<?=$rel->nome?>" >
                    <label class="input-group-text" for="data_relatorio">Data</label>
                    <input type="date" id="data_relatorio" class="form-control" value="<?=(($rel->data)?:date("Y-m-d"))?>" >
                    <button id="salvar_relatorio" class="btn btn-outline-success" type="button"><i class="fa-regular fa-floppy-disk"></i></button>
                    <button 
                          id="abrir_relatorio" 
                          class="btn btn-outline-primary" 
                          type="button"
                          data-bs-toggle="offcanvas"
                          href="#offcanvasDireita"
                          role="button"
                          aria-controls="offcanvasDireita"      
                    ><i class="fa-solid fa-folder-tree"></i></button>
                    <?php
                    if($_SESSION['modelo_relatorio']){
                    ?>
                    <button id="sair_relatorio" class="btn btn-outline-danger" type="button"><i class="fa-solid fa-right-from-bracket"></i></button>
                    <?php
                    }
                    ?>
                    <input type="hidden" id="codigo_relatorio" value="<?=$rel->codigo?>">
                  </div>
                </div>


            </div>

            <?php
              if($where){
            ?>

              <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th scope="col" colspan="2"><i 
                                        class="fa-solid fa-turn-down me-2"
                                        style = "-moz-transform: scaleX(-1); -o-transform: scaleX(-1); -webkit-transform: scaleX(-1); transform: scaleX(-1);"
                                    ></i> <span class="marcar_todos">Marcar Todos</span>                 
                    </th>
                    <th scope="col" colspan="2">
                        <?php
                        if(!$_SESSION['modelo_relatorio']){
                        ?>
                        <div class="input-group">
                          <label class="input-group-text" for="inputGroupFile01">Anexar em </label>
                          <select class="form-select" id="relatorio_anexar">
                            <option value="">:: Selecione ::</option>
                            <?php
                                $q = "select * from relatorio_modelos order by data desc";
                                $r = mysqli_query($con, $q);
                                while($s = mysqli_fetch_object($r)){
                            ?>
                            <option value="<?=$s->codigo?>"><?=$s->nome?></option>
                            <?php
                                }
                            ?>
                          </select>
                          <button id="anexar_relatorio" class="btn btn-outline-success" type="button"><i class="fa-solid fa-paperclip"></i></button>
                        </div>
                        <?php
                        }
                        ?>
                    </th>
                    <th scope="col" colspan="9">
                      <div class="d-flex justify-content-end">
                      
                        <button 
                          class="btn btn-success btn-sm novo" 
                          type="button"
                          data-bs-toggle="offcanvas"
                          href="#offcanvasDireita"
                          role="button"
                          aria-controls="offcanvasDireita"
                        >
                          <i class="fa-solid fa-plus"></i> Novo
                        </button>
                      </div>
                    </th>
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
                    <th scope="col">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    // $query = "select a.*, (SELECT count(*) FROM relatorio_modelos WHERE JSON_SEARCH(registros, 'one', a.codigo) IS NOT NULL) as vinculado from relatorio a where 1 {$where} order by a.dataCriacao asc";
                    $query = "select a.* from relatorio a where 1 {$where} order by a.dataCriacao asc";
                    $result = mysqli_query($con,$query);
                    
                    while($d = mysqli_fetch_object($result)){
                  ?>
                  <tr 
                    <?=(($d->deletado and !$d->observacoes)?'style="text-decoration: line-through; color:red"':false)?>
                    <?=(($d->observacoes and !$d->deletado)?'style="background-color:yellow;"':false)?>
                    <?=(($d->observacoes and $d->deletado)?'style="background-color:yellow; text-decoration: line-through; color:red"':false)?>
                  >
                    <td>
                      <?php
                      if(!$d->relatorio || $_SESSION['modelo_relatorio']){
                      ?>
                      <input type="checkbox" del="<?=(($d->deletado)?'s':false)?>" class="opcoes" <?=((in_array($d->codigo, $opcoes))?'checked':false)?> value="<?=$d->codigo?>">
                      <?php
                      }else{
                      ?>
                      <i info="<?=$d->codigo?>" class="fa-solid fa-circle-info text-warning"></i>
                      <?php
                      }
                      ?>
                    </td>
                    <td class="text-nowrap"><?=dataBr($d->dataCriacao)?></td>
                    <td class=""><?=$d->tituloItem?></td>
                    <td class="text-nowrap"><?=editarValores(['valor'=>$d->ValorPedidoXquantidade, 'campo'=>'ValorPedidoXquantidade', 'codigo'=>$d->codigo, 'deletado' => $d->deletado])?></td>
                    <td class="text-nowrap"><?=editarValores(['valor'=>$d->CustoEnvio, 'campo'=>'CustoEnvio', 'codigo'=>$d->codigo, 'deletado' => $d->deletado])?></td>
                    <td class="text-nowrap"><?=editarValores(['valor'=>$d->PrecoCusto, 'campo'=>'PrecoCusto', 'codigo'=>$d->codigo, 'deletado' => $d->deletado])?></td>
                    <td class="text-nowrap"><?=editarValores(['valor'=>$d->CustoEnvioSeller, 'campo'=>'CustoEnvioSeller', 'codigo'=>$d->codigo, 'deletado' => $d->deletado])?></td>
                    <td class="text-nowrap"><?=listarValores(['valor'=>($d->TarifaGatwayPagamento + $d->TarifaMarketplace), 'campo'=>'Comissao', 'codigo'=>$d->codigo, 'deletado' => $d->deletado])?></td>
                    <td class="text-nowrap"><?=listarValores(['valor'=>($d->ValorPedidoXquantidade - $d->PrecoCusto - $d->CustoEnvioSeller - $d->TarifaGatwayPagamento - $d->TarifaMarketplace), 'campo'=>'Lucro', 'codigo'=>$d->codigo, 'deletado' => $d->deletado])?></td>
                    <td class="text-nowrap"><?=$d->frete?></td>
                    <td class="text-nowrap"><?=number_format((($d->ValorPedidoXquantidade - $d->PrecoCusto - $d->CustoEnvioSeller - $d->TarifaGatwayPagamento - $d->TarifaMarketplace)/(($d->PrecoCusto + $d->CustoEnvioSeller + ($d->TarifaGatwayPagamento + $d->TarifaMarketplace))?:1))*100,2,',','.')?>%</td>
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
                      <i 
                          deletar="<?=$d->codigo?>" 
                          style="cursor:pointer;" 
                          class="fa-solid fa-trash text-danger ms-3"
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
                    <th class=""></th>
                    <th class="text-nowrap" valor="<?=$totalValorPedidoXquantidade?>" campo="ValorPedidoXquantidade">R$ <?=number_format($totalValorPedidoXquantidade,2,',','.')?></th>
                    <th class="text-nowrap" valor="<?=$totalCustoEnvio?>" campo="CustoEnvio">R$ <?=number_format($totalCustoEnvio,2,',','.')?></th>
                    <th class="text-nowrap" valor="<?=$totalPrecoCusto?>" campo="PrecoCusto">R$ <?=number_format($totalPrecoCusto,2,',','.')?></th>
                    <th class="text-nowrap" valor="<?=$totalCustoEnvioSeller?>" campo="CustoEnvioSeller">R$ <?=number_format($totalCustoEnvioSeller,2,',','.')?></th>
                    <th class="text-nowrap" valor="<?=$totalComissao?>" campo="Comissao">R$ <?=number_format(($totalComissao),2,',','.')?></th>
                    <th class="text-nowrap" valor="<?=$totalLucro?>" campo="Lucro">R$ <?=number_format(($totalLucro),2,',','.')?></th>
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

              <?php
              if($_SESSION['modelo_relatorio'] and $where){
              ?>
              <div class="d-flex justify-content-end">
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text">Para devolução digite o código do produto</span>
                    <input 
                          type="text" 
                          XXXinputmode="numeric"
                          class="form-control"
                          id="codigo_devolucao"
                          autocomplete="off"
                    >
                    <button class="btn btn-danger" id="incluir_devolucao">Devolver</button>
                  </div>
                </div>
              </div>

              <?php
                $query = "select * from relatorio where devolucao = '1' and devolucao_relatorio = '{$_SESSION['modelo_relatorio']}'";
                $result = mysqli_query($con,$query);
                if(mysqli_num_rows($result)){
              ?>

              <h5>Devoluções</h5>
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
                    <th scope="col">Ações</th>
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
                    <td class="text-nowrap">
                      <i 
                          class="fa-solid fa-rotate-left text-danger ms-3"
                          devolucaoDesfazer="<?=$d->codigo?>"
                          style="cursor:pointer;" 
                      ></i>
                    </td>
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
                    <th class="text-nowrap"></th>
                  </tr>  
                </tbody>
              </table>


              <div class="row">
                <div class="col">
                  <div class="card">
                    <table class="table table-hover">
                      <tr>
                        <th>Bruto:</th><td>R$ <?=(number_format($totalValorPedidoXquantidade, 2,',','.'))?></td>
                      </tr>
                      <tr>
                        <th>Deconto Devolução:</th><td>R$ <?=(number_format($devolucaoValorPedidoXquantidade, 2,',','.'))?></td>
                      </tr>
                      <tr>
                        <th>Valor Final:</th><td>R$ <?=(number_format($totalValorPedidoXquantidade-$devolucaoValorPedidoXquantidade, 2,',','.'))?></td>
                      </tr>
                    </table>
                  </div>
                </div>


                <div class="col">
                  <div class="card">
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
                  <div class="card">
                    <table class="table table-hover">
                      <tr>
                        <th>Lucro:</th><td>R$ <?=(number_format($totalLucro, 2,',','.'))?></td>
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

          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php
  if($where){
?>
<div style="display: flex; justify-content: flex-end; position:fixed; bottom:0; left:0; right:0; height:65px; border:solid 0px red; padding-top:10px; padding-bottom:10px; padding-left:30px; padding-right:100px; background-color:#fff; z-index:100;">

<table>
<thead>
  <tr class="calculaTitulos">
    <th class="col-3 text-nowrap" style="color:#333333">Cálculo dos Totais (itens selecionados)</th>
    <th class="col-1 text-nowrap">Pagamento Produto</th>
    <th class="col-1 text-nowrap">Pagamento Frete</th>
    <th class="col-1 text-nowrap">Custo Produto</th>
    <th class="col-1 text-nowrap">Custo Frete</th>
    <th class="col-1 text-nowrap">Comissão</th> 
    <th class="col-1 text-nowrap">Lucro</th>
    <th class="col-3 text-nowrap"></th>
  </tr>
</thead>
<tbody>
</tbody>
  <tr class="calculaTitulos">
    <td class="col-1 text-nowrap"></td>
    <td class="col-1 text-nowrap"><span class="rodapeValorPedidoXquantidade"></span></td>
    <td class="col-1 text-nowrap"><span class="rodapeCustoEnvio"></span></td>
    <td class="col-1 text-nowrap"><span class="rodapePrecoCusto"></span></td>
    <td class="col-1 text-nowrap"><span class="rodapeCustoEnvioSeller"></span></td>
    <td class="col-1 text-nowrap"><span class="rodapeComissao"></td> 
    <td class="col-1 text-nowrap"><span class="rodapeLucro"></td>
    <td class="col-3 text-nowrap"></td>
  </tr>
</table>

</div>
<?php
  }
?>
<script>




    $(function(){

        Carregando('none');

        //$("#codigo_devolucao").mask("9999999999999999");

        const calculadoraRodape = () => {

          rodapeValorPedidoXquantidade = 0;
          rodapeCustoEnvio = 0;
          rodapePrecoCusto = 0;
          rodapeCustoEnvioSeller = 0;
          rodapeComissao = 0;
          rodapeLucro = 0;
          campos = [
            'ValorPedidoXquantidade',
            'CustoEnvio',
            'PrecoCusto',
            'CustoEnvioSeller',
            'Comissao',
            'Lucro',                  
          ];
          console.log(campos)
          $(".opcoes").each(function(){
            if($(this).prop("checked") == true){
                codigo = $(this).val();
                deletado = $(this).attr("del");
                if(!deletado){
                  campos.map(function(campo){
                    eval(`rodape${campo} = ((rodape${campo})*1 + ($("input[opc-${campo}-${codigo}]").attr("valor"))*1)`)
                  })
                }
            }
          })

          campos.map(function(campo){
            eval(`$(".rodape${campo}").html(rodape${campo}.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }))`)
          })     

        }

        calculadoraRodape();

        const calculaTotal = (campo)=>{
          total = 0;
          $(`.moeda[campo="${campo}"]`).each(function(){
            valor = $(this).val();
            valor = valor.replace(",",".");
            total = (valor*1 + total*1);
          })
          totalF = total.toLocaleString('pt-br', {minimumFractionDigits: 2})
          $(`th[campo="${campo}"]`).html(`R$ ${totalF}`);

          des = false;
          $(".desfazer").each(function(){
            if($(this).css("opacity") == "1"){
              des = true;
            }
          })

          if(des == true){
            $(".atualizacao").css("opacity","1");
          }else{
            $(".atualizacao").css("opacity","0");
          }

        }

        /*
        $(".desfazer").off().click(function(){
          codigo = $(this).attr("codigo");
          campo = $(this).attr("campo");
          valor = $(this).attr("valor");
          
          $(`.moeda[opc-${campo}-${codigo}]`).val(valor.replace(".", ','));
          $(this).css("opacity","0");

          $.ajax({
              url:"src/relatorio_novo/index.php",
              type:"POST",
              data:{
                  codigo,
                  campo,
                  valor,
                  acao:'atualizaCampo'
              },
              success:function(dados){
                  //$("#paginaHome").html(dados);
                  // console.log(dados)
              }
          })

          calculaTotal(campo)

        })
        //*/

        $(".moeda").off().blur(function(){
          codigo = $(this).attr("codigo");
          campo = $(this).attr("campo");
          console.log(campo)
          valor = $(this).attr("valor");
          valorN = $(this).val();
          // valorA = $(`.desfazer[opc-${campo}-${codigo}]`).attr("valor");

          if(valorN.replace(",", '.') != valor){

            //$(`.desfazer[opc-${campo}-${codigo}]`).css("opacity","1");

            $.ajax({
              url:"src/relatorio_novo/index.php",
              type:"POST",
              data:{
                  codigo,
                  campo,
                  valor:valorN.replace(",", '.'),
                  acao:'atualizaCampo'
              },
              success:function(dados){
                  $("#paginaHome").html(dados);
                  //console.log(dados)
              }
            })

            //calculaTotal(campo)

          }

        })

        $("span.marcar_todos").off().click(function(){
          if($("input.marcar_todos").prop("checked") == true){
            $("input.marcar_todos").prop("checked", false);
            $(".opcoes").prop("checked", false);
          }else{
            $("input.marcar_todos").prop("checked", true);
            $(".opcoes").prop("checked", true);
          }
          calculadoraRodape();
        })

        $("input.marcar_todos").off().click(function(){
          if($(this).prop("checked") == true){
            $(".opcoes").prop("checked", true);
          }else{
            $(".opcoes").prop("checked", false);
          }
          calculadoraRodape();
        })

        $("input.opcoes").off().click(function(){
          calculadoraRodape();
        })        

        $("#salvar_relatorio").off().click(function(){
          nome_relatorio = $("#nome_relatorio").val();
          codigo_relatorio = $("#codigo_relatorio").val();
          data_relatorio = $("#data_relatorio").val();
          lista = [];
          $(".opcoes").each(function(){
            if($(this).prop("checked") == true){
              lista.push($(this).val());
            }
          })
          if(!nome_relatorio){
            $.alert({
              title:'Nome do Relatório',
              content:'Digite um nome para o relatório!',
              type:'red'
            })
            return false;
          }
          if(lista.length == 0){
            $.alert({
              title:'Registro Selecionados',
              content:'Para gerar um relatório é necessário ter pelo menos um registro selecionado!',
              type:'red'
            })
            return false;
          }          
          Carregando();
          $.ajax({
            url:"src/relatorio_novo/index.php",
            type:"POST",
            data:{
              nome_relatorio,
              codigo_relatorio,
              data_relatorio,
              lista,
              origem:'<?=$_SESSION['buscaOrigem']?>',
              acao:'relatorio'
            },
            success:function(dados){
              $("#paginaHome").html(dados);
              $.alert('Dados salvos com sucesso!')
            }
          })
        })


        $("#anexar_relatorio").off().click(function(){
          codigo_relatorio = $("#relatorio_anexar").val();
          lista = [];
          $(".opcoes").each(function(){
            if($(this).prop("checked") == true){
              lista.push($(this).val());
            }
          })
          if(!codigo_relatorio){
            $.alert({
              title:'Identificação do Relatório',
              content:'Selecione o relatório que deseja anexar!',
              type:'red'
            })
            return false;
          }
          if(lista.length == 0){
            $.alert({
              title:'Registro Selecionados',
              content:'Para gerar um relatório é necessário ter pelo menos um registro selecionado!',
              type:'red'
            })
            return false;
          }          
          Carregando();
          $.ajax({
            url:"src/relatorio_novo/index.php",
            type:"POST",
            data:{
              codigo_relatorio,
              lista,
              acao:'anexar_relatorio'
            },
            success:function(dados){
              $("#paginaHome").html(dados);
              $.alert('Dados salvos com sucesso!')
            }
          })
        })


        $("#abrir_relatorio").off().click(function(){
          Carregando();
          $.ajax({
              url:"src/relatorio_novo/relatorios.php",
              success:function(dados){
                $(".LateralDireita").html(dados);
              }
          })  
        })

        $("#sair_relatorio").off().click(function(){
          Carregando();
          $.ajax({
              url:"src/relatorio_novo/index.php",
              type:"POST",
              data:{
                  sair_relatorio:'sair'
              },
              success:function(dados){
                $("#paginaHome").html(dados);
              }
          })  
        })

        $("button[filtro]").off().click(function(){
          filtro = $(this).attr("filtro");
          buscaOrigem = $("#origem").val();
          buscaDataInicial = $("#data_inicial").val();
          buscaDataFinal = $("#data_final").val();
          $.ajax({
              url:"src/relatorio_novo/index.php",
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

        $("button[limpar]").off().click(function(){
          $.ajax({
              url:"src/relatorio_novo/index.php",
              type:"POST",
              data:{
                  filtro:'limpar',
              },
              success:function(dados){
                  $("#paginaHome").html(dados);
              }
          })
        })
        /*
        $(".atualizacao").off().click(function(){
          Carregando();
          $.ajax({
              url:"src/relatorio_novo/index.php",
              success:function(dados){
                  $("#paginaHome").html(dados);
              }
          })
        })
        //*/
        $("i[editar]").off().click(function(){
          editar = $(this).attr("editar");
          $.ajax({
              url:"src/relatorio_novo/form.php",
              type:"POST",
              data:{
                  editar,
              },
              success:function(dados){
                $(".LateralDireita").html(dados);
              }
          })          
        })

        $(".novo").off().click(function(){
          $.ajax({
              url:"src/relatorio_novo/novo.php",
              type:"POST",
              data:{
                  relatorio:'<?=$_SESSION['modelo_relatorio']?>',
                  conta:'<?=$conta?>',
                  origem:'<?=$_SESSION['buscaOrigem']?>',
                  planilha:'<?=$planilha?>',
              },
              success:function(dados){
                $(".LateralDireita").html(dados);
              }
          })          
        })

        $("i[deletar]").off().click(function(){
            deletar = $(this).attr("deletar");
            $.confirm({
                content:`Deseja realmente excluir o cadastro ?`,
                title:false,
                type:'red',
                buttons:{
                    'SIM':{
                        text:'<i class="fa-solid fa-trash-can"></i> Sim',
                        btnClass:'btn btn-danger',
                        action:function(){
                            $.ajax({
                                url:"src/relatorio_novo/index.php",
                                type:"POST",
                                data:{
                                    deletar
                                },
                                success:function(dados){
                                  //console.log(dados);
                                  $("#paginaHome").html(dados);
                                }
                            })
                        }
                    },
                    'NÃO':{
                        text:'<i class="fa-solid fa-ban"></i> Não',
                        btnClass:'btn btn-success'
                    }
                }
            });

        })


        $("#incluir_devolucao").click(function(){
          codigo_devolucao = $("#codigo_devolucao").val();
          if(!codigo_devolucao){
            $.alert({
              title:"Erro",
              content:"Para realizar a devolução é necessário o preenchimento do código do produto!",
              type:"red"
            })
            return false;
          }
          /*
          if(codigo_devolucao.length != 16){
            $.alert({
              title:"Erro",
              content:"O código do produto está incorreto ou incompleto!",
              type:"red"
            })
            return false;
          }
          //*/

          Carregando();
          $.ajax({
            url:"src/relatorio_novo/devolucao.php",
            type:"POST",
            data:{
              acao:"devolucao",
              codigo_devolucao,
              relatorio:'<?=$_SESSION['modelo_relatorio']?>'
            },
            success:function(dados){


              if(dados == 'erro'){
                    Carregando('none');
                    $.alert(`O código do produto informado <b>${codigo_devolucao}</b> não foi localizado nos registros do banco de dados.<br>Favor verifique o código e tente novamente!`)
                    return false;
                }

              janelaDevolucao = $.dialog({
                title:"Dados da Devolução",
                content:dados,
                columnClass:"col-md-6",
                type:"orange"
              })
            }
          })


        })


      //*
      $("i[devolucaoDesfazer]").click(function(){
        Carregando();
        devolucaoDesfazer = $(this).attr("devolucaoDesfazer");
        $.ajax({
            url:"src/relatorio_novo/index.php",
            type:"POST",
            data:{
                devolucaoDesfazer,
                acao:'devolucaoDesfazer'
            },
            success:function(dados){
                $("#paginaHome").html(dados);
            }
        })

      })
      //*/



    })
</script>