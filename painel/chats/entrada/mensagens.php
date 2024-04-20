<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");
?>
<style>

</style>
<?php
for($i=0;$i<100;$i++){
    if($i%2 == 0){
        $reverse = '-reverse';
        $cor = '#dcf8c6';
    }else{
        $reverse = false;
        $cor = '#ffffff';
    }
?>
    <div class="d-flex flex-row<?=$reverse?>">
        <div class="d-inline-flex flex-column m-1 p-2" style="max-width:60%; background-color:<?=$cor?>; border:0; border-radius:10px;">
            <div class="text-start" style="border:solid 0px red;">Texto da mensagem enviada Texto da mensagem enviada Texto da mensagem enviada Texto da mensagem enviada Texto da mensagem enviada </div>
            <div class="text-end" style="color:#b6a29a; font-size:10px; border:solid 0px black;">12:17</div>
        </div>
    </div>
<?php
}
?>

<script>

    $(function(){
        Carregando('none')

        altura = $(".exibeEmail").prop("scrollHeight");
        div = $(".exibeEmail").height();
        $(".exibeEmail").scrollTop(altura + div);




    });

</script>