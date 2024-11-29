<?php
    error_reporting(0);
    session_start();
    include("/ppbicudoinc/connect.php");
    include("fn.php");
    $con = AppConnect('app');
    $conEstoque = AppConnect('app');
    $md5 = md5(date("YmdHis"));

    $urlPainel = 'https://ppbicudo.mohatron.com/';
    //$urlPainel = 'http://206.81.10.165:8088/';
