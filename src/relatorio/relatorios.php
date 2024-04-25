<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

    if($_POST['excluir']){

        mysqli_query($con, "delete from relatorio_modelos where codigo = '{$_POST['excluir']}'");

        if($_POST['excluir'] == $_SESSION['modelo_relatorio']){
            $_SESSION['modelo_relatorio'] = false;
            echo "atualiza";
        }

        exit();

    }
?>
<style>
    span[edit]{
        cursor:pointer;
    }
    i[excluir]{
        cursor:pointer;
    }

    .Titulo<?=$md5?>{
        position:absolute;
        left:60px;
        top:8px;
        z-index:0;
    }
</style>
<h4 class="Titulo<?=$md5?>">Modelos de Relatórios</h4>

<ul class="list-group">
<?php
    $query = "select * from relatorio_modelos order by data desc";
    $result = mysqli_query($con, $query);
    while($d = mysqli_fetch_object($result)){
?>
  <li class="list-group-item">
    <div class="d-flex justify-content-between">
        <span edit="<?=$d->codigo?>"><i class="fa-regular fa-pen-to-square"></i> <?=$d->nome?></span>
        <i excluir="<?=$d->codigo?>" class="fa-regular fa-trash-can"></i>
    </div>
  </li>
<?php
    }
?>
</ul>

<script>
    $(function(){
        Carregando('none');
        $("span[edit]").click(function(){
            modelo = $(this).attr("edit");
            $.ajax({
              url:"src/relatorio/index.php",
              type:"POST",
              data:{
                modelo
              },
              success:function(dados){
                $("#paginaHome").html(dados);
                let myOffCanvas = document.getElementById('offcanvasDireita');
                let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                openedCanvas.hide();
              }
          })
        })

        $("i[excluir]").click(function(){
            obj = $(this).parent("div").parent("li");
            excluir = $(this).attr("excluir");
            $.confirm({
                title:"Excluir Relatório",
                content:"Confirma a exclusão do Relatório?",
                buttons:{
                    'sim':{
                        text:"SIM",
                        btnClass:'btn btn-danger btn-sm',
                        action:function(){
                            Carregando()
                            obj.remove();
                            $.ajax({
                                url:"src/relatorio/relatorios.php",
                                type:"POST",
                                data:{
                                    excluir
                                },
                                success:function(dados){
                                    console.log(dados)
                                    if(dados == 'atualiza'){
                                        $.ajax({
                                            url:"src/relatorio/index.php",
                                            success:function(dados){
                                                $("#paginaHome").html(dados);
                                            }
                                        })
                                    }
                                }
                            })
                        }
                    },
                    'nao':{
                        text:"NÃO",
                        btnClass:'btn btn-primary btn-sm',
                        action:function(){

                        }
                    }
                }
            })

        })
    })
</script>