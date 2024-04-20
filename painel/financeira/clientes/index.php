<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    if($_POST['acao'] == 'wapp'){
      $wgw = new wgw;
      echo $wgw->SendTxt([
        'mensagem'=>'Envio de teste',
        'para'=>'5592991886570'
      ]);
      echo $wgw->SendAudio([
        'mensagem'=>'Envio de teste',
        'para'=>'5592991886570'
      ]);
      exit();
    }

    if($_POST['delete']){
      $query = "delete from clientes where codigo = '{$_POST['delete']}'";
      mysqli_query($con, $query);
    }

    if($_POST['situacao']){
      $query = "update clientes set situacao = '{$_POST['opc']}' where codigo = '{$_POST['situacao']}'";
      mysqli_query($con, $query);
      exit();
    }

    if($_POST['acao'] == 'busca'){
      $_SESSION['busca_campo'] = $_POST['campo'];
      $_SESSION['busca_titulo'] = $_POST['titulo'];
      $_SESSION['texto_busca'] = $_POST['busca'];
    }
    if($_POST['acao'] == 'limpar'){
      $_SESSION['busca_campo'] = false;
      $_SESSION['busca_titulo'] = false;  
      $_SESSION['texto_busca'] = false;    
    }


    if($_SESSION['busca_campo'] == 'cpf'){
      $where = " and a.cpf = '{$_SESSION['texto_busca']}' ";
      $limit = false;
    }else if($_SESSION['busca_campo'] == 'nome'){
      $where = " and a.nome like '%".trim($_SESSION['texto_busca'])."%' ";
      $limit = false;
    }elseif($_SESSION['busca_campo'] == 'status'){
      $where = " and (select count(*) from consultas_log where cliente = a.codigo and (concat(log->>'$.statusCode','-',log->>'$.message') = '{$_SESSION['texto_busca']}' or concat(log->>'$.proposalStatusId','-',log->>'$.proposalStatusDisplayTitle') = '{$_SESSION['texto_busca']}') and ativo = '1') > 0 ";
      $limit = false;
    }else{
      $limit = " limit 50 ";
    }



?>
<style>
  .legenda_status{
    border-left:5px solid;
    border-left-color:green;
  }
</style>
<div class="col">
  <div class="m-3">

    <div class="row">
      <div class="col">
        <div class="card">
          <h5 class="card-header">Lista de Clientes</h5>


          <div class="card-body">

          <div class="row">
            <div class="col-md-8">
            <?php
            if($_SESSION['ProjectPainel']->codigo == 2){
              echo '<button Wapp class="btn btn-success" title="Realizar a Busca">Wapp</button> ';
            }


            // if($_SESSION['ProjectPainel']->codigo == 2){
            ?>
            <div class="input-group">
              <span class="input-group-text">Busca por</span>
              <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" campo="<?=(($_SESSION['busca_campo'])?:'cpf')?>" titulo="<?=(($_SESSION['busca_titulo'])?:'CPF')?>"><?=(($_SESSION['busca_titulo'])?:'CPF')?></button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" campo="cpf">CPF</a></li>
                <li><a class="dropdown-item" href="#" campo="nome">Nome</a></li>
                <li><a class="dropdown-item" href="#" campo="status">Situação</a></li>
              </ul>
              <input texto_busca type="text" class="form-control" <?=(($_SESSION['busca_campo'] == 'status')?'style="display:none"':false)?> value="<?=$_SESSION['texto_busca']?>">
              <select texto_busca class="form-select" <?=(($_SESSION['busca_campo'] != 'status')?'style="display:none"':false)?>>
                <option value="">:: Selecione o Status ::</option>
                <?php
                $q = "select * from status order by status asc";
                $r = mysqli_query($con,$q);
                while($s = mysqli_fetch_object($r)){
                ?>
                <option value="<?="{$s->status}-{$s->descricao}"?>" <?=(("{$s->status}-{$s->descricao}" == $_SESSION['texto_busca'])?'selected':false)?>><?="{$s->status}-{$s->descricao}"?></option>
                <?php
                }
                ?>
              </select>
              <button busca_resultado class="btn btn-success" title="Realizar a Busca"><i class="fa-solid fa-magnifying-glass"></i></button>  
              <button busca_limpar class="btn btn-danger" title="Limpar a Busca"><i class="fa-solid fa-eraser"></i></button>  
            </div>
            <?php
            // }
            ?>            
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <span class="input-group-text">CPF</span>
                <input type="text" class="form-control" id="cpf_novo">
                <button
                    novoCadastro
                    class="btn btn-success"
                    data-bs-toggle="offcanvas"
                    href="#offcanvasDireita"
                    role="button"
                    aria-controls="offcanvasDireita"
                >Novo</button>                
              </div>
            </div>

            </div>

            <div class="table-responsiveXXX">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Nome</th>
                  <th scope="col">CPF</th>
                  <th scope="col">Telefone</th>
                  <!-- <th scope="col">Nome da Mãe</th> -->
                  <th scope="col">Data de Cadastro</th>
                  <th scope="col">Situação</th>
                  <!-- <th scope="col">Situação</th> -->
                  <th style="width:60px;">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php

                  $query = "select * from consultas_log";
                  $result = mysqli_query($con, $query);
                  while($d = mysqli_fetch_object($result)){

                    $log = json_decode($d->log);
                    if($log->statusCode){
                      $status = $log->statusCode;
                      $descricao = $log->message;
                    }else if($log->proposalStatusId){
                      $status = $log->proposalStatusId;
                      $descricao = $log->proposalStatusDisplayTitle;
                    }

                    mysqli_query($con, "insert into status set status = '{$status}', descricao = '{$descricao}', unico = '".md5($status.$descricao)."'");

                  }

                  $query = "select 
                                  a.*,
                                  (select log from consultas_log where cliente = a.codigo and ativo = '1') as log
                            from clientes a 
                            where 1 {$where}
                            order by a.data_cadastro desc {$limit}";
                  // if($_SESSION['ProjectPainel']->codigo == 2) echo $query;
                  $result = mysqli_query($con, $query);
                  $k = 1;
                  while($d = mysqli_fetch_object($result)){

                    // mysqli_query($con, "update consultas_log set ativo = '1' where codigo = '{$d->ativo}'");

                    $log = json_decode($d->log);
                    $del = 'disabled';
                    if($log->statusCode){
                      $situacao = "{$log->statusCode} - {$log->message}";
                      $cor="orange";
                    }else if($log->proposalStatusId){
                      $situacao = "{$log->proposalStatusId} - {$log->proposalStatusDisplayTitle}";
                      if($log->proposalStatusId == 130){
                        $cor="green";
                      }else{
                        $cor="red";
                      }
                    }else{
                      $situacao = "000 - Cliente sem movimentação";
                      $cor="#ccc";
                      $del = false;
                    }

                ?>
                <tr>
                  <td><?=$k?></td>
                  <td><?=$d->nome?></td>
                  <td><?=$d->cpf?></td>
                  <td><?=$d->phoneNumber?></td>
                  <!-- <td><?=$d->motherName?></td> -->
                  <td><?=dataBr($d->data_cadastro)?></td>
                  <td class="legenda_status" style="border-left-color:<?=$cor?>;">
                    <?=$situacao?>
                  </td>
                  <!-- <td>

                  <div class="form-check form-switch">
                    <input class="form-check-input situacao" type="checkbox" <?=(($d->codigo == 1)?'disabled':false)?> <?=(($d->situacao)?'checked':false)?> usuario="<?=$d->codigo?>">
                  </div>

                  </td> -->
                  <td>
                    <?php
                    // if($_SESSION['ProjectPainel']->codigo == 2){
                    ?>

                    <div class="dropdown">
                      <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Ações
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" vctex="<?=$d->cpf?>"><i class="fa-solid fa-dollar-sign"></i> VcTex</a></li>
                        <!-- <li><a class="dropdown-item" href="#" facta="<?=$d->cpf?>"><i class="fa-solid fa-dollar-sign"></i> Facta</a></li> -->
                        <li><a 
                              class="dropdown-item" 
                              edit="<?=$d->codigo?>"
                              data-bs-toggle="offcanvas"
                              href="#offcanvasDireita"
                              role="button"
                              aria-controls="offcanvasDireita"
                            ><i class="fa-solid fa-pen-to-square"></i> Editar</a></li>
                        <li><a 
                          class="dropdown-item" 
                          logs="<?=$d->codigo?>"
                          data-bs-toggle="offcanvas"
                          href="#offcanvasDireita"
                          role="button"
                          aria-controls="offcanvasDireita"
                        ><i class="fa-solid fa-clipboard-list"></i> Eventos</a></li>
                        <?php
                        if($_SESSION['ProjectPainel']->codigo == 2){
                        ?>
                        <li><a 
                          class="dropdown-item" 
                          mensagens="<?=$d->codigo?>"
                          data-bs-toggle="offcanvas"
                          href="#offcanvasDireita"
                          role="button"
                          aria-controls="offcanvasDireita"
                        ><i class="fa-brands fa-whatsapp"></i> Mensagens</a></li>
                        <?php
                        }
                        if(!$del){
                        ?>
                        <li><a class="dropdown-item" href="#" delete="<?=$d->codigo?>"><i class="fa-solid fa-trash-can"></i> Excluir</a></li>
                        <?php
                        }
                        ?>
                      </ul>
                    </div>
                    <?php
                    // }
                    ?>
                    <!-- <button vctex="<?=$d->cpf?>" class="btn btn-warning">
                      VCTEX
                    </button> -->
                    <!-- <button facta="<?=$d->cpf?>" class="btn btn-warning">
                      FACTA
                    </button> -->

                    <!-- <button
                      class="btn btn-primary"
                      style="margin-bottom:1px"
                      edit="<?=$d->codigo?>"
                      data-bs-toggle="offcanvas"
                      href="#offcanvasDireita"
                      role="button"
                      aria-controls="offcanvasDireita"
                    >
                      Editar
                    </button>
                    <button class="btn btn-danger" delete="<?=$d->codigo?>" <?=$del?>>
                      Excluir
                    </button> -->
                  </td>
                </tr>
                <?php
                $k++;
                  }
                ?>
              </tbody>
            </table>
                </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>


<script>
    $(function(){
        Carregando('none');

        $("#cpf_novo").mask("999.999.999-99");
        <?php
        if(!$_SESSION['busca_campo'] or $_SESSION['busca_campo'] == 'cpf'){
        ?>
        $("input[texto_busca]").mask("999.999.999-99");
        <?php
        }
        ?>

        $("a[campo]").click(function(){
          campo = $(this).attr("campo");
          titulo = $(this).text();

          $("input[texto_busca]").val('');
          $("select[texto_busca]").val('');

          $("input[texto_busca]").unmask();

          $("button[campo]").attr("campo", campo);
          $("button[titulo]").attr("titulo", titulo);
          $("button[titulo]").text(titulo);

          if(campo != 'status'){

            $("input[texto_busca]").css("display", "block");
            $("select[texto_busca]").css("display", "none");

            
            if(campo == 'cpf'){
              $("input[texto_busca]").mask("999.999.999-99");
            }


          }else{
            $("input[texto_busca]").css("display", "none");
            $("select[texto_busca]").css("display", "block");
          }

        })

        $("button[busca_resultado]").click(function(){
          campo = $("button[campo]").attr("campo");
          titulo = $("button[titulo]").attr("titulo");
          if(campo == 'status'){
            busca = $("select[texto_busca]").val();
          }else{
            busca = $("input[texto_busca]").val();
          }
          
          Carregando();
          $.ajax({
                url:"financeira/clientes/index.php",
                type:"POST",
                data:{
                  campo,
                  titulo,
                  busca,
                  acao:'busca'
                },
                success:function(dados){
                  $("#paginaHome").html(dados);
                }
            })
        })

        $("button[busca_limpar]").click(function(){
          Carregando();
          $.ajax({
                url:"financeira/clientes/index.php",
                type:"POST",
                data:{
                  acao:'limpar'
                },
                success:function(dados){
                  $("#paginaHome").html(dados);
                }
            })
        })

        $("button[novoCadastro]").click(function(){
            cpf = $("#cpf_novo").val();
            if(!cpf){
              let myOffCanvas = document.getElementById('offcanvasDireita');
              let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
              openedCanvas.hide();
              $.alert({
                content:"Favor informe o número do CPF",
                title:"Identificação do Cadastro",
                type:'red'
              })
              return false;
            }

            if(cpf.length != 14 || !validarCPF(cpf)){
              let myOffCanvas = document.getElementById('offcanvasDireita');
              let openedCanvas = bootstrap.Offcanvas.getInstance(myOffCanvas);
              openedCanvas.hide();
              $.alert({
                content:"Número de CPF informado inválido",
                title:"Erro de CPF",
                type:'red'
              })
              return false;              
            }

            $.ajax({
                url:"financeira/clientes/form.php",
                type:"POST",
                data:{
                  cpf_novo:cpf
                },
                success:function(dados){
                    $(".LateralDireita").html(dados);
                }
            })
        })

        $("button[logs], a[logs]").click(function(){
            cliente = $(this).attr("logs");
            $.ajax({
                url:"financeira/clientes/logs.php",
                type:"POST",
                data:{
                  cliente
                },
                success:function(dados){
                  $(".LateralDireita").html(dados);
                }
            })
        })

        $("a[mensagens]").click(function(){
            mensagens = $(this).attr("mensagens");
            $.ajax({
                url:"financeira/clientes/wapp.php",
                type:"POST",
                data:{
                  mensagens
                },
                success:function(dados){
                  $(".LateralDireita").html(dados);
                }
            })
        })

        $("button[vctex], a[vctex]").click(function(){
            valor = $(this).attr("vctex");
            $.ajax({
                url:"financeira/vctex/consulta.php",
                type:"POST",
                data:{
                  acao:'consulta',
                  campo:'cpf',
                  rotulo:"CPF",
                  valor
                },
                success:function(dados){
                    $("#paginaHome").html(dados);
                }
            })
        })

        $("button[facta], a[facta]").click(function(){
            valor = $(this).attr("facta");
            $.ajax({
                url:"financeira/facta/consulta.php",
                type:"POST",
                data:{
                  acao:'consulta',
                  campo:'cpf',
                  rotulo:"CPF",
                  valor
                },
                success:function(dados){
                    $("#paginaHome").html(dados);
                }
            })
        })

        $("button[edit], a[edit]").click(function(){
            cod = $(this).attr("edit");
            $.ajax({
                url:"financeira/clientes/form.php",
                type:"POST",
                data:{
                  cod
                },
                success:function(dados){
                    $(".LateralDireita").html(dados);
                }
            })
        })

        $("button[delete], a[delete]").click(function(){
            deletar = $(this).attr("delete");
            $.confirm({
                content:"Deseja realmente excluir o cadastro ?",
                title:false,
                buttons:{
                    'SIM':function(){
                        $.ajax({
                            url:"financeira/clientes/index.php",
                            type:"POST",
                            data:{
                                delete:deletar
                            },
                            success:function(dados){
                              // $.alert(dados);
                              $("#paginaHome").html(dados);
                            }
                        })
                    },
                    'NÃO':function(){

                    }
                }
            });

        })


        $(".situacao").change(function(){

            situacao = $(this).attr("usuario");
            opc = false;

            if($(this).prop("checked") == true){
              opc = '1';
            }else{
              opc = '0';
            }


            $.ajax({
                url:"financeira/clientes/index.php",
                type:"POST",
                data:{
                    situacao,
                    opc
                },
                success:function(dados){
                    // $("#paginaHome").html(dados);
                }
            })

        });

        $("button[Wapp]").click(function(){
            $.ajax({
                url:"financeira/clientes/index.php",
                type:"POST",
                data:{
                    acao:'wapp'
                },
                success:function(dados){
                    // $("#paginaHome").html(dados);
                    console.log(dados);
                }
            })          
        })

    })
</script>