<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");


?>
<style>

</style>
<div class="m-3">
    <h4 atualiza>Relatório de Metas</h4>
    <table class="table table-hover">
        <tr>
            <td>Loja</td>
            <td>Quantidade</td>
            <td>Valor Bruto</td>
            <td>Valor Líquido</td>
        </tr>
    <?php
    echo $query = "select 
                    a.*,
                    count(*) as qt,
                    b.nome as origem_nome,
                    sum(a.ValorPedidoXquantidade) as bruto, 
                    (sum(a.ValorPedidoXquantidade) - sum(a.PrecoCusto)) as lucro 
                from relatorio a
                    left join origens b on a.origem = b.codigo 
                where date(a.dataCriacao) like '".date("Y-m")."%' group by a.origem order by b.nome asc ";
    $result = mysqli_query($con, $query);
    while($d = mysqli_fetch_object($result)){
    ?>
        <tr>
            <td><?=$d->origem_nome?></td>
            <td><?=$d->qt?></td>
            <td><?=$d->bruto?></td>
            <td><?=$d->lucro?></td>
        </tr>
    <?php
    }
    ?>
    </table>

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