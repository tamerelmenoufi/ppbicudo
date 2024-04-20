<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

        $arq = 'caixa/msg.json';
        if(is_file($arq)){
            echo $dados = utf8_encode(file_get_contents($arq));

            // echo $dados = json_decode(trim($dados));

            unlink("caixa/msg.json");
        }

?>