<?php
require_once("seguridad2.php");
require_once("template.php");
require_once("funciones.admin.ajax.php");
require_once("conexion.php");

set_file("admin","admin.html");
set_var("javascript",$ajax->getJavascript("./"));
pparse("admin");
?>