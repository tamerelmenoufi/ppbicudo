<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    $c = mysqli_fetch_object(mysqli_query($con, "select * from clientes where codigo = '{$_POST['cliente']}'"));


    // if($_POST['detalhes']){
    //     $detalhes = json_decode(base64_decode($_POST['detalhes']));
    //     $detalhes = json_encode($detalhes, JSON_PRETTY_PRINT);
    //     echo "{$detalhes}";
    //     exit();
    // }
?>
<style>
    .Titulo<?=$md5?>{
        position:absolute;
        left:60px;
        top:8px;
        z-index:0;
    }
</style>
<h4 class="Titulo<?=$md5?>">Eventos (Logs)</h4>

<h5><?=$c->nome?></h5>
<h6><?=$c->cpf?></h6>
<?php
    $query = "select * from consultas_log where cliente = '{$_POST['cliente']}' order by data desc";
    $result = mysqli_query($con, $query);

    if(!mysqli_num_rows($result)){
?>

<center>
    <h1 style="color:#a1a1a1; margin-top:100px;">Cliente sem Eventos</h1>
</center>

<?php
    }

    while($d = mysqli_fetch_object($result)){
        $sessoes =  json_decode($d->sessoes);
        $log = json_decode($d->log);
        $usuario = false;
        if($sessoes->acao == 'cron'){
            $titulo = "Sistema - Operação automática (Tarefas)";
        }else if($sessoes->ProjectPainel){
             $titulo = "Manual - usuário / Consultores (Painel)";
             $usuario = $sessoes->ProjectPainel->nome;
        }else if($sessoes->codUsr){
             $titulo = "Cliente - Realizada pela aplicação (Site)";
        }
        if($log->statusCode){
            $descricao = "{$log->statusCode} - {$log->message}";
            $detalhes = $d->sessoes; //base64_encode($d->log);
        }else if($log->proposalStatusId){
            $descricao = "{$log->proposalStatusId} - {$log->proposalStatusDisplayTitle}";
            $detalhes = $d->sessoes; //base64_encode($d->log);
        }   
        // echo $d->sessoes;
        // echo $d->log;

?>
    <div class="card mb-3">
    <div class="card-header">
        <?=$titulo?>
    </div>
    <div class="card-body">
        <p class="card-text"><?=$descricao?></p>
        <?php
        if($usuario){
        ?>
        <span style="color:#a1a1a1; font-size:12px;">Atendente: <?=$usuario?></span><br>
        <?php
        }
        ?>
        <span style="color:#a1a1a1; font-size:12px;">Processada em: <?=dataBr($d->data)?></span>
        <?php
        if($d->ativo){
        ?>
        <span class="text-success" style="font-size:12px; margin-left:20px;"><i class="fa-solid fa-check"></i> status atual</span>
        <?php
        }
        ?>
        <!-- <a detalhes="<?=$detalhes?>" class="btn btn-warning btn-sm">Log</a> -->
    </div>
    </div>
<?php
    }
?>

<script>
    $(function(){
        $("a[detalhes]").click(function(){
            detalhes = $(this).attr("detalhes");
            $.ajax({
                type:"POST",
                data:{
                    detalhes,
                },
                url:"financeira/clientes/logs.php",
                success:function(dados){
                    $.alert({
                        content:dados,
                        title:"Log",
                        type:"blue",
                        columnClass:"col-md-8"
                    })
                }
            })
        })
    })
</script>