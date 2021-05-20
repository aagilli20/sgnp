<?php
  session_start();
  if(! $_SESSION["logueado"]){
    //no esta logueado
    header("location:login.php");
    die();
  }
  if($_SESSION["tipo"]!=3){
    echo "<script languaje='javascript'> alert('Usuario sin Privilegios de Administrador!');  top.location='index.php'</script> ";
  }
?>