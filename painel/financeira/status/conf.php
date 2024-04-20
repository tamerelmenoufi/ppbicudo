<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

        if($_POST['excluir']){

            $e = mysqli_fetch_object(mysqli_query($con, "select * from status_mensagens where codigo = '{$_POST['excluir']}'"));
            
            if(is_file("../../volume/wapp/status/{$e->status}/{$e->arquivo}")) unlink("../../volume/wapp/status/{$e->status}/{$e->arquivo}");
            
            mysqli_query($con, "delete from status_mensagens where codigo = '{$_POST['excluir']}'");

        }

        if($_POST['situacao']){
            
            mysqli_query($con, "update status_mensagens set situacao = '{$_POST['opc']}' where codigo = '{$_POST['situacao']}'");

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
<h4 class="Titulo<?=$md5?>">Mensagens - Wapp</h4>

<h5><?="{$d->status} - {$d->descricao}"?></h5>

<div class="d-flex flex-row-reverse">
    <button novo type="button" class="btn btn-success btn-sm ms-3"><i class="fa-solid fa-comment-medical"></i> Novo</button>
    <button telefones type="button" class="btn btn-warning btn-sm"><i class="fa-solid fa-gears"></i> Telefones</button>
</div>
<?php
    $query = "select * from status_mensagens where status = '{$d->codigo}' order by codigo desc";
    $result = mysqli_query($con, $query);
    while($m = mysqli_fetch_object($result)){
?>
<div class="card mt-3">
  <div class="card-header">
    <?=$m->nome?>
  </div>
  <div class="card-body">
        <?php
            if($m->tipo == 'arq'){

                if(in_array($m->tipo_arquivo, ["ogg","mp3"])){
        ?>
        <audio controls>
        <source src="volume/wapp/status/<?="{$d->codigo}/{$m->arquivo}"?>" type="audio/ogg">
        <source src="volume/wapp/status/<?="{$d->codigo}/{$m->arquivo}"?>" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
        <?php
                }elseif(in_array($m->tipo_arquivo, ["mp4"])){
        ?>
        <video width="100%" height="auto" controls>
        <source src="volume/wapp/status/<?="{$d->codigo}/{$m->arquivo}"?>" type="audio/ogg">
        <source src="volume/wapp/status/<?="{$d->codigo}/{$m->arquivo}"?>" type="audio/mpeg">
            Your browser does not support the video tag.
        </video>
        <?php           
                }else{
        ?>
        <div class="alert alert-primary d-flex align-items-center" role="alert">
            <a style="margin-right:10px;" href="volume/wapp/status/<?="{$d->codigo}/{$m->arquivo}"?>" target="_blank">
                <i class="fa-solid fa-file-lines"></i> Arquivo Anexo
            </a>
        </div>
        <?php  
                }


            }elseif($m->tipo == 'img'){
                echo "<div class='d-flex justify-content-center'><img class='img-fluid' src='volume/wapp/status/{$d->codigo}/{$m->arquivo}' /></div>";
            }
            if($m->mensagem){
                echo "<p>{$m->mensagem}</p>";
            }
            
        ?>
        <div class="d-flex justify-content-between">
            <div class="form-check form-switch">
                <input situacao="<?=$m->codigo?>" class="form-check-input" type="checkbox" role="switch" id="situacao<?=$m->codigo?>" <?=(($m->situacao)?'checked':false)?>>
                <label class="form-check-label" for="situacao<?=$m->codigo?>">Disponibilizar mensagem</label>
            </div>
            <button editar="<?=$m->codigo?>" class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-pen-to-square"></i> Editar</button>
            <button enviar="<?=$m->codigo?>" class="btn btn-outline-success btn-sm"><i class="fa-brands fa-whatsapp"></i> Enviar</button>
            <button excluir="<?=$m->codigo?>" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash-can"></i> Excluir</button>
        </div>
  </div>
</div>
<?php
    }
?>

<script>
    $(function(){

        Carregando('none');

        $("button[novo]").click(function(){
            $.ajax({
                url:"financeira/status/conf_form.php",
                type:"POST",
                data:{
                    cod:'<?=$d->codigo?>'
                },
                success:function(dados){
                    $(".LateralDireita").html(dados);
                    // let myOffCanvas = document.getElementById('offcanvasDireita');
                    // let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                    // openedCanvas.hide();
                }
            });            
        })


        $("button[telefones]").click(function(){
            $.ajax({
                url:"financeira/status/conf_numeros.php",
                type:"POST",
                data:{
                    cod:'<?=$d->codigo?>'
                },
                success:function(dados){
                    $(".LateralDireita").html(dados);
                    // let myOffCanvas = document.getElementById('offcanvasDireita');
                    // let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                    // openedCanvas.hide();
                }
            });            
        })

        $("button[editar]").click(function(){
            editar = $(this).attr("editar");
            Carregando();
            $.ajax({
                url:"financeira/status/conf_form.php",
                type:"POST",
                data:{
                    cod:'<?=$d->codigo?>',
                    editar
                },
                success:function(dados){
                    $(".LateralDireita").html(dados);
                    // let myOffCanvas = document.getElementById('offcanvasDireita');
                    // let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                    // openedCanvas.hide();
                }
            });            
        })


        $("input[situacao]").change(function(){
            situacao = $(this).attr("situacao");
            
            if($(this).prop("checked") == true){
                opc = '1';
            }else{
                opc = '0';
            }
            $.ajax({
                url:"financeira/status/conf.php",
                type:"POST",
                data:{
                    cod:'<?=$d->codigo?>',
                    situacao,
                    opc
                },
                success:function(dados){
                    // $(".LateralDireita").html(dados);
                    // let myOffCanvas = document.getElementById('offcanvasDireita');
                    // let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                    // openedCanvas.hide();
                }
            });            
        })

        $("button[enviar]").click(function(){
            Carregando();
            envio = $(this).attr("enviar");
            $.ajax({
                url:"financeira/status/enviarWapp.php",
                type:"POST",
                data:{
                    envio
                },
                success:function(dados){
                    $.alert("Envios processados!");
                    Carregando('none');
                    //$(".LateralDireita").html(dados);
                    // let myOffCanvas = document.getElementById('offcanvasDireita');
                    // let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                    // openedCanvas.hide();
                }
            });            
        })

        $("button[excluir]").click(function(){
            excluir = $(this).attr("excluir");

                    $.confirm({
                        content:"Dejse realmente excluir a mensagem?",
                        title:"Aviso de exclusão",
                        buttons:{
                            'Sim':{
                                text:'Sim',
                                btnClass:'btn btn-danger btn-sm',
                                action:function(){
                                    Carregando();
                                    $.ajax({
                                        url:"financeira/status/conf.php",
                                        type:"POST",
                                        data:{
                                            excluir,
                                            cod:'<?=$d->codigo?>'
                                        },
                                        success:function(dados){
                                            $(".LateralDireita").html(dados);
                                            // let myOffCanvas = document.getElementById('offcanvasDireita');
                                            // let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
                                            // openedCanvas.hide();
                                        }
                                    });
                        }
                    },
                    'Nao':{
                        text:'Não',
                        btnClass:'btn btn-secondary btn-sm',
                        action:function(){

                        }
                    }
                }
            })
                        
        })


    })
</script>