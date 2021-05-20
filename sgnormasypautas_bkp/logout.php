<?php
require_once("seguridad.php");
session_start();
$_SESSION["logueado"]=false;
$_SESSION["usuario"]=null;
header("location:login.php");
die();
?>
