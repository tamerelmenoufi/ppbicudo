<?php


function formatarTelefoneCelular($telefone, $dddPadrao = '92') {
    // Remove todos os caracteres que não são dígitos
    $numeros = preg_replace('/\D/', '', $telefone);
    
    // Verifica se o número tem o DDD (11 dígitos com o nono dígito)
    if (strlen($numeros) == 11) {
        $ddd = substr($numeros, 0, 2);
        $numero = substr($numeros, 2);
    } elseif (strlen($numeros) == 10) {
        // Se tem 10 dígitos, adiciona o nono dígito
        $ddd = substr($numeros, 0, 2);
        $numero = '9' . substr($numeros, 2);
    } elseif (strlen($numeros) == 9) {
        // Se tem 9 dígitos, assume que já tem o nono dígito
        $ddd = $dddPadrao;
        $numero = $numeros;
    } elseif (strlen($numeros) == 8) {
        // Se tem 8 dígitos, assume que é um número sem o nono dígito
        $ddd = $dddPadrao;
        $numero = '9' . $numeros;
    } else {
        // Se não tem 8, 9, 10 ou 11 dígitos, não é um número de celular válido
        return false;
    }

    // Verifica se o número é um celular válido (inicia com 9 após o DDD)
    if ($numero[0] != '9') {
        return false;
    }

    // Formata no padrão (XX) 9XXXX-XXXX
    return $ddd. substr($numero, 0, 5) . substr($numero, 5);
}


    $dados = file_get_contents("dados.csv");

    $linhas = explode("\n", $dados);

    $x = 1;
    foreach($linhas as $i => $colunas){

        $n = formatarTelefoneCelular($colunas);

        if($n) { echo $x.' - '.$n."<br>"; $x++; }


    }