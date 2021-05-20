<?php
require_once("seguridad2.php");
require_once("template.php");
require_once("editar_fecha.php");
require_once("cortar_palabra.php");
require_once("conexion.php");
require_once("dompdf/dompdf_config.inc.php");

$id_pauta = $_REQUEST['pos'];
set_file("pdf","informe_por_tabla_pdf.html");
$sql = "SELECT FeLog,Usuario FROM LogUsr WHERE IdTabla=4 AND IdRegistro=$id_pauta";
$consulta = $conexion->Execute($sql);
$titulo = $conexion->GetRow("SELECT NombrePauta FROM Pauta WHERE IdPauta='$id_pauta'");
set_var("tabla","Pauta");  
set_var("identificador",cortar_palabra($titulo['NombrePauta'],70));
foreach($consulta as $log){
  $nick = $log['Usuario'];
  $usuario = $conexion->GetRow("SELECT Nombre,Apellido FROM Usuario WHERE Nick='$nick'");
  set_var("nick",$nick);
  set_var("apellido",cortar_palabra($usuario['Apellido'],26));
  set_var("nombre",cortar_palabra($usuario['Nombre'],20));
  set_var("fecha",fecha_normal($log['FeLog']));
  parse("cargar_usuarios");
}
unset($consulta);

$html=gparse("pdf");
$dompdf = new DOMPDF();
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream("informe_visitas_pauta.pdf");

?>