<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    if($_POST['acao'] == 'endereco'){

        $dados = $_POST;
        unset($dados['acao']);

        $campos = [];
        foreach($dados as $i => $v){
          $campos[] = "{$i} = '".addslashes($v)."'";
        }
        $campos[] = "coordenadas = ''";

        $query = "update configuracoes set  ".implode(", ",$campos)." WHERE codigo = '1'";
        mysqli_query($con, $query);
        exit();
    }


    $query = "select * from configuracoes where codigo = '1'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);
?>


    <div class="mb-3">
        <label class="form-label">CEP</label>
        <input type="text" class="form-control" value="<?=$d->cep?>" id="cep" >
    </div>

    <div class="mb-3">
        <label class="form-label">Rua</label>
        <input type="text" class="form-control" value="<?=$d->rua?>" id="rua" >
    </div>

    <div class="mb-3">
        <label class="form-label">Número</label>
        <input type="text" class="form-control" value="<?=$d->numero?>" id="numero" >
    </div>


    <div class="mb-3">
        <label class="form-label">Bairro</label>
        <input type="text" class="form-control" value="<?=$d->bairro?>" id="bairro" >
    </div>

    <div class="mb-3">
        <label class="form-label">Complemento</label>
        <input type="text" class="form-control" value="<?=$d->complemento?>" id="complemento" >
    </div>

    <div class="mb-3">
        <label class="form-label">Referência</label>
        <input type="text" class="form-control" value="<?=$d->referencia?>" id="referencia" >
    </div>

    <button
            class="btn btn-primary"

            data-bs-toggle="offcanvas"

            salvar_endereco

    >Editar Endereço</button>


<script>
    $(function(){

        Carregando('none');

        $("button[salvar_endereco]").click(function(){
            Carregando();
            data = [];
            data.push({name:'cep', value:$("#cep").val()});
            data.push({name:'rua', value:$("#rua").val()});
            data.push({name:'numero', value:$("#numero").val()});
            data.push({name:'bairro', value:$("#bairro").val()});
            data.push({name:'complemento', value:$("#complemento").val()});
            data.push({name:'referencia', value:$("#referencia").val()});
            data.push({name:'acao', value:'endereco'});
            $.ajax({
                url:"site/configuracoes/editar_endereco.php",
                type:"POST",
                data,
                success:function(dados){
                    $.ajax({
                        url:"site/configuracoes/endereco.php",
                        success:function(dados){
                            $(".endereco").html(dados);
                        }
                    });
                    $.ajax({
                        url:"site/configuracoes/visualizar_mapa.php",
                        success:function(dados){
                            $(".ver_mapa").html(dados);
                        }
                    });
                }
            })
        });

    })
</script>