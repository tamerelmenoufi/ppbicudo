<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    if($_POST['excluir']){

    }

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

        if($_POST['imagem']) unlink("../volume/paginas_topicos/{$_POST['imagem']}");

        $base64 = explode('base64,', $_POST['base64']);
        $img = base64_decode($base64[1]);
        $ext = substr($_POST['imagem_nome'], strripos($_POST['imagem_nome'],'.'), strlen($_POST['imagem_nome']));
        $nome = md5($_POST['base64'].$_POST['imagem_tipo'].$_POST['imagem_nome']).$ext;

        if(!is_dir("../volume/paginas_topicos")) mkdir("../volume/paginas_topicos");
        if(file_put_contents("../volume/paginas_topicos/".$nome, $img)){
          $dados['imagem'] = $nome;
        }
      }
      //Fim da Verificação da Imagem


      $campos = [];
      foreach($dados as $i => $v){
        $campos[] = "{$i} = '".addslashes($v)."'";
      }

      if($_POST['codigo']){
        $query = "UPDATE paginas_topicos set ".implode(", ",$campos)." WHERE codigo = '{$_POST['codigo']}'";
        mysqli_query($con, $query);
        $acao = mysqli_affected_rows($con);
      }else{
        $campos[] = "topicos = '{\"titulo\": [], \"descricao\": []}'";
        echo $query = "INSERT INTO paginas_topicos set ".implode(", ",$campos)."";
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



?>
<style>
  .titulo<?=$md5?>{
    position:fixed;
    top:7px;
    margin-left:50px;
  }
  .tab-pane{
    border-left:1px solid #dee2e6;
    border-right:1px solid #dee2e6;
    border-bottom:1px solid #dee2e6;
    padding:20px;
  }
</style>

<h3 class="titulo<?=$md5?>">Gerenciamento de Páginas com Tópicos</h3>



<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" src="site/paginas_topicos/formulario.php" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Formulário</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" <?=(($_POST['cod'])?false:'disabled')?> src="site/paginas_topicos/topicos.php" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Tópicos</button>
  </li>
</ul>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">...</div>
</div>




<script>
    $(function(){

      // Carregando('none');

      $.ajax({
        url:"site/paginas_topicos/formulario.php",
        type:"POST",
        data:{
          cod:'<?=$_POST['cod']?>',
        },
        success:function(dados){
          $("#home-tab-pane").html(dados);
        }
      });

      $("button[src]").click(function(){
        Carregando();
        url = $(this).attr("src");
        $.ajax({
          url,
          type:"POST",
          data:{
            cod:'<?=$_POST['cod']?>',
          },
          success:function(dados){
            $("#home-tab-pane").html(dados);
          }
        });
      });


    })
</script>