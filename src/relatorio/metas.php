<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");


?>
<style>
    td{
        white-space: nowrap;
        min-width: 150px;
    }
</style>
<div class="m-3">
    <h4 atualiza>Relatório de Metas</h4>
<?php

    $periodo = date("Y-m-");


    $m = mysqli_fetch_object(mysqli_query($con, "select * from metas where periodo = '{$periodo}-01'"));

    $meta_bruto = $m->meta;
    $meta_p1 = $m->p1;
    $meta_p2 = $m->p2;
    $meta_p3 = $m->p3;

    $query = "select 
                    a.*,
                    count(*) as quantidade,
                    b.nome as origem_nome,
                    day(a.dataCriacao) as dia,
                    sum(a.ValorPedidoXquantidade) as bruto, 
                    (sum(a.ValorPedidoXquantidade) - sum(a.PrecoCusto)) as lucro 
                from relatorio a
                    left join origens b on a.origem = b.codigo 
                where date(a.dataCriacao) like '".$periodo."%' group by day(a.dataCriacao), a.origem order by b.nome asc ";
    $result = mysqli_query($con, $query);
    while($d = mysqli_fetch_object($result)){

        $empresas[$d->origem] = $d->origem_nome;
        $r[$d->origem][$d->dia] = [
            'bruto' => $d->bruto,
            'lucro' => $d->lucro,
            'quantidade' => $d->quantidade,
        ];

        $vendas += $d->bruto;
        $lucratividade += $d->lucro;
        
        
        $quantidade += $d->quantidade;

    }

        $pendente = (($meta_bruto - $vendas) < 0)?"<span class='text-success'>R$ ".number_format(($meta_bruto - $vendas)*(-1),2,',','.')."</span>":"<span class='text-danger'>R$ ".number_format(($meta_bruto - $vendas)*(-1),2,',','.')."</span>";
        $meta_bruto = "R$ ".number_format($meta_bruto,2,',','.');
        $meta_p1 = number_format($meta_p1,2,',',false)."%";
        $meta_p2 = number_format($meta_p2,2,',',false)."%";
        $meta_p3 = number_format($meta_p3,2,',',false)."%";
        $lucro = number_format((($lucratividade/$vendas)*100),2,',',false)."%";
        $vendas = "R$ ".number_format($vendas,2,',','.');
        $lucratividade = "R$ ".number_format($lucratividade,2,',','.');
        


    $mes = 9;
    $ano = 2025;

    $diasNoMes = date("t", mktime(0, 0, 0, $mes, 1, $ano));

?>


<div class="card">
  <div class="card-header">
    <i class="fa-solid fa-gear" 
        config="<?="{$ano}-{$mes}-1"?>" 
        style="margin-right:20px; cursor:pointer;"
        data-bs-toggle="offcanvas"
        href="#offcanvasDireita"
        role="button"
        aria-controls="offcanvasDireita"
    ></i> Resumo das metas para o mês <?="{$mes}/{$ano}"?>
  </div>
  <div class="card-body">
    <table class="table">
        <tr>
            <th>Meta Bruto</th>
            <td colspan="3"><?=$meta_bruto?></td>
        </tr>
        <tr>
            <th>Meta Lucro</th>
            <td><?=$meta_p1?></td>
            <td><?=$meta_p2?></td>
            <td><?=$meta_p3?></td>
        </tr>
        <tr>
            <th>Vendas Realizadas</th>
            <td colspan="3"><?=$vendas?></td>
        </tr>
        <tr>
            <th>Lucratividade</th>
            <td colspan="3"><?=$lucratividade?></td>
        </tr>
        <tr>
            <th>Lucro Atualizado</th>
            <td colspan="3"><?=$lucro?></td>
        </tr>
        <tr>
            <th>Faturamento Pendente</th>
            <td colspan="3"><?=$pendente?></td>
        </tr>
        <tr>
            <th>Quantidade de Vendas</th>
            <td colspan="3"><?=$quantidade?></td>
        </tr>


    </table>
    <!-- <h5 class="card-title">Special title treatment</h5>
    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
    <a href="#" class="btn btn-primary">Go somewhere</a> -->
  </div>
</div>



    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Lojas/Dias</th>
                    <?php
                    for ($dia = 1; $dia <= $diasNoMes; $dia++) {
                    ?>
                    <th class="text-center" colspan="3">
                        <?=str_pad($dia, 2, "0", STR_PAD_LEFT)."/".str_pad($mes, 2, "0", STR_PAD_LEFT)?>
                        <table style="width:100%">
                            <tr>
                                <td class="text-center">Bruto</td>
                                <td class="text-center">Lucro</td>
                                <td class="text-center">Quantidade</td>
                            </tr>
                        </table>
                    </th>
                    <?php
                    }
                    ?>
                </tr>
            </thead>
        <?php
        foreach ($empresas as $i => $v) {
        ?>
            <tr>
                <td><?=(($v)?:"<span class='text-danger'>Não Identificada</span>")?></td>
        <?php
            $c = 0;
            for ($dia = 1; $dia <= $diasNoMes; $dia++) {
                if($c%2 == 0){
                    $cor = '#eeeeee';
                }else{
                    $cor = '#ffffff';
                }
                $c++;
        ?>
                <td class="text-center" style="background-color:<?=$cor?>"><?=(($r[$i][$dia]['bruto'])?"R$ ".number_format($r[$i][$dia]['bruto'],2,',','.'):'-')?></td>
                <td class="text-center" style="background-color:<?=$cor?>"><?=(($r[$i][$dia]['lucro'])?"R$ ".number_format($r[$i][$dia]['lucro'],2,',','.'):'-')?></td>
                <td class="text-center" style="background-color:<?=$cor?>"><?=(($r[$i][$dia]['quantidade'])?:'-')?></td>
        <?php
            }
        ?>    
            </tr>
        <?php
        }
        ?>
        </table>
    </div>
</div>
<script>
    $(function(){
        Carregando('none');

        $("i[config]").click(function(){
            config = $(this).attr("config");
            $.ajax({
                url:"src/relatorio/config.php",
                type:"POST",
                data:{
                    periodo:config
                },
                success:function(dados){
                    $(".LateralDireita").html(dados);
                },
                error:function(){
                    Carregando('none');
                    alert('Erro')
                }
            });            
        })
        
        $("h4[atualiza]").click(function(){
            Carregando();
            $.ajax({
                url:"src/relatorio/metas.php",
                success:function(dados){
                    $("#paginaHome").html(dados);
                },
                error:function(){
                    Carregando('none');
                    alert('Erro')
                }
            });
        })

    })
</script>