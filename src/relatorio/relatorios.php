<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");
?>
<ul class="list-group">
<?php
    $query = "select * from relatorio_modelos order by data desc";
    $result = mysqli_query($con, $query);
    while($d = mysqli_fetch_object($result)){
?>
  <li class="list-group-item">
    <div class="d-flex justify-content-between">
        <span><i class="fa-regular fa-pen-to-square"></i> <?=$d->nome?></span>
        <i class="fa-regular fa-trash-can"></i>
    </div>
  </li>
<?php
    }
?>
</ul>