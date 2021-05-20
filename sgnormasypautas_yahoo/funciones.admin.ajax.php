<?php
require_once("xajax_core/xajax.inc.php");
require_once("seguridad2.php");

$ajax=new xajax("servidor.admin.ajax.php");

// normas
$ajax->registerFunction("form_agregar_norma");
$ajax->registerFunction("form_buscar_norma");
$ajax->registerFunction("recargar_titulo");
$ajax->registerFunction("resultado_normas");
$ajax->registerFunction("form_norma");
$ajax->registerFunction("norma_pauta");
$ajax->registerFunction("desasociar_norma_pauta");
$ajax->registerFunction("asociar_norma_pauta");
$ajax->registerFunction("contacto_norma"); // ver si se pone en contacto
$ajax->registerFunction("desasociar_contacto_norma");
$ajax->registerFunction("asociar_contacto_norma");
$ajax->registerFunction("eliminar_norma");
$ajax->registerFunction("novedad_norma");
$ajax->registerFunction("asociar_novedad_norma");
$ajax->registerFunction("reiniciar_novedad_norma");

// pautas
$ajax->registerFunction("form_agregar_pauta");
$ajax->registerFunction("form_buscar_pauta");
$ajax->registerFunction("recargar_subtema");
$ajax->registerFunction("resultado_pautas");
$ajax->registerFunction("form_pauta");
$ajax->registerFunction("novedad_pauta");
$ajax->registerFunction("asociar_novedad_pauta");
$ajax->registerFunction("reiniciar_novedad_pauta");
$ajax->registerFunction("eliminar_pauta");
$ajax->registerFunction("pauta_norma");
$ajax->registerFunction("asociar_pauta_norma");
$ajax->registerFunction("desasociar_pauta_norma");
$ajax->registerFunction("form_items_pauta");
$ajax->registerFunction("recargar_items");
$ajax->registerFunction("modificar_item_pauta");
$ajax->registerFunction("eliminar_item_pauta");
$ajax->registerFunction("agregar_item_pauta");
$ajax->registerFunction("contacto_pauta"); // ver si no corresponde en contacto
$ajax->registerFunction("desasociar_contacto_pauta");
$ajax->registerFunction("asociar_contacto_pauta");

// temas
$ajax->registerFunction("form_temas");
$ajax->registerFunction("agregar_tema");
$ajax->registerFunction("eliminar_tema");
$ajax->registerFunction("filtrar_temas");

// contactos
$ajax->registerFunction("form_agregar_contacto");
$ajax->registerFunction("guardar_contacto");
$ajax->registerFunction("form_buscar_contacto");
$ajax->registerFunction("filtrar_contactos");
$ajax->registerFunction("eliminar_contacto");
$ajax->registerFunction("form_modificar_contacto");
$ajax->registerFunction("modificar_contacto");

// usuarios
$ajax->registerFunction("form_lista_usuario_activo");
$ajax->registerFunction("form_lista_usuario_inactivo");
$ajax->registerFunction("desactivar_usuario");
$ajax->registerFunction("eliminar_usuario");
$ajax->registerFunction("eliminar_usuario_inactivo");
$ajax->registerFunction("form_modificar_usuario");
$ajax->registerFunction("modificar_usuario");
$ajax->registerFunction("filtrar_usuarios");

?>
