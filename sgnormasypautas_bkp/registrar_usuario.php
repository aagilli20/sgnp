<?php
require_once("template.php");
require_once("conexion.php");

set_file("registrar","registrar_usuario.html");
set_var("nick","");
set_var("nombre","");
set_var("apellido","");
set_var("nrodoc","");
set_var("fe_nac","dd/mm/aaaa");
set_var("domicilio","");
set_var("mail","");
set_var("tel","");
set_var("cel","");
set_var("error","");
// llena el select_tipo_doc
$consulta = $conexion->Execute("SELECT * FROM TipoDocumento");
foreach($consulta as $tipo_doc){
  set_var("id_tipo_doc",$tipo_doc['IdTipoDoc']);
  set_var("tipo_doc",$tipo_doc['ValorTipoDoc']);
  set_var("selected1","");
  if($tipo_doc['IdTipoDoc']=='3'){set_var("selected1","selected=selected");}
  parse("cargar_tipo_doc");
}
unset($consulta);
// llena el select_sexo
$consulta = $conexion->Execute("SELECT * FROM Sexo");
foreach($consulta as $sexo){
  set_var("id_sexo",$sexo['IdSexo']);
  set_var("sexo",$sexo['Sexo']);
  set_var("selected2","");
  if($sexo['IdSexo']=='1'){set_var("selected2","selected=selected");}
  parse("cargar_sexo");
}
unset($consulta);
pparse("registrar");
?>