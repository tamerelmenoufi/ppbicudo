<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

    if($_POST['deletar']){
        if($_POST['imagem']){
            unlink("../volume/planilhas/".$_POST['imagem']);
        }
        $query = "delete from planilhas where codigo = '{$_POST['deletar']}'";
        mysqli_query($con, $query);
      }

      if($_POST['situacao']){
        $query = "update planilhas set situacao = '{$_POST['opc']}' where codigo = '{$_POST['situacao']}'";
        mysqli_query($con, $query);
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
                  <th scope="col">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $query = "select * from planilhas order by data desc";
                  $result = mysqli_query($con, $query);
                  while($d = mysqli_fetch_object($result)){
                ?>
                <tr>
                  <td style="white-space: nowrap;"><?=$d->lote?></td>
                  <td style="white-space: nowrap;"><?=$d->titulo?></td>
                  <td style="white-space: nowrap;"><?=$d->data?></td>
                  <td style="white-space: nowrap;"><?=$d->usuario?></td>
                  <td style="white-space: nowrap;"><?=$d->situacao?></td>
                  <td style="white-space: nowrap;">
                    <button class="btn btn-danger btn-sm" deletar="<?=$d->codigo?>" imagem="<?=$d->imagem?>">
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
            imagem = $(this).attr("imagem");
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
                                url:"src/planilhas/lista.php",
                                type:"POST",
                                data:{
                                    deletar,
                                    imagem
                                },
                                success:function(dados){
                                  console.log(dados);
                                  $("div[lista]").html(dados);
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


        $(".situacao").change(function(){

            situacao = $(this).attr("situacao");
            status = $(this).prop("checked");
            if(status === 'true'){
              opc = '1';
            }else{
              opc = '0';
            }

            $.ajax({
                url:"src/planilhas/lista.php",
                type:"POST",
                data:{
                    situacao,
                    opc
                },
                success:function(dados){
                    // $("#paginaHome").html(dados);
                }
            })

        });







    })
</script>