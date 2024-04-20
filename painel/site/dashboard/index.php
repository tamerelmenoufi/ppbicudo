<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");
?>
<style>
    .grafico{
        margin-bottom:10px;
    }
</style>

<div class="card m-3">
    <div class="card-header">
        Dados do Site
    </div>
    <div class="row g-0">       
<?php

    $placas = [
        'banners' => 'Banners',
        'servicos' => 'Produtos',
        'time' => 'Time da empresa',
        'depoimentos' => 'Depoimentos',
    ];

    foreach($placas as $tabela => $titulo){
        $r = mysqli_query($con, "select count(*) as qt, situacao from {$tabela} group by situacao");
        $total = $bloqueado = $liberado = 0;
        while($p = mysqli_fetch_object($r)){
            $total += $p->qt;
            if($p->situacao != 1) $bloqueado += $p->qt;
            else $liberado += $p->qt;
        }

?> 
<div class="col p-3">
    <div class="alert alert-primary" style="height:140px;">
        <div class="d-flex justify-content-between align-items-center">
            <div class="flex-fill">
                <b><?=$titulo?></b>
                <h1><?=$total?></h1>        
            </div>
            <canvas 
                    class="grafico"
                    height="100"
                    width="100"
                    bloqueado="<?=$bloqueado?>"
                    liberado="<?=$liberado?>"
                    total="<?=$total?>"
            ></canvas>        
        </div>
    </div>
</div>
<?php
    }
?>
</div>
</div>




<div class="card m-3">
    <div class="card-header">
        Dados dos Acessos
    </div>
    <div class="row g-0">

        <?php

            $mes_atual = date("Y-m",mktime(0,0,0,date("m"),date("d"),date("Y")));
            $mes_passado = date("Y-m",mktime(0,0,0,date("m"),1-1,date("Y")));

            $q = "select 
                        (select count(*) from log_acessos) as geral,
                        (select count(*) from log_acessos where data like '{$mes_passado}%') as mes_passado,
                        (select count(*) from log_acessos where data like '{$mes_atual}%') as mes_atual,
                        (select count(*) from log_acessos where data like '2024-03-09%') as hoje,
                        (select count(*) from log_acessos where data >= '2024-03-09 16:00:00%') as on_line
            ";
            $r = mysqli_query($con, $q);
            $p = mysqli_fetch_object($r);
        ?> 
            <div class="col p-3">
                <div class="alert alert-warning" style="height:140px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-fill">
                            <b>Geral</b>
                            <h1><?=$p->geral?></h1>        
                        </div>      
                    </div>
                </div>
            </div>
            <div class="col p-3">
                <div class="alert alert-warning" style="height:140px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-fill">
                            <b>02/2023</b>
                            <h1><?=$p->mes_passado?></h1>        
                        </div>      
                    </div>
                </div>
            </div>
            <div class="col p-3">
                <div class="alert alert-warning" style="height:140px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-fill">
                            <b>03/2024</b>
                            <h1><?=$p->mes_atual?></h1>        
                        </div>      
                    </div>
                </div>
            </div>

            <div class="col p-3">
                <div class="alert alert-warning" style="height:140px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-fill">
                            <b>Hoje</b>
                            <h1><?=$p->hoje?></h1>        
                        </div>
                        <canvas 
                                class="grafico2"
                                height="100"
                                width="100"
                                bloqueado="<?=($p->hoje - $p->on_line)?>"
                                liberado="<?=$p->on_line?>"
                                total="<?=$p->hoje?>"
                        ></canvas>        
                    </div>
                </div>
            </div>


        
    </div>
</div>




<script>
    $(function(){

        Carregando('none');

        $(".grafico").each(function(){

            const obj = $(this);
            const bloqueado = obj.attr("bloqueado");
            const liberado = obj.attr("liberado");

            const data = {
            labels: ['Bloqueado', 'Liberado'],
            datasets: [
                {
                label: 'Publicações',
                data: [bloqueado,liberado],
                backgroundColor: ['Red', 'Green'],
                }
            ]
            };

            const config = {
            type: 'doughnut',
            data: data,
            options: {
                    responsive: false,
                    plugins: {
                    // legend: {
                    //     position: 'top',
                    // },
                    legend:false,
                    title:false,
                    // title: {
                    //     display: true,
                    //     text: 'Chart.js Doughnut Chart'
                    // }
                    }
                },
            };

            const chart = new Chart(obj, config);
        })

        $(".grafico2").each(function(){

            const obj = $(this);
            const bloqueado = obj.attr("bloqueado");
            const liberado = obj.attr("liberado");

            const data = {
            labels: ['Acessos', 'On Line'],
            datasets: [
                {
                label: 'Publicações',
                data: [bloqueado,liberado],
                backgroundColor: ['Blue', 'Green'],
                }
            ]
            };

            const config = {
            type: 'doughnut',
            data: data,
            options: {
                    responsive: false,
                    plugins: {
                    // legend: {
                    //     position: 'top',
                    // },
                    legend:false,
                    title:false,
                    // title: {
                    //     display: true,
                    //     text: 'Chart.js Doughnut Chart'
                    // }
                    }
                },
            };

            const chart = new Chart(obj, config);
        })
        


    })
</script>