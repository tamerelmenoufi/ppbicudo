<?php
    session_start();
    include("/ppbicudoinc/connect.php");
    include("fn.php");
    $con = AppConnect('app');
    $conEstoque = AppConnect('app');
    $md5 = md5(date("YmdHis"));

    $urlPainel = 'http://ppbicudo.mohatron.com/';
