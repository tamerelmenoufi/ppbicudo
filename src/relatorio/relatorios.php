<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");
?>
<style>
    span[edit]{
        cursor:pointer;
    }
</style>
<ul class="list-group">
<?php
    $query = "select * from relatorio_modelos order by data desc";
    $result = mysqli_query($con, $query);
    while($d = mysqli_fetch_object($result)){
?>
  <li class="list-group-item">
    <div class="d-flex justify-content-between">
        <span edit="<?=$d->codigo?>"><i class="fa-regular fa-pen-to-square"></i> <?=$d->nome?></span>
        <i class="fa-regular fa-trash-can"></i>
    </div>
  </li>
<?php
    }
?>
</ul>

<script>
    $(function(){
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
    })
</script>