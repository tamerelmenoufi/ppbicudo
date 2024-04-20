<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");
?>
<style>
    .pagina{
        position:fixed;
        left:10px;
        top:60px;
        bottom:0;
        right:10px;
        background-color:#fff;
        border:solid 0px #333;
    }
    .relativo{
        position:relative;
        height:100%;
    }
    .listaEntradaAcoes{
        position:absolute;
        left:0;
        top:0px;
        height:80px;
        right:0;
        background-color:#eee;
    }
    .listaEntrada{
        position:absolute;
        left:0;
        top:80px;
        bottom:0;
        right:0;
        overflow:auto;
    }

    .exibeEmailTopo{
        position:absolute;
        left:0;
        top:0px;
        height:40px;
        right:0;
        background-color:#eeeeee;
        overflow:none;
        border-left:3px solid #e4e4e4;
    }
    .exibeEmail{
        position:absolute;
        left:0;
        top:40px;
        bottom:80px;
        right:0;
        background-color:#f7f7f7;
        overflow:auto;
        border-left:3px solid #e4e4e4;
        padding:10px;
    }

    .exibeEmailRodape{
        position:absolute;
        left:0;
        height:80px;
        bottom:0;
        right:0;
        background-color:#f5f1ee;
        overflow:none;
        border-left:3px solid #e4e4e4;
    }

    .ItemEmail div h5{
        font-size:14px;
        font-weight:bold;
        color:#a1a1a1;
        padding:0;
        margin:0;
    }
    .ItemEmail div span{
        font-size:12px;
        font-weight:normal;
        color:#333;
        padding:0;
        margin:0;
    }
</style>
<div class="pagina">


        <!-- Exibe apenas no PC -->
        <div class="relativo">
            <div class="row relativo">
                <div class="col-md-4 relativo">
                    <div class="listaEntradaAcoes d-flex flex-column">


                        <div style="background-color:#eeeeee; height:40px;">
                            <div class="btn-group btn-group-sm">
                                <a
                                    class="btn btn-secondary"
                                    data-bs-toggle="offcanvas"
                                    href="#offcanvasDireita"
                                    role="button"
                                    aria-controls="offcanvasDireita"
                                    escrever
                                >chat</a>
                                <!-- <a
                                    class="btn btn-primary"
                                    mover
                                >Mover</a> -->
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Mover
                                    </button>
                                    <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Caixa de Entrada</a></li>
                                    <li><a class="dropdown-item" href="#">Urgentes</a></li>
                                    <li><a class="dropdown-item" href="#">Empresa A</a></li>
                                    <li><a class="dropdown-item" href="#">Empresa C</a></li>
                                    </ul>
                                </div>

                                <a
                                    class="btn btn-secondary"
                                    excluir
                                >Excluir</a>
                            </div>
                        </div>

                        <div class="d-flex flex-row" style="height:40px; background-color:#fff">
                            <!-- <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="exampleCheck1" style="margin-left:0px; margin-top:15px;">
                            </div> -->


                            <!-- <div class="input-group input-group-sm ms-3 me-3">
                                <input type="text" class="form-control form-control-sm" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="button-addon2">
                                <button class="btn btn-sm btn-outline-secondary" type="button" id="button-addon2">Button</button>
                            </div> -->

                            <div class="input-group input-group-sm mb-3 ms-3 me-1 mt-2">
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                <button class="btn btn-outline-secondary" type="button" id="button-addon2">Button</button>
                            </div>

                        </div>

                    </div>
                    <div class="listaEntrada">
                        <ul class="list-group list-group-flush"></ul>
                    </div>
                </div>
                <div class="d-none d-md-block col-md-8 relativo">
                    <div class="exibeEmailTopo"></div>
                    <div class="exibeEmail"></div>
                    <div class="exibeEmailRodape">
                        <div class="d-flex justify-content-between align-items-center m-3">
                            <i class="fa-regular fa-face-smile p-3"></i>
                            <input type="text" class="form-control p-3" id="chatMensagem" aria-describedby="chatMensagem">
                            <i class="fa-solid fa-microphone p-3"></i>
                            <i class="fa-regular fa-paper-plane p-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>



</div>

<script>

    $(function(){

        Carregando('none')

        $.ajax({
            url:"chats/entrada/lista.php",
            success:function(dados){
                $(".listaEntrada ul").append(dados);
            }
        });

        $.ajax({
            url:"chats/entrada/mensagens.php",
            success:function(dados){
                $(".exibeEmail").append(dados);
            }
        });

        $("#chatMensagem").keypress(function(e){
            val = $(this).val();
            layout = '<div class="d-flex flex-row-reverse">'+
                     '<div class="d-inline-flex flex-column m-1 p-2" style="max-width:60%; background-color:#dcf8c6; border:0; border-radius:10px;">'+
                     '<div class="text-start" style="border:solid 0px red;">'+val+'</div>' +
                     '<div class="text-end" style="color:#b6a29a; font-size:10px; border:solid 0px black;">12:17</div>' +
                     '</div>' +
                     '</div>';

            if(e.which == 13 && val) {
                $(".exibeEmail").append(layout);
                $("#chatMensagem").val('');

                altura = $(".exibeEmail").prop("scrollHeight");
                div = $(".exibeEmail").height();
                $(".exibeEmail").scrollTop(altura + div);

                $.ajax({
                    url:"chats/entrada/send.php",
                    type:"POST",
                    data:{
                        msg:val,
                    },
                    success:function(dados){

                    }
                });


                // console.log('precionei o teclado!' + val)
            }
        });

        verificarMensagem = setInterval(() => {
            $.ajax({
                url:"chats/entrada/read.php",
                dataType:"JSON",
                success:function(dados){


                    layout = '<div class="d-flex flex-row">'+
                     '<div class="d-inline-flex flex-column m-1 p-2" style="max-width:60%; background-color:#ffffff; border:0; border-radius:10px;">'+
                     '<div class="text-start" style="border:solid 0px red;">'+dados.mensagem+'</div>' +
                     '<div class="text-end" style="color:#b6a29a; font-size:10px; border:solid 0px black;">12:17</div>' +
                     '</div>' +
                     '</div>';

                    $(".exibeEmail").append(layout);

                    altura = $(".exibeEmail").prop("scrollHeight");
                    div = $(".exibeEmail").height();
                    $(".exibeEmail").scrollTop(altura + div);


                    console.log(dados);
                }
            });
        }, 1000);


	 var lastScrollTop = 0, delta = 5;
	 $(".listaEntrada").scroll(function(){
		 var nowScrollTop = $(".listaEntrada").scrollTop();
         var altura = ( $(".listaEntrada ul").outerHeight() - $(".listaEntrada").outerHeight());

        if((nowScrollTop) >= (altura - 10)){
            console.log(`${nowScrollTop} de ${altura}`)
            Carregando()
            $.ajax({
                url:"chats/entrada/lista.php",
                success:function(dados){
                    $(".listaEntrada ul").append(dados);
                }
            });

        }else{
            // console.log(`Estou fora da área ${nowScrollTop} de ${altura}`);
        }

		//  if(Math.abs(lastScrollTop - nowScrollTop) >= delta){
		//  	if (nowScrollTop > lastScrollTop){
		//  		// ACTION ON
		//  		// SCROLLING DOWN
        //         console.log(`DESCE ${nowScrollTop}`)
		//  	} else {
		//  		// ACTION ON
		//  		// SCROLLING UP
        //          console.log(`SOBE ${nowScrollTop}`)
		// 	}
		//  lastScrollTop = nowScrollTop;
		//  }

	 });


     $(document).off('click').on('click','.ItemEmail div i', function(){
        alert('ação aqui');
     })

     $("a[escrever]").click(function(){
        $.alert('Agora vai ser escrito um e-mail');
     });

     $("a[mover]").click(function(){
        $.alert('Para qual pasta deseja mover este e-mail?');
     });

     $("a[excluir]").click(function(){
        $.confirm({
            content:"Deseja realmente excluir o e-mail?",
            title:"Alerta",
            buttons:{
                'SIM':function(){
                    $.alert('E-mail apagado!')
                },
                'NÃO':function(){

                }
            }
        });
     });
 });

</script>