<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

        $dados = [
            'mensagem' => 'Resposta de: '.($_POST['msg']),
        ];

        $dados = json_encode($dados);

        file_put_contents('caixa/msg.json', $dados);

?>