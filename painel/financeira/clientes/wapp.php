<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    if($_POST['acao'] == 'enviar'){
        $query = "insert into wapp_chat set de = '{$_POST['de']}', para = '{$_POST['para']}', mensagem = '{$_POST['mensagem']}', usuario = '{$_SESSION['ProjectPainel']->codigo}', data = NOW()";
        if(mysqli_query($con, $query)){

            $wgw = new wgw;
            $wgw->SendTxt([
              'mensagem'=>$_POST['mensagem'],
              'para'=>'55'.$_POST['para']
            ]);

        }

        exit();
    }

    if($_POST['acao'] == 'receber'){
        $query = "select * from wapp_chat where de = '{$_POST['de']}' and para = '{$_POST['para']}' and data > '{$_POST['ultimo_acesso']}' order by data desc ";
        $result = mysqli_query($con, $query);
        $retorno = [];
        while($d = mysqli_fetch_object($result)){
            $retorno[] = [
                'de'=>$d->de,
                'para'=>$d->para,
                'mensagem'=>$d->mensagem,
                'data'=>dataBr($d->data),
                'ultimo_acesso'=>$d->data
            ];
        }
        echo json_encode($retorno);
        exit();
    }

    $c = mysqli_fetch_object(mysqli_query($con, "select * from clientes where codigo = '{$_POST['mensagens']}'"));

    $phoneNumber = str_replace([' ','-','(',')'],false,trim($c->phoneNumber));

?>

<style>
    .Titulo<?=$md5?>{
        position:absolute;
        left:60px;
        top:8px;
        z-index:0;
    }
    .topo<?=$md5?>{
        position:absolute;
        background:#eee;
        left:0;
        right:0;
        height:40px;
        top:50px;
        padding:10px;
    }
    .palco<?=$md5?>{
        position:absolute;
        left:0;
        top:90px;
        bottom:85px;
        right:0;
        background-color:#f7f7f7;
        overflow:auto;
        border-left:3px solid #e4e4e4;
        padding:10px;      
    }    
    .rodape<?=$md5?>{
        position:absolute;
        background:#eee;
        left:0;
        right:0;
        bottom:0px;    
        height:85px;    
    }
</style>
<h4 class="Titulo<?=$md5?>">Mensagens WhatsApp</h4>
<div class="topo<?=$md5?>"><i class="fa-regular fa-comment-dots"></i> <?=$c->nome?></div>
<div class="palco<?=$md5?>">
    <?php
        $query = "select * from wapp_chat where (de = '{$ConfWappNumero}' and para = '{$phoneNumber}' or de = '{$phoneNumber}' and para = '{$ConfWappNumero}') order by data asc";
        $result = mysqli_query($con, $query);
        while($m = mysqli_fetch_object($result)){

            if($m->de == $ConfWappNumero){
    ?>
            <div class="d-flex flex-row-reverse">
                <div class="d-inline-flex flex-column m-1 p-2" style="max-width:60%; background-color:#dcf8c6; border:0; border-radius:10px;">
                    <div class="text-start" style="border:solid 0px red;"><?=$m->mensagem?></div>
                    <div class="text-end" style="color:#b6a29a; font-size:10px; border:solid 0px black;"><?=dataBr($m->data)?></div>
                </div>
            </div>
    <?php
            }else{
    ?>
            <div class="d-flex flex-row">
                <div class="d-inline-flex flex-column m-1 p-2" style="max-width:60%; background-color:#ffffff; border:0; border-radius:10px;">
                    <div class="text-start" style="border:solid 0px red;"><?=$m->mensagem?></div>
                    <div class="text-end" style="color:#b6a29a; font-size:10px; border:solid 0px black;"><?=dataBr($m->data)?></div>
                </div>
            </div>
    <?php
            }
            $ultimo_acesso = $m->data;
        }
    ?>
</div>
<div class="rodape<?=$md5?>">
    <div class="d-flex justify-content-between align-items-center m-3">
        <i class="fa-regular fa-face-smile p-3"></i>
        <input type="text" class="form-control p-3" id="chatMensagem" ultimo_acesso="<?=$ultimo_acesso?>" aria-describedby="chatMensagem">
        <i class="fa-solid fa-microphone p-3"></i>
        <i class="fa-regular fa-paper-plane p-3"></i>
    </div>
</div>

<script>
    $(function(){
        

        $("#chatMensagem").keypress(function(e){
            val = $(this).val();
            layout = '<div class="d-flex flex-row-reverse">'+
                     '<div class="d-inline-flex flex-column m-1 p-2" style="max-width:60%; background-color:#dcf8c6; border:0; border-radius:10px;">'+
                     '<div class="text-start" style="border:solid 0px red;">'+val+'</div>' +
                     '<div class="text-end" style="color:#b6a29a; font-size:10px; border:solid 0px black;">12:17</div>' +
                     '</div>' +
                     '</div>';

            if(e.which == 13 && val) {
                $(".palco<?=$md5?>").append(layout);

                $("#chatMensagem").val('');

                altura = $(".palco<?=$md5?>").prop("scrollHeight");
                div = $(".palco<?=$md5?>").height();
                $(".palco<?=$md5?>").scrollTop(altura + div);

                $.ajax({
                    url:"financeira/clientes/wapp.php",
                    type:"POST",
                    data:{
                        mensagem:val,
                        de:'<?=$ConfWappNumero?>',
                        para:'<?=$phoneNumber?>',
                        acao:'enviar'
                    },
                    success:function(dados){

                    }
                })
            }
        });


        verificarMensagem = setInterval(() => {
            ultimo_acesso = $("#chatMensagem").attr("ultimo_acesso");
            $.ajax({
                url:"financeira/clientes/wapp.php",
                type:"POST",
                dataType:"JSON",
                data:{
                    de:'<?=$phoneNumber?>',
                    para:'<?=$ConfWappNumero?>',
                    ultimo_acesso,
                    acao:'receber'
                },
                success:function(dados){

                    console.log(dados);

                    $.each(dados, function () {

                        layout = '<div class="d-flex flex-row">'+
                        '<div class="d-inline-flex flex-column m-1 p-2" style="max-width:60%; background-color:#ffffff; border:0; border-radius:10px;">'+
                        '<div class="text-start" style="border:solid 0px red;">'+this.mensagem+'</div>' +
                        '<div class="text-end" style="color:#b6a29a; font-size:10px; border:solid 0px black;">'+this.data+'</div>' +
                        '</div>' +
                        '</div>';

                        $(".palco<?=$md5?>").append(layout);

                        altura = $(".palco<?=$md5?>").prop("scrollHeight");
                        div = $(".palco<?=$md5?>").height();
                        $(".palco<?=$md5?>").scrollTop(altura + div);    
                        $("#chatMensagem").attr('ultimo_acesso', this.ultimo_acesso);   

                    })

                }
            });
        }, 10000);

    })
</script>