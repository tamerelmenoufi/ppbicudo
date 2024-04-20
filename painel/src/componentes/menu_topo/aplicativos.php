<?php
    include("{$_SERVER['DOCUMENT_ROOT']}/painel/lib/includes.php");
?>
<style>
    .appIcons{
        background-color:#fff;
        border:solid 1px #fff;
        cursor:pointer;
    }
    .appIcons:hover{
        background-color:#eee;
        border:solid 1px #ccc;
        cursor:pointer;
    }
    .appIcons span{
        font-size:10px;
    }
</style>

<ul class="navbar-nav">
    <li class="nav-item dropdown">
        <a
            class="nav-link d-flex align-items-center justify-content-center"
            href="#" id="navbarScrollingDropdown2"
            role="button"
            data-bs-toggle="dropdown"
            ria-expanded="false"
            style="
                    background-color:#eee;
                    border-radius:100%;
                    width:40px;
                    height:40px;
                    font-weight:bold;
                    "
        >
            <i class="fa-brands fa-buromobelexperte" style="font-size:25px;"></i>
        </a>
        <ul class="dropdown-menu  dropdown-menu-end" aria-labelledby="navbarScrollingDropdown2">
            <li class="MenuLogin">
                <div class="row">
                <?php
                if(
                    $_SESSION['ProjectPainel']->perfil == 'adm' or 
                    $_SESSION['ProjectPainel']->perfil == 'financeiro' or
                    $_SESSION['ProjectPainel']->perfil == 'consulta'
                ){
                ?>
                    <div class="col-4">
                        <div app="financeira" class="card w-100 d-flex align-items-center justify-content-center appIcons" >
                            <h3><i class="fa-solid fa-hand-holding-dollar"></i></h3>
                            <span>Financeira</span>
                        </div>
                    </div>
                <?php
                }
                ?>
                    <!-- <div class="col-4">
                        <div app="email" class="card w-100 d-flex align-items-center justify-content-center appIcons" >
                            <h3><i class="fa-solid fa-envelope-open-text"></i></h3>
                            <span>E-mail</span>
                        </div>
                    </div>

                    <div class="col-4">
                        <div app="agenda" class="card w-100 d-flex align-items-center justify-content-center appIcons" >
                            <h3><i class="fa-solid fa-calendar-days"></i></h3>
                            <span>Agenda</span>
                        </div>
                    </div> -->
                    <?php
                    if(
                        $_SESSION['ProjectPainel']->perfil == 'adm' or 
                        $_SESSION['ProjectPainel']->perfil == 'site' or 
                        $_SESSION['ProjectPainel']->perfil == 'consulta'
                    ){
                    ?>
                    <div class="col-4">
                        <div app="site" class="card w-100 d-flex align-items-center justify-content-center appIcons" >
                            <h3><i class="fa-solid fa-house"></i></h3>
                            <span>Site</span>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                    <!-- <div class="col-4">
                        <div app="chats" class="card w-100 d-flex align-items-center justify-content-center appIcons" >
                            <h3><i class="fa-regular fa-comments"></i></h3>
                            <span>Chat</span>
                        </div>
                    </div> -->

                </div>
            </li>
        </ul>
    </li>
</ul>

<script>
    $(function(){
        $("div[app]").click(function(){
            app = $(this).attr("app");
            window.location.href=`./?app=${app}`;
        });
    })
</script>