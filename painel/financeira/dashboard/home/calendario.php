<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    if($_POST['n']) $_SESSION['n'] = $_POST['n'];
    if($_POST['Y']) $_SESSION['Y'] = $_POST['Y'];

    // Configurações iniciais
    $month = (($_SESSION['n'])?:date("n"));
    $year = (($_SESSION['Y'])?:date("Y"));
    $first_day_of_month = mktime(0, 0, 0, $month, 1, $year);
    $end_day_of_month = mktime(0, 0, 0, $month + 1, 1-1, $year);
    $days_in_month = (((($end_day_of_month)/84600) - (($first_day_of_month)/84600)) + 1);

    $day_of_week = date("N", $first_day_of_month);
    $month_name = date("F", $first_day_of_month);

    $dados = [];
    // $query = "select a.codigo as cod_cliente, a.nome, a.cpf, a.ultimo_acesso, b.log, b.codigo from consultas_log b left join clientes a on a.codigo = b.cliente where a.ultimo_acesso like '{$year}-".str_pad($month, 2, "0", STR_PAD_LEFT)."%' order by b.codigo asc";
    $query = "select ultimo_acesso as data from clientes where ultimo_acesso like '{$year}-".str_pad($month, 2, "0", STR_PAD_LEFT)."%'";
    // $query = "select * from sisLog where data like '{$year}-".str_pad($month, 2, "0", STR_PAD_LEFT)."%' and tabela in ('clientes', 'CONSULTAS') and sessao->>'$.codUsr' > 0 group by sessao->>'$.codUsr'";

    $result = mysqli_query($con,$query);
    while($d = mysqli_fetch_object($result)){
        $dt = trim(explode(" ", $d->data)[0]);
        $dados[$dt]++;
    }

    // print_r($dados);

    // Cabeçalho do calendário
    // echo "<h2>Calendário de $month_name $year</h2>";
?>

<div class="input-group mb-3">
  <label class="input-group-text" for="inputGroupSelect01">Mês</label>
  <select dateN class="form-select">
    <option value="1" <?=(($month == '1')?'selected':false)?>>Jan</option>
    <option value="2" <?=(($month == '2')?'selected':false)?>>Fev</option>
    <option value="3" <?=(($month == '3')?'selected':false)?>>Mar</option>
    <option value="4" <?=(($month == '4')?'selected':false)?>>Abr</option>
    <option value="5" <?=(($month == '5')?'selected':false)?>>Mai</option>
    <option value="6" <?=(($month == '6')?'selected':false)?>>Jun</option>
    <option value="7" <?=(($month == '7')?'selected':false)?>>Jul</option>
    <option value="8" <?=(($month == '8')?'selected':false)?>>Ago</option>
    <option value="9" <?=(($month == '9')?'selected':false)?>>Set</option>
    <option value="10" <?=(($month == '10')?'selected':false)?>>Out</option>
    <option value="11" <?=(($month == '11')?'selected':false)?>>Nov</option>
    <option value="12" <?=(($month == '12')?'selected':false)?>>Dez</option>
  </select>

  <label class="input-group-text">Ano</label>
  <select dateY class="form-select" >
    <option value="2024">2024</option>
  </select>
  <button dateAcao class="btn btn-outline-secondary" type="button">Listar</button>
</div>

<?php
    echo "<table border='1' class='calendario'>";
    echo "<tr><th>Seg</th><th>Ter</th><th>Qua</th><th>Qui</th><th>Sex</th><th>Sáb</th><th>Dom</th></tr>";

    // Calcular espaços em branco para os dias do mês anterior
    $blank_spaces = $day_of_week - 1;

    // Calcular o número total de células na tabela
    $total_cells = $blank_spaces + $days_in_month;

    // Contador de dias
    $day_counter = 1;

    // Loop para construir a tabela
    for ($i = 0; $i < 6; $i++) {
        echo "<tr>";
        for ($j = 0; $j < 7; $j++) {
            if ($blank_spaces > 0) {
                echo "<td></td>";
                $blank_spaces--;
            } elseif ($day_counter <= $days_in_month) {
                $tem = $dados["{$year}-".str_pad($month, 2, "0", STR_PAD_LEFT)."-".str_pad($day_counter, 2, "0", STR_PAD_LEFT)];
                $data = "{$year}-".str_pad($month, 2, "0", STR_PAD_LEFT)."-".str_pad($day_counter, 2, "0", STR_PAD_LEFT);
                echo "<td>{$day_counter}".
                (($tem)?
                        "<div class='registros' data='{$data}'><i class=\"fa-solid fa-user-pen\"></i> ".$tem."</div>"
                       :
                        "<div class='registros_limpo'></div>"
                ).
                    "</td>";
                $day_counter++;
            }
        }
        echo "</tr>";
    }

    echo "</table>";

?>
<script>
    $(function(){
        $("button[dateAcao]").click(function(){
            n = ("00" + $("select[dateN]").val()).slice(-2);
            Y = $("select[dateY]").val();
            $.ajax({
                url:"financeira/dashboard/home/calendario.php",
                type:"POST",
                data:{
                    n,
                    Y
                },
                success:function(dados){
                    $("div[dbCalendar]").html(dados);

                    $.ajax({
                        url:"financeira/dashboard/home/tabela.php",
                        type:"POST",
                        data:{
                            data:`${Y}-${n}`
                        },
                        success:function(dados){
                            $("div[dbTabela").html(dados);
                        }
                    })

                    $.ajax({
                        url:"financeira/dashboard/home/cadastros.php",
                        type:"POST",
                        data:{
                            data:`${Y}-${n}`
                        },
                        success:function(dados){
                            $("div[dbCadastros").html(dados);
                        }
                    })

                }
            });
        })


        $(".registros").click(function(){
            
            Carregando();
            
            data = $(this).attr("data");

            $.ajax({
                url:"financeira/dashboard/home/tabela.php",
                type:"POST",
                data:{
                    data
                },
                success:function(dados){
                    $("div[dbTabela").html(dados);
                }
            })

            $.ajax({
                url:"financeira/dashboard/home/cadastros.php",
                type:"POST",
                data:{
                    data
                },
                success:function(dados){
                    $("div[dbCadastros").html(dados);
                }
            })

        })
    })
</script>