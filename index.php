<?php

    include("connect.php");
    $con = AppConnect("app");

    $query = "select * from contatos";
    $result = mysqli_query($con, $query);
    while($d = mysqli_fetch_object($result)){
        echo $d->nome."<br>";
    }   
    echo "Novo Ambiente";