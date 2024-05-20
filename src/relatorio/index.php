<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");


    function editarValores($d){
?>
    <div class="d-flex justify-content-start">
      R$ 
      <!-- <span opc="<?=$d['codigo']?>"><?=number_format($d['valor'],2,',',false)?></span> -->
      <input type="text" class="moeda" campo="<?=$d['campo']?>" valor="<?=$d['valor']?>" codigo="<?=$d['codigo']?>" value="<?=number_format($d['valor'],2,',',false)?>" inputmode="numeric" >
      <i class="fa-solid fa-arrow-rotate-left desfazer" campo="<?=$d['campo']?>" valor="<?=$d['valor']?>" codigo="<?=$d['codigo']?>"></i>
    </div>
<?php
    }


    $opcoes = [];

    if($_POST['acao'] == 'relatorio'){

      //data	nome	registros
      $registros = json_encode($_POST['lista']);
      if($_POST['codigo_relatorio']){
        $query = "UPDATE relatorio_modelos set nome = '{$_POST['nome_relatorio']}', data = NOW(), registros = '{$registros}' where codigo = '{$_POST['codigo_relatorio']}'";
        mysqli_query($con, $query);
        $_SESSION['modelo_relatorio'] = $_POST['codigo_relatorio'];
      }else{
        $query = "INSERT INTO relatorio_modelos set nome = '{$_POST['nome_relatorio']}', data = NOW(), registros = '{$registros}', origem = '{$_POST['origem']}'";
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

      $lista1 = mysqli_fetch_object(mysqli_query($con, "select registros from relatorio_modelos where codigo = '{$_POST['codigo_relatorio']}'"));
      $lista_completa = array_merge(json_decode($lista1->registros), $_POST['lista']);

      $registros = json_encode($lista_completa);

      $query = "UPDATE relatorio_modelos set registros = '{$registros}' where codigo = '{$_POST['codigo_relatorio']}'";
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

    if($_POST['sair_relatorio']){
      $_SESSION['modelo_relatorio'] = false;
    }

    if($_POST['modelo']){
      $_SESSION['modelo_relatorio'] = $_POST['modelo'];
    }

    if($_SESSION['modelo_relatorio']){
      $busca_disabled = 'disabled';
      $_SESSION['buscaOrigem'] = false;
      $_SESSION['buscaDataInicial'] = false;
      $_SESSION['buscaDataFinal'] = false;

      $q = "select * from relatorio_modelos where codigo = '{$_SESSION['modelo_relatorio']}'";
      $rel = mysqli_fetch_object(mysqli_query($con, $q));

      $registros = json_decode($rel->registros);
      $opcoes = $registros;
      $registros = implode(", ", $registros); 
      $where = " and codigo in ({$registros})";

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
                    <label class="input-group-text" for="inputGroupFile01">Relatório</label>
                    <input type="text" id="nome_relatorio" class="form-control" value="<?=$rel->nome?>" >
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
                    <th scope="col"><i 
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
                    <th scope="col" colspan="11"></th>
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
                      <input type="checkbox" class="opcoes" <?=((in_array($d->codigo, $opcoes))?'checked':false)?> value="<?=$d->codigo?>">
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
                    <td class="text-nowrap"><?=editarValores(['valor'=>$d->ValorPedidoXquantidade, 'campo'=>'ValorPedidoXquantidade', 'codigo'=>$d->codigo])?></td>
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

          </div>
        </div>
      </div>
    </div>

  </div>
</div>


<script>
    $(function(){
        Carregando('none');

        $(".desfazer").click(function(){
          codigo = $(this).attr("codigo");
          campo = $(this).attr("campo");
          valor = $(this).attr("valor");
          total = $(`th[campo="${campo}"]`).attr("valor");
          valorN = $(`.moeda[codigo="${codigo}"]`).val().replace(",", '.');

          total = (total*1 - valorN*1 + valor*1);
          totalF = total.replace(".",".");
          $(`th[campo="${campo}"]`).attr("valor", total);
          $(`th[campo="${campo}"]`).html(`R$ ${totalF}`);

          $(`.moeda[codigo="${codigo}"]`).val(valor.replace(".", ','));
          $(this).css("opacity","0");

        })

        $(".moeda").blur(function(){
          codigo = $(this).attr("codigo");
          campo = $(this).attr("campo");
          console.log(campo)
          valor = $(this).attr("valor");
          valorN = $(this).val();
          total = $(`th[campo="${campo}"]`).attr("valor");
          console.log(total)
          valorA = $(`.desfazer[codigo="${codigo}"]`).attr("valor");

          if(valorN.replace(",", '.') != valorA){
            $(`.desfazer[codigo="${codigo}"]`).css("opacity","1");
            console.log(total);
            total = (total*1 - valor*1 + (valorN.replace(",", '.'))*1);
            console.log(total);
            totalF = total.toLocaleString('pt-br', {minimumFractionDigits: 2})
            $(`th[campo="${campo}"]`).attr("valor", total);
            $(`th[campo="${campo}"]`).html(`R$ ${totalF}`);

          }

        })

        $("span.marcar_todos").click(function(){
          if($("input.marcar_todos").prop("checked") == true){
            $("input.marcar_todos").prop("checked", false);
            $(".opcoes").prop("checked", false);
          }else{
            $("input.marcar_todos").prop("checked", true);
            $(".opcoes").prop("checked", true);
          }
        })

        $("input.marcar_todos").click(function(){
          if($(this).prop("checked") == true){
            $(".opcoes").prop("checked", true);
          }else{
            $(".opcoes").prop("checked", false);
          }
        })

        $("#salvar_relatorio").click(function(){
          nome_relatorio = $("#nome_relatorio").val();
          codigo_relatorio = $("#codigo_relatorio").val();
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
            url:"src/relatorio/index.php",
            type:"POST",
            data:{
              nome_relatorio,
              codigo_relatorio,
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


        $("#anexar_relatorio").click(function(){
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
            url:"src/relatorio/index.php",
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


        $("#abrir_relatorio").click(function(){
          Carregando();
          $.ajax({
              url:"src/relatorio/relatorios.php",
              success:function(dados){
                $(".LateralDireita").html(dados);
              }
          })  
        })

        $("#sair_relatorio").click(function(){
          Carregando();
          $.ajax({
              url:"src/relatorio/index.php",
              type:"POST",
              data:{
                  sair_relatorio:'sair'
              },
              success:function(dados){
                $("#paginaHome").html(dados);
              }
          })  
        })

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