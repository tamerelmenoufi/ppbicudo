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


    $query = "select 
                    a.*,
                    count(*) as quantidade,
                    b.nome as origem_nome,
                    day(a.dataCriacao) as dia,
                    sum(a.ValorPedidoXquantidade) as bruto, 
                    (sum(a.ValorPedidoXquantidade) - sum(a.PrecoCusto)) as lucro 
                from relatorio a
                    left join origens b on a.origem = b.codigo 
                where date(a.dataCriacao) like '".date("Y-m")."%' group by day(a.dataCriacao), a.origem order by b.nome asc ";
    $result = mysqli_query($con, $query);
    while($d = mysqli_fetch_object($result)){
        $empresas[$d->origem] = $d->origem_nome;
        $r[$d->origem][$d->dia] = [
            'bruto' => $d->bruto,
            'lucro' => $d->lucro,
            'quantidade' => $d->quantidade,
        ];
    }

    $mes = 9;
    $ano = 2025;

    $diasNoMes = date("t", mktime(0, 0, 0, $mes, 1, $ano));

?>
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
                <td><?=(($v)?:"<span class='text-danger'>Não Identificado</span>")?></td>
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