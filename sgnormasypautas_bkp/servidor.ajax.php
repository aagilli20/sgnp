<?php
require_once("funciones.ajax.php");
require_once("template.php");
require_once("conexion.php");
require_once("cortar_palabra.php");
require_once("editar_fecha.php");
require_once("validacion.php");
require_once("seguridad.php");
require_once("registrar_log.php");

error_reporting(E_ALL & ~E_NOTICE);

////////////////////////////////////////////////////
// Mapa del Sitio
////////////////////////////////////////////////////

function form_mapa(){
  global $conexion;
  $html = "";
  set_file("mapa","form_mapa.html");
  $temas = $conexion->Execute("SELECT * FROM Tema");
  foreach($temas as $tema){
    $tema_aux = cortar_palabra($tema['Tema'],26);
    $id_tema = $tema['IdTema'];
    $normas = $conexion->Execute("SELECT IdNorma,FeNorma,TituloNorma FROM Norma WHERE IdTema='$id_tema'");
    $pautas = $conexion->Execute("SELECT IdPauta,FePauta,NombrePauta FROM Pauta WHERE IdTema='$id_tema'");
    $cant_normas = $conexion->GetRow("SELECT Count(*) FROM Norma WHERE IdTema='$id_tema'");
    $cant_pautas = $conexion->GetRow("SELECT Count(*) FROM Pauta WHERE IdTema='$id_tema'");
    if(($cant_normas['Count(*)']==0) && ($cant_pautas['Count(*)']==0)){
      set_var("tema",$tema_aux);
      set_var("tipo","");
      set_var("via_ajax","");
      set_var("dato","No existen Normas ni Pautas registradas para este Tema");
      set_var("fecha","");
      parse("cargar_mapa");
    }else{
      if($cant_normas['Count(*)']>0){
        foreach($normas as $norma){
          set_var("tema",$tema_aux);
          set_var("tipo","Norma");
          $id_norma = $norma['IdNorma'];
          set_var("via_ajax","onclick='xajax_form_norma($id_norma);'");
          set_var("dato",cortar_palabra($norma['TituloNorma'],26));
          set_var("fecha",$norma['FeNorma']);
          parse("cargar_mapa");
        }
      }
      if($cant_pautas['Count(*)']>0){
        foreach($pautas as $pauta){
          set_var("tema",$tema_aux);
          set_var("tipo","Pauta");
          $id_pauta = $pauta['IdPauta'];
          set_var("via_ajax","onclick='xajax_form_pauta($id_pauta);'");
          set_var("dato",cortar_palabra($pauta['NombrePauta'],26));
          set_var("fecha",$pauta['FePauta']);
          parse("cargar_mapa");
        }
      }
    }
  }
  $html=gparse("mapa");
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;

}


////////////////////////////////////////////////////
// Gestion de Normas
////////////////////////////////////////////////////

function form_buscar_norma(){
  // carga el formulario de busqueda de normas
  global $conexion;
  $html="";
  set_file("norma","form_buscar_norma.html");
  $consulta = $conexion->Execute("SELECT * FROM Tema");
  set_var("id_tema",-1);
  set_var("tema","Seleccione un Tema");
  parse("cargar_tema");
  foreach($consulta as $tema){
    set_var("id_tema",$tema['IdTema']);
    set_var("tema",$tema['Tema']);
    parse("cargar_tema");
  }
  unset($consulta);
  $consulta = $conexion->Execute("SELECT IdNorma,TituloNorma FROM Norma");
  set_var("id_norma",-1);
  set_var("titulo_norma","Seleccione un Título");
  parse("cargar_titulo");
  foreach($consulta as $norma){
    set_var("id_norma",$norma['IdNorma']);
    set_var("titulo_norma",$norma['TituloNorma']);
    parse("cargar_titulo");
  }
  unset($consulta);
  $consulta = $conexion->Execute("SELECT * FROM Nivel");
  set_var("id_nivel",-1);
  set_var("nivel","Seleccione un Nivel");
  parse("cargar_nivel");
  foreach($consulta as $nivel){
    set_var("id_nivel",$nivel['IdNivel']);
    set_var("nivel",$nivel['Nivel']);
    parse("cargar_nivel");
  }
  unset($consulta);
  set_var("via_link","index.php");
  $html=gparse("norma");
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function recargar_titulo($id_tema){
  // vuelve a cargar el select de titulos según el tema seleccionado
  global $conexion;
  $html="";
  set_file("combinado","select_combinado.html");
  set_var("nombre","select_titulo");
  set_var("id","idnorma");
  $consulta = $conexion->Execute("SELECT IdNorma,TituloNorma FROM Norma WHERE IdTema=$id_tema");
  set_var("valor",-1);
  set_var("opcion","Seleccione un Título");
  parse("cargar_combinado");
  foreach($consulta as $norma){
    set_var("valor",$norma['IdNorma']);
    set_var("opcion",$norma['TituloNorma']);
    parse("cargar_combinado");
  }
  unset($consulta);
  $html=gparse("combinado");
  $respuesta=new xajaxResponse();
  $respuesta->assign("celda_select_titulo","innerHTML",$html);
  return $respuesta;
}

function resultado_normas($id_tema,$id_norma,$id_nivel,$numero,$fe_desde,$fe_hasta,$keyword){
  // muestra el resultado de la busqueda de normas
  global $conexion;
  $html="";
  $sql="";
  $bandera = false;
  // Validación Pre Búsqueda
  if(!(is_numerico($numero) || $numero==null)){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('No se puede ingresar texto en el Número de la Norma')");
    return $respuesta;
  }
  if(!(is_clean_text($keyword) || $keyword==null)){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('La palabra clave debe contener sólo texto')");
    return $respuesta;
  }
  if(!(fecha_valida($fe_desde) || $fe_desde==null || $fe_desde=="dd/mm/aaaa")){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('La Fecha Desde ingresada no es válida')");
    return $respuesta;
  }
  if(!(fecha_valida($fe_hasta) || $fe_hasta==null || $fe_hasta=="dd/mm/aaaa")){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('La Fecha Hasta ingresada no es válida')");
    return $respuesta;
  }
  if(fecha_valida($fe_desde) && fecha_valida($fe_hasta)){
    if(!is_fecha1_menor($fe_desde,$fe_hasta)){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('La Fecha Desde debe ser menor que la fecha Hasta')");
      return $respuesta;
    }
  } 
  // fin validacion

  if(($id_tema>0) || ($id_norma>0) || ($id_nivel>0) || ($numero>0) || ($keyword!=null) ||  (fecha_valida($fe_desde)) || (fecha_valida($fe_hasta))){
    // metodo de busqueda
    if($id_norma>0){
      $sql = "SELECT IdNorma,NroNorma,TituloNorma,FeNorma,IdNivel FROM Norma WHERE IdNorma=$id_norma";
    }else{
      // compongo un where con lo que ingrese
      $sql = "SELECT IdNorma,NroNorma,TituloNorma,FeNorma,IdNivel FROM Norma WHERE";
      if($id_tema>0){
        $sql = $sql." IdTema=".$id_tema;
        $bandera = true;
        }
      if($id_nivel>0){
        if($bandera){$sql = $sql." "."AND";}
        $sql = $sql." IdNivel=".$id_nivel;
        $bandera = true;
      }
      if($numero>0){
        if($bandera){$sql = $sql." "."AND";}
        $sql = $sql." NroNorma=".$numero;
        $bandera = true;
      }
      if(fecha_valida($fe_desde)){
        if($bandera){$sql = $sql." "."AND";}
        $fe_desde = fecha_mysql($fe_desde);
        $sql = $sql." FeNorma>="."'$fe_desde'";
        $bandera = true;
      }
      if(fecha_valida($fe_hasta)){
        if($bandera){$sql = $sql." "."AND";}
        $fe_hasta = fecha_mysql($fe_hasta);
        $sql = $sql." FeNorma<="."'$fe_hasta'";
        $bandera = true;
      }
      if($keyword!=null){
          if($bandera){$sql = $sql." "."AND";}
          $aux = "%".$keyword."%";
          $sql = $sql." DescripcionNorma LIKE "."'$aux'";
      }
    } // end if idnomra>0
  }else{
    $sql = "SELECT IdNorma,NroNorma,TituloNorma,FeNorma,IdNivel FROM Norma";
  } // end if verifica si selecciono filtro
  //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
  // parseo del resultado
  //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
  $consulta = $conexion->Execute($sql);
  if($consulta->RecordCount()>0){
    if($consulta){
      set_file("resul","form_resultado_norma.html");
      foreach($consulta as $norma){
        $id_nivel = $norma['IdNivel'];
        $nivel = $conexion->GetRow("SELECT Nivel FROM Nivel WHERE IdNivel=$id_nivel");
        if($norma['NroNorma']==-1){
          set_var("numero","N/C");
        }else{
          set_var("numero",$norma['NroNorma']);
        }
        set_var("id",$norma['IdNorma']);
        $aux = cortar_palabra($norma['TituloNorma'],32);
        set_var("titulo",$aux);
        set_var("nivel",$nivel['Nivel']);
        $feaux = fecha_normal($norma['FeNorma']);
        set_var("fecha",$feaux);
        parse("cargar_normas");
      } // end for
      set_var("via_link",'#');
      set_var("via_ajax","onclick='xajax_form_buscar_norma();'");
      $html=gparse("resul");
      unset($consulta);
    }else{
      set_file("info","infoajax.html");
      set_var("mensaje","Error al procesar la consulta".$conexion->ErrorMsg());
      set_var("via_link","#");
      set_var("via_ajax","onclick='xajax_form_buscar_norma();'");
      $html=gparse("info");
    } // end if $consulta
  }else{
    set_file("info","infoajax.html");
    set_var("mensaje","No existen coincidencias con la Norma Buscada");
    set_var("via_link","#");
    set_var("via_ajax","onclick='xajax_form_buscar_norma();'");
    $html=gparse("info");
  } // end if sin resultados
  $respuesta = new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function form_norma($id_norma){
  global $conexion;
  set_file("norma","form_norma.html");
  $sql = "SELECT * FROM Norma WHERE IdNorma=$id_norma";
  $consulta = $conexion->GetRow($sql);
  set_var("titulo",$consulta['TituloNorma']);
  if($consulta['NroNorma']==-1){
    set_var("numero","No Corresponde");
  }else{
    set_var("numero",$consulta['NroNorma']);
  }
  $id_tema = $consulta['IdTema'];
  $tema = $conexion->GetRow("SELECT Tema FROM Tema WHERE IdTema=$id_tema");
  set_var("tema",$tema['Tema']);
  $id_nivel = $consulta['IdNivel'];
  $nivel = $conexion->GetRow("SELECT Nivel FROM Nivel WHERE IdNivel=$id_nivel");
  set_var("nivel",$nivel['Nivel']);
  $feaux = fecha_normal($consulta['FeNorma']);
  set_var("fecha",$feaux);
  set_var("descrip",$consulta['DescripcionNorma']);
  set_var("id_norma",$id_norma);
  set_var("contacto","xajax_resultado_contactos_norma($id_norma)");
  if(isset($consulta['DocNorma'])){  
    set_var("documento_norma","Ver el Documento asociado a esta Norma");
    set_var("url_doc",$consulta['DocNorma']);
    set_var("link_enviar_mail","Enviar Documento vía E-Mail");
  }
  else{
    set_var("documento_norma","Esta Norma no tiene Documentación Asociada");
    set_var("url_doc","./docs/normas/doc_nulo_norma.pdf");
    set_var("link_enviar_mail","");
  }
  $html=gparse("norma");
  registrar_log_usr(3,$id_norma,$consulta['TituloNorma']);
  unset($consulta);
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function norma_pauta($id_norma){
// busca en NormaPauta las pautas asociadas a una norma
  global $conexion;
  $html="";
  $coincidencia = $conexion->GetRow("SELECT Count(*) FROM NormaPauta WHERE IdNorma=$id_norma");
  if($coincidencia['Count(*)']>0){
    set_file("resul","form_resultado_pauta.html");
    $sql = "SELECT IdPauta FROM NormaPauta WHERE IdNorma=$id_norma";
    $consulta = $conexion->Execute($sql);
    foreach($consulta as $norma_pauta){
      $id_pauta = $norma_pauta['IdPauta'];
      $sql = "SELECT FePauta,NombrePauta,IdTema FROM Pauta WHERE IdPauta=$id_pauta";
      $pauta = $conexion->GetRow($sql);
      set_var("id",$id_pauta);
      $feaux = fecha_normal($pauta['FePauta']);
      set_var("fecha",$feaux);
      $aux = cortar_palabra($pauta['NombrePauta'],26);
      set_var("nombre",$aux);
      $id_aux = $pauta['IdTema'];
      $tema = $conexion->GetRow("SELECT Tema FROM Tema WHERE IdTema=$id_aux");
      unset($aux);
      $aux = cortar_palabra($tema['Tema'],26);
      set_var("tema",$aux);
      parse("cargar_pautas");
    } // end for
    set_var("via_link","#");
    set_var("via_ajax","onclick='xajax_form_norma($id_norma);'");
    $html=gparse("resul");
    unset($consulta);
  } else {
    set_file("info","infoajax.html");
    set_var("mensaje","Esta Norma no tiene Pautas asociadas".$conexion->ErrorMsg());
    set_var("via_link","#");
    set_var("via_ajax","onclick='xajax_form_norma($id_norma);'");
    $html=gparse("info");
  } // end if $consulta 
  $respuesta = new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}


////////////////////////////////////////////////////
// Gestion de Pautas
////////////////////////////////////////////////////


function form_buscar_pauta(){
  // carga el formulario de busqueda de pautas
  global $conexion;
  $html="";
  set_file("pauta","form_buscar_pauta.html");
  $consulta = $conexion->Execute("SELECT * FROM Tema");
  set_var("id_tema",-1);
  set_var("tema","Seleccione un Tema");
  parse("cargar_tema");
  foreach($consulta as $tema){
    set_var("id_tema",$tema['IdTema']);
    set_var("tema",$tema['Tema']);
    parse("cargar_tema");
  }
  unset($consulta);
  $consulta = $conexion->Execute("SELECT IdPauta,NombrePauta FROM Pauta");
  set_var("id_pauta",-1);
  set_var("nombre_pauta","Seleccione una Pauta");
  parse("cargar_subtema");
  foreach($consulta as $pauta){
    set_var("id_pauta",$pauta['IdPauta']);
    set_var("nombre_pauta",$pauta['NombrePauta']);
    parse("cargar_subtema");
  }
  unset($consulta);
  set_var("via_link","index.php");
  $html=gparse("pauta");
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function recargar_subtema($id_tema){
  // vuelve a cargar el select de subtemas según el tema seleccionado
  global $conexion;
  $html="";
  set_file("combinado","select_combinado.html");
  set_var("nombre","select_subtema");
  set_var("id","idpauta");
  $consulta = $conexion->Execute("SELECT IdPauta,NombrePauta FROM Pauta WHERE IdTema=$id_tema");
  set_var("valor",-1);
  set_var("opcion","Seleccione una Pauta");
  parse("cargar_combinado");
  foreach($consulta as $pauta){
    set_var("valor",$pauta['IdPauta']);
    set_var("opcion",$pauta['NombrePauta']);
    parse("cargar_combinado");
  }
  unset($consulta);
  $html=gparse("combinado");
  $respuesta=new xajaxResponse();
  $respuesta->assign("celda_select_subtema","innerHTML",$html);
  return $respuesta;
}

function resultado_pautas($id_tema,$id_pauta,$fe_desde,$fe_hasta,$keyword){
// muestra el resultado de la busqueda de pautas
global $conexion;
$html = "";
$sql = "";
$sql_item = "";
$bandera = false;
$bandera_item = false;

// Validación Pre Búsqueda
if(!(is_clean_text($keyword) || $keyword==null)){
  $respuesta=new xajaxResponse();
  $respuesta->script("alert('La palabra clave debe contener sólo texto')");
  return $respuesta;
}
if(!(fecha_valida($fe_desde) || $fe_desde==null || $fe_desde=="dd/mm/aaaa")){
  $respuesta=new xajaxResponse();
  $respuesta->script("alert('La Fecha Desde ingresada no es válida')");
  return $respuesta;
}
if(!(fecha_valida($fe_hasta) || $fe_hasta==null || $fe_hasta=="dd/mm/aaaa")){
  $respuesta=new xajaxResponse();
  $respuesta->script("alert('La Fecha Hasta ingresada no es válida')");
  return $respuesta;
}
if(fecha_valida($fe_desde) && fecha_valida($fe_hasta)){
  if(!is_fecha1_menor($fe_desde,$fe_hasta)){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('La Fecha Desde debe ser menor que la fecha Hasta')");
    return $respuesta;
  }
} 
// fin validacion

if(($id_tema>0) || ($id_pauta>0) || ($keyword!=null) || (fecha_valida($fe_desde)) || (fecha_valida($fe_hasta))){
  // selecciono al menos un filtro de busqueda
  // metodo de busqueda
  if($id_pauta>0){
    $sql = "SELECT IdPauta,FePauta,NombrePauta,IdTema FROM Pauta WHERE IdPauta=$id_pauta";
  }else{
    // compongo un where con lo que ingrese
    $sql = "SELECT IdPauta,FePauta,NombrePauta,IdTema FROM Pauta WHERE";
    if($id_tema>0){
      $sql = $sql." IdTema=".$id_tema;
      $bandera = true;
    }
    if(fecha_valida($fe_desde)){
      if($bandera){$sql = $sql." "."AND";}
      $fe_desde = fecha_mysql($fe_desde);
      $sql = $sql." FePauta>="."'$fe_desde'";
      $bandera = true;
    }
    if(fecha_valida($fe_hasta)){
      if($bandera){$sql = $sql." "."AND";}
      $fe_hasta = fecha_mysql($fe_hasta);
      $sql = $sql." FePauta<="."'$fe_hasta'";
      $bandera = true;
    }
    if($keyword!=null){
      if($bandera){$sql = $sql." "."AND";}
      $aux = "%".$keyword."%";
      $sql = $sql." NombrePauta LIKE "."'$aux'";
      $sql_item = "SELECT IdPauta FROM ItemPauta WHERE Descripcion LIKE '$aux' OR Observacion LIKE '$aux'";
      $bandera_item = true;
    }
  } // end if idpauta>0
}else{
  $sql = "SELECT IdPauta,FePauta,NombrePauta,IdTema FROM Pauta";
} // end if verifica si selecciono filtro
$consulta = $conexion->Execute($sql);
$cant = $consulta->RecordCount();
if($bandera_item==true){
$consul_item = $conexion->Execute($sql_item);
$cant_item = $consul_item->RecordCount();
}else{
  $cant_item=-10;
}
  //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
  // parseo del resultado
  //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
if(($cant>0)||($cant_item>0)){
  set_file("resul","form_resultado_pauta.html");
  if($cant>0){
    foreach($consulta as $pauta){
      set_var("id",$pauta['IdPauta']);
      $feaux = fecha_normal($pauta['FePauta']);
      set_var("fecha",$feaux);
      $aux = cortar_palabra($pauta['NombrePauta'],26);
      set_var("nombre",$aux);
      $id_aux = $pauta['IdTema'];
      $tema = $conexion->GetRow("SELECT Tema FROM Tema WHERE IdTema=$id_aux");
      unset($aux);
      $aux = cortar_palabra($tema['Tema'],26);
      set_var("tema",$aux);
      parse("cargar_pautas");
    } // end for consulta
  }
  if($cant_item>0){
    foreach($consul_item as $item_pauta){
      $id = $item_pauta['IdPauta'];
      $pauta = $conexion->GetRow("SELECT IdPauta,FePauta,NombrePauta,IdTema FROM Pauta WHERE IdPauta=$id");
      set_var("id",$pauta['IdPauta']);
      $feaux = fecha_normal($pauta['FePauta']);
      set_var("fecha",$feaux);
      $aux = cortar_palabra($pauta['NombrePauta'],26);
      set_var("nombre",$aux);
      $id_aux = $pauta['IdTema'];
      $tema = $conexion->GetRow("SELECT Tema FROM Tema WHERE IdTema=$id_aux");
      unset($aux);
      $aux = cortar_palabra($tema['Tema'],26);
      set_var("tema",$aux);
      parse("cargar_pautas");
    } // end for items
  }
  set_var("via_link","#");
  set_var("via_ajax","onclick='xajax_form_buscar_pauta();'");
  $html=gparse("resul");
  unset($consulta);
  unset($consul_item);
}else{
  set_file("info","infoajax.html");
  set_var("mensaje","No existen coincidencias con la Pauta Buscada");
  set_var("via_link","#");
  set_var("via_ajax","onclick='xajax_form_buscar_pauta();'");
  $html=gparse("info");
} // end if sin resultados
$respuesta = new xajaxResponse();
$respuesta->assign("divcontenido","innerHTML",$html);
return $respuesta;
}

function form_pauta($id_pauta){
  global $conexion;
  set_file("pauta","form_pauta.html");
  $sql = "SELECT * FROM Pauta WHERE IdPauta=$id_pauta";
  $consulta = $conexion->GetRow($sql);
  $nombre_pauta = $consulta['NombrePauta'];
  $aux = cortar_palabra($consulta['NombrePauta'],70);
  set_var("nombre",$aux);
  $id_tema = $consulta['IdTema'];
  $tema = $conexion->GetRow("SELECT Tema FROM Tema WHERE IdTema=$id_tema");
  set_var("tema",$tema['Tema']);
  $feaux = fecha_normal($consulta['FePauta']);
  set_var("fecha",$feaux);
  set_var("id_pauta",$id_pauta);
  if(isset($consulta['DocPauta'])){  
    set_var("documento_pauta","Ver el Documento asociado a esta Pauta");
    set_var("url_doc",$consulta['DocPauta']);
    set_var("link_enviar_mail","Enviar Documento vía E-Mail");
  }
  else{
    set_var("documento_pauta","Esta Pauta no tiene Documentación Asociada");
    set_var("url_doc","./docs/pautas/doc_nulo_pauta.pdf");
    set_var("link_enviar_mail","");
  }
  unset($consulta);
  $sql = "SELECT * FROM ItemPauta WHERE IdPauta=$id_pauta ORDER BY IdNivel";
  $consulta = $conexion->Execute($sql);
  $idaux = -1;
  $menor = -1;
  foreach($consulta as $itempauta){
    if($idaux!=$itempauta['IdNivel']){
      $idaux = $itempauta['IdNivel'];
      if($menor==-1) $menor = $idaux;
      set_var("id_nivel",$idaux);
      $nivel = $conexion->GetRow("SELECT Nivel FROM Nivel WHERE IdNivel=$idaux");
      set_var("nivel",$nivel['Nivel']);
      parse("cargar_nivel");
    }
  }
  if($menor==-1){
    set_var("descrip","Esta Pauta no tiene ítems, descargar Documento");
    set_var("obs","");
  }else{
    foreach($consulta as $itempauta){
      if($itempauta['IdNivel']==$menor){
        set_var("descrip",$itempauta['Descripcion']);
        set_var("obs",$itempauta['Observacion']);
      }
    }
  }
  set_var("contacto","xajax_resultado_contactos_pauta($id_pauta);");
  $html=gparse("pauta");
  registrar_log_usr(4,$id_pauta,$nombre_pauta);
  unset($consulta);
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function recargar_items($id_pauta,$id_nivel){
  // vuelve a cargar los items según el nivel seleccionado
  global $conexion;
  $html="";
  set_file("items_combi","form_items_combinado.html");
  $sql = "SELECT Descripcion,Observacion FROM ItemPauta WHERE IdPauta=$id_pauta AND IdNivel=$id_nivel";
  $consulta = $conexion->GetRow($sql);
  set_var("descrip",$consulta['Descripcion']);
  set_var("obs",$consulta['Observacion']);
  unset($consulta);
  $html=gparse("items_combi");
  $respuesta=new xajaxResponse();
  $respuesta->assign("datos_item","innerHTML",$html);
  return $respuesta;
}

function pauta_norma($id_pauta){
// busca en NormaPauta las normas asociadas a una pauta
  global $conexion;
  $html="";
  $coincidencia = $conexion->GetRow("SELECT Count(*) FROM NormaPauta WHERE IdPauta=$id_pauta");
  if($coincidencia['Count(*)']>0){
    set_file("resul","form_resultado_norma.html");
    $sql = "SELECT IdNorma FROM NormaPauta WHERE IdPauta=$id_pauta";
    $consulta = $conexion->Execute($sql);
    foreach($consulta as $norma_pauta){
      $id_norma = $norma_pauta['IdNorma'];
      $sql = "SELECT NroNorma,TituloNorma,FeNorma,IdNivel FROM Norma WHERE IdNorma=$id_norma";
      $norma = $conexion->GetRow($sql);
      set_var("id",$id_norma);
      $feaux = fecha_normal($norma['FeNorma']);
      set_var("fecha",$feaux);
      $aux = cortar_palabra($norma['TituloNorma'],26);
      set_var("titulo",$aux);
      set_var("numero",$norma['NroNorma']);
      $id_nivel = $norma['IdNivel'];
      $nivel = $conexion->GetRow("SELECT Nivel FROM Nivel WHERE IdNivel=$id_nivel");
      set_var("nivel",$nivel['Nivel']);
      parse("cargar_normas");
    } // end for
    set_var("via_link","#");
    set_var("via_ajax","onclick='xajax_form_pauta($id_pauta);'");
    $html=gparse("resul");
    unset($consulta);
  } else {
    set_file("info","infoajax.html");
    set_var("mensaje","Esta Pauta no tiene Normas asociadas".$conexion->ErrorMsg());
    set_var("via_link","#");
    set_var("via_ajax","onclick='xajax_form_pauta($id_pauta);'");
    $html=gparse("info");
  } // end if $consulta 
  $respuesta = new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}


////////////////////////////////////////////////////
// Gestión de Contactos
////////////////////////////////////////////////////


function resultado_contactos_norma($id_norma){
global $conexion;
  $html="";
  $cantidad = $conexion->GetRow("SELECT Count(*) FROM ContactoNorma WHERE IdNorma=$id_norma");
  if($cantidad['Count(*)']){
    set_file("resul","form_resultado_contacto.html");
    $sql = "SELECT IdContacto FROM ContactoNorma WHERE IdNorma=$id_norma";
    $consulta = $conexion->Execute($sql);
    foreach($consulta as $contactonorma){
      $id_contacto = $contactonorma['IdContacto'];
      $contacto = $conexion->GetRow("SELECT * FROM Contacto WHERE IdContacto=$id_contacto");
      set_var("contacto",$contacto['NombreContacto']);
      set_var("telefono",$contacto['TelefonoContacto']);
      set_var("centrex",$contacto['Centrex']);
      set_var("mail",$contacto['EMailContacto']);
      parse(cargar_contactos);
    }
    set_var("via_ajax","xajax_form_norma($id_norma);");
    $html=gparse("resul");
    unset($consulta);
  }else{
    // no hay contactos asociados
    set_file("info","infoajax.html");
    set_var("mensaje","Esta Norma no tiene Contactos asociados");
    set_var("via_link","#");
    set_var("via_ajax","onclick='xajax_form_norma($id_norma);'");
    $html=gparse("info");
  }
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function resultado_contactos_pauta($id_pauta){
  global $conexion;
  $html="";
  $cantidad = $conexion->GetRow("SELECT Count(*) FROM ContactoPauta WHERE IdPauta=$id_pauta");
  if($cantidad['Count(*)']){
    set_file("resul","form_resultado_contacto.html");
    $sql = "SELECT IdContacto FROM ContactoPauta WHERE IdPauta=$id_pauta";
    $consulta = $conexion->Execute($sql);
    foreach($consulta as $contactopauta){
      $id_contacto = $contactopauta['IdContacto'];
      $contacto = $conexion->GetRow("SELECT * FROM Contacto WHERE IdContacto=$id_contacto");
      set_var("contacto",$contacto['NombreContacto']);
      set_var("telefono",$contacto['TelefonoContacto']);
      set_var("centrex",$contacto['Centrex']);
      set_var("mail",$contacto['EMailContacto']);
      parse(cargar_contactos);
    }
    set_var("via_ajax","xajax_form_pauta($id_pauta);");
    $html=gparse("resul");
    unset($consulta);
  }else{
    // no hay contactos asociados
    set_file("info","infoajax.html");
    set_var("mensaje","Esta Pauta no tiene Contactos asociados");
    set_var("via_link","#");
    set_var("via_ajax","onclick='xajax_form_pauta($id_pauta);'");
    $html=gparse("info");
  }
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}


////////////////////////////////////////////////////
// Gestion de Datos de Usuario
////////////////////////////////////////////////////


function form_mis_datos(){
  global $conexion;
  global $_SESSION;
  $user = $_SESSION["usuario"];
  $html="";
  $sql="SELECT Nombre,Apellido,Domicilio,Telefono,Celular,EMail FROM Usuario WHERE Nick='$user'";
  $datos_usuario=$conexion->GetRow($sql);
  if($datos_usuario){
    set_file("mis_datos","form_mis_datos.html");
    set_var("nombre",$datos_usuario['Nombre']);
    set_var("apellido",$datos_usuario['Apellido']);
    set_var("domicilio",$datos_usuario['Domicilio']);
    set_var("telefono",$datos_usuario['Telefono']);
    set_var("celular",$datos_usuario['Celular']);
    set_var("email",$datos_usuario['EMail']);
    $html=gparse("mis_datos");
    }else{
    set_file("info","infoajax.html");
    $msj = "Se produjo un error al cargar sus datos, por favor intentelo nuevamente";
    set_var("mensaje",$msj);
    set_var("via_link","index.php");
    set_var("via_ajax","");
    $html=gparse("info");
    }
    $respuesta=new xajaxResponse();
    $respuesta->assign("divcontenido","innerHTML",$html);
    return $respuesta;
}

function modificar_mis_datos($nombre,$apellido,$domicilio,$email,$telefono,$celular){
  require_once("validacion.php");
  global $conexion;
  global $_SESSION;
  $html="";
  if(!(empty($nombre) || empty($apellido) || empty($email))){
    if(is_email($email)){
      if(is_clean_text($nombre,1,50) && is_clean_text($apellido,1,30)){
        if(is_alphanumeric($domicilio,1,60) || $domicilio==null){
          if((is_alphanumeric($telefono,1,20) || $telefono==null)&&(is_alphanumeric($celular,1,60) || $celular==null)){
            $user = $_SESSION["usuario"];
            $sql="UPDATE Usuario SET Nombre='$nombre',
                              Apellido='$apellido',
                              Domicilio='$domicilio',
                              Telefono='$telefono',
                              Celular='$celular',
                              EMail='$email' WHERE Nick='$user'";
            set_file("info","infoajax.html");
            if($conexion->Execute($sql)){
              set_var("mensaje","Sus datos fueron actualizados correctamente");
            }else{
              set_var("mensaje","Se produjo un error inesperado".$conexion->ErrorMsg());
            }
            set_var("via_ajax","");
            set_var("via_link","index.php");
            $html=gparse("info");
            $respuesta=new xajaxResponse();
            $respuesta->assign("divcontenido","innerHTML",$html);
          }else{
            $respuesta=new xajaxResponse();
            $respuesta->script("alert('El celular y el teléfono sólo pueden contener carcteres alfanuméricos')");
          }
        }else{
          $respuesta=new xajaxResponse();
          $respuesta->script("alert('El domicilio sólo puede contener caracteres alfanuméricos')");
        }
      }else{
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('El nombre y apellido deben contener sólo texto')");
      }
    }else{
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('Debe ingresar un E-Mail valido')");
    }
  }else{
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('El Nombre, Apellido y el E-Mail son datos Obligatorios')");
  }

  return $respuesta;
}


////////////////////////////////////////////////////
// Cambio de Password
////////////////////////////////////////////////////


function form_cambiar_password(){
  $html="";
  set_file("cambiar","form_cambiar_password.html");	
  $html=gparse("cambiar");
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function cambiar_password($pass1,$pass2){
  require_once("validacion.php");
  global $conexion;
  global $_SESSION;
  $user=$_SESSION["usuario"];
  $html="";
  set_file("info","infoajax.html");
  if((!strcmp($pass1,$pass2)) && (!empty($pass1))){
      if(is_alphanumeric($pass1,5,20)){
      $pass=sha1($pass1);
      $sql="UPDATE Usuario SET Password='$pass' WHERE Nick='$user'";
      if($conexion->Execute($sql)){
        set_var("mensaje","Su contraseña se fue cambiada con exito");
        set_var("via_ajax","");
        set_var("via_link","index.php");
      }else{
        set_var("mensaje","Se produjo un error inesperado".$conexion->ErrorMsg());
        set_var("via_ajax","");
        set_var("via_link","index.php");
      }
    }else{
      set_var("mensaje","La contraseña debe ser alfanumerica y contener entre 5 y 20 dígitos");
      set_var("via_ajax","onclick='xajax_form_cambiar_password();'");
      set_var("via_link","#");
    }
  }else{
    set_var("mensaje","Debe ingresar dos veces la misma contraseña");
    set_var("via_ajax","onclick='xajax_form_cambiar_password();'");
    set_var("via_link","#");
  }
  $html=gparse("info");
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

////////////////////////////////////////////////////
// Enviar documento vía e-mail
////////////////////////////////////////////////////

function form_enviar_email($id_dato,$id_tabla,$comentario){
  $html="";
  set_file("mail","form_enviar_email.html");	
  set_var("iddato",$id_dato);
  set_var("idtabla",$id_tabla);
  set_var("comentario",$comentario);
  if($id_tabla==3){
    set_var("via_ajax","onclick=xajax_form_norma('$id_dato')");
    set_var("asunto","Norma: Título de la Norma");
  }else{
    set_var("via_ajax","onclick=xajax_form_pauta('$id_dato')");
    set_var("asunto","Pauta: Nombre de la Pauta");
  }
  $html=gparse("mail");
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function enviar_email($correo,$mensaje,$id_dato,$id_tabla){
  // validación previa
  if(!(is_email($correo))){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Debe ingresar una Dirección de Correo Válida');");
    return $respuesta;
  }
  if($mensaje==null){
    $mensaje = "Atte. Mesa de Orientación y Servicios.";
  }
  // consultamos el nombre de la pauta o norma y la url del doc
  global $conexion;
  $consulta = "";
  $nombre_doc = "";
  $urldoc = null;
  $asunto = "";
  if($id_tabla==3){
    $consulta = $conexion->GetRow("SELECT TituloNorma,DocNorma FROM Norma WHERE IdNorma=$id_dato");
    $nombre_doc = "norma".$id_dato.".pdf";
    $urldoc = $consulta['DocNorma'];
    $asunto = "Norma: ".$consulta['TituloNorma'];
  }else{
    $consulta = $conexion->GetRow("SELECT NombrePauta,DocPauta FROM Pauta WHERE IdPauta=$id_dato");
    $nombre_doc = "pauta".$id_dato.".pdf";
    $urldoc = $consulta['DocPauta'];
    $asunto = "Pauta: ".$consulta['NombrePauta'];
  }

  // enviamos el e-mail
  require_once("./PHPMailer/class.phpmailer.php");
  $mail = new phpmailer();
  $mail->PluginDir = "./PHPMailer/";
  $mail->Mailer = "imap";
  //Asignamos a Host el nombre de nuestro servidor pop3
  $mail->Host = "mx.000webhost.com";
  //Le indicamos que el servidor requiere autenticación
  $mail->SMTPAuth = true;
  //Le decimos cual es nuestro nombre de usuario y password
  $mail->Username = "noreply@desasgnp.hostoi.com"; 
  $mail->Password = "pass1234";
  //Indicamos cual es nuestra dirección de correo y el nombre que 
  //queremos que vea el usuario que lee nuestro correo
  $mail->From = "moys@santafe.edu.ar";
  $mail->FromName = "MOYS";
  //una cuenta gratuita, por tanto lo pongo a 30  
  $mail->Timeout=30;
  //Indicamos cual es la dirección de destino del correo
  $mail->AddAddress($correo);
  //Indicamos el asunto
  $mail->Subject = utf8_decode($asunto);
  //Indicamos el cuerpo del mensaje
  $mail->Body = utf8_decode($mensaje);
  if($urldoc!=null){
    $mail->AddAttachment($urldoc,$nombre_doc);
  }
  //se envia el mensaje, si no ha habido problemas 
  //la variable $exito tendra el valor true
  $exito = $mail->Send();
  //Si el mensaje no ha podido ser enviado se realizaran 4 intentos mas como mucho 
  //para intentar enviar el mensaje, cada intento se hara 5 segundos despues 
  //del anterior, para ello se usa la funcion sleep	
  $intentos=1; 
  while ((!$exito) && ($intentos < 5)) {
	sleep(5);
     	//echo $mail->ErrorInfo;
     	$exito = $mail->Send();
     	$intentos=$intentos+1;	
   }	
   if(!$exito){
	$comentario = "Problemas enviando correo electrónico";
   }else{
	$comentario = "Mensaje enviado correctamente";
   }

  $respuesta=new xajaxResponse();
  $respuesta->script("xajax_form_enviar_email('$id_dato','$id_tabla','$comentario');");
  return $respuesta;
}


////////////////////////////////////////////////////
// Procesar la solicitud
////////////////////////////////////////////////////

$ajax->processRequest();
?>
