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
    <img src="img/logo.png" style="height:60px;" alt="">
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <h4 style="color:#393287">capital - Painel de Controle</h4>

    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="site/dashboard/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
      </div>
    </div>
    <?php
    if($_SESSION['ProjectPainel']->perfil == 'adm'){
    ?>
    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="site/usuarios/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
         <i class="fa-solid fa-users"></i> Usuários do Sistema
        </a>
      </div>
    </div>
    <?php
    }
    if($_SESSION['ProjectPainel']->perfil == 'adm' or $_SESSION['ProjectPainel']->perfil == 'site'){

    ?>
    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="site/menu/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-bars-staggered"></i> Menus
        </a>
      </div>
    </div>

    <div class="row mb-1 menu-cinza">
      <div class="col">
        <a url="site/banners/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
           <i class="fa-solid fa-panorama"></i> Banners
        </a>
      </div>
    </div>

    <div class="row  mb-1 menu-cinza">
      <div class="col">
        <a url="site/paginas_topicos/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-pager"></i> Páginas com Tópicos
        </a>
      </div>
    </div>


    <div class="row  mb-1 menu-cinza">
      <div class="col">
        <a url="site/destaques/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-regular fa-newspaper"></i> Destaques
        </a>
      </div>
    </div>

    <div class="row  mb-1 menu-cinza">
      <div class="col">
        <a url="site/noticias/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-regular fa-newspaper"></i> Notícias
        </a>
      </div>
    </div>

    <div class="row  mb-1 menu-cinza">
      <div class="col">
        <a url="site/servicos/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
         <i class="fa-solid fa-box-open"></i> Produtos
        </a>
      </div>
    </div>

    <!-- <div class="row  mb-1 menu-cinza">
      <div class="col">
        <a url="site/portifolio/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
         <i class="fa-solid fa-box-open"></i> Portifólio
        </a>
      </div>
    </div> -->


    <div class="row  mb-1 menu-cinza">
      <div class="col">
        <a url="site/depoimentos/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
           <i class="fa-solid fa-message"></i> Depoimentos
        </a>
      </div>
    </div>


    <div class="row  mb-1 menu-cinza">
      <div class="col">
        <a url="site/time/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-people-carry-box"></i> Time da Empresa
        </a>
      </div>
    </div>


    <div class="row  mb-1 menu-cinza">
      <div class="col">
        <a url="site/configuracoes/index.php" class="text-decoration-none texto-cinza" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fa-solid fa-gears"></i> Configurações
        </a>
      </div>
    </div>
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