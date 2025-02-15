<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

    if($_POST['filtro']){
        $_SESSION['pastaData'] = $_POST['filtro'];
    }

    if(!$_SESSION['pastaData']){
        $_SESSION['pastaData'] = date("Y-m");
    }

    if($_POST['excluir']){

        mysqli_query($con, "delete from relatorio_modelos where codigo = '{$_POST['excluir']}'");
        mysqli_query($con, "UPDATE relatorio set relatorio = '0' where relatorio = '{$_POST['excluir']}'");

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
    span[devolucao]{
        cursor:pointer;
        color:red;
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

<input id="filtro" type="month" class="form-control" style="margin-bottom:20px;" value="<?=$_SESSION['pastaData']?>">

<ul class="list-group">
<?php
    $query = "select *, year(data) as ano, month(data) as mes from relatorio_modelos where data like '{$_SESSION['pastaData']}%' order by data desc";
    $result = mysqli_query($con, $query);
    while($d = mysqli_fetch_object($result)){
?>
  <li class="list-group-item">
    <div class="d-flex justify-content-between">
        <div class="d-flex flex-column bd-highlight">
            <span edit="<?=$d->codigo?>"><i class="fa-regular fa-pen-to-square"></i> <?="{$d->mes}/{$ano} - {$d->nome}"?></span>
            <span devolucao="<?=$d->codigo?>"><i class="fa-solid fa-rotate-left"></i> <?="{$d->mes}/{$ano} - {$d->nome}"?> (Devoluções)</span>
        </div>
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
            campo = 'registros';
            $.ajax({
              url:"src/relatorio_novo/index.php",
              type:"POST",
              data:{
                modelo,
                campo
              },
              success:function(dados){
                $("#paginaHome").html(dados);
                let myOffCanvas = document.getElementById('offcanvasDireita');
                let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                openedCanvas.hide();
              }
          })
        })

        $("span[devolucao]").click(function(){
            modelo = $(this).attr("devolucao");
            campo = 'devolucoes';
            $.ajax({
              url:"src/relatorio_novo/index.php",
              type:"POST",
              data:{
                modelo,
                campo
              },
              success:function(dados){
                $("#paginaHome").html(dados);
                let myOffCanvas = document.getElementById('offcanvasDireita');
                let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                openedCanvas.hide();
              }
          })
        })

        $("#filtro").change(function(){
            filtro = $(this).val()
            $.ajax({
              url:"src/relatorio_novo/relatorios.php",
              type:"POST",
              data:{
                filtro
              },
              success:function(dados){
                $(".LateralDireita").html(dados);
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
                                url:"src/relatorio_novo/relatorios.php",
                                type:"POST",
                                data:{
                                    excluir
                                },
                                success:function(dados){
                                    if(dados == 'atualiza'){
                                        $.ajax({
                                            url:"src/relatorio_novo/index.php",
                                            success:function(dados){
                                                $("#paginaHome").html(dados);
                                            }
                                        })
                                    }else{
                                        Carregando('none');
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