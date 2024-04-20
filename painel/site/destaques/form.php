<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    if($_POST['acao'] == 'salvar'){

      $dados = $_POST;
      unset($dados['acao']);
      unset($dados['codigo']);

      //Imagem
      $img = false;
      unset($dados['base64']);
      unset($dados['imagem_tipo']);
      unset($dados['imagem_nome']);

      if($_POST['base64'] and $_POST['imagem_tipo'] and $_POST['imagem_nome']){

        if($_POST['imagem']) unlink("../volume/destaques/{$_POST['imagem']}");

        $base64 = explode('base64,', $_POST['base64']);
        $img = base64_decode($base64[1]);
        $ext = substr($_POST['imagem_nome'], strripos($_POST['imagem_nome'],'.'), strlen($_POST['imagem_nome']));
        $nome = md5($_POST['base64'].$_POST['imagem_tipo'].$_POST['imagem_nome']).$ext;

        if(!is_dir("../volume/destaques")) mkdir("../volume/destaques");
        if(file_put_contents("../volume/destaques/".$nome, $img)){
          $dados['imagem'] = $nome;
        }
      }
      //Fim da Verificação da Imagem


      $campos = [];
      foreach($dados as $i => $v){
        $campos[] = "{$i} = '{$v}'";
      }
      if($_POST['codigo']){
        $query = "UPDATE destaques set ".implode(", ",$campos)." WHERE codigo = '{$_POST['codigo']}'";
        mysqli_query($con, $query);
        $acao = mysqli_affected_rows($con);
      }else{
        $query = "INSERT INTO destaques set ".implode(", ",$campos)."";
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


    if($_POST['cod']){
      $query = "select * from destaques where codigo = '{$_POST['cod']}'";
      $result = mysqli_query($con, $query);
      $d = mysqli_fetch_object($result);
    }

?>
<style>
  .titulo<?=$md5?>{
    position:fixed;
    top:7px;
    margin-left:50px;
  }
</style>

<h3 class="titulo<?=$md5?>">Gerenciamento de Destques</h3>

    <form id="acaoMenu">

      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Título do Banner" value="<?=$d->titulo?>">
        <label for="titulo">Título</label>
        <div class="form-text">Digite o título da Notícia.</div>
      </div>

      <div class="form-floating mb-3">
        <!-- <textarea id="materia" name="materia"><?=$d->materia?></textarea> -->
        <!-- <label for="materia">Matéria</label> -->
        <!-- <div class="form-text">Digite o conteúdo da Matéria.</div> -->
        <textarea id="materia" name="materia"><?=$d->materia?></textarea>

      </div>

      <div showImage class="form-floating" style="display:<?=(($d->imagem)?'block':'none')?>">
        <img src="<?=$localPainel?>site/volume/destaques/<?=$d->imagem?>" class="img-fluid mt-3 mb-3" alt="" />
      </div>

      <!-- <div class="form-floating"> -->
        <input type="file" class="form-control" placeholder="Banner">
        <input type="hidden" id="base64" name="base64" value="" />
        <input type="hidden" id="imagem_tipo" name="imagem_tipo" value="" />
        <input type="hidden" id="imagem_nome" name="imagem_nome" value="" />
        <input type="hidden" id="imagem" name="imagem" value="<?=$d->imagem?>" />
        <!-- <label for="url">Banner</label> -->
        <div class="form-text mb-3">Selecione a imagem para o Banner</div>
      <!-- </div> -->

      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="video" name="video" value="<?=$d->video?>">
        <label for="video">Encorporar Vídeo (Youtube)</label>
        <div class="form-text">Digite o endereço do vídeo no Youtube (Ex.: https://www.youtube.com/watch?v=LXb3EKWsInQ).</div>
      </div>

      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="botao_titulo" name="botao_titulo" value="<?=$d->botao_titulo?>">
        <label for="botao_titulo">Título do Botão</label>
        <div class="form-text">Digite o nome do botão na área de destaque (a ausência da informação desabilita o botão)</div>
      </div>
      
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="botao_url" name="botao_url" value="<?=$d->botao_url?>">
        <label for="botao_url">Linque do Botão</label>
        <div class="form-text">Digite o linque ou endereço URL para o direcionamento do botão</div>
      </div>

      <!-- 
      <div class="form-floating">
        <select id="situacao" name="situacao" class="form-control" placeholder="Situação">
          <option value="1" <?=(($d->situacao == '1')?'selected':false)?>>Liberado</option>
          <option value="0" <?=(($d->situacao == '0')?'selected':false)?>>Bloqueado</option>
        </select>
        <label for="situacao">Banner</label>
        <div class="form-text">Selecione a imagem para o Banner</div>
      </div> -->




      <button type="submit" data-bs-dismiss="offcanvas" class="btn btn-primary mt-3"> <i class="fa fa-save"></i> Salvar Dados</button>
      <button cancelar type="button" data-bs-dismiss="offcanvas" class="btn btn-danger mt-3"> <i class="fa fa-cancel"></i> Cancelar</button>

      <input type="hidden" id="acao" name="acao" value="salvar" >
      <input type="hidden" id="codigo" name="codigo" value="<?=$d->codigo?>" >
    </form>

<script>

    ClassicEditor
    .create( document.querySelector( '#materia' ) )
    .then( editor => {
        console.log( editor );
    } )
    .catch( error => {
        console.error( error );
    } );

// console.log(editor);

    $(function(){




      Carregando('none');

      // $("#acaoMenu button[cancelar]").click(function(){
      //   $("div[formBanners]").html('');
      // })


      $( "form" ).on( "submit", function( event ) {

        event.preventDefault();
        // materia = editor.getData();
        data = $( this ).serialize();
        // data.push({name:'materia', value:editor});
        console.log(data);

        $.ajax({
          url:"site/destaques/form.php",
          type:"POST",
          data,
          success:function(dados){

            $.alert({
              content:dados,
              type:"orange",
              title:false,
              buttons:{
                'ok':{
                  text:'<i class="fa-solid fa-check"></i> OK',
                  btnClass:'btn btn-warning'
                }
              }
            });

            $("div[lista]").html('');
            $.ajax({
              url:"site/destaques/lista.php",
              success:function(dados){
                  // $("div[lista]").html(dados);
                  $("#paginaHome").html(dados);
              }
            });

          }
        });
      });





      if (window.File && window.FileList && window.FileReader) {

        $('input[type="file"]').change(function () {

            if ($(this).val()) {
                var files = $(this).prop("files");
                for (var i = 0; i < files.length; i++) {
                    (function (file) {
                        var fileReader = new FileReader();
                        fileReader.onload = function (f) {


                        /*
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
                            console.log(canvas.toDataURL(file.type));

                            ///////


                            // var Base64 = canvas.toDataURL(file.type); //f.target.result;

                            // $("#encode_file").val(Base64);
                            // $("#encode_file").attr("nome", name);
                            // $("#encode_file").attr("tipo", type);

                            // $(".Foto").css("background-image",`url(${Base64})`);
                            // $(".Foto div i").css("opacity","0");
                            // $(".Apagar span").css("opacity","1");

                            //////



                        }

                        //////////////////////////////////////////////////////////////////
                        //*/


                        var Base64 = f.target.result;
                        var type = file.type;
                        var name = file.name;

                        $("#base64").val(Base64);
                        $("#imagem_tipo").val(type);
                        $("#imagem_nome").val(name);

                        $("div[showImage] img").attr("src",Base64);
                        $("div[showImage]").css("display",'block');



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