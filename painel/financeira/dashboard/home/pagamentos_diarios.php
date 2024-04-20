<?php
        include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");
?>
<style>
  .legenda_status{
    border-left:5px solid;
    border-left-color:green;
  }
  .Titulo<?=$md5?>{
        position:absolute;
        left:60px;
        top:8px;
        z-index:0;
    }

</style>

<h4 class="Titulo<?=$md5?>">Pagamentos por Período</h4>


<div class="col">
  <div class="m-3">

    <div class="row">
      <div class="col">
        <div class="card">
          <h5 class="card-header">Período de <?=$_POST['periodo']?> </h5>
          <div class="card-body">

            <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th scope="col">Data</th>
                  <th scope="col">Contratos</th>
                  <th scope="col">Valor</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  list($m, $a) = explode("/", $_POST['periodo']);
                  $query = "select sum(dados->'$.data.simulationData.totalReleasedAmount') as valor, data, count(*) as contratos from consultas where proposta->>'$.statusCode' = '130' and data like '{$a}-{$m}%' group by day(data) order by data asc";
                  $result = mysqli_query($con, $query);
                  $totais_valor = $totais_contratos = 0;
                  while($d = mysqli_fetch_object($result)){
                    $totais_valor = $totais_valor + $d->valor;
                    $totais_contratos = $totais_contratos + $d->contratos;
                    list($data, $hora) = explode(" ",$d->data);
                ?>
                <tr>
                  <td><?=dataBr($data)?></td>
                  <td><?=$d->contratos?></td>
                  <td>R$ <?=number_format($d->valor,2,",",".")?></td>
                </tr>
                <?php
                  }
                ?>
                <tr>
                  <td style="text-align:right">TOTAIS</td>
                  <td><?=$totais_contratos?></td>
                  <td>R$ <?=number_format($totais_valor,2,",",".")?></td>
                </tr>
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
 
    })
</script>