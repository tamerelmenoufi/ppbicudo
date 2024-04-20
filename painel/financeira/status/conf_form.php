<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");


        if($_POST['acao'] == 'salvar'){

            $dados = $_POST;
            unset($dados['acao']);
            unset($dados['codigo']);
      
            //Imagem
            $img = false;
            unset($dados['base64_arq']);
            unset($dados['imagem_tipo_arq']);
            unset($dados['imagem_nome_arq']);
            unset($dados['imagem_arq']);
      
            if($_POST['base64_arq'] and $_POST['imagem_tipo_arq'] and $_POST['imagem_nome_arq']){
      
              if(is_file("../../volume/wapp/status/{$_POST['status']}/{$_POST['imagem_arq']}")) unlink("../../volume/wapp/status/{$_POST['status']}/{$_POST['imagem_arq']}");
      
              $base64 = explode('base64,', $_POST['base64_arq']);
              $img = base64_decode($base64[1]);
              $ext = substr($_POST['imagem_nome_arq'], strripos($_POST['imagem_nome_arq'],'.'), strlen($_POST['imagem_nome_arq']));
              $nome = md5($_POST['base64_arq'].$_POST['imagem_tipo_arq'].$_POST['imagem_nome_arq'].date("YmdHis")).$ext;
      
              if(!is_dir("../../volume")) mkdir("../../volume");
              if(!is_dir("../../volume/wapp")) mkdir("../../volume/wapp");
              if(!is_dir("../../volume/wapp/status")) mkdir("../../volume/wapp/status");
              if(!is_dir("../../volume/wapp/status/{$_POST['status']}")) mkdir("../../volume/wapp/status/{$_POST['status']}");
              if(file_put_contents("../../volume/wapp/status/{$_POST['status']}/".$nome, $img)){
                $dados['arquivo'] = $nome;
              }

              $dados['tipo_arquivo'] = $ext;

            }
            //Fim da Verificação da Imagem
      
            $campos = [];
            foreach($dados as $i => $v){
              $campos[] = "{$i} = '{$v}'";
            }
            if($_POST['codigo']){
              $query = "UPDATE status_mensagens set ".implode(", ",$campos)." WHERE codigo = '{$_POST['codigo']}'";
              mysqli_query($con, $query);
              $acao = mysqli_affected_rows($con);
            }else{
              echo $query = "INSERT INTO status_mensagens set ".implode(", ",$campos)."";
              mysqli_query($con, $query);
              $acao = mysqli_affected_rows($con);
            }
      
            if($acao){
              echo "Atualização realizada com sucesso!";
            }else{
              echo "Nenhuma alteração foi registrada!";
            }
      
            exit();
      
        }


        $query = "select * from status where codigo = '{$_POST['cod']}'";
        $result = mysqli_query($con, $query);
        $d = mysqli_fetch_object($result);

?>
<style>
    .Titulo<?=$md5?>{
        position:absolute;
        left:60px;
        top:8px;
        z-index:0;
    }
</style>
<h4 class="Titulo<?=$md5?>">Mensagens - Wapp</h4>

<h5><?="{$d->status} - {$d->descricao}"?></h5>

<?php
    $query = "select * from status_mensagens where codigo = '{$_POST['editar']}'";
    $result = mysqli_query($con, $query);
    $m = mysqli_fetch_object($result);
?>
<form>
    <div class="mb-3">
        <label for="nome" class="form-label">Nome</label>
        <input type="text" class="form-control" name="nome" id="nome" value="<?=$m->nome?>" aria-describedby="nome_descricao">
        <div id="nome_descricao" class="form-text">Digite o nome de identificação da mensagem</div>
    </div>

    <div class="mb-3">
        <label for="mensagem" class="form-label">Mensagem</label>
        <textarea class="form-control" style="height:100px;" id="mensagem" name="mensagem" placeholder="Descrição do Banner"><?=$m->mensagem?></textarea>
        <div id="mensagem_descricao" class="form-text">Digite conteúdo de sua mensagem</div>
    </div>

    <div showImage_arq class="form-floating" style="display:<?=(($m->arquivo)?'block':'none')?>">
        <img src="volume/wapp/status/<?="{$d->codigo}/{$m->arquivo}"?>" class="img-fluid mt-3 mb-3" alt="" />
    </div>


    <input type="file" opc="arq" class="form-control" placeholder="Arquivo">
    <input type="hidden" id="base64_arq" name="base64_arq" value="" />
    <input type="hidden" id="imagem_tipo_arq" name="imagem_tipo_arq" value="" />
    <input type="hidden" id="imagem_nome_arq" name="imagem_nome_arq" value="" />
    <input type="hidden" id="imagem_arq" name="imagem_arq" value="<?=$m->arquivo?>" />
    <div class="form-text mb-3">Anexar um arquivo</div>

    <button voltar type="button" class="btn btn-primary">Voltar</button>
    <button type="submit" class="btn btn-primary">Submit</button>
    <input type="hidden" name="status" id="status" value="<?=$d->codigo?>" />
    <input type="hidden" id="acao" name="acao" value="salvar" >
    <input type="hidden" id="codigo" name="codigo" value="<?=$m->codigo?>" >
    <input type="hidden" id="tipo" name="tipo" value="<?=$m->tipo?>" >
</form>

<script>


$(function(){

    Carregando('none');

    $( "form" ).on( "submit", function( event ) {

        data = [];

        event.preventDefault();

        data = $( this ).serialize();
        Carregando();
        $.ajax({
            url:"financeira/status/conf_form.php",
            type:"POST",
            data,
            success:function(dados){
                console.log(dados)
                $.ajax({
                    type:"POST",
                    data:{
                        cod:'<?=$d->codigo?>'
                    },
                    url:"financeira/status/conf.php",
                    success:function(dados){
                        $(".LateralDireita").html(dados);
                    }
                });

            }
        });
    });


    $("button[voltar]" ).on( "click", function( event ) {
        Carregando();
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

    });

    if (window.File && window.FileList && window.FileReader) {

        $('input[type="file"]').change(function () {

            if ($(this).val()) {
                var files = $(this).prop("files");
                opc = $(this).attr("opc");
                for (var i = 0; i < files.length; i++) {
                    (function (file) {
                        var fileReader = new FileReader();
                        fileReader.onload = function (f) {


                        var Base64 = f.target.result;
                        var type = file.type;
                        var name = file.name;
                        console.log(type);
                        if(type.indexOf("image") != -1){

                        //*
                        //////////////////////////////////////////////////////////////////

                        var img = new Image();
                        img.src = f.target.result;

                        img.onload = function () {



                            // CREATE A CANVAS ELEMENT AND ASSIGN THE IMAGES TO IT.
                            var canvas = document.createElement("canvas");

                            var value = 50;

                            // RESIZE THE IMAGES ONE BY ONE.
                            w = img.width;
                            h = img.height;
                            img.width = 800 //(800 * 100)/img.width // (img.width * value) / 100
                            img.height = (800 * h / w) //(img.height/100)*img.width // (img.height * value) / 100

                            var ctx = canvas.getContext("2d");
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            canvas.width = img.width;
                            canvas.height = img.height;
                            ctx.drawImage(img, 0, 0, img.width, img.height);

                            // $('.Foto').append(img);      // SHOW THE IMAGES OF THE BROWSER.
                            // console.log(canvas.toDataURL(file.type));

                            ///////


                            var Base64 = canvas.toDataURL(file.type); //f.target.result;

                            $("#base64_arq").val(Base64);
                            $("#imagem_tipo_arq").val(type);
                            $("#imagem_nome_arq").val(name);
                            $("#tipo").val('img');

                            $("div[showImage_arq] img").attr("src",Base64);
                            $("div[showImage_arq]").css("display",'block');

                            //////



                        }

                        //////////////////////////////////////////////////////////////////
                        //*/

                        }else{

                        
                            var Base64 = f.target.result;
                            var type = file.type;
                            var name = file.name;

                            $("#base64_arq").val(Base64);
                            $("#imagem_tipo_arq").val(type);
                            $("#imagem_nome_arq").val(name);
                            $("#tipo").val('arq');

                            $("div[showImage_arq] img").attr("src",Base64);
                            $("div[showImage_arq]").css("display",'block');
                        
                        }


                    };
                    fileReader.readAsDataURL(file);
                })(files[i]);
            }
        }
        });
        } else {
        alert('Nao suporta HTML5');
        }


})


</script>