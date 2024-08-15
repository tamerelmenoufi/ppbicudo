<?php

    $dados = file_get_contents("dados.csv");

    $linhas = explode("\n", $dados);

    foreach($linhas as $i => $colunas){

        echo $colunas."<br>";


    }