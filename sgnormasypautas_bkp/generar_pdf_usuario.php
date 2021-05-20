<?php
require_once("seguridad2.php");
require_once("template.php");
require_once("conexion.php");
require_once("cortar_palabra.php");
require_once("dompdf/dompdf_config.inc.php");
require_once("editar_fecha.php");

$nick = $_REQUEST['pos'];
$fe_desde = $_REQUEST['des'];
$fe_hasta = $_REQUEST['has'];
$html = "";
$sql = "";
$periodo = "";
$fe_desde2 = fecha_mysql($fe_desde);
$fe_hasta2 = fecha_mysql($fe_hasta);
set_file("pdf","informe_por_usuario_pdf.html");

// buscamos en el log
if(($fe_desde!=null && $fe_desde!="dd/mm/aaaa") && ($fe_hasta!=null && $fe_hasta!="dd/mm/aaaa")){
  $periodo = "Período: desde el día ".$fe_desde." hasta el día ".$fe_hasta;
  $sql = "SELECT FeLog,IdTabla,IdRegistro,DatoObservado FROM LogUsr WHERE Usuario='$nick' AND FeLog>'$fe_desde2' AND FeLog<'$fe_hasta2' ORDER BY IdTabla";
}else{
  if($fe_desde!=null && $fe_desde!="dd/mm/aaaa"){
    $periodo = "Período: desde el día ".$fe_desde; 
    $sql = "SELECT FeLog,IdTabla,IdRegistro,DatoObservado FROM LogUsr WHERE Usuario='$nick' AND FeLog>'$fe_desde2' ORDER BY IdTabla";
  }else{
    if($fe_hasta!=null && $fe_hasta!="dd/mm/aaaa"){
      $periodo = "Período: hasta el día ".$fe_hasta; 
      $sql = "SELECT FeLog,IdTabla,IdRegistro,DatoObservado FROM LogUsr WHERE Usuario='$nick' AND FeLog<'$fe_hasta2' ORDER BY IdTabla";
    }else{
      $sql = "SELECT FeLog,IdTabla,IdRegistro,DatoObservado FROM LogUsr WHERE Usuario='$nick' ORDER BY IdTabla";
    }
  }
}
// mostramos los resultados
$consulta = $conexion->Execute($sql);
set_var("nick",$nick);
set_var("periodo",$periodo);
foreach($consulta as $registro){
  if($registro['IdTabla']==3){
    set_var("tabla","Norma");
    $id_norma = $registro['IdRegistro'];
    $aux = $conexion->GetRow("SELECT IdTema FROM Norma WHERE IdNorma=$id_norma");
    $id_tema = $aux['IdTema'];
    $tema = $conexion->GetRow("SELECT Tema FROM Tema WHERE IdTema=$id_tema");
    set_var("tema",cortar_palabra($tema['Tema'],30));
  }
  if($registro['IdTabla']==4){
    set_var("tabla","Pauta");
    $id_pauta = $registro['IdRegistro'];
    $aux = $conexion->GetRow("SELECT IdTema FROM Pauta WHERE IdPauta=$id_pauta");
    $id_tema = $aux['IdTema'];
    $tema = $conexion->GetRow("SELECT Tema FROM Tema WHERE IdTema=$id_tema");
    set_var("tema",cortar_palabra($tema['Tema'],30));
  }
  set_var("dato",cortar_palabra($registro['DatoObservado'],26));
  set_var("fecha",fecha_normal($registro['FeLog']));
  parse("cargar_registros");
}

$html=gparse("pdf");
$dompdf = new DOMPDF();
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream("informe_visitas_usuario.pdf");

?>