<?php

    function dataBr($dt){
        list($d, $h) = explode(" ",$dt);
        list($y,$m,$d) = explode("-",$d);
        $data = false;
        if($y && $m && $d){
            $data = "{$d}/{$m}/$y".(($h)?" {$h}":false);
        }
        return $data;
    }

    function dataMysql($dt){
        list($d, $h) = explode(" ",$dt);
        list($d,$m,$y) = explode("/",$d);
        $data = false;
        if($y && $m && $d){
            $data = "{$y}-{$m}-$d".(($h)?" {$h}":false);
        }
        return $data;
    }

    function montaCheckbox($v){
        $campo = $v['campo'];
        $vetor = $v['vetor'];
        $rotulo = $v['rotulo'];
        $dados = json_decode($v['dados']);
        $exibir = $v['exibir'];
        $destino = $v['campo_destino'];
        // $lista[] = print_r($dados, true);
        $lista[] = '<div class="mb-3"><label for="'.$campo.'"><b>'.$rotulo.'</b></label></div>';
        for($i=0;$i<count($vetor);$i++){
            $lista[] = '  <div class="mb-3 form-check">
            <input
                    type="checkbox"
                    name="'.$campo.'[]"
                    value="'.$vetor[$i].'"
                    class="form-check-input"
                    id="'.$campo.$i.'"
                    '.((@in_array($vetor[$i],$dados))?'checked':false).'
                    '.(($exibir[$vetor[$i]])?' exibir="'.$destino.'" ':' ocultar="'.$destino.'"').'
            >
            <label class="form-check-label" for="'.$campo.$i.'">'.$vetor[$i].'</label>
            </div>';
        }

        if($lista){
            return implode(" ",$lista);
        }
    }

    function montaRadio($v){
        $campo = $v['campo'];
        $vetor = $v['vetor'];
        $rotulo = $v['rotulo'];
        $dados = $v['dados'];
        $exibir = $v['exibir'];
        $destino = $v['campo_destino'];

        $lista[] = '<div class="mb-3"><label for="'.$campo.'"><b>'.$rotulo.'</b></label></div>';
        for($i=0;$i<count($vetor);$i++){
            $lista[] = '  <div class="mb-3 form-check">
            <input
                    type="radio"
                    name="'.$campo.'"
                    value="'.$vetor[$i].'"
                    class="form-check-input"
                    id="'.$campo.$i.'"
                    '.(($vetor[$i] == $dados)?'checked':false).'
                    '.(($exibir[$vetor[$i]])?' exibir="'.$destino.'" ':' ocultar="'.$destino.'"').'
            >
            <label class="form-check-label" for="'.$campo.$i.'">'.$vetor[$i].'</label>
            </div>';
        }
        if($lista){
            return implode(" ",$lista);
        }
    }



    function montaCheckboxFiltro($v){
        $campo = $v['campo'];
        $vetor = $v['vetor'];
        $rotulo = $v['rotulo'];
        $dados = $v['dados'];
        $exibir = $v['exibir'];
        $destino = $v['campo_destino'];
        // $lista[] = print_r($dados, true);
        $lista[] = '<div class="mb-3"><label for="'.$campo.'"><b>'.$rotulo.'</b></label></div>';
        for($i=0;$i<count($vetor);$i++){
            $lista[] = '  <div class="mb-3 form-check">
            <input
                    type="checkbox"
                    name="'.$campo.'[]"
                    value="'.$vetor[$i].'"
                    class="form-check-input"
                    id="'.$campo.$i.'"
                    '.((@in_array($vetor[$i],$dados))?'checked':false).'
                    '.(($exibir[$vetor[$i]])?' exibir="'.$destino.'" ':' ocultar="'.$destino.'"').'
            >
            <label class="form-check-label" for="'.$campo.$i.'">'.$vetor[$i].'</label>
            </div>';
        }

        if($lista){
            return implode(" ",$lista);
        }
    }



    function montaOpcPrint($v){
        $campo = $v['campo'];
        $vetor = $v['vetor'];
        $rotulo = $v['rotulo'];
        $dados = json_decode($v['dados']);
        // $lista[] = print_r($dados, true);
        $lista[] = '<div class="mt-3" style="width:100%; float:none;"><b>'.$rotulo.'</b></div><div style="width:100%; float:none;">';
        for($i=0;$i<count($vetor);$i++){
            $lista[] = '  <span margin-left:15px;">
            <i class="fa-solid fa-square" style="color:#ccc"></i> '.$vetor[$i].'</span>';
        }
        $lista[] = '</div>';
        if($lista){
            return implode(" ",$lista);
        }
    }


    function array_multisum($arr){
        $sum = array_sum($arr);
        foreach($arr as $child) {
            $sum += is_array($child) ? array_multisum($child) : 0;
        }
        return $sum;
    }

    function sisLogRegistro($q){
        global $con;
        $q = strtolower($q);
        list($p1, $p2) = explode("set", $q);
        list($p3, $p4) = explode("where", $q);
        $query = str_replace("update", "select codigo from", $p1)." where ".$p4;
        $result = mysqli_query($con, $query);
        $r = [];
        while($d = mysqli_fetch_object($result)){
            $r[] = (int)($d->codigo);
        }
        return json_encode($r);
    }

    function sisLog($d){

        $remove = ["\\n", "\\t", "  ", "	"];
        $d = str_replace($remove, " ", $d);

        global $con;
        global $_POST;
        global $_SESSION;
        global $_SERVER;
        $r = [];
        $tabela = false;

        $result = mysqli_query($con, $d);
    
        $query = addslashes($d);
        $file = $_SERVER["PHP_SELF"];
        $sessao = addslashes(json_encode($_SESSION));
        $dados = addslashes(json_encode($_POST));
        $p = explode(" ",trim($query));
        $operacao = strtoupper(trim($p[0]));
        if(strtolower(trim($p[0])) == 'insert'){
            $tabela =  strtoupper(trim($p[2]));
            $r[] = mysqli_insert_id($con);
            $registro = json_encode($r);
        }
        if(strtolower(trim($p[0])) == 'update'){
            $tabela =  strtoupper(trim($p[1]));
            $registro = sisLogRegistro($d);
        }

        if($tabela){
            mysqli_query($con, "
                INSERT INTO sisLog set 
                                        file = '{$file}',
                                        tabela = '{$tabela}',
                                        operacao = '{$operacao}',
                                        registro = '{$registro}',
                                        sessao = '{$sessao}',
                                        dados = '{$dados}',
                                        query = '{$query}',
                                        data = NOW()
            ");
        }

        return $result;
    
    }