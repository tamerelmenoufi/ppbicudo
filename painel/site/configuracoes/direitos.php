<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    $query = "select * from configuracoes where codigo = '1'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);
?>


    <div class="mb-3">
        <label class="form-label">Direitos Reservados</label>
        <div class="form-control" ><?=$d->direitos?></div>
    </div>


    <button
            class="btn btn-primary"

            data-bs-toggle="offcanvas"
            href="#offcanvasDireita"
            role="button"
            aria-controls="offcanvasDireita"
            editar_direitos

    >Editar Direitos Reservados</button>

<script>
    $(function(){

        Carregando('none');

        $("button[editar_direitos]").click(function(){
            $.ajax({
                url:"site/configuracoes/editar_direitos.php",
                success:function(dados){
                    $(".LateralDireita").html(dados);
                }
            })
        });

    })
</script>