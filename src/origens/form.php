<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");


    if($_POST['acao'] == 'salvar'){

        $data = $_POST;
        $attr = [];

        unset($data['codigo']);
        unset($data['acao']);

        //Imagem
        $img = false;
        unset($dados['base64']);
        unset($dados['imagem_tipo']);
        unset($dados['imagem_nome']);

        if($_POST['base64'] and $_POST['imagem_tipo'] and $_POST['imagem_nome']){

            if($_POST['imagem']) unlink("../volume/origens/{$_POST['imagem']}");

            $base64 = explode('base64,', $_POST['base64']);
            $img = base64_decode($base64[1]);
            $ext = substr($_POST['imagem_nome'], strripos($_POST['imagem_nome'],'.'), strlen($_POST['imagem_nome']));
            $nome = md5($_POST['base64'].$_POST['imagem_tipo'].$_POST['imagem_nome']).$ext;

            if(!is_dir("../volume/origens")) mkdir("../volume/origens");
            if(file_put_contents("../volume/origens/".$nome, $img)){
            $dados['imagem'] = $nome;
            }
        }
        //Fim da Verificação da Imagem


        foreach ($data as $name => $value) {
            $attr[] = "{$name} = '" . mysqli_real_escape_string($con, $value) . "'";
        }

        $attr = implode(', ', $attr);

        if($_POST['codigo']){
            $query = "update origens set {$attr} where codigo = '{$_POST['codigo']}'";
            mysqli_query($con,$query);
            $cod = $_POST['codigo'];
        }else{
            $query = "insert into origens set {$attr}";
            mysqli_query($con,$query);
            $cod = mysqli_insert_id($con);
        }

        $retorno = [
            'status' => true,
            'codigo' => $query
        ];

        echo json_encode($retorno);

        exit();
    }


    $query = "select * from origens where codigo = '{$_POST['cod']}'";
    $result = mysqli_query($con,$query);
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
<h4 class="Titulo<?=$md5?>">Cadastro de Origem</h4>
    <form id="form-<?= $md5 ?>">
        <div class="row">
            <div class="col">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome completo" value="<?=$d->nome?>">
                    <label for="nome">Nome*</label>
                </div>

                <div showImage class="form-floating" style="display:<?=(($d->imagem)?'block':'none')?>">
                    <img src="<?=$localPainel?>site/volume/origens/<?=$d->imagem?>" class="img-fluid mt-3 mb-3" alt="" />
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
                    <select name="status" class="form-control" id="status">
                        <option value="1" <?=(($d->status == '1')?'selected':false)?>>Liberado</option>
                        <option value="0" <?=(($d->status == '0')?'selected':false)?>>Bloqueado</option>
                    </select>
                    <label for="email">Situação</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div style="display:flex; justify-content:end">
                    <button cancelar type="button" data-bs-dismiss="offcanvas" class="btn btn-danger btn-sm me-3" > <i class="fa fa-cancel"></i> Cancelar</button>
                    <button type="submit" class="btn btn-success btn-sm"><i class="fa-regular fa-floppy-disk"></i> Salvar</button>
                    <input type="hidden" id="codigo" value="<?=$_POST['cod']?>" />
                </div>
            </div>
        </div>
    </form>

    <script>
        $(function(){
            Carregando('none');

            $('#form-<?=$md5?>').submit(function (e) {

                e.preventDefault();

                var codigo = $('#codigo').val();
                var campos = $(this).serializeArray();

                if (codigo) {
                    campos.push({name: 'codigo', value: codigo})
                }

                campos.push({name: 'acao', value: 'salvar'})

                Carregando();

                $.ajax({
                    url:"src/origens/form.php",
                    type:"POST",
                    typeData:"JSON",
                    mimeType: 'multipart/form-data',
                    data: campos,
                    success:function(dados){
                    console.log(dados)
                        // if(dados.status){
                            $.ajax({
                                url:"src/origens/index.php",
                                type:"POST",
                                success:function(dados){
                                    $("#paginaHome").html(dados);
                                    let myOffCanvas = document.getElementById('offcanvasDireita');
                                    let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                                    openedCanvas.hide();
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


                $("button[cancelar]").click(function(){
                    let myOffCanvas = document.getElementById('offcanvasDireita');
                    let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                    openedCanvas.hide();
                })


        })
    </script>