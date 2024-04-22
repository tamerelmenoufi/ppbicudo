<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="img/icone.png">
    <title>PPBICUDO - Painel de Controle</title>
    <?php
    include("lib/header.php");
    ?>
  </head>
  <body translate="no">



    <table class="table table-striped">
        <thead>
        <tr>
            <th scope="col">Lote</th>
            <th scope="col">Origem</th>
            <th scope="col">Data</th>
            <th scope="col">Usuário</th>
            <th scope="col">Situação</th>
            <th scope="col" width="80">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php
            $query = "select a.*, b.nome as usuario_nome, o.nome as origem, (select count(*) from relatorio where planilha = a.codigo) as registros from planilhas a left join usuarios b on a.usuario = b.codigo left join origens o on a.origem = o.codigo order by a.data desc limit 100";
            $result = mysqli_query($con, $query);
            while($d = mysqli_fetch_object($result)){
        ?>
        <tr>
            <td style="white-space: nowrap;"><?=strtoupper($d->lote)?></td>
            <td style="white-space: nowrap;"><?=$d->origem?></td>
            <td style="white-space: nowrap;"><?=dataBr($d->data)?></td>
            <td style="white-space: nowrap;"><?=$d->usuario_nome?></td>
            <td style="white-space: nowrap;">
            <i 
                situacao="<?=$d->codigo?>" 
                planilha="<?=$d->planilha?>" 
                class="fa-solid fa-file-arrow-up text-<?=(($d->situacao == '1' and $d->registros)?'success':'secondary situacao')?>" 
                style="font-size:30px; <?=(($d->situacao == '1' and $d->registros)?false:'cursor:pointer')?>"
            ></i> <?=(($d->registros)?:false)?>
            </td>
            <td acoes style="white-space: nowrap;">
            <?php
            if(!$d->registros){
            ?>
            <button class="btn btn-danger btn-sm" deletar="<?=$d->codigo?>" planilha="<?=$d->planilha?>">
            <i class="fa-solid fa-trash-can"></i> Excluir
            </button>
            <?php
            }
            ?>
            </td>
        </tr>
        <?php
            }
        ?>
        </tbody>
    </table>




    <?php
    include("lib/footer.php");
    ?>

    <script>
 

    </script>

  </body>
</html>