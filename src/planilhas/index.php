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

      $i = 0;
      foreach($result as $l => $dados){
        $valores = [];
        $linhas = [];
        foreach($dados as $campo => $valor){
          $campos[$campo] = $campo;
          $valores[] = "'{$valor}'";
        }
        if($i%3 == 0){
          if($i>0){
            $query = implode(', ', $linhas);
            $linhas = [];
          }else{
            $query;
          }
          $query = "INSERT INTO planilhas (".implode(', ',$campos).") VALUES ".$query;
        }
        $linhas[]= "('".implode("', '", $valores)."')";
      }

      echo $query;


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
                  <th scope="col">Título</th>
                  <th scope="col">Data</th>
                  <th scope="col">Usuário</th>
                  <th scope="col">Situação</th>
                  <th scope="col" width="80">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $query = "select a.*, b.nome as usuario_nome from planilhas a left join usuarios b on a.usuario = b.codigo order by a.data desc";
                  $result = mysqli_query($con, $query);
                  while($d = mysqli_fetch_object($result)){
                ?>
                <tr>
                  <td style="white-space: nowrap;"><?=$d->lote?></td>
                  <td style="white-space: nowrap;"><?=$d->titulo?></td>
                  <td style="white-space: nowrap;"><?=dataBr($d->data)?></td>
                  <td style="white-space: nowrap;"><?=$d->usuario_nome?></td>
                  <td style="white-space: nowrap;">
                    <i 
                      situacao="<?=$d->codigo?>" 
                      planilha="<?=$d->planilha?>" 
                      class="fa-solid fa-file-arrow-up text-<?=(($d->situacao == '1')?'success':'secondary situacao')?>" 
                      style="font-size:30px; <?=(($d->situacao == '1')?false:'cursor:pointer')?>"
                    ></i>
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

            $.ajax({
                url:"src/planilhas/index.php",
                type:"POST",
                data:{
                    situacao,
                    planilha
                },
                success:function(dados){
                  $.alert({
                    content:dados,
                    classColumn:'col-md-12'
                  });
                    // $("#paginaHome").html(dados);
                }
            })

        });







    })
</script>