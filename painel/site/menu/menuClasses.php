<?php
    function MontaMenu($v,$b='|'){

        global $con;
        $w = strlen($b)*20;
        $query = "select a.*, (select count(*) from menus where vinculo = a.codigo) as qt from menus a where a.vinculo = '{$v}' and a.situacao = '1' order by a.ordem asc";
        $result = mysqli_query($con, $query);


        if($v == 0){
?>
        <div
            class="itemMenu"
            style='position:relative; margin-left:<?=$w?>px; display: flex; justify-content: space-between;'
            >
            <div>Nível Raíz</div>
            <div class="acoes">
                <!-- <i class="bi bi-eraser excluir" cod="<?=$d->codigo?>" vinculo="<?=$d->codigo?>"></i> -->
                <i class="fa fa-plus adicionar" cod="<?=$d->codigo?>" vinculo="<?=$d->codigo?>"></i>
                <!-- <i class="bi bi-pencil editar" cod="<?=$d->codigo?>" vinculo="<?=$d->codigo?>"></i> -->
            </div>
        </div>
<?php
        }
?>
<div class="grupoMenus">
<?php
        while($d = mysqli_fetch_object($result)){

?>
<div>
        <div
            class="itemMenu listaMenu"
            cod="<?=$d->codigo?>"
            style='position:relative; margin-left:<?=$w?>px; display: flex; justify-content: space-between;'
            >
            <div>| <?=$d->codigo?> - <?=$d->titulo?></div>
            <div class="acoes">
                <i class="fa-regular fa-trash-can <?=(($d->qt)?"excluir_blq":'excluir')?>" qt="<?=$d->qt?>" cod="<?=$d->codigo?>" vinculo="<?=$d->codigo?>"></i>
                <i class="fa fa-plus adicionar" cod="<?=$d->codigo?>" vinculo="<?=$d->codigo?>"></i>
                <i class="fa fa-pen editar" cod="<?=$d->codigo?>" vinculo="<?=$d->codigo?>"></i>
            </div>
        </div>
<?php
            MontaMenu($d->codigo, $b.'|');
?>
</div>
<?php
        }
?>
</div>
<?php

    }