<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

    require '../../planilhas/vendor/autoload.php'; // Caminho para o autoload gerado pelo Composer
    use PhpOffice\PhpSpreadsheet\IOFactory;

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
      $result = json_decode($result);

      $quantidade = count($result);

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

      foreach($result as $l => $dados){
        $query = "INSERT INTO relatorio SET ";
        $valores = [];
        $valores[] = "`planilha` = '{$_POST['situacao']}'";
        foreach($dados as $i => $val){
          if(!in_array($i,$remove)){
            if($i == 'Porcentagem'){
              $valores[] = "`{$i}` = '".substr($val,0,-1)."'";
            }else{
              $valores[] = "`{$i}` = '{$val}'";
            }
          }
        }
        $query .= implode(", ",$valores);
        // echo $query."<hr>";
        mysqli_query($con, $query);
      }

      mysqli_query($con, "update planilhas set situacao = '1' where codigo = '{$_POST['situacao']}'");

      echo json_encode([
        'mensagem' => 'Dados importados com sucesso!',
        'quantidade' => $quantidade
      ]);

      exit();
    }

    if($_POST['deletar']){
      if($_POST['planilha']){
          unlink("../volume/planilhas/".$_POST['planilha']);
      }
      $query = "delete from planilhas where codigo = '{$_POST['deletar']}'";
      mysqli_query($con, $query);
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
                  $query = "select a.*, b.nome as usuario_nome, o.nome as origem, (select count(*) from relatorio where planilha = a.codigo) as registros from planilhas a left join usuarios b on a.usuario = b.codigo left join origens o on a.origem = o.codigo order by a.data desc";
                  $result = mysqli_query($con, $query);
                  while($d = mysqli_fetch_object($result)){
                ?>
                <tr>
                  <td style="white-space: nowrap;"><?=strtoupper($d->lote)?></td>
                  <td style="white-space: nowrap;"><?=$d->origem?></td>
                  <td style="white-space: nowrap;"><?=dataBr($d->data)?></td>
                  <td style="white-space: nowrap;"><?=$d->usuario_nome?></td>
                  <td style="white-space: nowrap;">
                    <i 
                      situacao="<?=$d->codigo?>" 
                      planilha="<?=$d->planilha?>" 
                      class="fa-solid fa-file-arrow-up text-<?=(($d->situacao == '1')?'success':'secondary situacao')?>" 
                      style="font-size:30px; <?=(($d->situacao == '1')?false:'cursor:pointer')?>"
                    ></i> <?=(($d->registros)?:false)?>
                  </td>
                  <td style="white-space: nowrap;">
                    <button class="btn btn-danger btn-sm" deletar="<?=$d->codigo?>" planilha="<?=$d->planilha?>">
                    <i class="fa-solid fa-trash-can"></i> Excluir
                    </button>
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
            $.confirm({
                content:"Deseja realmente excluir o cadastro ?",
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
            if(!situacao || !planilha){
              console.log('Entro no erro!')
              return false;
            }
            obj = $(this);
            $.ajax({
                url:"src/planilhas/index.php",
                type:"POST",
                typeData:"JSON",
                data:{
                    situacao,
                    planilha
                },
                success:function(dados){
                  console.log(dados);
                  $.alert({
                    content:dados.mensagem,
                    classColumn:'col-md-12'
                  });
                  obj.removeClass("color-secondary");
                  obj.addClass("color-success");
                  obj.css("cursor","");
                  obj.attr("planilha","");
                  obj.attr("situacao","");
                  obj.parent("td").append(dados.quantidade);

                    // $("#paginaHome").html(dados);
                }
            })

        });







    })
</script>