<?php

class Facta {

    public $ambiente = 'producao'; //homologacao ou producao

    public $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIxNDAzIiwibHZsIjoiMiIsInVzciI6Ijk2NzUzIiwiY3J0IjoiOTY3NTMiLCJpYXQiOjE3MTA3OTEyMTIsImV4cCI6MTcxMDc5NDgxMn0.3NifAuyPI-UYQ7fKIwLsztF7Pjx0IHBE_aK1AUoyA44';

    public function Ambiente($opc){
        if($opc == 'homologacao'){
            return 'https://webservice-homol.facta.com.br/';
        }else{
            return 'https://webservice.facta.com.br/';
        }
    }

    public function Token(){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->Ambiente($this->ambiente).'gera-token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Basic OTY3NTM6a2M4emRmZjljdWxoajFjbGpoZWQ=';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return json_encode(json_decode($result),JSON_UNESCAPED_UNICODE);
       
    }

    public function Tabelas($dados){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->Ambiente($this->ambiente).'comercial/tabelas-coeficientes?averbador=3&tipo_operacao=13&data='.date("d/m/Y"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Bearer '.$dados['token'];
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_encode(json_decode($result),JSON_UNESCAPED_UNICODE);

    }

    public function Saldo($dados){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->Ambiente($this->ambiente).'fgts/saldo?cpf='.$dados['cpf']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Bearer '.$dados['token'];
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_encode(json_decode($result),JSON_UNESCAPED_UNICODE);

    }


    public function Calculo($dados){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->Ambiente($this->ambiente).'fgts/calculo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dados['json']);

        $headers = array();
        $headers[] = 'Authorization: Bearer '.$dados['token'];
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_encode(json_decode($result),JSON_UNESCAPED_UNICODE);

    }


    public function Simulador($dados){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->Ambiente($this->ambiente).'proposta/etapa1-simulador');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $post = $dados['dados'];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $headers = array();
        $headers[] = 'Authorization: Bearer '.$dados['token'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_encode(json_decode($result),JSON_UNESCAPED_UNICODE);

    }




    public function DadosPessoais($token = false){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->Ambiente($this->ambiente).'proposta/etapa2-dados-pessoais');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $post = array(
            'id_simulador' => '0000000',
            'cpf' => '00000000000',
            'nome' => 'Fulano de Tal',
            'sexo' => 'M',
            'estado_civil' => '6',
            'data_nascimento' => '01/01/2000',
            'rg' => '000000',
            'estado_rg' => 'RS',
            'data_expedicao' => '01/01/2000',
            'orgao_emissor' => 'SSP',
            'estado_natural' => 'RS',
            'cidade_natural' => '35',
            'nacionalidade' => '1',
            'celular' =>'(000) 00000-0000',
            'renda' => '1100',
            'cep' => '00000000',
            'endereco' => 'Rua A',
            'numero' => '1',
            'bairro' => 'Centro',
            'cidade' => '35',
            'estado' => 'RS',
            'nome_mae' => 'NAO DECLARADO',
            'nome_pai' => 'NAO DECLARADO',
            'valor_patrimonio' => '1',
            'cliente_iletrado_impossibilitado' => 'N',
            'banco' => '999',
            'agencia' => '9999',
            'conta' => '9999999');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $headers = array();
        $headers[] = 'Authorization: Bearer '.$this->token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_encode(json_decode($result),JSON_UNESCAPED_UNICODE);

    }



    public function Cadastro($token = false){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->Ambiente($this->ambiente).'proposta/etapa3-proposta-cadastro');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $post = array('codigo_cliente' => 0000204,'id_simulador' => '0000765'); //whatsapp ou sms
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $headers = array();
        $headers[] = 'Authorization: Bearer '.$this->token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_encode(json_decode($result),JSON_UNESCAPED_UNICODE);

    }

    public function Envio($token = false){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->Ambiente($this->ambiente).'proposta/envio-link');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $post = array('codigo_af' => 00005127,'tipo_envio' => 'whatsapp'); //whatsapp ou sms
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $headers = array();
        $headers[] = 'Authorization: Bearer '.$this->token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_encode(json_decode($result),JSON_UNESCAPED_UNICODE);

    }

}