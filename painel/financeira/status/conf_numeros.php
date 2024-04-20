<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    if($_POST['acao'] == 'salvar'){

        $telefones = trim(str_replace([' ',"\n","-","(",")","/"], false,$_POST['numeros']));

        mysqli_query($con, "update wapp_config set telefones_teste = '{$telefones}' where codigo = '1'");

        exit();
    }
  
    $query = "SELECT * FROM `wapp_config` where codigo = '1'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);
    
?>


<div class="card mt-3">
  <div class="card-header">
    Lista de Números (WhatsApp)
  </div>
  <div class="card-body">
    <p>Cadastre no campo abaixo os números dos testefones WhatsApp utilizados para os testes.</p>
    <span style="color:#a1a1a1">Incluir no formato ex:. 5592988887777 e os números separados por vírgulas.</span>
    <p><span style="color:#a1a1a1">Exemplo: 5591988887777,5592991234567,5511987655678</span></p>
    <textarea id="numeros" class="form-control" rows="10"><?=$d->telefones_teste?></textarea>
    <button voltar type="button" class="btn btn-primary mt-3">Salvar / Sair</button>

  </div>
</div>

<script>
    $(function(){

        $("button[voltar]" ).on( "click", function( event ) {
            Carregando();

            numeros = $("#numeros").val();
            $.ajax({
                url:"financeira/status/conf_numeros.php",
                type:"POST",
                data:{
                    numeros,
                    acao:'salvar'
                },
                success:function(dados){
                    $.ajax({
                        type:"POST",
                        data:{
                            cod:'<?=$_POST['cod']?>'
                        },
                        url:"financeira/status/conf.php",
                        success:function(dados){
                            $(".LateralDireita").html(dados);
                        }
                    });
                }
            })

        });
    })
</script>