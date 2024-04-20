<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");
?>
<style>

</style>

<?php
for($i=0;$i<100;$i++){
?>
<li class="list-group-item">

    <div class="d-flex justify-content-between align-items-center ItemEmail">
        <div class="p-2">
            <img src="https://bootdey.com/img/Content/avatar/avatar4.png" style="width:60px; height:60px; border-radius:100%" >
        </div>
        <div class="p-2 d-flex flex-column align-items-start flex-grow-1">
            <h5>Joaquim de Oliveira Mello</h5>
            <span>jom@capitalsolucoes.com.br</span>
        </div>
        <div class="p-2">
            <i class="fa-solid fa-computer d-none d-md-block"></i>
            <i class="fa-solid fa-mobile-screen-button d-block d-sm-none"></i>
        </div>
    </div>

</li>
<?php
}
?>

<script>

    $(function(){
        Carregando('none')

    });

</script>