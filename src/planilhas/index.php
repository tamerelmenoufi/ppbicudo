<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

    if($_POST['situacao']){

      $postdata = http_build_query(
          array(
              'arquivo' => "../src/volume/planilhas/".$_POST['planilha']
          )
      );
      $opts = array('http' =>
          array(
              'method'  => 'POST',
              'header'  => 'Content-Type: application/x-www-form-urlencoded',
              'content' => $postdata
          )
      );
      $context  = stream_context_create($opts);
      $result = file_get_contents("{$urlPainel}planilhas/ler.php", false, $context);
      $json = $result;
      $result = json_decode($result);

      $remove = [
        'dataAprovacao',
        'Status',
        'idItem',
        'precoUnitario',
        'quantidade',
        'condicao',
        'tipoAnuncio',
        'CarrinhoMercadoLivre',
        'OutrasEntradas',
        'TarifaEnvio',
        'Sobrou',
        'PrecoBase',
        'TotalLiquido'
      ];

      $quantidade = 0;
      $comandos = [];
      foreach($result as $l => $dados){
        $query = "INSERT INTO relatorio SET ";
        $valores = [];
        $valores[] = "`planilha` = '{$_POST['situacao']}'";
        $valores[] = "`origem` = '{$_POST['origem']}'";
        foreach($dados as $i => $val){
          if(!in_array($i,$remove)){
            if($i == 'Porcentagem'){
              $valores[] = "`{$i}` = '".substr($val,0,-1)."'";
            }else{
              $valores[] = "`{$i}` = '".addslashes($val)."'";
            }
          }
        }
        $query .= implode(", ",$valores);
        // echo $query."<hr>";
        $comandos[] = $query;
        if(mysqli_query($con, $query)){
          $quantidade++;
        }
      }

      mysqli_query($con, "update planilhas set situacao = '1' where codigo = '{$_POST['situacao']}'");

      echo json_encode([
        'mensagem' => 'Dados importados com sucesso!',
        'quantidade' => $quantidade,
        'comandos' => $comandos,
        'json' => $json
      ]);

      exit();
    }

    if($_POST['deletar']){
      if($_POST['planilha']){
          unlink("../volume/planilhas/".$_POST['planilha']);
      }
      $query = "delete from planilhas where codigo = '{$_POST['deletar']}'";
      if(mysqli_query($con, $query)){
        mysqli_query($con, "DELTE FROM `relatorio` where planilha = '{$_POST['deletar']}'");
      }
    }

?>
<style>



</style>



<div class="col">
  <div class="m-3">
    <div class="row">
      <div class="col">
        <div class="card">
          <h5 class="card-header">Lista de Planilhas</h5>
          <div class="card-body">
            <div style="display:flex; justify-content:end">
                <button
                    novoCadastro
                    class="btn btn-success"
                    data-bs-toggle="offcanvas"
                    href="#offcanvasDireita"
                    role="button"
                    aria-controls="offcanvasDireita"
                ><i class="fa-regular fa-file"></i> Novo</button>
            </div>

            <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th scope="col">Lote</th>
                  <th scope="col">Origem</th>
                  <th scope="col">Data</th>
                  <th scope="col">Usuário</th>
                  <th scope="col">Situação</th>
                  <th scope="col" width="80">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $query = "select a.*, b.nome as usuario_nome, o.nome as origem_nome from planilhas a left join usuarios b on a.usuario = b.codigo left join origens o on a.origem = o.codigo order by a.data desc limit 10";
                  // $query = "select * from planilhas order by data desc limit 100";
                  $result = mysqli_query($con, $query);
                  while($d = mysqli_fetch_object($result)){
                    $q = "select count(*) as registros from relatorio where planilha = '{$d->codigo}'";
                    $r = mysqli_query($con, $q);
                    $d1 = mysqli_fetch_object($r);
                ?>
                <tr>
                  <td style="white-space: nowrap;"><?=strtoupper($d->lote)?></td>
                  <td style="white-space: nowrap;"><?=$d->origem_nome?></td>
                  <td style="white-space: nowrap;"><?=dataBr($d->data)?></td>
                  <td style="white-space: nowrap;"><?=$d->usuario_nome?></td>
                  <td style="white-space: nowrap;">
                    <i 
                      situacao="<?=$d->codigo?>" 
                      planilha="<?=$d->planilha?>" 
                      origem="<?=$d->origem?>" 
                      class="fa-solid fa-file-arrow-up text-<?=(($d->situacao == '1' and $d1->registros)?'success':'secondary situacao')?>" 
                      style="font-size:30px; <?=(($d->situacao == '1' and $d1->registros)?false:'cursor:pointer')?>"
                    ></i> <?=(($d1->registros)?:false)?>
                  </td>
                  <td acoes style="white-space: nowrap;">
                  <?php
                  // if(!$d1->registros){
                  ?>
                    <button class="btn btn-danger btn-sm" deletar="<?=$d->codigo?>" planilha="<?=$d->planilha?>" registros="<?=$d1->registros?>">
                    <i class="fa-solid fa-trash-can"></i> Excluir
                    </button>
                  <?php
                  // }
                  ?>
                  </td>
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
</div>

<script>
    $(function(){

        Carregando('none');


        $("button[novoCadastro]").click(function(){
            $.ajax({
                url:"src/planilhas/form.php",
                success:function(dados){
                    $(".LateralDireita").html(dados);
                }
            })
        })

        $("button[deletar]").click(function(){
            deletar = $(this).attr("deletar");
            planilha = $(this).attr("planilha");
            registros = $(this).attr("registros");
            if(registros*1 > 0){
              msg = `Esta operação irá excluir ${registros} registros do banco de dados<br>Deseja realmente excluir o cadastro ?`; 
            }else{
              msg = `Deseja realmente excluir o cadastro ?`; 
            }
            $.confirm({
                content:msg,
                title:false,
                type:'red',
                buttons:{
                    'SIM':{
                        text:'<i class="fa-solid fa-trash-can"></i> Sim',
                        btnClass:'btn btn-danger',
                        action:function(){
                            $.ajax({
                                url:"src/planilhas/index.php",
                                type:"POST",
                                data:{
                                    deletar,
                                    planilha
                                },
                                success:function(dados){
                                  console.log(dados);
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


        $(".situacao").click(function(){

            situacao = $(this).attr("situacao");
            planilha = $(this).attr("planilha");
            origem = $(this).attr("origem");
            if(!situacao || !planilha || !origem){
              console.log('Entro no erro!')
              return false;
            }
            obj = $(this);
            $.ajax({
                url:"src/planilhas/index.php",
                type:"POST",
                dataType:"JSON",
                mimeType: 'multipart/form-data',
                data:{
                    situacao,
                    planilha,
                    origem
                },
                success:function(dados){
                  console.log(dados);
                  $.alert({
                    title:"Alerta de Importação",
                    content:dados.mensagem,
                    // columnClass:'col-md-12'
                  });
                  obj.removeClass("text-secondary");
                  obj.addClass("text-success");
                  obj.css("cursor","");
                  obj.attr("planilha","");
                  obj.attr("situacao","");
                  obj.attr("origem","");
                  obj.parent("td").append(dados.quantidade);
                  if(dados.quantidade > 0){
                    //obj.parent("td").parent("tr").children("td[acoes]").children("button[deletar]").remove();
                  }
                    // $("#paginaHome").html(dados);
                },
                error:function(){
                  console.log('erro')
                }
            })

        });







    })
</script>