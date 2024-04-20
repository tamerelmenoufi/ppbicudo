<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    $data = (($_POST['data'])?:date("Y-m"));

    $query = "select
                (select count(*) from clientes where data_cadastro like '{$data}%') as novos_cadastros,
                (select count(*) from consultas where data like '{$data}%') as simulacoes,
                (select count(*) from consultas where data like '{$data}%' and dados->>'$.statusCode' = '200') as simulacoes_positiva,
                (select count(*) from consultas where data like '{$data}%' and dados->>'$.statusCode' != '200') as simulacoes_negativa,

                (select count(*) from consultas where data like '{$data}%' and proposta->>'$.statusCode') as propostas,
                (select count(*) from consultas where data like '{$data}%' and proposta->>'$.statusCode' and proposta->>'$.statusCode' = '130') as propostas_pagas,
                (select count(*) from consultas where data like '{$data}%' and proposta->>'$.statusCode' and proposta->>'$.statusCode' in ('200', '95', '60', '61')) as propostas_pendentes,
                (select count(*) from consultas where data like '{$data}%' and proposta->>'$.statusCode' and proposta->>'$.statusCode' not in ('200', '130', '95', '60', '61')) as propostas_erro
            ";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);

    $dados = [
        ['NC', 'Novos Cadastros', $d->novos_cadastros],
        ['SR', 'Simulações Realizadas', $d->simulacoes],
        ['SS', 'Simulações bem Sucedidas', $d->simulacoes_positiva],
        ['SN', 'Simulações Negadas', $d->simulacoes_negativa],
        ['PR', 'Propostas Realizadas', $d->propostas],
        ['AP', 'Antecipação Paga', $d->propostas_pagas],
        ['PP', 'Propostas com Pendências', $d->propostas_pendentes],
        ['PN', 'Propostas Negadas', $d->propostas_erro]
    ];

    list($Y1,$m1,$d1) = explode("-",$data);
    if($d1) $dt = "{$d1}/{$m1}/{$Y1}";
    else $dt = "{$m1}/{$Y1}";
?>

<div class="row">
    <div class="col-md-6">
      <h5>Solicitações no período de <?=$dt?></h5>
      <table class="table table-hover">
        <thead>
          <tr>
            <th scope="col" class="text-center">Sigla</th>
            <th scope="col">Descrição</th>
            <th scope="col" class="text-center">Quanidade</th>
          </tr>
        </thead>
        <tbody>
      <?php
          foreach($dados as $item => $valor){

            eval("\${$valor[0]} = {$valor[2]};");
      ?>
          <tr>
            <td class="text-center"><?=$valor[0]?></td>
            <td><?=$valor[1]?></td>
            <td class="text-center">
              <button
                  class="btn btn-primary btn-sm"
                  filtro="<?=$valor[0]?>"
                  periodo="<?=$data?>"
                  data-bs-toggle="offcanvas"
                  href="#offcanvasDireita"
                  role="button"
                  aria-controls="offcanvasDireita"
              >
                  <i class="fa-solid fa-arrow-up-right-from-square"></i> <?=$valor[2]?>
              </button>
          </td>
          </tr>
      <?php
          }

      ?>
        </tbody>
      </table>
    </div>
    <div class="col-md-6">
      <h5>Representação Gráfica das Solicitações</h5>
      <canvas id="solicitacoes" style="margin-top:30px;"></canvas>
    </div>
</div>




<script>

    ///////////////////////// Grafico ////////////////////////////////////////////////////////////


    new Chart("solicitacoes", {
        type: "bar",
        data: {
            labels: ['N.C.','S.R.','S.S.','S.N.','P.R.', 'A.P.', 'P.P.', 'P.N.'],
            datasets: [{
                data: ['<?=$NC?>', '<?=$SR?>', '<?=$SS?>', '<?=$SN?>', '<?=$PR?>', '<?=$AP?>', '<?=$PP?>', '<?=$PN?>'],
                label: 'Solicitações',
                borderColor: "blue",
                backgroundColor:"rgb(2, 62, 198, 0.7)",
                fill: false
            }]
            // datasets: [{
            // label: 'Pré-cadastro',
            // data: [<?=$pre_cadastro?>],
            // borderColor: "blue",
            // backgroundColor:"rgb(2, 62, 198, 0.7)",
            // fill: false
            // },{
            // label: 'Autorização',
            // data: [<?=$autorizacao?>],
            // borderColor: "green",
            // backgroundColor:"rgb(1, 174, 50, 0.7)",
            // fill: false
            // },{
            // label: 'Simulação',
            // data: [<?=$simulacao?>],
            // borderColor: "gray",
            // backgroundColor:"rgb(116, 116, 116, 0.7)",
            // fill: false
            // },{
            // label: 'Cadastros',
            // data: [<?=$cadastro?>],
            // borderColor: "red",
            // backgroundColor:"rgb(200, 3, 54, 0.7)",
            // fill: false
            // },{
            // label: 'Contratos',
            // data: [<?=$contrato?>],
            // borderColor: "orange",
            // backgroundColor:"rgb(247, 152, 2, 0.7)",
            // fill: false
            // }]
        },
        options: {
          legend: {display: false},
          title: {
              display: true,
              text: "Gráfico de Representação dos Cadastros",
              fontSize: 16
          },
          tooltips: {
            enabled: true,
            callbacks: {
              ticks: {
                label: function(tooltipItem, data) {
                    console.log(tooltipItem)
                    console.log(data)
                  }
              }
            }
          }
        }
    });


    $(function(){
      Carregando('none');
        
      $("button[filtro]").click(function(){

        filtro = $(this).attr("filtro");
        periodo = $(this).attr("periodo");
        Carregando();
        $.ajax({
          url:"financeira/dashboard/home/filtro.php",
          type:"POST",
          data:{
            filtro,
            periodo
          },
          success:function(dados){
            $(".LateralDireita").html(dados);
          }
        })

      })
        
    })
</script>