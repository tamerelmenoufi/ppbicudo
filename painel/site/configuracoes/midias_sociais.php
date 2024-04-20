<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");

    $query = "select * from configuracoes where codigo = '1'";
    $result = mysqli_query($con, $query);
    $d = mysqli_fetch_object($result);

    $midias = json_decode($d->midias_sociais);
?>
<label class="form-label">Complete os endereços das mídias sociais correspondentes:</label>
<?php
    $midias_sociais = [
    'facebook' => 'https://www.facebook.com/',
    'twitter' => 'https://twitter.com/',
    'instagram' => 'https://www.instagram.com/',
    'youtube' => 'https://www.youtube.com/',
    'linkedin' => 'https://www.linkedin.com/',
    'whatsapp' => 'https://api.whatsapp.com/'
    ];
foreach($midias_sociais as $ind => $url){
?>
<div class="form-floating mb-3">
  <div class="input-group mb-3">
    <!-- <div class="input-group-text">
      <input class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input">
    </div> -->
    <div class="input-group-text">
      <?=$url?>
    </div>
    <div class="form-control">
        <?=$midias->$ind?>
    </div>
    <!-- <input name=midias[<?=$ind?>] id="midias<?=$ind?>" value="<?=$midias->$ind?>" type="text" class="form-control" aria-label="Text input with checkbox"> -->
  </div>
</div>
<?php
}
?>