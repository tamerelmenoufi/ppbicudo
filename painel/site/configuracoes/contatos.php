<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    $query = "select * from configuracoes where codigo = '1'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);
?>


    <div class="mb-3">
        <label class="form-label">Telefone</label>
        <div class="form-control"><?=$d->telefone?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">E-mail</label>
        <div class="form-control" ><?=$d->email?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">E-mail (Assinatura)</label>
        <div class="form-control" ><?=$d->email_assinatura?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">E-mail (Resposta)</label>
        <div class="form-control" ><?=$d->email_resposta?></div>
    </div>


    <button
            class="btn btn-primary"

            data-bs-toggle="offcanvas"
            href="#offcanvasDireita"
            role="button"
            aria-controls="offcanvasDireita"
            editar_contato

    >Editar Contatos / Mídias Sociais</button>

<script>
    $(function(){

        Carregando('none');

        $("button[editar_contato]").click(function(){
            $.ajax({
                url:"site/configuracoes/editar_contatos.php",
                success:function(dados){
                    $(".LateralDireita").html(dados);
                }
            })
        });

        $("button[editar_midias_sociais]").click(function(){
            $.ajax({
                url:"site/configuracoes/editar_midias_sociais.php",
                success:function(dados){
                    $(".LateralDireita").html(dados);
                }
            })
        });

    })
</script>