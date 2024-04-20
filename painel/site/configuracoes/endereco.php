<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    $query = "select * from configuracoes where codigo = '1'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);
?>


    <div class="mb-3">
        <label class="form-label">CEP</label>
        <div class="form-control"><?=$d->cep?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">Rua</label>
        <div class="form-control" ><?=$d->rua?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">Número</label>
        <div class="form-control" ><?=$d->numero?></div>
    </div>


    <div class="mb-3">
        <label class="form-label">Bairro</label>
        <div class="form-control" ><?=$d->bairro?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">Complemento</label>
        <div class="form-control" ><?=$d->complemento?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">Referência</label>
        <div class="form-control" ><?=$d->referencia?></div>
    </div>

    <button
            class="btn btn-primary"

            data-bs-toggle="offcanvas"
            href="#offcanvasDireita"
            role="button"
            aria-controls="offcanvasDireita"
            editar_endereco

    >Editar Endereço</button>
    <button
            class="btn btn-success ml-3"
            data-bs-toggle="offcanvas"
            href="#offcanvasDireita"
            role="button"
            aria-controls="offcanvasDireita"
            editar_mapa
    >Editar Mapa</button>

<script>
    $(function(){

        Carregando('none');

        $("button[editar_endereco]").click(function(){
            $.ajax({
                url:"site/configuracoes/editar_endereco.php",
                success:function(dados){
                    $(".LateralDireita").html(dados);
                }
            })
        });

        $("button[editar_mapa]").click(function(){
            $.ajax({
                url:"site/configuracoes/editar_mapa.php",
                success:function(dados){
                    $(".LateralDireita").html(dados);
                }
            })
        });

    })
</script>