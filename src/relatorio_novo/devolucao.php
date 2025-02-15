<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

    if($_POST['acao'] == 'devolucao'){

        $query = "select * from relatorio where codigoPedido = '".trim($_POST['codigo_devolucao'])."'";
        $result = mysqli_query($con, $query);
        $d = mysqli_fetch_object($result);

        if(!$d->codigo){
            echo 'erro';
            exit();
        }
    }

?>
<style>
  .titulo<?=$md5?>{
    position:fixed;
    top:7px;
    margin-left:50px;
  }

</style>

        <div class="form-floating mb-3">
            <div class="form-control"><?=$d->codigoPedido?></div>
            <label for="codigoPedido">Código do Produto*</label>
        </div>

        <div class="form-floating mb-3">
            <div class="form-control"><?=dataBr($d->dataCriacao)?></div>
            <label for="dataCriacao">Data*</label>
        </div>

        <div class="form-floating mb-3">
            <div class="form-control"><?=$d->tituloItem?></div>
            <label for="tituloItem">Anúncios*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="date" require name="devolucao_data" id="devolucao_data" class="form-control" value="">
            <label for="dataCriacao">Data da Devolução*</label>
        </div>        

        <button cancelar type="button" class="btn btn-danger mt-3"> <i class="fa fa-cancel"></i> Cancelar</button>
        <button devolver type="button" class="btn btn-primary mt-3"> <i class="fa fa-save"></i> Devolver</button>


<script>


    $(function(){

      Carregando('none');

      $("button[devolver]").click(function(){
        devolucao = '1';
        devolucao_data = $("#devolucao_data").val();
        devolucao_relatorio = '<?=$_POST['relatorio']?>';
        codigo_pedido = '<?=$_POST['codigo_devolucao']?>';

        if(!devolucao || !devolucao_data || !devolucao_relatorio || !codigo_pedido){
            $.alert({
                title:"Erro",
                content:"Ocorreu um erro, favor confira os dados da devolução e tente novamente!",
                type:"red"
            })
            return false;
        }

        $.ajax({
            url:"src/relatorio_novo/index.php",
            type:"POST",
            data:{
                devolucao,
                devolucao_data,
                devolucao_relatorio,
                codigo_pedido,
                acao:'devolucao'
            },
            success:function(dados){
                $("#paginaHome").html(dados);
                janelaDevolucao.close();
            }
        })

      })

      $("button[cancelar]").click(function(){
        janelaDevolucao.close();
      })




    })
</script>