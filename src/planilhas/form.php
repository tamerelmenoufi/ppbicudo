<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    if($_POST['acao'] == 'salvar'){

      $dados = $_POST;
      unset($dados['acao']);

      //Imagem
      $img = false;
      unset($dados['base64']);
      unset($dados['imagem_tipo']);
      unset($dados['imagem_nome']);

      if($_POST['base64'] and $_POST['imagem_tipo'] and $_POST['imagem_nome']){

        if($_POST['imagem']) unlink("../volume/planilhas/{$_POST['imagem']}");

        $base64 = explode('base64,', $_POST['base64']);
        $img = base64_decode($base64[1]);
        $ext = substr($_POST['imagem_nome'], strripos($_POST['imagem_nome'],'.'), strlen($_POST['imagem_nome']));
        $nome = md5($_POST['base64'].$_POST['imagem_tipo'].$_POST['imagem_nome']).$ext;

        if(!is_dir("../volume")) mkdir("../volume");
        if(!is_dir("../volume/planilhas")) mkdir("../volume/planilhas");
        if(file_put_contents("../volume/planilhas/".$nome, $img)){
          $dados['imagem'] = $nome;
          $dados['lote'] = md5($nome.date("YmdHis"));
          $dados['data'] = date("Y-m-d H:i:s");
          $dados['usuario'] = $_SESSION['appLogin']->codigo;
          $dados['situacao'] = '0';

        }
      }
      //Fim da Verificação da Imagem


      $campos = [];
      foreach($dados as $i => $v){
        $campos[] = "{$i} = '{$v}'";
      }

      $query = "INSERT INTO planilhas set ".implode(", ",$campos)."";
      mysqli_query($con, $query);
      $acao = mysqli_affected_rows($con);

      if($acao){
        echo "Atualização realizada com sucesso!";
      }else{
        echo "Nenhuma alteração foi registrada!";
      }

      exit();


    }


?>
<style>
  .titulo<?=$md5?>{
    position:fixed;
    top:7px;
    margin-left:50px;
  }
</style>

<h3 class="titulo<?=$md5?>">Importação de Planilhas</h3>

    <form id="acaoMenu">


        <input type="file" class="form-control" placeholder="Banner">
        <input type="hidden" id="base64" name="base64" value="" />
        <input type="hidden" id="imagem_tipo" name="imagem_tipo" value="" />
        <input type="hidden" id="imagem_nome" name="imagem_nome" value="" />
        <input type="hidden" id="imagem" name="imagem" value="<?=$d->imagem?>" />
        <div class="form-text mb-3">Selecione a planilha para importação</div>


      <button type="submit" data-bs-dismiss="offcanvas" class="btn btn-primary mt-3"> <i class="fa fa-save"></i> Importar Planilha</button>
      <button cancelar type="button" data-bs-dismiss="offcanvas" class="btn btn-danger mt-3"> <i class="fa fa-cancel"></i> Cancelar</button>

      <input type="hidden" id="acao" name="acao" value="salvar" >

    </form>

<script>


    $(function(){

      Carregando('none');

      $( "form" ).on( "submit", function( event ) {

        event.preventDefault();
        data = $( this ).serialize();

        $.ajax({
          url:"site/planilhas/form.php",
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
              url:"site/planilhas/index.php",
              success:function(dados){
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

                          var Base64 = f.target.result;
                          var type = file.type;
                          var name = file.name;

                          $("#base64").val(Base64);
                          $("#imagem_tipo").val(type);
                          $("#imagem_nome").val(name);

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