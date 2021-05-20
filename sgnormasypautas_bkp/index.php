<?php
require_once("seguridad.php");
require_once("template.php");
require_once("funciones.ajax.php");
require_once("conexion.php");

set_file("index","index.html");
set_var("javascript",$ajax->getJavascript("./"));
$consulta = $conexion->Execute("SELECT * FROM Novedad");
$bandera = false;
foreach($consulta as $registro){
  // si es una norma la parseo
  if($registro['IdNorma']!=null){
    $bandera = true;
    $id = $registro['IdNorma'];
    $norma = $conexion->GetRow("SELECT TituloNorma FROM Norma WHERE IdNorma=$id");
    set_var("descrip",$norma['TituloNorma']);
    set_var("via_ajax","onclick='xajax_form_norma($id);'");
    parse("cargar_novedades");
  }
  if($registro['IdPauta']!=null){
    $bandera = true;
    $id = $registro['IdPauta'];
    $pauta = $conexion->GetRow("SELECT NombrePauta FROM Pauta WHERE IdPauta=$id");
    set_var("descrip",$pauta['NombrePauta']);
    set_var("via_ajax","onclick='xajax_form_pauta($id);'");
    parse("cargar_novedades");
  }
}
if(!$bandera){
  set_var("descrip","No hay Novedades");
  set_var("via_ajax","");
  parse("cargar_novedades");
}
pparse("index");
?>