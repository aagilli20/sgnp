<?php
require_once("seguridad2.php");
require_once("template.php");
require_once("funciones.info.ajax.php");
require_once("conexion.php");

set_file("info","informe.html");
set_var("javascript",$ajax->getJavascript("./"));
pparse("info");
?>