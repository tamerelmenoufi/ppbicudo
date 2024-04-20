<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    if($_POST['excluir']){
      $query = "DELETE from menus WHERE codigo = '{$_POST['excluir']}'";
      mysqli_query($con, $query);
      $acao = mysqli_affected_rows($con);
      if($acao){
        echo "Atualização realizada com sucesso!";
      }else{
        echo "Ocorreu um erro!";
      }
      exit();
    }

    if($_POST['acao'] == 'salvar'){

      $dados = $_POST;
      unset($dados['acao']);
      unset($dados['codigo']);

      $campos = [];
      foreach($dados as $i => $v){
        $campos[] = "{$i} = '".addslashes($v)."'";
      }
      if($_POST['codigo']){
        $query = "UPDATE menus set ".implode(", ",$campos)." WHERE codigo = '{$_POST['codigo']}'";
        mysqli_query($con, $query);
        $acao = mysqli_affected_rows($con);
      }else{
        $query = "INSERT INTO menus set ".implode(", ",$campos)."";
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
      $query = "select * from menus where codigo = '{$_POST['cod']}'";
      $result = mysqli_query($con, $query);
      $d = mysqli_fetch_object($result);
    }else{
      $query = "select * from menus where codigo = '{$_POST['vinculo']}'";
      $result = mysqli_query($con, $query);
      $v = mysqli_fetch_object($result);
    }

?>
<style>

</style>

<div class="card">
  <div class="card-header">
    <?=(($d->codigo)?"Editar o registro do menu `<i>{$d->titulo}</i>`":"Inserir um novo menu venculado ao nível <i>".(($v->titulo)?:'da Raíz')."</i>")?>
  </div>
  <div class="card-body">
    <form id="acaoMenu">
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="titulo" placeholder="Página Principal" value="<?=$d->titulo?>">
        <label for="titulo">Título</label>
        <div class="form-text">Digite o nome do menu que aparecerá no site.</div>
      </div>
      <div class="form-floating">
        <input type="text" class="form-control" id="url" placeholder="URL" value="<?=$d->url?>">
        <label for="url">URL</label>
        <div class="form-text">Digite o endereço URL (linque para a navegação).</div>
      </div>
      <button salvar type="button" class="btn btn-primary mt-3"> <i class="fa fa-save"></i> Salvar Dados</button>
      <button cancelar type="button" class="btn btn-danger mt-3"> <i class="fa fa-cancel"></i> Cancelar</button>

      <input type="hidden" id="acao" value="salvar" >
      <input type="hidden" id="codigo" value="<?=$d->codigo?>" >
      <input type="hidden" id="vinculo" value="<?=(($_POST['cod'])?$d->vinculo:$_POST['vinculo'])?>" >
    </form>
  </div>
</div>

<script>
    $(function(){

      Carregando('none');

      $("#acaoMenu button[cancelar]").click(function(){
        $("div[menuForm]").html('');
      })

      $("#acaoMenu button[salvar]").click(function(){
        data = [];

        $("#acaoMenu input[id]").each(function(){
          // console.log($(this).val());
          data.push({'name':$(this).attr('id'), 'value':$(this).val()});
        });
        // console.log(data)
        $.ajax({
          url:"site/menu/form.php",
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
            $("div[menuForm]").html('');
            $.ajax({
              url:"site/menu/menu.php",
              success:function(dados){
                  $("div[montaMenu]").html(dados);
                  // console.log(dados);
              }
            });
          },
          error:function(){
            $.alert('Ocorreu um erro');
          }
        });
      });

    })
</script>