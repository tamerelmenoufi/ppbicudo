<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");


    if($_POST['acao'] == 'padrao'){

        mysqli_query($con, "update configuracoes set api_vctex_tabela_padrao = '{$_POST['id']}' where codigo = '1'");

    }

    $tab_disc = [
        'name' => 'Nome',
        'annualFee' => 'Taxa Anual',
        'monthlyFee' => 'Taxa Mensal',
        'maxDisbursedAmount' => 'Valor Máximo',
        'minDisbursedAmount' => 'Valor Mínimo',
        'maxNumberOfYearsAntecipated' => 'Máximo Antecipação',
        'minNumberOfYearsAntecipated' => 'Mínimo Antecipação',
    ];


    $vctex = new Vctex;

    $query = "select *, api_vctex_dados->>'$.token.accessToken' as token from configuracoes where codigo = '1'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);

    $agora = time();

    if($agora < $d->api_expira){
        $tabelas = $d->api_vctex_tabelas;
    }else{
        $retorno = $vctex->Token();
        $dados = json_decode($retorno);
        if($dados->statusCode == 200){
            $tabelas = $vctex->Tabelas($dados->token->accessToken);
            mysqli_query($con, "update configuracoes set api_vctex_expira = '".($agora + $dados->token->expires)."', api_vctex_dados = '{$retorno}', api_vctex_tabelas = '{$tabelas}' where codigo = '1'");
        }else{
            $tabelas = 'error';
        }
    }

    $tabelas = json_decode($d->api_vctex_tabelas);

?>

<div class="card m-3">
  <h5 class="card-header">Sistema Capital Financeira - VCTEX</h5>
  <div class="card-body">
    <h5 class="card-title">Tabelas disponíveis</h5>
    <p class="card-text">
        <table class="table">
            <thead>
                <tr>
                <th scope="col"><?=$tab_disc['name']?></th>
                <th scope="col"><?=$tab_disc['monthlyFee']?></th>
                <th scope="col"><?=$tab_disc['annualFee']?></th>
                <th scope="col"><?=$tab_disc['minDisbursedAmount']?></th>
                <th scope="col"><?=$tab_disc['maxDisbursedAmount']?></th>
                <th scope="col"><?=$tab_disc['minNumberOfYearsAntecipated']?></th>
                <th scope="col"><?=$tab_disc['maxNumberOfYearsAntecipated']?></th>
                <th scope="col">Padrão</th>
                </tr>
            </thead>
            <tbody>
            <?php
                foreach($tabelas->data as $i => $v){
            ?>
                <tr class="<?=(($v->id == $d->api_vctex_tabela_padrao)?'bg-info bg-gradient':false)?>">
                    <td><?=$v->name?></td>
                    <td><?=$v->monthlyFee?></td>
                    <td><?=$v->annualFee?></td>
                    <td><?=$v->minDisbursedAmount?></td>
                    <td><?=$v->maxDisbursedAmount?></td>
                    <td><?=$v->minNumberOfYearsAntecipated?></td>
                    <td><?=$v->maxNumberOfYearsAntecipated?></td>
                    <td>
                        <input padrao type="checkbox" class="form-check-input" value="<?=$v->id?>" <?=(($v->id == $d->api_vctex_tabela_padrao)?'checked':false)?>>
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
                url:"financeira/vctex/index.php",
                success:function(dados){
                    $("#paginaHome").html(dados);
                }
            })
        })

        $("input[padrao]").click(function(){
            id = $(this).val();
            Carregando();
            $.ajax({
                url:"financeira/vctex/index.php",
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