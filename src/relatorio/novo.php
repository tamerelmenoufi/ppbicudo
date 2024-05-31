<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

    if($_POST['acao'] == 'salvar'){

      $dados = $_POST;
      unset($dados['acao']);

      $numeros = [
        'ValorPedidoXquantidade',
        'CustoEnvio',
        'PrecoCusto',
        'CustoEnvioSeller',
        'TarifaGatwayPagamento',
        'TarifaMarketplace'
      ];

      $campos = [];
      foreach($dados as $i => $v){
        if(in_array($i, $numeros)){
          $v = str_replace(",",".",$v);
          $campos[] = "{$i} = '{$v}'";
        }else{
          $campos[] = "{$i} = '".addslashes($v)."'";
        }
        
      }

      $query = "INSERT INTO relatorio SET ".implode(", ",$campos);
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

<h3 class="titulo<?=$md5?>">Inserir Registro de Venda</h3>

    <form id="acaoMenu">

        <div class="form-floating mb-3">
            <input type="text" require name="codigoPedido" id="codigoPedido" class="form-control" placeholder="Pagamento Produto" value="<?=$d->codigoPedido?>">
            <label for="codigoPedido">Código do Produto*</label>
        </div>

        <div class="form-floating mb-3">
            <div class="form-control"><?=dataBr($d->dataCriacao)?></div>
            <input type="datetime-local" require name="dataCriacao" id="dataCriacao" class="form-control" placeholder="Pagamento Produto" value="<?=$d->dataCriacao?>">

            <label for="dataCriacao">Data*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" require name="tituloItem" id="tituloItem" class="form-control" placeholder="Pagamento Produto" value="<?=$d->tituloItem?>">
            <label for="tituloItem">Anúncios*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" require name="ValorPedidoXquantidade" id="ValorPedidoXquantidade" class="form-control" placeholder="Pagamento Produto" value="<?=number_format($d->ValorPedidoXquantidade,2,',',false)?>">
            <label for="ValorPedidoXquantidade">Pagamento Produto*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" require name="CustoEnvio" id="CustoEnvio" class="form-control" placeholder="Pagamento Frete" value="<?=number_format($d->CustoEnvio,2,',',false)?>">
            <label for="CustoEnvio">Pagamento Frete*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" require name="PrecoCusto" id="PrecoCusto" class="form-control" placeholder="Custo Produto" value="<?=number_format($d->PrecoCusto,2,',',false)?>">
            <label for="PrecoCusto">Custo Produto*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" require name="CustoEnvioSeller" id="CustoEnvioSeller" class="form-control" placeholder="Custo Frete" value="<?=number_format($d->CustoEnvioSeller,2,',',false)?>">
            <label for="CustoEnvioSeller">Custo Frete*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" require name="TarifaGatwayPagamento" id="TarifaGatwayPagamento" class="form-control" placeholder="Taxa Entrega" value="<?=number_format($d->TarifaGatwayPagamento,2,',',false)?>">
            <label for="TarifaGatwayPagamento">Taxa Entrega*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" require name="TarifaMarketplace" id="TarifaMarketplace" class="form-control" placeholder="Taxa Marketplace" value="<?=number_format($d->TarifaMarketplace,2,',',false)?>">
            <label for="TarifaMarketplace">Taxa Marketplace*</label>
        </div>


        <div class="form-floating mb-3">
            <textarea name="observacoes" id="observacoes" class="form-control" style="height:200px;"><?=$d->observacoes?></textarea>
            <label for="observacoes">Observações*</label>
        </div>        


        <button cancelar type="button" data-bs-dismiss="offcanvas" class="btn btn-danger mt-3"> <i class="fa fa-cancel"></i> Cancelar</button>
        <button type="submit" class="btn btn-primary mt-3"> <i class="fa fa-save"></i> Salvar</button>

        <input type="hidden" id="acao" name="acao" value="salvar" >
        <input type="hidden" id="relatorio" name="relatorio" value="<?=$_POST['relatorio']?>" >
        <input type="hidden" id="conta" name="conta" value="<?=$_POST['conta']?>" >
        <input type="hidden" id="origem" name="origem" value="<?=$_POST['origem']?>" >
        <input type="hidden" id="planilha" name="planilha" value="<?=$_POST['planilha']?>" >

    </form>

<script>


    $(function(){

      Carregando('none');

      $("#deletado").click(function(){
        if($(this).prop("checked") == true){
          $(".deletado").css("display","block");
        }else{
          $(".deletado").css("display","none");
        }
        $("#deletado_justificativa").val('');
      })

      $( "form" ).on( "submit", function( event ) {
        Carregando();
        event.preventDefault();
        data = $( this ).serialize();

        // deletado = $("#deletado").prop('checked');
        // justificativa = $("#deletado_justificativa").val();
        // if(deletado == true && !justificativa){
        //   $.alert({
        //     title:"Justificativa",
        //     content:"Informe a justificativa da exclusão do registro!",
        //     type:'red'
        //   });
        //   // let myOffCanvas = document.getElementById('offcanvasDireita');
        //   // let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
        //   // openedCanvas.show();
        //   Carregando('none');
        //   return false;
        // }

        $.ajax({
          url:"src/relatorio/form.php",
          type:"POST",
          data,
          success:function(dados){
            // console.log(dados)
            Carregando('none');
            $.alert({
              content:dados,
              type:"orange",
              title:false,
              buttons:{
                'ok':{
                  text:'<i class="fa-solid fa-check"></i> OK',
                  btnClass:'btn btn-warning',
                  action:function(){
                    Carregando();
                    $.ajax({
                      url:"src/relatorio/index.php",
                      success:function(dados){
                          $("#paginaHome").html(dados);
                          let myOffCanvas = document.getElementById('offcanvasDireita');
                          let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                          openedCanvas.hide();
                      }
                    });                    
                  }
                }
              }
            });

          }
        });
      });

      $("button[cancelar]").click(function(){
          let myOffCanvas = document.getElementById('offcanvasDireita');
          let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
          openedCanvas.hide();
      })




    })
</script>