<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");


?>
<style>

</style>
<div class="m-3">
    <h4 atualiza>Relatório de Metas</h4>

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