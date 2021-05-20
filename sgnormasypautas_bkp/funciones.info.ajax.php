<?php
require_once("xajax_core/xajax.inc.php");
require_once("seguridad2.php");

$ajax=new xajax("servidor.info.ajax.php");


// por norma
$ajax->registerFunction("form_buscar_norma");
$ajax->registerFunction("recargar_titulo");
$ajax->registerFunction("resultado_normas");
$ajax->registerFunction("informe_norma");


// por pauta
$ajax->registerFunction("form_buscar_pauta");
$ajax->registerFunction("recargar_subtema");
$ajax->registerFunction("resultado_pautas");
$ajax->registerFunction("informe_pauta");

 
// por usuarios
$ajax->registerFunction("form_lista_usuario_activo");
$ajax->registerFunction("filtrar_usuarios");
$ajax->registerFunction("informe_usuario");

?>
