<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    if($_GET['s']){
        $_SESSION = [];
        header("location:./");
        exit();
    }

    $app = (($_GET['app'])?:'financeira');

    if($_SESSION['ProjectPainel']->perfil == 'adm'){
        $url = "{$app}/home/index.php";
    }else if($_SESSION['ProjectPainel']->perfil == 'financeiro'){
        $url = "financeira/home/index.php";
    }else if($_SESSION['ProjectPainel']->perfil == 'site'){
        $url = "site/home/index.php";
    }else if($_SESSION['ProjectPainel']->perfil == 'consulta'){
        $url = "{$app}/home/index.php";
    }else{
        $url = "src/login/index.php";
    }
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="img/icone.png" rel="icon">
    <title>capitalsolucoes - Painel de controle</title>
    <?php
    include("lib/header.php");
    ?>
  </head>
  <style>
body {

    background: url(img/fundopncred.jpg);
    background-repeat: no-repeat center fixed;
    background-size: cover;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
}

</style>

  <body>

    <div class="Carregando">
        <div><i class="fa-solid fa-rotate fa-pulse"></i></div>
    </div>

    <div class="CorpoApp"></div>

    <?php
    include("lib/footer.php");
    ?>

    <script>
        $(function(){
            Carregando();
            $.ajax({
                url:"<?=$url?>",
                success:function(dados){
                    $(".CorpoApp").html(dados);
                }
            });

            setInterval(() => {
                $.ajax({
                    url:"lib/sessao.php",
                    success:function(dados){
                        $("body").attr("sessao",dados);
                    }
                });                
            }, 5000);
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