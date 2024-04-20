<?php
    function AppConnect($db = 'capital'){
        $con = mysqli_connect("capitalsolucoes.com.br:8033","root","","SenhaDoBanco");
        mysqli_set_charset( $con, 'utf8');
        return $con;
    }