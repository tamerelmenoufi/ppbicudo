<?php
    session_start();

    if($_SERVER["HTTP_HOST"] == 'capital.mohatron.com'){
        header("location:http://painel.capitalsolucoesam.com.br");
        exit();
    }

    // include("connect_local.php");

    include("/capitalinc/connect.php");
    $con = AppConnect('capital');

    $ConfWappNumero = '12266700079';

    //Configurações diversas
    include("/capitalinc/config.php");

    // include("/appinc/connect.php");
    include("fn.php");

    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/vendor/api/vctex.php");
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/vendor/api/facta.php");

    //Bibliotecas de comunicação
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/vendor/wapp/wgw/classes.php");

    $md5 = md5(date("YmdHis"));

    // $localPainel = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"]."/painel/";
    // $localSite = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"]."/site/";

    // $localPainel = "http://capital.mohatron.com/painel/";
    // $localSite = "http://capital.mohatron.com/site/";


    $localPainel = "https://painel.capitalsolucoesam.com.br/";
    $localSite = "https://capitalsolucoesam.com.br/";

    