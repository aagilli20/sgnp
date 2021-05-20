<?php
require_once("template.php");
require_once("adodb5/adodb.inc.php");
// configuramos el fetch mode para que al realizar las consultas los indices del arreglo
// tengan los mismos nombres que las columnas de la base de datos
$ADODB_FETCH_MODE=ADODB_FETCH_ASSOC;
$conexion=ADONewConnection("mysqli");
if(! $conexion->Connect("127.0.0.1","Admin","15314","desaSGNP")){
  set_file("info","informacion.html");
  set_var("javascript","");
  set_var("mensaje","error al conectar con la base de datos, por favor intentelo nuevamente");
  set_var("via_link","index.php");
  set_var("via_ajax","");
  pparse("info");
}
$acentos = $conexion->query("SET NAMES 'utf8'");
if(! $acentos){
  set_file("info","informacion.html");
  set_var("javascript","");
  set_var("mensaje","error al conectar con la base de datos, por favor intentelo nuevamente");
  set_var("via_link","index.php");
  set_var("via_ajax","");
  pparse("info");
}
?>
