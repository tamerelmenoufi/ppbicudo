<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

    if($_SESSION['appLogin']){
        $url = "src/home/index.php";
    }else{
        $url = "src/login/index.php";
    }
    
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="img/icone.png">
    <title>PPBICUDO - Painel de Controle</title>
    <?php
    include("lib/header.php");
    ?>
  </head>
  <body translate="no">

    <div class="Carregando">
        <div><i class="fa-solid fa-rotate fa-pulse"></i></div>
    </div>

    <div class="CorpoApp"></div>

    <?php
    include("lib/footer.php");
    ?>

    <script>
        $(function(){
            // Carregando();
            $.ajax({
                url:"<?=$url?>",
                success:function(dados){
                    $(".CorpoApp").html(dados);
                }
            });
        })


        //Jconfirm
        jconfirm.defaults = {
            typeAnimated: true,
            type: "blue",
            smoothContent: true,
        }

    </script>

  </body>
</html>