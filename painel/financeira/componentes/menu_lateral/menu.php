<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");
?>

<style>
.menu-cinza{
  padding:8px;
  font-size:15px;
  border-bottom:1px solid #d7d7d7;
  cursor:pointer;
}

.texto-cinza{
  color:#5e5e5e;
}

</style>
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
  <div class="offcanvas-header">
    <img src="img/logo.png" style="height:43px;" alt="">
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <h4 style="color:#393287">Capital - Financeira</h4>

    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="financeira/dashboard/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
      </div>
    </div>
    <?php
      if($_SESSION['ProjectPainel']->perfil == 'adm' or $_SESSION['ProjectPainel']->perfil == 'financeiro'){
    ?>
    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="financeira/clientes/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-chart-line"></i> Clientes
        </a>
      </div>
    </div>
    
    <?php
      if($_SESSION['ProjectPainel']->perfil == 'adm'){
    ?>
    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="financeira/status/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-chart-line"></i> Status
        </a>
      </div>
    </div>
    <?php
      }
    ?>

    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="financeira/vctex/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-chart-line"></i> VCTEX - Tabelas
        </a>
      </div>
    </div>

    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="financeira/vctex/consulta.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-chart-line"></i> VCTEX - Consultas
        </a>
      </div>
    </div>



    <!-- <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="financeira/facta/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-chart-line"></i> FACTA - Tabelas
        </a>
      </div>
    </div>

    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="financeira/facta/consulta.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-chart-line"></i> FACTA - Consultas
        </a>
      </div>
    </div> -->

    <?php
      }
    ?>


  </div>
</div>

<script>
  $(function(){
    $("a[url]").click(function(){
      Carregando();
      url = $(this).attr("url");
      $.ajax({
        url,
        success:function(dados){
          $("#paginaHome").html(dados);
        }
      });
    });
  })
</script>