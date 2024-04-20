<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");


    if($_POST['acao'] == 'padrao'){

        mysqli_query($con, "update configuracoes set api_facta_tabela_padrao = '{$_POST['id']}' where codigo = '1'");

    }




    $facta = new facta;

    $query = "select *, api_facta_dados->>'$.token' as token from configuracoes where codigo = '1'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);

    $agora = time();

    if($agora < $d->api_facta_expira){
        $tabelas = $d->api_facta_tabelas;
        $dados = json_decode($d->api_facta_dados);
        $token = $dados->token;
    }else{
        $retorno = $facta->Token();
        $dados = json_decode($retorno);
        $token = $dados->token;
        $tabelas = $facta->tabelas(['token' => $token]);
        //if($dados->statusCode == 200){
            mysqli_query($con, "update configuracoes set api_facta_expira = '".($agora + 3600)."', api_facta_dados = '{$retorno}', api_facta_tabelas = '{$tabelas}' where codigo = '1'");
        //}
    }

    $tabelas = json_decode($d->api_facta_tabelas);

?>

<div class="card m-3">
  <h5 class="card-header">Sistema Capital Financeira - FACTA</h5>
  <div class="card-body">
    <h5 class="card-title">Tabelas disponíveis</h5>
    <p class="card-text">
        <table class="table">
            <thead>
                <tr>
                <th scope="col">ID</th>
                <th scope="col">Nome</th>
                <th scope="col">Taxa</th>
                <th scope="col">Padrão</th>
                </tr>
            </thead>
            <tbody>
            <?php
                foreach($tabelas->data as $i => $v){
            ?>
                <tr class="<?=(($v->CD_TABELA_FF == $d->api_facta_tabela_padrao)?'bg-info bg-gradient':false)?>">
                    <td><?=$v->CD_TABELA_FF?></td>
                    <td><?=$v->TABELA?></td>
                    <td><?=$v->TX_MENSAL?></td>
                    <td>
                        <input padrao type="checkbox" class="form-check-input" value="<?=$v->CD_TABELA_FF?>" <?=(($v->CD_TABELA_FF == $d->api_facta_tabela_padrao)?'checked':false)?>>
                    </td>
                </tr>
            <?php
                }
            ?>
            </tbody>
        </table>
    </p>
    <button atualiza class="btn btn-primary">Atualizar</button>
  </div>
</div>


<script>
    $(function(){

        Carregando('none');

        $("button[atualiza]").click(function(){
            Carregando();
            $.ajax({
                url:"financeira/facta/index.php",
                success:function(dados){
                    $("#paginaHome").html(dados);
                }
            })
        })

        $("input[padrao]").click(function(){
            id = $(this).val();
            Carregando();
            $.ajax({
                url:"financeira/facta/index.php",
                type:"POST",
                data:{
                    acao:'padrao',
                    id
                },
                success:function(dados){
                    $("#paginaHome").html(dados);
                }
            })
        })


    })
</script>