<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    if($_POST['acao'] == 'direitos'){

        $dados = $_POST;
        unset($dados['acao']);

        $campos = [];
        $campos['direitos'] = "direitos = '".$_POST['direitos']."'";

        $query = "update configuracoes set  ".implode(", ",$campos)." WHERE codigo = '1'";
        mysqli_query($con, $query);
        exit();
    }


    $query = "select * from configuracoes where codigo = '1'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);

?>

<form class="acaoContatos">

    <div class="mb-3">
        <!-- <label class="form-label">E-mail (Mensagem Resposta)</label>
        <input type="text" class="form-control" value="<?=$d->direitos?>" id="email_resposta" > -->
        <textarea id="direitos" name="direitos"><?=$d->direitos?></textarea>
    </div>


    <button
            class="btn btn-primary"
            data-bs-toggle="offcanvas"
            type="submit"
            salvar_direitos
    >Salvar Direitos Reservados</button>
    <input type="hidden" id="acao" name="acao" value="direitos" >
</form>
<script>

    ClassicEditor
    .create( document.querySelector( '#direitos' ) )
    .then( editor => {
        console.log( editor );
    } )
    .catch( error => {
        console.error( error );
    } );
    // console.log(editor);



    $(function(){

        Carregando('none');

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
                url:"site/configuracoes/editar_direitos.php",
                type:"POST",
                data,
                success:function(dados){

                    $.ajax({
                        url:"site/configuracoes/direitos.php",
                        success:function(dados){
                            $(".direitos").html(dados);
                        }
                    });

                }
            })
        });

    })
</script>