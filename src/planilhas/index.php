<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");
    require '../../planilhas/vendor/autoload.php'; // Caminho para o autoload gerado pelo Composer
    use PhpOffice\PhpSpreadsheet\IOFactory;

    
    if($_POST['deletar']){
      if($_POST['planilha']){
          unlink("../volume/planilhas/".$_POST['planilha']);
      }
      $query = "delete from planilhas where codigo = '{$_POST['deletar']}'";
      mysqli_query($con, $query);
    }

    if($_POST['situacao']){

      $xlsxFilePath = "../volume/planilhas/".$_POST['planilha'];
      $spreadsheet = IOFactory::load($xlsxFilePath);
      $worksheet = $spreadsheet->getActiveSheet();
      $highestRow = $worksheet->getHighestRow();
      $highestColumn = $worksheet->getHighestColumn();
      $campos = [];
      $retorno = [];

      for ($row = 1; $row <= $highestRow; $row++) {
          // for ($col = 'A'; $col <= $highestColumn; $col++) {
          for ($col = 0; $col < 49; $col++) {
              $cellValue = $worksheet->getCell($col . $row)->getValue();
              // Faça algo com o valor da célula, por exemplo, exiba-o
               echo "Valor na célula {$col}{$row}: " . $cellValue . "<br>";
              // if($row == 1){
              //   echo $campos[$col] = $cellValue;
              // }else{
              //   $retorno[][$campos[$col]] = $cellValue;
              // }
          }
      }

      // echo json_encode($retorno);

      // $query = "update planilhas set situacao = '{$_POST['opc']}' where codigo = '{$_POST['situacao']}'";
      // mysqli_query($con, $query);
      exit();
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