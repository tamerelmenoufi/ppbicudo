<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

        $query = "SELECT * FROM `wapp_config` where codigo = '1'";
        $result = mysqli_query($con, $query);
        $d = mysqli_fetch_object($result);

        $numeros = explode(",", $d->telefones_teste);

        $query = "select * from status_mensagens where codigo = '{$_POST['envio']}'";
        $result = mysqli_query($con, $query);
        $d = mysqli_fetch_object($result);

        $msg = trim(str_replace(["\n","\r"],"\\n",$d->mensagem));

        $dadosParaEnviar = http_build_query(
            array(
                'numeros' => $numeros,
                'mensagem' => (($msg)?:''),
                'instancia' => 3,
                'tipo' => (($d->tipo)?:''), //img, arq
                'arquivo' => (($d->arquivo)?"https://painel.capitalsolucoesam.com.br/volume/wapp/status/{$d->status}/{$d->arquivo}":'') //URL ou Bse64
            )
        );
        $opcoes = array('http' =>
               array(
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $dadosParaEnviar
            )
        );

        $contexto = stream_context_create($opcoes);
        $result   = file_get_contents('http://wapp.mohatron.com/tme.php', false, $contexto);

?>