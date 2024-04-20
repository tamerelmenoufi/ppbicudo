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
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <!-- <label class="form-check-label" for="exampleCheck1">Marcar Todos</label> -->
            </div>
        </div>
        <div class="p-2 d-flex flex-column align-items-start flex-grow-1">
            <h5>tecnologia@capitalsolucoes.com.br</h5>
            <span>Agenda das atividades desenvolvidas</span>
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