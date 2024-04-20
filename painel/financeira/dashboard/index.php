<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    function rotulo_valores($d){
        list($m, $a) = explode("/",$d);
        $r = [
            '01' => 'Jan',
            '02' => 'Fev',
            '03' => 'Mar',
            '04' => 'Abr',
            '05' => 'Mai',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Ago',
            '09' => 'Set',
            '10' => 'Out',
            '11' => 'Nov',
            '12' => 'Dez'
        ];
        return "{$r[$m]}/$a";
    }

    $query = "select
                (select count(*) from clientes) as clientes,
                (select count(*) from consultas) as simulacoes,
                (select count(*) from consultas where proposta->>'$.statusCode') as contratos,
                (select count(*) from consultas where proposta->>'$.statusCode' = '130') as pagos,
                (select sum(dados->'$.data.simulationData.totalReleasedAmount') from consultas where proposta->>'$.statusCode' = '130') as valor,
            ";
    $q = [];
    for($i=0; $i<12; $i++){
        $dt = date("Y-m", mktime(0,0,0,date("m") - $i,date("d"), date("Y")));
        $valor_rotulo[$i] = date("m/Y", mktime(0,0,0,date("m") - $i,date("d"), date("Y")));
        $q[] = "(select sum(dados->'$.data.simulationData.totalReleasedAmount') from consultas where proposta->>'$.statusCode' = '130' and data like '{$dt}%') as valor{$i}";
    }
    $query = $query.implode(", ", $q);
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);

?>
<style>
    .calendario{
        width:100%;
    }
    .calendario td{
        font-size:12px;
        text-align:center;
        min-height:25px;
        padding:5px;
        vertical-align:top;
    }

    .calendario th{
        font-size:12px;
        text-align:center;
        min-height:25px;
        padding:5px;
    }
    .registros{
        padding:5px;
        font-size:12px;
        margin:5px;
        width:100%;
        height:25px;
        border-radius:5px;
        background:blue;
        color:#fff;
        cursor:pointer;
    }
    .registros_limpo{
        padding:5px;
        font-size:12px;
        margin:5px;
        width:100%;
        height:25px;
        border-radius:5px;
        background:#fff;
        color:#fff;
    }
    .alert div{
        font-size:12px;
        color:#a1a1a1;
        text-align:left;
    }
    .alert h1{
        text-align:center;
    }
</style>
<div class="card m-3">
  <h5 class="card-header">Sistema Capital Financeira</h5>
  <div class="card-body">

    <div class="row">
        <div class="col-md-2">
            <div class="alert alert-primary" role="alert">
                <div>Clientes</div>
                <h1 class="contagem" valor="<?=$d->clientes?>" tipo="numero"><?=$d->clientes?></h1>
            </div>
        </div>
        <div class="col-md-2">
            <div class="alert alert-primary" role="alert">
                <div>Simulações</div>
                <h1 class="contagem" valor="<?=$d->simulacoes?>" tipo="numero"><?=$d->simulacoes?></h1>
            </div>
        </div>
        <div class="col-md-2">
            <div class="alert alert-primary" role="alert">
                <div>Contratos</div>
                <h1 class="contagem" valor="<?=$d->contratos?>" tipo="numero"><?=$d->contratos?></h1>
            </div>
        </div>
        <div class="col-md-2">
            <div class="alert alert-primary" role="alert">
                <div>Contratos Pagos</div>
                <h1 class="contagem" valor="<?=$d->pagos?>" tipo="numero"><?=$d->pagos?></h1>
            </div>
        </div>
        <div class="col-md-3">
            <div class="alert alert-primary" role="alert">
                <div>Pagamentos Acumulados</div>
                <h1 class="contagem" valor="<?=$d->valor?>" tipo="moeda">R$ <?=number_format($d->valor,2,',','.')?></h1>
            </div>
        </div>
    </div>


    <div class="row">
        <?php
        for($i=11;$i>=0;$i--){
            $opc = "valor{$i}";
        ?>
        <div class="col-md-1">
            <div 
                class="alert alert-success" 
                role="alert" 
                valores_dias<?=((!$d->$opc)?'BLQ':false)?>="<?=$valor_rotulo[$i]?>"
                <?php
                if($d->$opc){
                ?>              
                data-bs-toggle="offcanvas"
                href="#offcanvasDireita"
                role="button"
                aria-controls="offcanvasDireita"
                style="cursor:pointer"
                <?php
                }
                ?>
            >
                <div><?=rotulo_valores($valor_rotulo[$i])?></div>
                <h6
                    class="contagem"
                    valor="<?=$d->$opc?>"
                    tipo="moeda"  
                >R$ <?=number_format($d->$opc,2,',','.')?></h6>
            </div>
        </div>
        <?php
        }
        ?>
    </div>



    <h5 class="card-title">Relatórios e estatísticas</h5>
    <p class="card-text">Tela de exibição das informações de consultas, contratos e histórico dos clientes.</p>
    <!-- <a href="#" class="btn btn-primary">Go somewhere</a> -->
    <div class="row">
        <div class="col-md-4">
            <div dbCalendar></div>
        </div>
        <div class="col-md-8">
            <div dbTabela></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div dbCadastros></div>
        </div>
    </div>
  </div>
</div>


<script>
    $(function(){

        Carregando('none');

        $.ajax({
            url:"financeira/dashboard/home/calendario.php",
            success:function(dados){

                $("div[dbCalendar]").html(dados);
                
                dateN = ("00" + $("select[dateN]").val()).slice(-2);
                dateY = $("select[dateY]").val();

                $.ajax({
                    url:"financeira/dashboard/home/tabela.php",
                    type:"POST",
                    data:{
                        data:`${dateY}-${dateN}`
                    },
                    success:function(dados){
                        $("div[dbTabela").html(dados);
                    }
                })

                $.ajax({
                    url:"financeira/dashboard/home/cadastros.php",
                    type:"POST",
                    data:{
                        data:`${dateY}-${dateN}`
                    },
                    success:function(dados){
                        $("div[dbCadastros").html(dados);
                    }
                })
                
            }
        })

        $("div[valores_dias]").click(function(){
            periodo = $(this).attr("valores_dias");
            $.ajax({
                    url:"financeira/dashboard/home/pagamentos_diarios.php",
                    type:"POST",
                    data:{
                        periodo
                    },
                    success:function(dados){
                        $(".LateralDireita").html(dados);
                    }
                })
        })



    })
</script>