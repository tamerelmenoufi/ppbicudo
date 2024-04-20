<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");
?>

<div class="row">
    <div class="col acao" componente="ms_popup" local="src/cliente/home.php"><i class="fa-solid fa-circle-user"></i><p>Cliente</p></div>
    <div class="col acao" componente="ms_popup_100" local="src/produtos/pedido.php"><i class="fa-solid fa-bell-concierge"></i><p>Pedido</p></div>
    <div class="col acao" componente="ms_popup_100" local="src/produtos/pagar.php"><i class="fa-solid fa-circle-dollar-to-slot"></i><p>Pagar</p></div>
</div>
<script>
    ///////////////
    $(function(){

    })
</script>