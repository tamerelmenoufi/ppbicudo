<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");


    if($_POST['cod']){
      $query = "select * from paginas_topicos where codigo = '{$_POST['cod']}'";
      $result = mysqli_query($con, $query);
      $d = mysqli_fetch_object($result);
    }

    // else{
    //   $query = "select * from paginas_topicos where codigo = '{$_POST['vinculo']}'";
    //   $result = mysqli_query($con, $query);
    //   $v = mysqli_fetch_object($result);
    // }

?>



    <form id="acaoMenu">

      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Título da Página" value="<?=$d->titulo?>">
        <label for="titulo">Título</label>
        <div class="form-text">Digite o nome do menu que aparecerá no site.</div>
      </div>

      <div class="form-floating mb-3">
        <textarea class="form-control" style="height:100px;" id="descricao" name="descricao" placeholder="Descrição da Página"><?=$d->descricao?></textarea>
        <label for="titulo">Descrição do Banner</label>
        <div class="form-text">Digite a descrição do Banner.</div>
      </div>

      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="subtitulo" name="subtitulo" placeholder="Subtitulo da Página" value="<?=$d->subtitulo?>">
        <label for="subtitulo">Subtitulo</label>
        <div class="form-text">Digite o subtitulo da página que aparecerá no site.</div>
      </div>

      <div showImage class="form-floating" style="display:<?=(($d->imagem)?'block':'none')?>">
        <img src="<?=$localPainel?>site/volume/paginas_topicos/<?=$d->imagem?>" class="img-fluid mt-3 mb-3" alt="" />
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

      <!-- <div class="form-floating">
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
    $(function(){

      Carregando('none');

      // $("#acaoMenu button[cancelar]").click(function(){
      //   $("div[formBanners]").html('');
      // })


      $( "form" ).on( "submit", function( event ) {

        data = [];

        event.preventDefault();

        data = $( this ).serialize();

        $.ajax({
          url:"site/paginas_topicos/form.php",
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
              url:"site/paginas_topicos/lista.php",
              success:function(dados){
                  // $("div[listaBanners]").html(dados);
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