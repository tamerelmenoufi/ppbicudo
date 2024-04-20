<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

        if($_POST['cpf_novo']){
            $d = mysqli_fetch_object(mysqli_query($con, "select * from clientes where cpf = '{$_POST['cpf_novo']}'"));
            if($d->codigo) $_POST['cod'] = $d->codigo;
        }

        list($bancos) = mysqli_fetch_row(mysqli_query($con, "select bancos from configuracoes where codigo = '1'"));
        $bancos = json_decode($bancos);

        $siglas = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];

    if($_POST['acao'] == 'salvar'){

        $data = $_POST;
        $attr = [];

        unset($data['codigo']);
        unset($data['acao']);
        unset($data['senha']);

        foreach ($data as $name => $value) {
            if($name == 'birthdate' or $name == 'document_issueDate'){
                $attr[] = "{$name} = '" . dataMysql($value) . "'";
            }else{
                $attr[] = "{$name} = '" . addslashes($value) . "'";
            }
            
        }

        $attr = implode(', ', $attr);

        if($_POST['codigo']){
            $query = "update clientes set {$attr} where codigo = '{$_POST['codigo']}'";
            mysqli_query($con, $query);
            $cod = $_POST['codigo'];
        }else{
            $query = "insert into clientes set data_cadastro = NOW(), ultimo_acesso = NOW(), {$attr}";
            mysqli_query($con, $query);
            $cod = mysqli_insert_id($con);
        }

        $retorno = [
            'status' => true,
            'codigo' => $cod
        ];

        echo json_encode($retorno);

        exit();
    }

    $query = "select * from clientes where codigo = '{$_POST['cod']}'";
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
<h4 class="Titulo<?=$md5?>">Cadastro do Usuário</h4>
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


                <div class="form-floating mb-3">
                    <input required type="text" name="birthdate" id="birthdate" class="form-control" placeholder="Data de Nascimento" value="<?=dataBr($d->birthdate)?>">
                    <label for="birthdate">Data de Nascimento*</label>
                </div>


                <div class="form-floating mb-3">
                    <select name="gender" id="gender" class="form-select">
                        <option value="M" <?=(($d->gender == 'M')?'selected':false)?>>Masculino</option>
                        <option value="F" <?=(($d->gender == 'F')?'selected':false)?>>Feminino</option>
                    </select>
                    <label for="gender">Gênero*</label>
                </div>


                <div class="form-floating mb-3">
                    <input required type="text" name="phoneNumber" id="phoneNumber" class="form-control" placeholder="Telefone" value="<?=$d->phoneNumber?>">
                    <label for="phoneNumber">Telefone*</label>
                </div>


                <div class="form-floating mb-3">
                    <input type="email" name="email" id="email" class="form-control" placeholder="E-mail" value="<?=$d->email?>">
                    <label for="email">E-mail</label>
                </div>


                <div class="form-floating mb-3">
                    <select name="maritalStatus" id="maritalStatus" class="form-select">
                        <option value="Solteiro" <?=(($d->maritalStatus == 'Solteiro')?'selected':false)?>>Solteiro</option>
                        <option value="Casado" <?=(($d->maritalStatus == 'Casado')?'selected':false)?>>Casado</option>
                        <option value="Uniao Estavel" <?=(($d->maritalStatus == 'Uniao Estavel')?'selected':false)?>>União Estável</option>
                        <option value="Divorciado" <?=(($d->maritalStatus == 'Divorciado')?'selected':false)?>>Divorciado</option>
                        <option value="Separado" <?=(($d->maritalStatus == 'Separado')?'selected':false)?>>Separado</option>
                        <option value="Viúvo" <?=(($d->maritalStatus == 'Viúvo')?'selected':false)?>>Viúvo</option>
                    </select>
                    <label for="maritalStatus">Estado Civil*</label>
                </div>


                <div class="form-floating mb-3">
                    <input required type="text" name="nationality" id="nationality" class="form-control" placeholder="Nacionalidade" value="<?=$d->nationality?>">
                    <label for="nationality">Nacionalidade*</label>
                </div>


                <div class="form-floating mb-3">
                    <input required type="text" name="naturalness" id="naturalness" class="form-control" placeholder="Naturalidade" value="<?=$d->naturalness?>">
                    <label for="naturalness">Naturalidade*</label>
                </div>


                <div class="form-floating mb-3">
                    <input required type="text" name="motherName" id="motherName" class="form-control" placeholder="Nome da Mãe" value="<?=$d->motherName?>">
                    <label for="motherName">Nome da Mãe*</label>
                </div>


                <div class="form-floating mb-3">
                    <input type="text" name="fatherName" id="fatherName" class="form-control" placeholder="Nome do Pai" value="<?=$d->fatherName?>">
                    <label for="fatherName">Nome do Pai</label>
                </div>


                <div class="form-floating mb-3">
                    <select name="pep" id="pep" class="form-select">
                        <option value="false" <?=(($d->pep == 'false')?'selected':false)?>>Não</option>
                        <option value="true" <?=(($d->pep == 'true')?'selected':false)?>>Sim</option>
                    </select>
                    <label for="pep">Exposta Politicamente*</label>
                </div>

                <h5>Documentação</h5>
                <div class="form-floating mb-3">
                    <select name="document_type" id="document_type" class="form-select">
                        <option value="rg" <?=(($d->document_type == 'rg')?'selected':false)?>>RG</option>
                        <option value="cnh" <?=(($d->document_type == 'cnh')?'selected':false)?>>CNH</option>
                    </select>
                    <label for="document_type">Tipo de Documento*</label>
                </div>


                <div class="form-floating mb-3">
                    <input required type="text" name="document_number" id="document_number" class="form-control" placeholder="Número do Documento" value="<?=$d->document_number?>">
                    <label for="document_number">Número do Documento*</label>
                </div>


                <div class="form-floating mb-3">
                    <select required name="document_issuingState" id="document_issuingState" class="form-select">
                        <option value="">:: Selecione o estado ::</option>
                        <?php
                        foreach($siglas as $i => $sigla){
                        ?>
                        <option value="<?=$sigla?>" <?=(($d->document_issuingState == $sigla)?'selected':false)?>><?=$sigla?></option>
                        <?php
                        }
                        ?>
                    </select>    
                    <label for="document_issuingState">Origem do Documento*</label>


                </div>


                <div class="form-floating mb-3">
                    <input required type="text" name="document_issuingAuthority" id="document_issuingAuthority" class="form-control" placeholder="Orgão Emissor" value="<?=$d->document_issuingAuthority?>">
                    <label for="document_issuingAuthority">Orgão Emissor*</label>
                </div>


                <div class="form-floating mb-3">
                    <input required type="text" name="document_issueDate" id="document_issueDate" class="form-control" placeholder="Data da Emissão" value="<?=dataBr(trim($d->document_issueDate))?>">
                    <label for="document_issueDate">Data da Emissão*</label>
                </div>

                <h5>Endereço</h5>

                <div class="form-floating mb-3">
                    <input required type="text" name="address_zipCode" id="address_zipCode" class="form-control" placeholder="CEP" value="<?=$d->address_zipCode?>">
                    <label for="address_zipCode">CEP*</label>
                </div>


                <div class="form-floating mb-3">
                    <input required type="text" name="address_street" id="address_street" class="form-control" placeholder="Avenida, rua ou Beco" value="<?=$d->address_street?>">
                    <label for="address_street">Logradouro*</label>
                </div>


                <div class="form-floating mb-3">
                    <input required type="text" name="address_number" id="address_number" class="form-control" placeholder="Número da Moradia" value="<?=$d->address_number?>">
                    <label for="address_number">Número da Moradia*</label>
                </div>


                <div class="form-floating mb-3">
                    <input type="text" name="address_complement" id="address_complement" class="form-control" placeholder="Conjunto, Edifício, Condomínio, Bloco" value="<?=$d->address_complement?>">
                    <label for="address_complement">Complemento</label>
                </div>


                <div class="form-floating mb-3">
                    <input type="text" name="address_neighborhood" id="address_neighborhood" class="form-control" placeholder="Bairro" value="<?=$d->address_neighborhood?>">
                    <label for="address_neighborhood">Bairro</label>
                </div>


                <div class="form-floating mb-3">
                    <input required type="text" name="address_city" id="address_city" class="form-control" placeholder="Cidade" value="<?=$d->address_city?>">
                    <label for="address_city">Cidade*</label>
                </div>


                <div class="form-floating mb-3">
                    <select required name="address_state" id="address_state" class="form-select">
                        <option value="">:: Selecione o Estado ::</option>
                        <?php
                        foreach($siglas as $i => $sigla){
                        ?>
                        <option value="<?=$sigla?>" <?=(($d->address_state == $sigla)?'selected':false)?>><?=$sigla?></option>
                        <?php
                        }
                        ?>
                    </select>   
                    <label for="address_state">Estado*</label>
                </div>


                <div class="form-floating mb-3">
                    <input required type="text" name="renda" id="renda" class="form-control" placeholder="Cidade" value="<?=$d->renda?>">
                    <label for="renda">Renda*</label>
                </div>

                <div class="form-floating mb-3">
                    <input required type="text" name="valor_patrimonio" id="valor_patrimonio" class="form-control" placeholder="Cidade" value="<?=$d->valor_patrimonio?>">
                    <label for="valor_patrimonio">Valor do Patrimônio*</label>
                </div>
                
                <div class="form-floating mb-3">
                    <select name="cliente_iletrado_impossibilitado" id="cliente_iletrado_impossibilitado" class="form-select">
                        <option value="nao" <?=(($d->cliente_iletrado_impossibilitado == 'nao')?'selected':false)?>>Não</option>
                        <option value="sim" <?=(($d->cliente_iletrado_impossibilitado == 'sim')?'selected':false)?>>Sim</option>
                    </select>
                    <label for="cliente_iletrado_impossibilitado">Cliente Iletrado Impossibilitado*</label>
                </div>



                <h5>Dados Bancários</h5>
                <div class="form-floating mb-3">
                    <select required name="bankCode" id="bankCode" class="form-select">
                        <option value="">:: Selecione o Banco ::</option>
                        <?php
                        arsort($banco);
                        foreach($bancos as $cod => $banco){
                        ?>
                        <option value="<?=$banco->value?>" <?=(($d->bankCode == $banco->value)?'selected':false)?>><?="{$banco->value} - {$banco->label}"?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="bankCode">Banco*</label>
                </div>

                <div class="form-floating mb-3">
                    <select name="accountType" id="accountType" class="form-select">
                        <option value="corrente" <?=(($d->accountType == 'corrente')?'selected':false)?>>Corrente</option>
                        <option value="poupanca" <?=(($d->accountType == 'poupanca')?'selected':false)?>>Poupança</option>
                    </select>
                    <label for="accountType">Tipo da Conta*</label>
                </div>


                <div class="form-floating mb-3">
                    <input required type="text" name="accountNumber" id="accountNumber" class="form-control" placeholder="Número da Conta" value="<?=$d->accountNumber?>">
                    <label for="accountNumber">Número da Conta*</label>
                </div>


                <div class="form-floating mb-3">
                    <input required type="text" name="accountDigit" id="accountDigit" class="form-control" placeholder="Dígito da Conta" value="<?=$d->accountDigit?>">
                    <label for="accountDigit">Dígito da Conta*</label>
                </div>


                <div class="form-floating mb-3">
                    <input required type="text" name="branchNumber" id="branchNumber" class="form-control" placeholder="Agência" value="<?=$d->branchNumber?>">
                    <label for="branchNumber">Agência*</label>
                </div>


            </div>
        </div>

        <div class="row">
            <div class="col">
                <div style="display:flex; justify-content:end">
                    <button type="submit" class="btn btn-success btn-ms">Salvar</button>
                    <input type="hidden" id="codigo" value="<?=$_POST['cod']?>" />
                    <input type="hidden" id="retorno" value="<?=($_POST['retorno']?:'financeira/clientes/index.php')?>" />
                </div>
            </div>
        </div>
    </form>

    <script>
        $(function(){
            Carregando('none');

            $("#cpf").mask("999.999.999-99");
            $("#phoneNumber").mask("(99) 99999-9999");
            $("#birthdate, #document_issueDate").mask("99/99/9999");
            $("#address_zipCode").mask("99999-999");


            $('#form-<?=$md5?>').submit(function (e) {

                e.preventDefault();

                var codigo = $('#codigo').val();
                var retorno = $('#retorno').val();
                var campos = $(this).serializeArray();

                if (codigo) {
                    campos.push({name: 'codigo', value: codigo})
                }

                campos.push({name: 'acao', value: 'salvar'})

                cpf = $("#cpf").val();
                if(!validarCPF(cpf)){
                    $.alert({
                        title:"Erro",
                        content:"CPF Inválido",
                        type:'red'
                    });
                    return false;
                }

                Carregando();

                $.ajax({
                    url:"financeira/clientes/form.php",
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

        })
    </script>