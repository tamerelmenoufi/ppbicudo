<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");
?>
<style>

</style>

<div class="p-3">
    <div class="row">
        <div montaMenu class="col-md-6"></div>
        <div menuForm class="col-md-6"></div>
    </div>
</div>

<script>
    $(function(){

        Carregando('none');

        $.ajax({
            url:"site/menu/menu.php",
            success:function(dados){
                $("div[montaMenu]").html(dados);
            }
        });
    })
</script>