<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");
    include("menuClasses.php");

    if($_POST['acao'] == 'ordem'){
        // print_r($_POST);
        // $query = false;
        foreach($_POST['posicao'] as $ordem => $codigo){
            $query = "UPDATE menus set ordem = '{$ordem}' where codigo = '{$codigo}'";
            mysqli_query($con, $query);
        }


        exit();
    }

?>
<style>
    .itemMenu{
        font-weight:bold;
        padding:10px;
        font-size:20px;
    }
    .itemMenu:hover{
        color:#3279fc;
        font-weight:bold;
        font-size:20px;
        background-color:#e8ffff;
        border-radius:7px;
        padding:10px;
        cursor:move;
    }
    .itemMenu i{
        color:#fff;
    }
    .itemMenu:hover i.editar{
        color:green;
        font-size:20px;
        cursor:pointer;
        font-weight:bold;
    }
    .itemMenu:hover i.adicionar{
        color:blue;
        font-size:20px;
        cursor:pointer;
        font-weight:bold;
        margin-right:10px;
    }
    .itemMenu:hover i.excluir{
        color:red;
        font-size:20px;
        cursor:pointer;
        font-weight:bold;
        margin-right:40px;
    }

    .itemMenu:hover i.excluir_blq{
        color:#ff9b93;
        font-size:20px;
        cursor:pointer;
        font-weight:bold;
        margin-right:40px;
    }

    .placeholder{
        background-color:#eee;
        height:50px;
        width:100%;
    }

</style>

<div class="card">
    <h5 class="card-header">Gerenciamento do Menu Principal</h5>
    <div class="card-body">
    <?php
        $_SESSION['MenuSortable'] = [];
        MontaMenu(0);
    ?>
    </div>
</div>
<script>
    $(function(){

        Carregando('none');

        $( ".grupoMenus" ).sortable({
            revert: false,
            placeholder: 'placeholder',
            stop: function () {
                posicao = [];
                $( ".listaMenu" ).each(function(){
                    posicao.push($(this).attr('cod'));
                });
                $.ajax({
                    url:"site/menu/menu.php",
                    type:"POST",
                    data:{
                        acao:"ordem",
                        posicao,
                    },
                    success:function(dados){
                        // $.dialog(dados);
                    }
                })
            }
        });

        $("i.editar").click(function(){
            $("div[menuForm]").html('');
            cod = $(this).attr('cod');
            vinculo = $(this).attr('vinculo');

            $.ajax({
                url:"site/menu/form.php",
                type:"POST",
                data:{
                    cod,
                    vinculo,
                },
                success:function(dados){
                    $("div[menuForm]").html(dados);
                }
            })
        })
        $("i.adicionar").click(function(){
            $("div[menuForm]").html('');
            vinculo = $(this).attr('cod');
            $.ajax({
                url:"site/menu/form.php",
                type:"POST",
                data:{
                    vinculo,
                },
                success:function(dados){
                    $("div[menuForm]").html(dados);
                }
            })
        })
        $("i.excluir").click(function(){
            excluir = $(this).attr("cod");
            $("div[menuForm]").html('');
            $.confirm({
                title:false,
                content:"Deseja Relamente excluir o registro do Menu?",
                type:'red',
                buttons:{
                    'SIM':{
                        text:'<i class="fa fa-trash"></i> Sim',
                        btnClass:'btn-danger',
                        action:function(){

                            $.ajax({
                                url:"site/menu/form.php",
                                type:"POST",
                                data:{
                                    excluir,
                                },
                                success:function(dados){

                                    $.alert({
                                        content:dados,
                                        type:"orange",
                                        title:false,
                                        buttons:{
                                            'ok':{
                                            text:'<i class="fa-solid fa-check"></i> OK',
                                            btnClass:'btn btn-warning'
                                            }
                                        }
                                    });

                                    $.ajax({
                                        url:"site/menu/menu.php",
                                        success:function(dados){
                                            $("div[montaMenu]").html(dados);
                                        }
                                    });

                                }
                            })

                        }
                    },
                    'NÃO':{
                        text:'<i class="fa fa-cancel"></i> Não',
                        btnClass:'btn-success'
                    }
                }
            })
        })


        $("i.excluir_blq").click(function(){
            qt = $(this).attr("qt");
            $.alert({
                title:false,
                content:`<center>Esse menu não pode ser excluído.<br>Existem <b>${qt}</b> sumbenu(s) vinculado(s)?</center>`,
                type:'red',
                buttons:{
                    'Entendi':{
                        text:"<i class='fa fa-cancel'></i> Entendi",
                        btnClass:'btn btn-danger'
                    }
                }
            })
        })

    })
</script>