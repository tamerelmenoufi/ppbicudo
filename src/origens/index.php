<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

    if($_POST['delete']){
      // $query = "delete from origens where codigo = '{$_POST['delete']}'";
      if($_POST['imagem']) unlink("../volume/origens/{$_POST['imagem']}");
      $query = "update origens set deletado = '1' where codigo = '{$_POST['delete']}'";
      mysqli_query($con,$query);
    }

    if($_POST['situacao']){
      $query = "update origens set status = '{$_POST['opc']}' where codigo = '{$_POST['situacao']}'";
      mysqli_query($con,$query);
      exit();
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
    white-space: nowrap;
  }
  .label{
    font-size:10px;
    color:#a1a1a1;
  }
</style>
<div class="col">
  <div class="m-3">

    <div class="row">
      <div class="col">
        <div class="card">
          <h5 class="card-header">Lista de Usuários</h5>
          <div class="card-body">

            <div class="d-flex justify-content-end mb-3">
                <button
                    novoCadastro
                    class="btn btn-success btn-sm"
                    data-bs-toggle="offcanvas"
                    href="#offcanvasDireita"
                    role="button"
                    aria-controls="offcanvasDireita"
                    style="margin-left:20px;"
                >Novo</button>
            </div>

            <div class="table-responsive d-none d-md-block">
              <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th scope="col">Origem</th>
                    <th scope="col">Situação</th>
                    <th scope="col">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $query = "select a.*, (select count(*) from planilhas where origem = a.codigo) as quantidade from origens a where a.deletado != '1' order by a.nome asc";
                    $result = mysqli_query($con,$query);
                    
                    while($d = mysqli_fetch_object($result)){
                  ?>
                  <tr>
                    <td class="w-100"><?=$d->nome?></td>
                    <td>

                    <div class="form-check form-switch">
                      <input class="form-check-input situacao" type="checkbox" <?=(($d->status)?'checked':false)?> situacao="<?=$d->codigo?>">
                    </div>

                    </td>
                    <td>
                      <button
                        class="btn btn-primary"
                        edit="<?=$d->codigo?>"
                        data-bs-toggle="offcanvas"
                        href="#offcanvasDireita"
                        role="button"
                        aria-controls="offcanvasDireita"
                      >
                        Editar
                      </button>
                      <?php
                      if(!$d->quantidade){
                      ?>
                      <button class="btn btn-danger" delete="<?=$d->codigo?>" imagem='<?=$d->imagem?>'>
                        Excluir
                      </button>
                      <?php
                      }
                      ?>
                    </td>
                  </tr>
                  <?php
                    }
                  ?>
                </tbody>
              </table>
            </div>


            <div class="d-block d-md-none d-lg-none d-xl-none d-xxl-none">
            <?php

                    $query = "select a.*, (select count(*) from relatorio where planilha = a.codigo) as quantidade from origens a where a.deletado != '1' order by a.nome asc";
                    $result = mysqli_query($con,$query);
                  
                  while($d = mysqli_fetch_object($result)){
                ?>
                <div class="card mb-3 p-3">
                    <div class="row">
                      <div class="col-12 d-flex justify-content-end">
                        <div class="form-check form-switch">
                          <input class="form-check-input situacao" type="checkbox" <?=(($d->status)?'checked':false)?> situacao="<?=$d->codigo?>">
                          Situação
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-12">
                        <label class="label">Nome</label>
                        <div><?=$d->nome?></div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-6 p-2">
                        <button
                          class="btn btn-primary w-100"
                          edit="<?=$d->codigo?>"
                          data-bs-toggle="offcanvas"
                          href="#offcanvasDireita"
                          role="button"
                          aria-controls="offcanvasDireita"
                        >
                          Editar
                        </button>
                      </div>
                      <?php
                      if(!$d->quantidade){
                      ?>
                      <div class="col-6 p-2">
                        <button class="btn btn-danger w-100" delete="<?=$d->codigo?>" imagem='<?=$d->imagem?>'>
                          Excluir
                        </button>
                      </div>
                      <?php
                      }
                      ?>
                    </div>
                  </div>
                <?php
                  }
                ?>
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
                url:"src/origens/form.php",
                success:function(dados){
                    $(".LateralDireita").html(dados);
                }
            })
        })


        $("button[edit]").click(function(){
            cod = $(this).attr("edit");
            $.ajax({
                url:"src/origens/form.php",
                type:"POST",
                data:{
                  cod
                },
                success:function(dados){
                    $(".LateralDireita").html(dados);
                }
            })
        })

        

        $("button[delete]").click(function(){
            deletar = $(this).attr("delete");
            imagem = $(this).attr("imagem");
            $.confirm({
                content:"Deseja realmente excluir o cadastro ?",
                title:false,
                buttons:{
                    'SIM':function(){
                        $.ajax({
                            url:"src/origens/index.php",
                            type:"POST",
                            data:{
                                delete:deletar,
                                imagem
                            },
                            success:function(dados){
                                $("#paginaHome").html(dados);
                            }
                        })
                    },
                    'NÃO':function(){

                    }
                }
            });

        })


        $(".situacao").change(function(){

            situacao = $(this).attr("situacao");
            opc = false;

            if($(this).prop("checked") == true){
              opc = '1';
            }else{
              opc = '0';
            }

            $.ajax({
                url:"src/origens/index.php",
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