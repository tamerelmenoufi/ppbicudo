<?php
    error_reporting(E_ALL);
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

    if($_POST['periodo']) $_SESSION['periodo'] = $_POST['periodo'];
    if($_POST['data_inicial']) $_SESSION['metaDataInicial'] = $_POST['data_inicial'];
    if($_POST['data_final']) $_SESSION['metaDataFinal'] = $_POST['data_final'];

    if($_SESSION['metaDataInicial'] and !$_SESSION['metaDataFinal']){
        $periodo = " and periodo = '{$_SESSION['metaDataInicial']}'";
        $where = " and data = '{$_SESSION['metaDataInicial']}'";
    }else if($_SESSION['metaDataInicial'] and $_SESSION['metaDataFinal']){
        $periodo = " and periodo between '{$_SESSION['metaDataInicial']}' and '{$_SESSION['metaDataFinal']}'";
        $where = " and data between '{$_SESSION['metaDataInicial']}' and '{$_SESSION['metaDataFinal']}'";
    }else{
        $periodo = " and periodo like '".date("Y-m")."%'";
        $where = " and data like '".date("Y-m")."%'";
        $_SESSION['metaDataInicial'] = date("Y-m-d");
    }

?>
<style>
    td{
        white-space: nowrap;
        min-width: 150px;
    }
</style>
<div class="m-3">
    <div class="d-flex justify-content-between mb-3">
        <h4 atualiza>Relatório de Metas</h4>
        <div class="w-50">
            <div class="input-group">
                <label class="input-group-text">Filtro por Período </label>
                <label class="input-group-text" for="data_inicial"> De </label>
                <input type="date" id="data_inicial" class="form-control" value="<?=$_SESSION['metaDataInicial']?>" >
                <label class="input-group-text" for="data_final"> A </label>
                <input type="date" id="data_final" class="form-control" value="<?=$_SESSION['metaDataFinal']?>" >
                <button filtro="filtrar" class="btn btn-outline-secondary" type="button">Buscar</button>
                </div>
            </div>
        </div>
        <!--<input type="month" max="<?= date('Y-m') ?>" style="width:150px;" value="<?=$_SESSION['periodo']?>" class="form-control  form-control-sm" periodo />-->
    </div>
    
<?php

    //$periodo = " and periodo = '{$_SESSION['periodo']}-01'"; //formato mensal

    $m = mysqli_fetch_object(mysqli_query($con, "select * from metas where 1 {$periodo}"));

    $meta_bruto = $m->meta;
    $meta_p1 = $m->p1;
    $meta_p2 = $m->p2;
    $meta_p3 = $m->p3;

    // $query = "select * from relatorio_modelos where data = '{$periodo}'"

    // $query = "select 
    //                 a.*,
    //                 count(*) as quantidade,
    //                 b.nome as origem_nome,
    //                 day(a.dataCriacao) as dia,
    //                 sum(a.ValorPedidoXquantidade) as bruto, 
    //                 (sum(a.ValorPedidoXquantidade) - sum(a.PrecoCusto)) as lucro 
    //             from relatorio a
    //                 left join origens b on a.origem = b.codigo 
    //             where date(a.dataCriacao) like '".$periodo."%' group by day(a.dataCriacao), a.origem order by b.nome asc ";
    
    
    $query = "select * from relatorio_modelos where 1 {$where}";

    $result = mysqli_query($con, $query);
    while($d1 = mysqli_fetch_object($result)){

        $registros = json_decode($d1->registros, true);
        $registros = (($registros)?implode(",",$registros):false);

         if($registros){

            $q = "select 
                        day(dataCriacao) as dia,
                        count(*) as quantidade,
                        sum(ValorPedidoXquantidade) as bruto, 
                        (sum(ValorPedidoXquantidade) - sum(PrecoCusto)) as lucro 
                    from relatorio 
                    where codigo in ({$registros}) and devolucao != '1' and deletado != '1' group by day(dataCriacao)";
            $qr = mysqli_query($con, $q);
            while($d = mysqli_fetch_object($qr)){
                $empresas[$d1->codigo] = $d1->nome;
                $r[$d1->codigo][$d->dia] = [
                    'bruto' => $d->bruto,
                    'lucro' => $d->lucro,
                    'quantidade' => $d->quantidade,
                ];
                $vendas += $d->bruto;
                $lucratividade += $d->lucro;
                $quantidade += $d->quantidade;
            }
        }

    }


    $pendente = (($meta_bruto - $vendas) < 0)?"<span class='text-success'>R$ ".number_format(($meta_bruto - $vendas)*(-1),2,',','.')."</span>":"<span class='text-danger'>R$ ".number_format(($meta_bruto - $vendas)*(-1),2,',','.')."</span>";
    //$meta_bruto = "R$ ".number_format($meta_bruto,2,',','.');
    //$meta_p1 = number_format($meta_p1,2,',',false)."%";
    //$meta_p2 = number_format($meta_p2,2,',',false)."%";
    //$meta_p3 = number_format($meta_p3,2,',',false)."%";
    //$lucro = number_format((($lucratividade/($vendas)?:1)*100),2,',',false)."%";
    //$vendas = "R$ ".number_format($vendas,2,',','.');
    //$lucratividade = "R$ ".number_format($lucratividade,2,',','.');
    


    $mes = explode("-", $_SESSION['metaDataInicial'])[1];
    $ano = explode("-", $_SESSION['metaDataInicial'])[0];

    $diasNoMes = date("t", mktime(0, 0, 0, $mes, 1, $ano));

    /*

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

<?php
    //*/
?>


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

        $("input[periodo]").change(function(){
            Carregando();
            periodo = $(this).val();
            $.ajax({
                url:"src/relatorio/metas.php",
                type:"POST",
                data:{
                    periodo
                },
                success:function(dados){
                    $("#paginaHome").html(dados);
                },
                error:function(){
                    Carregando('none');
                    alert('Erro')
                }
            });
        })

        $("button[filtro]").click(function(){
            Carregando();
            data_inicial = $("#data_inicial").val();
            data_final = $("#data_final").val();
            $.ajax({
                url:"src/relatorio/metas.php",
                type:"POST",
                data:{
                    data_inicial,
                    data_final
                },
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