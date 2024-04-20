<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");


    if($_POST['acao'] == 'salvar'){

        $data = $_POST;
        $attr = [];

        unset($data['codigo']);
        unset($data['acao']);

        foreach ($data as $name => $value) {
            $attr[] = "{$name} = '" . addslashes($value) . "'";
        }

        $attr = implode(', ', $attr);

        // $query = "update status set {$attr} where codigo = '{$_POST['codigo']}'";
        // mysqli_query($con, $query);
        $cod = $_POST['codigo'];


        $retorno = [
            'status' => true,
            'codigo' => $cod
        ];

        echo json_encode($retorno);

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
<h4 class="Titulo<?=$md5?>">Configurações para o Status</h4>
    <form id="form-<?= $md5 ?>">
        <div class="row">
            <div class="col">
                <div class="form-floating mb-3">
                    <input required type="text" class="form-control" id="nome" name="nome" placeholder="Nome completo" value="<?=$d->nome?>">
                    <label for="nome">Nome*</label>
                </div>
                <div class="form-floating mb-3">
                    <input required type="text" name="cpf" id="cpf" class="form-control" placeholder="CPF" value="<?=(($_POST['cpf'])?:$d->cpf)?>">
                    <label for="cpf">CPF*</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div style="display:flex; justify-content:end">
                    <button type="submit" class="btn btn-success btn-ms">Salvar</button>
                    <input type="hidden" name="codigo" id="codigo" value="<?=$d->codigo?>">
                </div>
            </div>
        </div>
    </form>

    <script>
        $(function(){
            Carregando('none');


            $('#form-<?=$md5?>').submit(function (e) {

                e.preventDefault();

                var campos = $(this).serializeArray();


                campos.push({name: 'acao', value: 'salvar'})

                Carregando();

                $.ajax({
                    url:"financeira/status/conf.php",
                    type:"POST",
                    typeData:"JSON",
                    mimeType: 'multipart/form-data',
                    data: campos,
                    success:function(dados){
                        // console.log(dados)
                        // if(dados.status){
                            $.ajax({
                                url:retorno,
                                type:"POST",
                                success:function(dados){
                                    $(".LateralDireita").html(dados);
                                    // let myOffCanvas = document.getElementById('offcanvasDireita');
                                    // let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                                    // openedCanvas.hide();
                                }
                            });
                        // }
                    },
                    error:function(erro){

                        // $.alert('Ocorreu um erro!' + erro.toString());
                        //dados de teste
                    }
                });

            });

        })
    </script>