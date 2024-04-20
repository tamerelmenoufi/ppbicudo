<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    if($_POST['acao'] == 'contatos'){

        $dados = $_POST;
        unset($dados['acao']);
        unset($dados['midias']);

        $campos = [];
        foreach($dados as $i => $v){
          $campos[] = "{$i} = '".addslashes($v)."'";
        }
        $campos[] = "coordenadas = ''";
        $campos['midias_sociais'] = "midias_sociais = '".json_encode($_POST['midias'])."'";

        echo $query = "update configuracoes set  ".implode(", ",$campos)." WHERE codigo = '1'";
        mysqli_query($con, $query);
        exit();
    }


    $query = "select * from configuracoes where codigo = '1'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);

    $midias = json_decode($d->midias_sociais);
?>

<form class="acaoContatos">
    <div class="mb-3">
        <label class="form-label">Telefone</label>
        <input type="text" class="form-control" value="<?=$d->telefone?>" id="telefone" name="telefone" >
    </div>

    <div class="mb-3">
        <label class="form-label">E-mail</label>
        <input type="text" class="form-control" value="<?=$d->email?>" id="email" name="email" >
    </div>

    <div class="mb-3">
        <label class="form-label">E-mail (Assinatura)</label>
        <input type="text" class="form-control" value="<?=$d->email_assinatura?>" id="email_assinatura" name="email_assinatura" >
    </div>

    <div class="mb-3">
        <!-- <label class="form-label">E-mail (Mensagem Resposta)</label>
        <input type="text" class="form-control" value="<?=$d->email_resposta?>" id="email_resposta" > -->
        <textarea id="email_resposta" name="email_resposta"><?=$d->email_resposta?></textarea>
    </div>


    <?php
    $midias_sociais = [
    'facebook' => 'https://www.facebook.com/',
    'twitter' => 'https://twitter.com/',
    'instagram' => 'https://www.instagram.com/',
    'youtube' => 'https://www.youtube.com/',
    'linkedin' => 'https://www.linkedin.com/',
    'whatsapp' => 'https://api.whatsapp.com/'
    ];
    foreach($midias_sociais as $ind => $url){
    ?>
    <div class="form-floating mb-3">
    <div class="input-group mb-3">
        <!-- <div class="input-group-text">
        <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input">
        </div> -->
        <div class="input-group-text">
        <?=$url?>
        </div>
        <input name=midias[<?=$ind?>] id="midias<?=$ind?>" value="<?=$midias->$ind?>" type="text" class="form-control" aria-label="Text input with checkbox">
    </div>
    </div>
    <?php
    }
    ?>

    <button
            class="btn btn-primary"
            data-bs-toggle="offcanvas"
            type="submit"
            salvar_contatos
    >Salvar Contatos / Mídias Sociais</button>
    <input type="hidden" id="acao" name="acao" value="contatos" >
</form>
<script>

    ClassicEditor
    .create( document.querySelector( '#email_resposta' ) )
    .then( editor => {
        console.log( editor );
    } )
    .catch( error => {
        console.error( error );
    } );
    // console.log(editor);



    $(function(){

        Carregando('none');

        $("#telefone").mask("+ 55 99 99999-9999");

        $("form.acaoContatos").on( "submit", function( event ) {

            Carregando();

            // data = [];
            // data.push({name:'telefone', value:$("#telefone").val()});
            // data.push({name:'email', value:$("#email").val()});
            // data.push({name:'email_assinatura', value:$("#email_assinatura").val()});
            // data.push({name:'email_resposta', value:$("#email_resposta").val()});
            // data.push({name:'acao', value:'contatos'});
            // console.log(data);

            event.preventDefault();
            // materia = editor.getData();
            data = $( this ).serialize();
            // data.push({name:'materia', value:editor});
            // console.log(data)

            $.ajax({
                url:"site/configuracoes/editar_contatos.php",
                type:"POST",
                data,
                success:function(dados){

                    $.ajax({
                        url:"site/configuracoes/contatos.php",
                        success:function(dados){
                            $(".contatos").html(dados);
                        }
                    });

                    $.ajax({
                        url:"site/configuracoes/midias_sociais.php",
                        success:function(dados){
                            $(".midias_sociais").html(dados);
                        }
                    });

                }
            })
        });

    })
</script>