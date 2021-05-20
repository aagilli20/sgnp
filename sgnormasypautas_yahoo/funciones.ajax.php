<?php
require_once("xajax_core/xajax.inc.php");
require_once("seguridad.php");

$ajax=new xajax("servidor.ajax.php");

// Mapa del Sitio
$ajax->registerFunction("form_mapa");

// Normas
$ajax->registerFunction("form_buscar_norma");
$ajax->registerFunction("recargar_titulo");
$ajax->registerFunction("resultado_normas");
$ajax->registerFunction("form_norma");
$ajax->registerFunction("norma_pauta");

// pautas
$ajax->registerFunction("form_buscar_pauta");
$ajax->registerFunction("recargar_subtema");
$ajax->registerFunction("resultado_pautas");
$ajax->registerFunction("form_pauta");
$ajax->registerFunction("recargar_items");
$ajax->registerFunction("pauta_norma");

// contactos
$ajax->registerFunction("resultado_contactos_norma");
$ajax->registerFunction("resultado_contactos_pauta");

// datos de usuario
$ajax->registerFunction("form_mis_datos");
$ajax->registerFunction("modificar_mis_datos");

// password
$ajax->registerFunction("form_cambiar_password");
$ajax->registerFunction("cambiar_password");

// enviar documento vía e-mail
$ajax->registerFunction("form_enviar_email");
$ajax->registerFunction("enviar_email");

?>
