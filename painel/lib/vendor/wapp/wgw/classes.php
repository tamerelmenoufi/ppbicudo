<?php


class wgw {

    public function key(){
        global $Conf;
        return $Conf['wgw-key'];
    }

    public function acao($dados = false){
        global $ConfWappNumero;
        $acao = [
            'd' => 'SendChatStateComposing',
            'g' => 'SendChatStateRecording',
            'p' => 'SendChatStatePaused'
        ];


        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://app.whatsgw.com.br/api/WhatsGw/'.$acao[$dados['acao']],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'apikey='.$this->key().'&phone_number='.$ConfWappNumero.'&contact_phone_number='.$dados['para'],
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // return $response;

    }

    public function SendTxt($dados = false){

      global $ConfWappNumero;

        $this->acao(['acao'=>'d','para'=>$dados['para']]);

        // sleep(20);

        $this->acao(['acao'=>'d','para'=>$dados['para']]);

        // sleep(1);

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://app.whatsgw.com.br/api/WhatsGw/Send',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
        "apikey" : "'.$this->key().'",
        "phone_number" : "'.$ConfWappNumero.'",
        "contact_phone_number" : "'.$dados['para'].'",
        "message_custom_id" : "'.date("YmdHis").'",
        "message_type" : "text",
        "message_body" : "'.$dados['mensagem'].'"
        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        return $response;

    }

    public function SendAudio($dados = false){

      global $ConfWappNumero;

        $this->acao(['acao'=>'g','para'=>$dados['para']]);

        // sleep(20);

        $this->acao(['acao'=>'p','para'=>$dados['para']]);
        
        // sleep(1);

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://app.whatsgw.com.br/api/WhatsGw/Send',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
        "apikey" : "'.$this->key().'",
        "phone_number" : "'.$ConfWappNumero.'",
        "contact_phone_number" : "'.$dados['para'].'",
        "message_custom_id" : "'.date("YmdHis").'",
        "message_type" : "ptt",
        "check_status" : "1",
        "message_body_mimetype" : "audio/ogg; codecs=opus",
        "message_body_filename" : "file.ogg",
        "message_body" : "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"
        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        echo $response;

    }
    
}