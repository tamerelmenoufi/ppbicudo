<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

    if($_POST['acao'] == 'salvar'){

      echo $query = "update metas set 
                                meta = '{$_POST['meta']}', 
                                p1 = '".str_replace(",",".",$_POST['p1'])."', 
                                p2 = '".str_replace(",",".",$_POST['p2'])."', 
                                p3 = '".str_replace(",",".",$_POST['p3'])."' 
                        where codigo = '{$_POST['codigo']}'";
        mysqli_query($con, $query);
        exit();
    }

    
    $query = "select *, concat(month(periodo),'/',year(periodo)) as periodo from metas where periodo = '{$_POST['periodo']}'";
    $result = mysqli_query($con, $query);

    if(!mysqli_num_rows($result)){
        $q = "INSERT INTO metas set periodo = '{$_POST['periodo']}'";
        mysqli_query($con, $q);
        $result = mysqli_query($con, $query);
    }

    $d = mysqli_fetch_object($result);

?>
<style>
  .titulo<?=$md5?>{
    position:fixed;
    top:7px;
    margin-left:50px;
  }
  .deletado{
    display:<?=(($d->deletado)?'block':'none')?>
  }
</style>

<h3 class="titulo<?=$md5?>">Editar Metas de <?=$d->periodo?></h3>

    <form id="acaoMenu">

        <div class="form-floating mb-3">
            <div class="form-control"><?=$d->periodo?></div>
            <label for="codigoPedido">Período</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" require name="meta" id="meta" class="form-control" placeholder="Meta" value="<?=number_format($d->meta,2,',',false)?>">
            <label for="meta">Meta*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" require name="p1" id="p1" class="form-control" placeholder="Percentual 1" value="<?=number_format($d->p1,2,',',false)?>">
            <label for="p1">Percentual 1*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" require name="p2" id="p2" class="form-control" placeholder="Percentual 2" value="<?=number_format($d->p2,2,',',false)?>">
            <label for="p2">Percentual 2*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" require name="p3" id="p3" class="form-control" placeholder="Percentual 3" value="<?=number_format($d->p3,2,',',false)?>">
            <label for="p3">Percentual 3*</label>
        </div>        

        <button cancelar type="button" data-bs-dismiss="offcanvas" class="btn btn-danger mt-3"> <i class="fa fa-cancel"></i> Cancelar</button>
        <button type="submit" class="btn btn-primary mt-3"> <i class="fa fa-save"></i> Salvar</button>

        <input type="hidden" id="acao" name="acao" value="salvar" >
        <input type="hidden" id="codigo" name="codigo" value="<?=$d->codigo?>" >

    </form>

<script>


    $(function(){

      Carregando('none');

      $( "form" ).on( "submit", function( event ) {
        Carregando();
        event.preventDefault();
        data = $( this ).serialize();
        $.ajax({
          url:"src/relatorio/config.php",
          type:"POST",
          data,
          success:function(dados){
            console.log(dados)
            $.ajax({
                url:"src/relatorio/metas.php",
                success:function(dados){
                    $("#paginaHome").html(dados);
                    let myOffCanvas = document.getElementById('offcanvasDireita');
                    let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                    openedCanvas.hide();
                }
            });     

          }
        });
      });

      $("button[cancelar]").click(function(){
          let myOffCanvas = document.getElementById('offcanvasDireita');
          let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
          openedCanvas.hide();
      })




    })
</script>