<?php

class Vctex {

    public $ambiente = 'producao'; //homologacao ou producao

    public function Ambiente($opc){
        if($opc == 'homologacao'){
            return 'https://fgts.sandbox.salaryfits.com.br/api/';
        }else{
            return 'https://www.appvctex.com.br/api/';
        }
    }

    public function Token(){

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->Ambiente($this->ambiente).'authentication/login',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "cpf":"99713047249",
            "password":"Neto@224"
        }', //hrB35yHac@
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response; //."\n".$this->Ambiente($this->ambiente)."\n".$this->apiKey($this->ambiente, $loja)."\n";

    }


    public function Tabelas($token){

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->Ambiente($this->ambiente).'service/fee-schedule',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Authorization: Bearer '.$token
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response; //."\n".$this->Ambiente($this->ambiente)."\n".$this->apiKey($this->ambiente, $loja)."\n";

    }


    public function Simular($dados){

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->Ambiente($this->ambiente).'service/simulation',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{"clientCpf":"'.$dados['cpf'].'","feeScheduleId":'.$dados['tabela'].'}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer '.$dados['token']
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response; //."\n".$this->Ambiente($this->ambiente)."\n".$this->apiKey($this->ambiente, $loja)."\n";
    }

    public function Credito($dados){

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->Ambiente($this->ambiente)."service/proposal",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $dados['json'],
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer '.$dados['token']
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response; //."\n".$this->Ambiente($this->ambiente)."/orders/preview"."\n".$this->apiKey($this->ambiente)."\n";

    }


    public function Conculta($dados){

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->Ambiente($this->ambiente).'service/proposal/status/'.$dados['proposalId'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Authorization: Bearer '.$dados['token']
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response; //."<br>".$this->Ambiente($this->ambiente).'status/:'.$dados['proposalId']."<br>".$dados['proposalId']."<br>".$dados['token']."\n";
        // return $response."<br>".$this->Ambiente($this->ambiente).'service/proposal/status/'.$dados['proposalId']."\n";

    }

}