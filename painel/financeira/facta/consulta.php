<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    function numero($v){
        $remove = [" ","/","-",".","(",")"];
        return str_replace($remove, false, $v);
    }

    $facta = new facta;

    $query = "select *, api_facta_dados->>'$.token' as token from configuracoes where codigo = '1'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);

    $token = $d->token;

    $agora = time();

    // if($agora > $d->api_expira){
        $retorno = $facta->Token();
        $dados = json_decode($retorno);
        if($dados->erro == false){
            $token = $dados->token;
            echo $q = "update configuracoes set api_facta_expira = '".($agora + 7200)."', api_facta_dados = '{$retorno}' where codigo = '1'";
            mysqli_query($con, $q);
        }else{
            $tabelas = 'error';
        }
    // }



    if($_POST['acao'] == 'limpar'){
        $_SESSION['facta_campo'] = false;
        $_SESSION['facta_rotulo'] = false;
        $_SESSION['facta_valor'] = false;
    }


    if($_POST['acao'] == 'consulta'){
        $_SESSION['facta_campo'] = $_POST['campo'];
        $_SESSION['facta_rotulo'] = $_POST['rotulo'];
        $_SESSION['facta_valor'] = $_POST['valor'];
    }


    //consulta do saldo
    if($_POST['acao'] == 'saldo'){
        $_SESSION['facta_campo'] = $_POST['campo'];
        $_SESSION['facta_rotulo'] = $_POST['rotulo'];
        $_SESSION['facta_valor'] = $_POST['valor'];

        $query = "select * from clientes where codigo = '{$_POST['cliente']}'";
        $result = mysqli_query($con, $query);
        $cliente = mysqli_fetch_object($result);
        $retorno = $facta->Saldo([
            'token'=>$token,
            'cpf' => numero($cliente->cpf)
        ]);

        $consulta = uniqid();

        $query = "insert into consultas_facta set 
                                                    consulta = '{$consulta}',
                                                    operadora = 'FACTA',
                                                    cliente = '{$cliente->codigo}',
                                                    data = NOW(),
                                                    tabela_taxa = '{$_POST['taxa']}',
                                                    tabela = '{$_POST['tabela']}',
                                                    saldo = '{$retorno}'
                                                    
                ";
        mysqli_query($con, $query);

    }   
    
    //calculo
    if($_POST['acao'] == 'calculo'){

        $_SESSION['facta_campo'] = $_POST['campo'];
        $_SESSION['facta_rotulo'] = $_POST['rotulo'];
        $_SESSION['facta_valor'] = $_POST['valor'];

        $query = "select a.*, b.cpf from consultas_facta a left join clientes b on a.cliente = b.codigo where a.codigo = '{$_POST['calculo']}'";
        $result = mysqli_query($con, $query);
        $d = mysqli_fetch_object($result);

        $saldo = json_decode($d->saldo);

        $datas = $saldo->retorno;
        $parcelas = [];
        for($i = 1; $i <= 12; $i++){
            eval("\$data = \$datas->dataRepasse_$i;");
            eval("\$valor = \$datas->valor_$i;");
            if($data){
                if(in_array($i, $_POST['parcelas'])){
                    $parcelas[] = ["dataRepasse_{$i}" => $data, "valor_{$i}" => $valor];
                }else{
                    $parcelas[] = ["dataRepasse_{$i}" => $data, "valor_{$i}" => '0.00'];
                }   
            }
        }

        $retorno = [
            "cpf" => numero($d->cpf),
            "taxa" => $d->tabela_taxa,
            "tabela" => $d->tabela,
            "parcelas" => $parcelas
        ];

        echo $json = json_encode($retorno);

        $retorno = $facta->Calculo([
            'token'=>$token,
            'json' => $json
        ]);

        $q = "update consultas_facta set calculo = '{$retorno}' where codigo = '{$_POST['calculo']}'";
        mysqli_query($con, $q);

    }


    //Simulador
    if($_POST['acao'] == 'simulador'){

        $_SESSION['facta_campo'] = $_POST['campo'];
        $_SESSION['facta_rotulo'] = $_POST['rotulo'];
        $_SESSION['facta_valor'] = $_POST['valor'];

        $query = "select a.*, b.cpf, b.birthdate from consultas_facta a left join clientes b on a.cliente = b.codigo where a.codigo = '{$_POST['simulador']}'";
        $result = mysqli_query($con, $query);
        $d = mysqli_fetch_object($result);

        $calculo = json_decode($d->calculo);


        $dados = [
            'produto' => 'D',
            'tipo_operacao' => '13',
            'averbador' => '20095',
            'convenio' => '3',
            'cpf' => numero($d->cpf),
            'data_nascimento' => dataBr($d->birthdate),
            'login_certificado' => '96753',
            'simulacao_fgts' => ''
        ];

        echo $retorno = $facta->Simulador([
            'token'=> $token,
            'dados' => $dados
        ]);

        echo $q = "update consultas_facta set simulador = '{$retorno}' where codigo = '{$_POST['simulador']}'";
        mysqli_query($con, $q);

    }


    if($_SESSION['facta_campo'] and $_SESSION['facta_valor']){
        $query = "select * from clientes where {$_SESSION['facta_campo']} like '%{$_SESSION['facta_valor']}%'";
        $result = mysqli_query($con, $query);
        $cliente = mysqli_fetch_object($result);
    }

?>

<div class="card m-3">
  <h5 class="card-header">Sistema Capital Financeira - FACTA</h5>
  <div class="card-body">
    <h5 class="card-title">Consultas / Simulações /Propostas</h5>
    <div class="card-text" style="min-height:400px;">
        
    <div class="input-group mb-3">
        <!-- <button opcao_busca class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><?=(($_SESSION['facta_rotulo'])?:'CPF')?></button>
        <ul class="dropdown-menu">
            <li><a selecione="cpf" class="dropdown-item" href="#">CPF</a></li>
            <li><a selecione="nome" class="dropdown-item" href="#">Nome</a></li>
        </ul> -->
        <span class="input-group-text">CPF</span>
        <input 
            type="text" 
            class="form-control" 
            aria-label="Text input with dropdown button"
            busca
            value="<?=$_SESSION['facta_valor']?>"
        >
        <button
            buscar
            type="button" 
            class="btn btn-outline-secondary"
            campo="<?=(($_SESSION['facta_campo'])?:'cpf')?>"
            rotulo="<?=(($_SESSION['facta_rotulo'])?:'CPF')?>"    
        >Buscar</button>
        <button
            limpar
            type="button" 
            class="btn btn-outline-danger"   
        >Limpar</button>
        <button
            clientes
            type="button" 
            class="btn btn-outline-primary"   
        ><i class="fa-solid fa-users"></i> Clientes</button>
    </div>

    <?php
    if($_SESSION['facta_campo'] and $_SESSION['facta_valor'] and !$cliente->codigo){
    ?>
    <div class="row">
        <div class="col">
            <div class="alert alert-secondary" role="alert">
                <div class="d-flex flex-column justify-content-center align-items-center" style="height:300px;">
                    <h1 class="text-color-secondary">Busca sem resultados <i class="fa-regular fa-face-frown-open"></i></h1>
                    <button 
                        novo
                        type="button"
                        class="btn btn-outline-primary btn-sm mt-3"
                        data-bs-toggle="offcanvas"
                        href="#offcanvasDireita"
                        role="button"
                        aria-controls="offcanvasDireita"
                    ><i class="fa-regular fa-user"></i> Cadastrar um novo cliente</button>
                </div>
            </div>
        </div>
    </div>
    <?php
    }else if($cliente->codigo){
    ?>
    <div class="input-group mb-3">
        <span class="input-group-text"><?=$cliente->nome?></span>
        <span class="input-group-text"><?=$cliente->cpf?></span>
        <select id="tabela" class="form-select">
            <?php
                $q = "select * from configuracoes where codigo = '1'";
                $r = mysqli_query($con, $q);
                $tab = mysqli_fetch_object($r);
                $t = json_decode($tab->api_facta_tabelas);
                $tabela_descricao = [];
                foreach($t->data as $i => $v){
                    $tabela_descricao[$v->id] = $v->name;
            ?>
            <option value="<?=$v->id?>" taxa<?=$v->id?>="<?=$v->taxa?>" <?=(($tab->api_facta_tabela_padrao == $v->id)?'selected':false)?>><?="{$v->id} - {$v->name} ({$v->taxa})"?></option>
            <?php
                }
            ?>
        </select>
        <button saldo class="btn btn-outline-secondary" type="button" id="button-addon1">Verificar Saldo</button>
    </div>
    <?php
    }
    
    $query = "select * from consultas_facta where cliente = '{$cliente->codigo}' order by codigo desc";
    $result = mysqli_query($con, $query);
    while($d = mysqli_fetch_object($result)){
        $saldo = json_decode($d->saldo);
        $calculo = json_decode($d->calculo);
    ?>
    <div class="card mb-3 border-<?=(($d->status_proposta == 200)?'success':'primary')?>">
        <div class="card-header bg-<?=(($d->status_proposta == 200)?'success':'primary')?> text-white">
            <?=(($d->status_proposta == 200)?'PROPOSTA':'SIMULAÇÃO')?> - <?=strtoupper($d->consulta)?>
        </div>
    <?php
        if($saldo->erro == true){
    ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Mensagem</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?=$saldo->codigo?></td>
                    <td><?=$saldo->msg?></td>
                </tr>
            </tbody>
        </table>
    <?php
        }else{
    ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <?php
                    if(!$calculo or $calculo->permitido == 'NAO'){
                    ?>
                    <th style="width:20px;"><input type="checkbox" class="form-check-input" todas_parcelas="<?=$d->codigo?>" ></th>
                    <?php
                    }
                    ?>
                    <th>Período</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php
                for($i = 1; $i <= 12; $i++){
                    eval("\$valor = \$saldo->retorno->valor_{$i};");
                    eval("\$periodo = \$saldo->retorno->dataRepasse_{$i};");
                    if($periodo){
                ?>
                <tr>
                    <?php
                    if(!$calculo or $calculo->permitido == 'NAO'){
                    ?>
                    <td>
                        <input type="checkbox" class="form-check-input" parcelas<?=$d->codigo?> value="<?=$i?>">
                    </td>
                    <?php
                    }
                    ?>
                    <td><?=$periodo?></td>
                    <td><?=$valor?></td>
                </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Data Consulta do Saldo</th>
                    <th>Saldo Total</th>
                    <th>Tablea</th>
                    <th>Taxa</th>
                    <?php
                    if(!$calculo or $calculo->permitido == 'NAO'){
                    ?>
                    <th>Cálculo</th>
                    <?php
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?="{$saldo->retorno->data_saldo} {$saldo->retorno->horaSaldo}"?></td>
                    <td><?="{$saldo->retorno->saldo_total}"?></td>
                    <td><?="{$d->tabela} - {$tabela_descricao[$d->tabela]}"?></td>
                    <td><?="{$d->tabela_taxa}"?></td>
                    <?php
                    if(!$calculo or $calculo->permitido == 'NAO'){
                    ?>
                    <td>
                        <button calculo="<?=$d->codigo?>" class="btn btn-primary btn-sm">Gerar Cálculo</button>
                    </td>
                    <?php
                    }
                    ?>
                </tr>
            </tbody>
            <?php
            if($calculo){
                if($calculo->permitido == 'NAO'){
            ?>
            <tbody>
                <tr>
                    <td colspan="4">Permitido: NÃO, <?="{$calculo->msg}"?></td>
                </tr>
            </tbody>
            <?php
                }else{
            ?>
            <tbody>
                <tr>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Permitido</th>
                                <th>Simulação</th>
                                <th>Data</th>
                                <th>Valor Líquido</th>
                                <th>Parcelas</th>
                                <th>Tabela</th>
                                <?php
                                if(!$simulador){
                                ?>
                                <td>
                                    Simulador
                                </td>
                                <?php
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?=$calculo->permitido?></td>
                                <td><?=$calculo->simulacao_fgts?></td>
                                <td><?=$calculo->data_solicitacao?></td>
                                <td><?=$calculo->valor_liquido?></td>
                                <td><?=$calculo->parcelas_selecionadas?></td>
                                <td><?=$calculo->tabela?></td>    
                                <?php
                                if(!$simulador){
                                ?>
                                <td>
                                    <button simulador="<?=$d->codigo?>" class="btn btn-primary btn-sm">Ativar Simulador</button>
                                </td>
                                <?php
                                }
                                ?>                            
                            </tr>
                        </tbody>
                    </table>
                </tr>
            </tbody>
            <?php
                }
            }
            ?>
        </table>
    <?php
        }
    ?>
    </div>
    <?php
    }
    ?>
    
    

    </div>
    <button atualiza class="btn btn-primary">Atualizar</button>
  </div>
</div>


<script>
    $(function(){

        Carregando('none');

        $("input[todas_parcelas]").click(function(){
            opc = $(this).prop("checked");
            cod = $(this).attr("todas_parcelas");
            if(opc == true){
                $(`input[parcelas${cod}]`).prop("checked", true);
            }else{
                $(`input[parcelas${cod}]`).prop("checked", false);
            }
        })

        // var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        // var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        //     return new bootstrap.Tooltip(tooltipTriggerEl)
        // })

        // $("button[copiar]").click(function(){
        //     obj = $(this);
        //     texto = $(this).attr("copiar");
        //     CopyMemory(texto);
        //     obj.removeClass('btn-outline-secondary');
        //     obj.addClass('btn-outline-success');
        //     // obj.children("span").text("Código PIX Copiado!");
        // });

        $("button[clientes]").click(function(){
            Carregando();
            $.ajax({
                url:"financeira/clientes/index.php",
                success:function(dados){
                    $("#paginaHome").html(dados);
                }
            });
        });

        // $("button[novo]").click(function(){
        //     Carregando();
        //     $.ajax({
        //         url:"financeira/clientes/form.php",
        //         type:"POST",
        //         data:{
        //             cpf:'<?=$_SESSION['facta_valor']?>',
        //             retorno:"financeira/facta/consulta.php"
        //         },
        //         success:function(dados){
        //             $(".LateralDireita").html(dados);
        //         }
        //     });
        // });

        $("input[busca]").mask("999.999.999-99");

        $("button[atualiza]").click(function(){
            Carregando();
            $.ajax({
                url:"financeira/facta/consulta.php",
                success:function(dados){
                    $("#paginaHome").html(dados);
                }
            })
        })

        // $("a[selecione]").click(function(){
        //     campo = $(this).attr("selecione");
        //     rotulo = $(this).text();
        //     $("button[buscar]").attr("campo", campo);
        //     $("button[buscar]").attr("rotulo", rotulo);
        //     $("button[opcao_busca]").text(rotulo);
        //     if(campo == 'cpf'){
        //         $("input[busca]").mask("999.999.999-99");
        //     }else{
        //         $("input[busca]").unmask();
        //     }
        //     $("input[busca]").val('');
        // })

        $("button[buscar]").click(function(){
            

            campo = $(this).attr("campo");
            rotulo = $(this).attr("rotulo");
            valor = $("input[busca]").val();
            console.log(`Buscar: ${valor} em ${campo}`);
            if(campo == 'cpf'){
                if(!validarCPF(valor)){
                    $.alert({
                        content:"CPF inválido!",
                        title:"Erro",
                        type:'red'
                    });
                    return false;
                }
            }
            Carregando();
            $.ajax({
                url:"financeira/facta/consulta.php",
                type:"POST",
                data:{
                    campo,
                    rotulo,
                    valor,
                    acao:'consulta'
                },
                success:function(dados){
                    $("#paginaHome").html(dados);
                }
            })

        })     
        
        $("button[limpar]").click(function(){
            Carregando();

            $.ajax({
                url:"financeira/facta/consulta.php",
                type:"POST",
                data:{
                    acao:'limpar'
                },
                success:function(dados){
                    $("#paginaHome").html(dados);
                }
            })
        })     

        // $("button[atualiza_proposta]").click(function(){

        //     proposalId = $(this).attr("proposalId");
        //     atualiza_proposta = $(this).attr("atualiza_proposta");

        //     Carregando();

        //     $.ajax({
        //         url:"financeira/facta/consulta.php",
        //         type:"POST",
        //         data:{
        //             acao:'atualiza_proposta',
        //             campo:'<?=$_SESSION['facta_campo']?>',
        //             rotulo:'<?=$_SESSION['facta_rotulo']?>',
        //             valor:'<?=$_SESSION['facta_valor']?>',
        //             proposalId,
        //             atualiza_proposta
        //         },
        //         success:function(dados){
        //             $("#paginaHome").html(dados);
        //         }
        //     })            
            

        // })     

        $("button[saldo]").click(function(){

            tabela = $("#tabela").val();
            taxa = $(`option[taxa${tabela}]`).attr(`taxa${tabela}`);

            $.confirm({
                title:"Saldo",
                content:"Confirma a consulta do Saldo?",
                type:"orange",
                buttons:{
                    'sim':{
                        text:'Sim',
                        btnClass:'btn btn-success btn-sm',
                        action:function(){
                            Carregando();

                            $.ajax({
                                url:"financeira/facta/consulta.php",
                                type:"POST",
                                data:{
                                    acao:'saldo',
                                    campo:'<?=$_SESSION['facta_campo']?>',
                                    rotulo:'<?=$_SESSION['facta_rotulo']?>',
                                    valor:'<?=$_SESSION['facta_valor']?>',
                                    cliente:'<?=$cliente->codigo?>',
                                    tabela,
                                    taxa
                                },
                                success:function(dados){
                                    $("#paginaHome").html(dados);
                                    // console.log(dados);
                                },
                                error:function(){
                                    alert('Erro')
                                }
                            })  
                        }
                    },
                    'nao':{
                        text:'Não',
                        btnClass:'btn btn-danger btn-sm',
                        action:function(){
                            
                        }
                    }
                }
            })
          
        }) 


        $("button[calculo]").click(function(){

            calculo = $(this).attr("calculo");

            parcelas = [];
            $(`input[parcelas${calculo}]`).each(function(){
                if($(this).prop("checked")){
                    parcelas.push($(this).val());
                }
            })

            if(!parcelas.length){
                $.alert({
                    type:"red",
                    title:"Intervalo de Período",
                    content:'Favor definir o intervalo de período que deseja antecipar.'
                });
                return false;
            }
            $.confirm({
                title:"Cálculo",
                content:"Confirma a verificação do cálculo?",
                type:"orange",
                buttons:{
                    'sim':{
                        text:'Sim',
                        btnClass:'btn btn-success btn-sm',
                        action:function(){
                            Carregando();

                            $.ajax({
                                url:"financeira/facta/consulta.php",
                                type:"POST",
                                data:{
                                    acao:'calculo',
                                    campo:'<?=$_SESSION['facta_campo']?>',
                                    rotulo:'<?=$_SESSION['facta_rotulo']?>',
                                    valor:'<?=$_SESSION['facta_valor']?>',
                                    parcelas,
                                    calculo
                                },
                                success:function(dados){
                                    $("#paginaHome").html(dados);
                                    // console.log(dados);
                                },
                                error:function(){
                                    alert('Erro')
                                }
                            })  
                        }
                    },
                    'nao':{
                        text:'Não',
                        btnClass:'btn btn-danger btn-sm',
                        action:function(){
                            
                        }
                    }
                }
            })

        }) 

        $("button[simulador]").click(function(){

            simulador = $(this).attr("simulador");

            $.confirm({
                title:"Simulador",
                content:"Confirma a ativação do simulador?",
                type:"orange",
                buttons:{
                    'sim':{
                        text:'Sim',
                        btnClass:'btn btn-success btn-sm',
                        action:function(){
                            Carregando();

                            $.ajax({
                                url:"financeira/facta/consulta.php",
                                type:"POST",
                                data:{
                                    acao:'simulador',
                                    campo:'<?=$_SESSION['facta_campo']?>',
                                    rotulo:'<?=$_SESSION['facta_rotulo']?>',
                                    valor:'<?=$_SESSION['facta_valor']?>',
                                    simulador
                                },
                                success:function(dados){
                                    $("#paginaHome").html(dados);
                                    // console.log(dados);
                                },
                                error:function(){
                                    alert('Erro')
                                }
                            })  
                        }
                    },
                    'nao':{
                        text:'Não',
                        btnClass:'btn btn-danger btn-sm',
                        action:function(){
                            
                        }
                    }
                }
            })

        }) 




        // $("button[proposta]").click(function(){

        //     proposta = $(this).attr("proposta");

        //     $.confirm({
        //         title:"Proposta",
        //         content:"Confirma a solicitação de proposta?",
        //         type:"orange",
        //         buttons:{
        //             'sim':{
        //                 text:'Sim',
        //                 btnClass:'btn btn-success btn-sm',
        //                 action:function(){
        //                     Carregando();

        //                     $.ajax({
        //                         url:"financeira/facta/consulta.php",
        //                         type:"POST",
        //                         data:{
        //                             acao:'proposta',
        //                             campo:'<?=$_SESSION['facta_campo']?>',
        //                             rotulo:'<?=$_SESSION['facta_rotulo']?>',
        //                             valor:'<?=$_SESSION['facta_valor']?>',
        //                             proposta
        //                         },
        //                         success:function(dados){
        //                             $("#paginaHome").html(dados);
        //                             // console.log(dados);
        //                         },
        //                         error:function(){
        //                             alert('Erro')
        //                         }
        //                     })  
        //                 }
        //             },
        //             'nao':{
        //                 text:'Não',
        //                 btnClass:'btn btn-danger btn-sm',
        //                 action:function(){
                            
        //                 }
        //             }
        //         }
        //     })

        // })  


    })
</script>