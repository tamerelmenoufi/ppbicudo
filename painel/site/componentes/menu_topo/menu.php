<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");
?>
<style>
  .MenuLogin{
    min-width:250px;
    margin:0 10px 0 10px;
  }
</style>

<nav class="navbar navbar-expand bg-light p-0">
  <div class="container-fluid">
    <div data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
      <div class="d-flex justify-content-start" style="height:50px">
        <i class="fa-solid fa-bars" style="font-size: 16px;margin-right:10px;margin-top: 15px;"></i>
        <div class="d-none d-sm-block">
          <img src="img/logo.png" style="height:50px; margin-right:20px;" >
        </div>
      </div>
    </div>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarScroll">

        <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
          <li class="nav-item">

          </li>
        </ul>

        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarScrollingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <?=$_SESSION['ProjectPainel']->nome?> <i class="fa-solid fa-user"></i>
                </a>
                <ul class="dropdown-menu  dropdown-menu-end" aria-labelledby="navbarScrollingDropdown">
                    <li class="MenuLogin">
                      <ul class="list-group  list-group-flush">
                        <!-- <li class="list-group-item" aria-disabled="true">
                          <i class="fa-solid fa-user"></i> Dados Pessoais
                        </li>
                        <a class="list-group-item" href='#'>
                          <i class="fa-solid fa-key"></i> Senha de Acesso
                        </a>
                        <li class="list-group-item">
                          <i class="fa-solid fa-calendar-check"></i> Atividades
                        </li> -->
                        <a class="list-group-item" href='?s=1'>
                          <i class="fa-solid fa-right-from-bracket"></i> Sair
                        </a>
                      </ul>
                    </li>
                </ul>
            </li>
        </ul>

        <div class="menuAplicativos"></div>
    </div>
  </div>
</nav>


<script>
  $(function(){

    $.ajax({
      url:"src/componentes/menu_topo/aplicativos.php",
      success:function(dados){
        $(".menuAplicativos").html(dados);
      }
    })

  })
</script>