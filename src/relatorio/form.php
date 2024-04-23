<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

    if($_POST['acao'] == 'salvar'){

      $dados = $_POST;
      unset($dados['acao']);
      unset($dados['codigo']);

      $campos = [];
      foreach($dados as $i => $v){
        $campos[] = "{$i} = '{$v}'";
      }

      $query = "UPDATE relatorio SET ".implode(", ",$campos)."";
      mysqli_query($con, $query);
      $acao = mysqli_affected_rows($con);

      if($acao){
        echo "Atualização realizada com sucesso!";
      }else{
        echo "Nenhuma alteração foi registrada!";
      }

      exit();

    }

    $query = "select * from relatorio where codigo = '{$_POST['editar']}'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);

?>
<style>
  .titulo<?=$md5?>{
    position:fixed;
    top:7px;
    margin-left:50px;
  }
</style>

<h3 class="titulo<?=$md5?>">Editar Registro de Venda</h3>

    <form id="acaoMenu">

        <div class="form-floating mb-3">
            <input type="text" name="dataCriacao" id="dataCriacao" class="form-control" placeholder="dataCriacao" value="<?=$d->dataCriacao?>">
            <label for="dataCriacao">dataCriacao*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" name="codigoPedido" id="codigoPedido" class="form-control" placeholder="codigoPedido" value="<?=$d->codigoPedido?>">
            <label for="codigoPedido">codigoPedido*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" name="pedidoOrigem" id="pedidoOrigem" class="form-control" placeholder="pedidoOrigem" value="<?=$d->pedidoOrigem?>">
            <label for="pedidoOrigem">pedidoOrigem*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" name="tituloItem" id="tituloItem" class="form-control" placeholder="tituloItem" value="<?=$d->tituloItem?>">
            <label for="tituloItem">tituloItem*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" name="frete" id="frete" class="form-control" placeholder="frete" value="<?=$d->frete?>">
            <label for="frete">frete*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" name="ValorPedidoXquantidade" id="ValorPedidoXquantidade" class="form-control" placeholder="ValorPedidoXquantidade" value="<?=$d->ValorPedidoXquantidade?>">
            <label for="ValorPedidoXquantidade">ValorPedidoXquantidade*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" name="CustoEnvio" id="CustoEnvio" class="form-control" placeholder="CustoEnvio" value="<?=$d->CustoEnvio?>">
            <label for="CustoEnvio">CustoEnvio*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" name="CustoEnvioSeller" id="CustoEnvioSeller" class="form-control" placeholder="CustoEnvioSeller" value="<?=$d->CustoEnvioSeller?>">
            <label for="CustoEnvioSeller">CustoEnvioSeller*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" name="TarifaGatwayPagamento" id="TarifaGatwayPagamento" class="form-control" placeholder="TarifaGatwayPagamento" value="<?=$d->TarifaGatwayPagamento?>">
            <label for="TarifaGatwayPagamento">TarifaGatwayPagamento*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" name="TarifaMarketplace" id="TarifaMarketplace" class="form-control" placeholder="TarifaMarketplace" value="<?=$d->TarifaMarketplace?>">
            <label for="TarifaMarketplace">TarifaMarketplace*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" name="PrecoCusto" id="PrecoCusto" class="form-control" placeholder="PrecoCusto" value="<?=$d->PrecoCusto?>">
            <label for="PrecoCusto">PrecoCusto*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" name="Porcentagem" id="Porcentagem" class="form-control" placeholder="Porcentagem" value="<?=$d->Porcentagem?>">
            <label for="Porcentagem">Porcentagem*</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" name="Conta" id="Conta" class="form-control" placeholder="Conta" value="<?=$d->Conta?>">
            <label for="Conta">Conta*</label>
        </div>


        <button cancelar type="button" data-bs-dismiss="offcanvas" class="btn btn-danger mt-3"> <i class="fa fa-cancel"></i> Cancelar</button>
        <button type="submit" data-bs-dismiss="offcanvas" class="btn btn-primary mt-3"> <i class="fa fa-save"></i> Importar Planilha</button>

        <input type="hidden" id="acao" name="acao" value="salvar" >
        <input type="hidden" id="codigo" name="codigo" value="<?=$d->codigo?>" >

    </form>

<script>


    $(function(){

      Carregando('none');

      $( "form" ).on( "submit", function( event ) {
        Carregando();
        event.preventDefault();
        data = $( this ).serialize();
        $.ajax({
          url:"src/relatorio/form.php",
          type:"POST",
          data,
          success:function(dados){
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