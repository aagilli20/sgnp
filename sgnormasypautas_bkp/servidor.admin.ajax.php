<?php
require_once("funciones.admin.ajax.php");
require_once("template.php");
require_once("conexion.php");
require_once("cortar_palabra.php");
require_once("editar_fecha.php");
require_once("validacion.php");
require_once("registrar_log.php");
require_once("seguridad2.php");

error_reporting(E_ALL & ~E_NOTICE);

////////////////////////////////////////////////////
// Gestion de Normas
////////////////////////////////////////////////////

function form_agregar_norma(){
  global $conexion;
  $html = "";
  set_file("norma","form_agregar_norma.html");
  set_var("error","");
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
  $consulta = $conexion->Execute("SELECT * FROM Nivel");
  set_var("id_nivel",-1);
  set_var("nivel","Seleccione un Nivel");
  parse("cargar_nivel");
  foreach($consulta as $nivel){
    set_var("id_nivel",$nivel['IdNivel']);
    set_var("nivel",$nivel['Nivel']);
    set_var("selected1","");
    parse("cargar_nivel");
  }
  unset($consulta);
  $html=gparse("norma");
  $respuesta=new xajaxResponse();
  $respuesta->assign("medio","innerHTML",$html);
  return $respuesta;
}

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
  set_var("via_link","admin.php");
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

  if(($id_tema>0) || ($id_norma>0) || ($id_nivel>0) || ($numero>0) || ($keyword!=null) || (fecha_valida($fe_desde)) || (fecha_valida($fe_hasta))){
    // selecciono al menos un filtro de busqueda
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
      set_var("via_link","#");
      set_var("via_ajax","onclick='xajax_form_buscar_norma();'");
      $html=gparse("resul");
      unset($consulta);
    } else {
      set_file("info","infoajax.html");
      set_var("mensaje","No hubo coincidencias".$conexion->ErrorMsg());
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
  $html = "";
  $norma = $conexion->GetRow("SELECT * FROM Norma WHERE IdNorma=$id_norma");
  if($norma){
    // parseamos los datos
    set_file("norma","form_norma_admin.html");
    set_var("error","");
    set_var("id_norma",$norma['IdNorma']);
    if($norma['NroNorma']==-1){set_var("numero","");}
    else{set_var("numero",$norma['NroNorma']);}
    $fecha = fecha_normal($norma['FeNorma']);
    set_var("fecha",$fecha);
    $aux = cortar_palabra($norma['TituloNorma'],32);
    set_var("titulo",$aux);
    set_var("descrip",$norma['DescripcionNorma']);
    $consulta = $conexion->Execute("SELECT * FROM Tema");
    foreach($consulta as $tema){
      set_var("id_tema",$tema['IdTema']);
      set_var("tema",$tema['Tema']);
      set_var("selected2","");
      if($tema['IdTema']==$norma['IdTema']){set_var("selected2","selected=selected");}
      parse("cargar_tema");
    }
    unset($consulta);
    $consulta = $conexion->Execute("SELECT * FROM Nivel");
    foreach($consulta as $nivel){
      set_var("id_nivel",$nivel['IdNivel']);
      set_var("nivel",$nivel['Nivel']);
      set_var("selected1","");
      if($nivel['IdNivel']==$norma['IdNivel']){set_var("selected1","selected=selected");}
      parse("cargar_nivel");
    }
    unset($consulta);
    if(isset($norma['DocNorma'])){  
      set_var("documento_norma","Ver el Documento asociado a esta Norma");
      set_var("url_doc",$norma['DocNorma']);
    }
    else{
      set_var("documento_norma","Esta Norma no tiene Documentación Asociada");
      set_var("url_doc","./docs/normas/doc_nulo_norma.pdf");
    }
    set_var("contacto","onclick='xajax_listado_contacto($id_norma);'");
    $html=gparse("norma");
  }else{
    set_file("info","infoajax.html");
    set_var("mensaje","Debe seleccionar al menos un método de busqueda");
    set_var("via_link","#");
    set_var("via_ajax","onclick='xajax_form_buscar_norma();'");
    $html=gparse("info");
  }
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function norma_pauta($id_norma){
  // busca en NormaPauta las pautas asociadas a una norma
  global $conexion;
  $html="";
  set_file("resul","form_resultado_pauta_admin.html");
  $coincidencia = $conexion->GetRow("SELECT Count(*) FROM NormaPauta WHERE IdNorma=$id_norma");
  if($coincidencia['Count(*)']>0){
    $sql = "SELECT IdPauta FROM NormaPauta WHERE IdNorma=$id_norma";
    $consulta = $conexion->Execute($sql);
    foreach($consulta as $norma_pauta){
      $id_pauta = $norma_pauta['IdPauta'];
      $sql = "SELECT FePauta,NombrePauta,IdTema FROM Pauta WHERE IdPauta=$id_pauta";
      $pauta = $conexion->GetRow($sql);
      set_var("idpauta",$id_pauta);
      set_var("idnorma",$id_norma);
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
    unset($consulta);
  } else {
      $id_pauta = "";
      set_var("idpauta","");
      set_var("idnorma",$id_norma);
      set_var("fecha","");
      set_var("nombre","No hay pautas asociadas a esta Norma");
      set_var("tema","");
      parse("cargar_pautas");
  } // end if $consulta 
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
  set_var("via_link","#");
  set_var("via_ajax","onclick='xajax_form_norma($id_norma);'");
  $html=gparse("resul");
  $respuesta = new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function desasociar_norma_pauta($id_pauta,$id_norma){
  global $conexion;
  if($id_pauta!=null){
    $html = "";
    $sql = "DELETE FROM NormaPauta WHERE IdNorma=$id_norma AND IdPauta=$id_pauta";
    $ok = $conexion->Execute($sql);
    if($ok){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('La asociación se elimino con exito')");
      $respuesta->script("xajax_norma_pauta($id_norma);");
    }else{
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('Se produjo un error, intente eliminar la asociación nuevamente')");
      $respuesta->script("xajax_norma_pauta($id_norma);");
    }
  }else{
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('No existen asociaciones para Eliminar')");
  }
  return $respuesta;
}

function asociar_norma_pauta($id_norma,$id_pauta){
  global $conexion;
  $html ="";
  if($id_pauta==-1){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Debe seleccionar una Pauta valida')");
    $respuesta->script("xajax_norma_pauta($id_norma);");
  }else{
    $cantidad = $conexion->GetRow("SELECT Count(*) FROM NormaPauta WHERE IdNorma=$id_norma AND IdPauta=$id_pauta");
    if($cantidad['Count(*)']>0){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('La asociación entre la Norma y la Pauta ya existe')");
      $respuesta->script("xajax_norma_pauta($id_norma);");
    }else{
      $sql = "INSERT INTO NormaPauta VALUES ($id_norma,$id_pauta)";
      $ok = $conexion->Execute($sql);
      if($ok){
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('La asociación se cargo correctamente')");
        $respuesta->script("xajax_norma_pauta($id_norma);");
      }else{
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('Se produjo un error, intente eliminar la asociación nuevamente')");
        $respuesta->script("xajax_norma_pauta($id_norma);");
      }
    }
  }
  return $respuesta;
}

function contacto_norma($id_norma){
// busca en NormaPauta las pautas asociadas a una norma
  global $conexion;
  $html="";
  set_file("resul","resultado_contacto_norma_admin.html");
  $coincidencia = $conexion->GetRow("SELECT Count(*) FROM ContactoNorma WHERE IdNorma=$id_norma");
  if($coincidencia['Count(*)']>0){
    $sql = "SELECT IdContacto FROM ContactoNorma WHERE IdNorma=$id_norma";
    $consulta = $conexion->Execute($sql);
    foreach($consulta as $contacto_norma){
      $id_contacto = $contacto_norma['IdContacto'];
      $sql = "SELECT NombreContacto FROM Contacto WHERE IdContacto=$id_contacto";
      $contacto = $conexion->GetRow($sql);
      set_var("idcontacto",$id_contacto);
      set_var("idnorma",$id_norma);
      set_var("contacto",$contacto['NombreContacto']);
      parse("cargar_contactos");
    } // end for
    unset($consulta);
  }else{
    set_var("idcontacto","");
    set_var("idnorma",$id_norma);
    set_var("contacto","No hay Contactos asociados a esta Norma");
    parse("cargar_contactos");
  } // end if $consulta 
  $consulta = $conexion->Execute("SELECT IdContacto,NombreContacto FROM Contacto");
  set_var("id_contacto",-1);
  set_var("nombre_contacto","Seleccione un Contacto");
  parse("cargar_select_contactos");
  foreach($consulta as $contacto){
    set_var("id_contacto",$contacto['IdContacto']);
    set_var("nombre_contacto",$contacto['NombreContacto']);
    parse("cargar_select_contactos");
  }
  unset($consulta);
  set_var("via_link","#");
  set_var("via_ajax","onclick='xajax_form_norma($id_norma);'");
  $html=gparse("resul");
  $respuesta = new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function desasociar_contacto_norma($id_contacto,$id_norma){
  global $conexion;
  if($id_contacto!=null){
    $sql = "DELETE FROM ContactoNorma WHERE IdContacto=$id_contacto AND IdNorma=$id_norma";
    $ok = $conexion->Execute($sql);
    if($ok){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('La asociación se elimino con exito')");
      $respuesta->script("xajax_contacto_norma($id_norma);");
    }else{
      $respuesta=new xajaxResponse();
      $msg = $conexion->ErrorMsg();
      $respuesta->script("alert('Se produjo un error, intente eliminar la asociación nuevamente')");
      $respuesta->script("xajax_contacto_norma($id_norma);");
    }
  }else{
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('No hay contactos para eliminar')");
  }
  return $respuesta;
}

function asociar_contacto_norma($id_norma,$id_contacto){
  global $conexion;
  $html ="";
  if($id_contacto==-1){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Debe seleccionar un contacto valido')");
    $respuesta->script("xajax_contacto_norma($id_norma);");
  }else{
    $cantidad = $conexion->GetRow("SELECT Count(*) FROM ContactoNorma WHERE IdContacto=$id_contacto AND IdNorma=$id_norma");
    if($cantidad['Count(*)']>0){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('La asociación entre la Norma y el Contacto ya existe')");
      $respuesta->script("xajax_contacto_norma($id_norma);");
    }else{
      $sql = "INSERT INTO ContactoNorma VALUES ($id_contacto,$id_norma)";
      $ok = $conexion->Execute($sql);
      if($ok){
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('La asociación se cargo correctamente')");
        $respuesta->script("xajax_contacto_norma($id_norma);");
      }else{
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('Se produjo un error, intente eliminar la asociación nuevamente')");
        $respuesta->script("xajax_contacto_norma($id_norma);");
      }
    }
  }
  return $respuesta;
}

function eliminar_norma($id_norma){
  global $conexion;
  $html = "";
  // veo que no tenga pautas asociadas
  $cantidad = $conexion->GetRow("SELECT Count(*) FROM NormaPauta WHERE IdNorma=$id_norma");
  if($cantidad['Count(*)']==0){
    // veo que no tenga contactos asociados
    unset($cantidad);
    $cantidad = $conexion->GetRow("SELECT Count(*) FROM ContactoNorma WHERE IdNorma=$id_norma");
    if($cantidad['Count(*)']==0){
      unset($cantidad);
      $cantidad = $conexion->GetRow("SELECT Count(*) FROM Novedad WHERE IdNorma=$id_norma");
      if($cantidad['Count(*)']==0){
        // no tiene tablas asociadas, se puede eliminar
        // eliminamos el documento asociado y luego la norma
        $titulo = $conexion->GetRow("SELECT TituloNorma,DocNorma FROM Norma WHERE IdNorma=$id_norma");
        if($titulo['DocNorma']!=null){
          $archi = "./docs/normas/norma".$id_norma.".pdf";
          unlink($archi);
        }
        if($conexion->Execute("DELETE FROM Norma WHERE IdNorma=$id_norma")){
          // se elimino con exito
          registrar_log_abm(2,3,$id_norma,$titulo['TituloNorma']);
          set_file("info","infoajax.html");
          set_var("mensaje","La Norma fue eliminada con exito");
          set_var("via_link","admin.php");
          set_var("via_ajax","");
          $html=gparse("info");
          $respuesta = new xajaxResponse();
          $respuesta->assign("divcontenido","innerHTML",$html);
        }else{
          // error al eliminar la tabla
          $respuesta=new xajaxResponse();
          $respuesta->script("alert('Se produjo un error al eliminar la tabla, intentelo nuevamente!');");
          $respuesta->script("xajax_form_norma('$id_norma');");
        }
      }else{
        // esta en novedades
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('No se puede eliminar la norma porque esta en Novedades');");
        $respuesta->script("xajax_form_norma('$id_norma');");
      }
    }else{
      // tiene contactos asociados
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('No se puede eliminar la norma porque tiene Contactos asociados');");
      $respuesta->script("xajax_form_norma('$id_norma');");
    }
  }else{
    // tiene pautas asociadas
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('No se puede eliminar la norma porque tiene Pautas asociadas');");
    $respuesta->script("xajax_form_norma('$id_norma');");
  }
  return $respuesta;
}

function novedad_norma($id_norma){
  global $conexion;
  $html="";
  set_file("resul","resultado_novedad_norma_admin.html");
  $bandera = false;
  $consulta = $conexion->Execute("SELECT * FROM Novedad");
  foreach($consulta as $registro){
    if($registro['IdNorma']!=null){
      $bandera = true;
      $id = $registro['IdNorma'];
      $norma = $conexion->GetRow("SELECT TituloNorma FROM Norma WHERE IdNorma=$id");
      set_var("novedad",$norma['TituloNorma']);
      set_var("idnovedad",$registro['IdNovedad']);
      set_var("idnorma",$id_norma);
      parse("cargar_novedades");
    }
    if($registro['IdPauta']!=null){
      $bandera = true;
      $id = $registro['IdPauta'];
      $pauta = $conexion->GetRow("SELECT NombrePauta FROM Pauta WHERE IdPauta=$id");
      set_var("novedad",$pauta['NombrePauta']);
      set_var("idnovedad",$registro['IdNovedad']);
      set_var("idnorma",$id_norma);
      parse("cargar_novedades");
    }
  }
  unset($consulta);
  if(!$bandera){
    set_var("idnovedad","");
    set_var("idnorma",$id_norma);
    set_var("novedad","No hay Novedades");
    parse("cargar_novedades");
  }
  $consulta = $conexion->Execute("SELECT IdNovedad FROM Novedad WHERE IdNorma IS NULL AND IdPauta IS NULL");
  foreach($consulta as $novedad){
    set_var("id_novedad",$novedad['IdNovedad']);
    parse("cargar_select_novedades");
  }
  unset($consulta);
  set_var("via_link","#");
  set_var("via_ajax","onclick='xajax_form_norma($id_norma);'");
  $html=gparse("resul");
  $respuesta = new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function asociar_novedad_norma($id_norma,$id_novedad){
  global $conexion;
  if(empty($id_novedad)){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('No hay lugar en Novedades para esta Norma, elimine alguna Novedad!');");
    $respuesta->script("xajax_novedad_norma($id_norma);");
  }else{
    $cantidad = $conexion->GetRow("SELECT Count(*) FROM Novedad WHERE IdNorma=$id_norma");
    if($cantidad['Count(*)']>0){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('Esta Norma ya se encuentra en Novedades');");
      $respuesta->script("xajax_novedad_norma($id_norma);");
    }else{
      $ok = $conexion->Execute("UPDATE Novedad SET IdNorma=$id_norma WHERE IdNovedad=$id_novedad");
      if($ok){
        registrar_log_abm(1,5,$id_novedad,"Norma-".$id_norma);
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('La Norma se incluyó en Novedades de manera exitosa');");
        $respuesta->script("xajax_novedad_norma($id_norma);");
      }else{
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('Se produjo un error al realizar la operación, intentelo nuevamente');");
        $respuesta->script("xajax_novedad_norma($id_norma);");
      }
    }
  }
  return $respuesta;
}

function reiniciar_novedad_norma($id_novedad,$id_norma){
  global $conexion;
  if($id_novedad!=null){
    $sql = "UPDATE Novedad SET IdNorma=null,IdPauta=null WHERE IdNovedad=$id_novedad";
    $ok = $conexion->Execute($sql);
    if($ok){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('La asociación se elimino con exito')");
      $respuesta->script("xajax_novedad_norma($id_norma);");
    }else{
      $respuesta=new xajaxResponse();
      $msg = $conexion->ErrorMsg();
      $respuesta->script("alert('Se produjo un error, intente eliminar la asociación nuevamente')");
      $respuesta->script("xajax_novedad_norma($id_norma);");
    }
  }else{
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('No hay Novedades para Eliminar')");
  }
  return $respuesta;
}


////////////////////////////////////////////////////
// Gestion de Pautas
////////////////////////////////////////////////////


function form_agregar_pauta(){
  global $conexion;
  $html = "";
  set_file("pauta","form_agregar_pauta.html");
  set_var("error","");
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
  $html=gparse("pauta");
  $respuesta=new xajaxResponse();
  $respuesta->assign("medio","innerHTML",$html);
  return $respuesta;
}

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
  set_var("via_link","admin.php");
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
    } // end foreach consulta
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
    } // end foreach items
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
  $html = "";
  $pauta = $conexion->GetRow("SELECT * FROM Pauta WHERE IdPauta=$id_pauta");
  if($pauta){
    // parseamos los datos
    set_file("pauta","form_pauta_admin.html");
    set_var("error","");
    set_var("id_pauta",$pauta['IdPauta']);
    $fecha = fecha_normal($pauta['FePauta']);
    set_var("fecha",$fecha);
    set_var("nombre",$pauta['NombrePauta']);
    $consulta = $conexion->Execute("SELECT * FROM Tema");
    foreach($consulta as $tema){
      set_var("id_tema",$tema['IdTema']);
      set_var("tema",$tema['Tema']);
      set_var("selected","");
      if($tema['IdTema']==$pauta['IdTema']){set_var("selected","selected=selected");}
      parse("cargar_tema");
    }
    unset($consulta);
    if(isset($pauta['DocPauta'])){  
      set_var("documento_pauta","Ver el Documento asociado a esta Pauta");
      set_var("url_doc",$pauta['DocPauta']);
    }else{
      set_var("documento_pauta","Esta Pauta no tiene Documentación Asociada");
      set_var("url_doc","./docs/pautas/doc_nulo_pauta.pdf");
    }
    $html=gparse("pauta");
  }else{
    set_file("info","infoajax.html");
    set_var("mensaje","Debe seleccionar al menos un método de busqueda");
    set_var("via_link","#");
    set_var("via_ajax","onclick='xajax_form_buscar_pauta();'");
    $html=gparse("info");
  }
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function novedad_pauta($id_pauta){
  global $conexion;
  $html="";
  set_file("resul","resultado_novedad_pauta_admin.html");
  $bandera = false;
  $consulta = $conexion->Execute("SELECT * FROM Novedad");
  foreach($consulta as $registro){
    if($registro['IdNorma']!=null){
      $bandera = true;
      $id = $registro['IdNorma'];
      $norma = $conexion->GetRow("SELECT TituloNorma FROM Norma WHERE IdNorma=$id");
      set_var("novedad",$norma['TituloNorma']);
      set_var("idnovedad",$registro['IdNovedad']);
      set_var("idpauta",$id_pauta);
      parse("cargar_novedades");
    }
    if($registro['IdPauta']!=null){
      $bandera = true;
      $id = $registro['IdPauta'];
      $pauta = $conexion->GetRow("SELECT NombrePauta FROM Pauta WHERE IdPauta=$id");
      set_var("novedad",$pauta['NombrePauta']);
      set_var("idnovedad",$registro['IdNovedad']);
      set_var("idpauta",$id_pauta);
      parse("cargar_novedades");
    }
  }
  unset($consulta);
  if(!$bandera){
    set_var("idnovedad","");
    set_var("idpauta",$id_pauta);
    set_var("novedad","No hay Novedades");
    parse("cargar_novedades");
  }
  $consulta = $conexion->Execute("SELECT IdNovedad FROM Novedad WHERE IdNorma IS NULL AND IdPauta IS NULL");
  foreach($consulta as $novedad){
    set_var("id_novedad",$novedad['IdNovedad']);
    parse("cargar_select_novedades");
  }
  unset($consulta);
  set_var("via_link","#");
  set_var("via_ajax","onclick='xajax_form_pauta($id_pauta);'");
  $html=gparse("resul");
  $respuesta = new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function asociar_novedad_pauta($id_pauta,$id_novedad){
  global $conexion;
  if(empty($id_novedad)){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('No hay lugar en Novedades para esta Norma, elimine alguna Novedad!');");
    $respuesta->script("xajax_novedad_pauta($id_pauta);");
  }else{
    $cantidad = $conexion->GetRow("SELECT Count(*) FROM Novedad WHERE IdPauta=$id_pauta");
    if($cantidad['Count(*)']>0){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('Esta Pauta ya se encuentra en Novedades');");
      $respuesta->script("xajax_novedad_pauta($id_pauta);");
    }else{
      $ok = $conexion->Execute("UPDATE Novedad SET IdPauta=$id_pauta WHERE IdNovedad=$id_novedad");
      if($ok){
        registrar_log_abm(1,5,$id_novedad,"Pauta-".$id_pauta);
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('La Pauta se incluyó en Novedades de manera exitosa');");
        $respuesta->script("xajax_novedad_pauta($id_pauta);");
      }else{
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('Se produjo un error al realizar la operación, intentelo nuevamente');");
        $respuesta->script("xajax_novedad_pauta($id_pauta);");
      }
    }
  }
  return $respuesta;
}

function reiniciar_novedad_pauta($id_novedad,$id_pauta){
  global $conexion;
  if($id_novedad!=null){
    $sql = "UPDATE Novedad SET IdNorma=null,IdPauta=null WHERE IdNovedad=$id_novedad";
    $ok = $conexion->Execute($sql);
    if($ok){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('La asociación se elimino con exito')");
      $respuesta->script("xajax_novedad_pauta($id_pauta);");
    }else{
      $respuesta=new xajaxResponse();
      $msg = $conexion->ErrorMsg();
      $respuesta->script("alert('Se produjo un error, intente eliminar la asociación nuevamente')");
      $respuesta->script("xajax_novedad_pauta($id_pauta);");
    }
  }else{
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('No hay Novedades para Eliminar')"); 
  }
  return $respuesta;
}

function eliminar_pauta($id_pauta){
  global $conexion;
  $html = "";
  // veo que no tenga normas asociadas
  $cantidad = $conexion->GetRow("SELECT Count(*) FROM NormaPauta WHERE IdPauta=$id_pauta");
  if($cantidad['Count(*)']==0){
    // veo que no tenga items asociados
    unset($cantidad);
    $cantidad = $conexion->GetRow("SELECT Count(*) FROM ItemPauta WHERE IdPauta=$id_pauta");
    if($cantidad['Count(*)']==0){
      // veo que no aparezca en novedades
      unset($cantidad);
      $cantidad = $conexion->GetRow("SELECT Count(*) FROM Novedad WHERE IdPauta=$id_pauta");
      if($cantidad['Count(*)']==0){
        // veo que no tenga contactos asociados
        unset($cantidad);
        $cantidad = $conexion->GetRow("SELECT Count(*) FROM ContactoPauta WHERE IdPauta=$id_pauta");
        if($cantidad['Count(*)']==0){
          // no tiene tablas asociadas, se puede eliminar
          // eliminamos el documento asociado y luego la pauta
          $dato = $conexion->GetRow("SELECT NombrePauta,DocPauta FROM Pauta WHERE IdPauta=$id_pauta");
          if($dato['DocPauta']!=null){
            $archi = "./docs/pautas/pauta".$id_pauta.".pdf";
            unlink($archi);
          }
          if($conexion->Execute("DELETE FROM Pauta WHERE IdPauta=$id_pauta")){
            // se elimino con exito
            registrar_log_abm(2,4,$id_pauta,$dato['NombrePauta']);
            set_file("info","infoajax.html");
            set_var("mensaje","La Pauta fue eliminada con exito");
            set_var("via_link","admin.php");
            set_var("via_ajax","");
            $html=gparse("info");
            $respuesta = new xajaxResponse();
            $respuesta->assign("divcontenido","innerHTML",$html);
          }else{
            // error al eliminar la tabla
            $respuesta=new xajaxResponse();
            $respuesta->script("alert('Se produjo un error al eliminar la tabla, intentelo nuevamente!');");
            $respuesta->script("xajax_form_pauta($id_pauta);");
          }
        }else{
          // tiene contactos asociados
          $respuesta=new xajaxResponse();
          $respuesta->script("alert('No se puede eliminar la Pauta porque tiene Contactos asociadas');");
          $respuesta->script("xajax_form_pauta($id_pauta);");
        }
      }else{
        // esta en novedades
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('No se puede eliminar la Pauta porque esta en Novedades');");
        $respuesta->script("xajax_form_pauta($id_pauta);");
      }
    }else{
      // tiene items
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('No se puede eliminar la Pauta porque tiene Items asociados');");
      $respuesta->script("xajax_form_pauta($id_pauta);");
    }
  }else{
    // tiene normas asociadas
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('No se puede eliminar la Pauta porque tiene Normas asociadas');");
    $respuesta->script("xajax_form_pauta($id_pauta);");
  }
  return $respuesta;

}

function pauta_norma($id_pauta){
// busca en NormaPauta las normas asociadas a una norma
  global $conexion;
  $html="";
  set_file("resul","form_resultado_norma_admin.html");
  $coincidencia = $conexion->GetRow("SELECT Count(*) FROM NormaPauta WHERE IdPauta=$id_pauta");
  if($coincidencia['Count(*)']>0){
      $sql = "SELECT IdNorma FROM NormaPauta WHERE IdPauta=$id_pauta";
      $consulta = $conexion->Execute($sql);
      foreach($consulta as $norma_pauta){
        $id_norma = $norma_pauta['IdNorma'];
        $sql = "SELECT NroNorma,TituloNorma,IdNivel,IdTema FROM Norma WHERE IdNorma=$id_norma";
        $norma = $conexion->GetRow($sql);
        set_var("idpauta",$id_pauta);
        set_var("idnorma",$id_norma);
        if($norma['NroNorma']>0){
          set_var("numero",$norma['NroNorma']);
        }else{
          set_var("numero","N/C");
        }
        $aux = cortar_palabra($norma['TituloNorma'],26);
        set_var("titulo",$aux);
        $id_aux = $norma['IdNivel'];
        $nivel = $conexion->GetRow("SELECT Nivel FROM Nivel WHERE IdNivel=$id_aux");
        set_var("nivel",$nivel['Nivel']);
        unset($id_aux);
        $id_aux = $norma['IdTema'];
        $tema = $conexion->GetRow("SELECT Tema FROM Tema WHERE IdTema=$id_aux");
        set_var("tema",$tema['Tema']);
        parse("cargar_normas");
      } // end for
      unset($consulta);
    } else {
        $id_norma = "";
        set_var("idpauta",$id_pauta);
        set_var("idnorma","");
        set_var("numero","");
        set_var("titulo","No hay Normas asociadas a esta Pauta");
        set_var("nivel","");
        set_var("tema","");
        parse("cargar_normas");
    } // end if $consulta 
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
  set_var("titulo_norma","Seleccione una Norma");
  parse("cargar_titulo");
  foreach($consulta as $norma){
    set_var("id_norma",$norma['IdNorma']);
    set_var("titulo_norma",$norma['TituloNorma']);
    parse("cargar_titulo");
  }
  unset($consulta);
  set_var("via_link","#");
  set_var("via_ajax","onclick='xajax_form_pauta($id_pauta);'");
  $html=gparse("resul");
  $respuesta = new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function asociar_pauta_norma($id_pauta,$id_norma){
  global $conexion;
  $html ="";
  if($id_norma==-1){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Debe seleccionar una Norma valida')");
    $respuesta->script("xajax_pauta_norma($id_pauta);");
  }else{
    $cantidad = $conexion->GetRow("SELECT Count(*) FROM NormaPauta WHERE IdNorma=$id_norma AND IdPauta=$id_pauta");
    if($cantidad['Count(*)']>0){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('La asociación entre la Norma y la Pauta ya existe')");
      $respuesta->script("xajax_pauta_norma($id_pauta);");
    }else{
      $sql = "INSERT INTO NormaPauta VALUES ($id_norma,$id_pauta)";
      $ok = $conexion->Execute($sql);
      if($ok){
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('La asociación se cargo correctamente')");
        $respuesta->script("xajax_pauta_norma($id_pauta);");
      }else{
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('Se produjo un error, intente eliminar la asociación nuevamente')");
        $respuesta->script("xajax_pauta_norma($id_pauta);");
      }
    }
  }
  return $respuesta;
}

function desasociar_pauta_norma($id_pauta,$id_norma){
  global $conexion;
  if($id_norma!=null){
    $html = "";
    $sql = "DELETE FROM NormaPauta WHERE IdNorma=$id_norma AND IdPauta=$id_pauta";
    $ok = $conexion->Execute($sql);
    if($ok){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('La asociación se elimino con exito')");
      $respuesta->script("xajax_pauta_norma($id_pauta);");
    }else{
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('Se produjo un error, intente eliminar la asociación nuevamente')");
      $respuesta->script("xajax_pauta_norma($id_pauta);");
    }
  }else{
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('No existen asociaciones para Eliminar')");
  }
  return $respuesta;
}

function form_items_pauta($id_pauta){
  global $conexion;
  $html = "";
  set_file("items","form_itempauta_admin.html");
  set_var("idpauta",$id_pauta);
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
    set_var("descrip","Esta Pauta no tiene items");
    set_var("obs","");
  }else{
    foreach($consulta as $itempauta){
      if($itempauta['IdNivel']==$menor){
        set_var("descrip",$itempauta['Descripcion']);
        set_var("obs",$itempauta['Observacion']);
      }
    }
  }
  unset($consulta);
  $consulta = $conexion->Execute("SELECT * FROM Nivel");
  set_var("id_nivel_add",-1);
  set_var("nivel_add","Seleccione un Nivel");
  parse("cargar_nivel_add");
  foreach($consulta as $nivel){
    set_var("id_nivel_add",$nivel['IdNivel']);
    set_var("nivel_add",$nivel['Nivel']);
    parse("cargar_nivel_add");
  }
  unset($consulta);
  $html=gparse("items");
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function recargar_items($id_pauta,$id_nivel){
  // vuelve a cargar los items según el nivel seleccionado
  global $conexion;
  $html="";
  set_file("items_combi","form_items_combinado_admin.html");
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

function modificar_item_pauta($id_pauta,$id_nivel,$descrip,$obs){
  global $conexion;
  if(!empty($id_nivel)){
    $sql = "UPDATE ItemPauta SET Descripcion='$descrip',Observacion='$obs' WHERE IdPauta='$id_pauta' AND IdNivel=$id_nivel";
    if($conexion->Execute($sql)){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('El Item se modifico correctamente')");
      $respuesta->script("xajax_form_items_pauta($id_pauta);");
    }else{
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('Error al modificar el item, vuelva a intentarlo')");
      $respuesta->script("xajax_form_items_pauta($id_pauta);");
    }
  }else{
    // no existen items para modificar
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Esta Pauta No tiene Items para modificar')");
    $respuesta->script("xajax_form_items_pauta($id_pauta);");
  }
  return $respuesta;
}

function eliminar_item_pauta($id_pauta,$id_nivel){
  global $conexion;
  if(!empty($id_nivel)){
    $sql = "DELETE FROM ItemPauta WHERE IdPauta='$id_pauta' AND IdNivel=$id_nivel";
    if($conexion->Execute($sql)){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('El Item se elimino correctamente')");
      $respuesta->script("xajax_form_items_pauta($id_pauta);");
    }else{
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('Error al eliminar el item, vuelva a intentarlo')");
      $respuesta->script("xajax_form_items_pauta($id_pauta);");
    }
  }else{
    // no existen items para modificar
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Esta Pauta No tiene Items para eliminar')");
    $respuesta->script("xajax_form_items_pauta($id_pauta);");
  }
  return $respuesta;
}

function agregar_item_pauta($id_pauta,$id_nivel,$descrip,$obs){
  global $conexion;
  if($id_nivel>0){
    if(!(empty($descrip) && empty($obs))){
      $cantidad = $conexion->GetRow("SELECT Count(*) FROM ItemPauta WHERE IdPauta=$id_pauta AND IdNivel=$id_nivel");
      if($cantidad['Count(*)']==0){
        // guardo el item
        $sql = "INSERT INTO ItemPauta VALUES($id_pauta,$id_nivel,'$descrip','$obs')";
        if($conexion->Execute($sql)){
          $respuesta=new xajaxResponse();
          $respuesta->script("alert('El Item se guardo correctamente')");
          $respuesta->script("xajax_form_items_pauta($id_pauta);");
        }else{
          $respuesta=new xajaxResponse();
          $respuesta->script("alert('Error al guardar el Item, intentelo nuevamente')");
          $respuesta->script("xajax_form_items_pauta($id_pauta);");
        }
      }else{
        // ya existe un item para ese nivel
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('Ya existe un Item para ese Nivel, puede modificarlo')");
        $respuesta->script("xajax_form_items_pauta($id_pauta);");
      }      
    }else{
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('Debe ingresar alguna Descripción y/o una Observación')");
    }
  }else{
    // no existen items para modificar
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Debe seleccionar un Nivel para agregar un Item')");
  }
  return $respuesta;
}

function contacto_pauta($id_pauta){
// busca en NormaPauta las pautas asociadas a una norma
  global $conexion;
  $html="";
  set_file("resul","resultado_contacto_pauta_admin.html");
  $coincidencia = $conexion->GetRow("SELECT Count(*) FROM ContactoPauta WHERE IdPauta=$id_pauta");
  if($coincidencia['Count(*)']>0){
    $sql = "SELECT IdContacto FROM ContactoPauta WHERE IdPauta=$id_pauta";
    $consulta = $conexion->Execute($sql);
    foreach($consulta as $contacto_pauta){
      $id_contacto = $contacto_pauta['IdContacto'];
      $sql = "SELECT NombreContacto FROM Contacto WHERE IdContacto=$id_contacto";
      $contacto = $conexion->GetRow($sql);
      set_var("idcontacto",$id_contacto);
      set_var("idpauta",$id_pauta);
      set_var("contacto",$contacto['NombreContacto']);
      parse("cargar_contactos");
    } // end for
    unset($consulta);
  }else{
    set_var("idcontacto","");
    set_var("idpauta",$id_pauta);
    set_var("contacto","No hay Contactos asociados a esta Pauta");
    parse("cargar_contactos");
  } // end if $consulta 
  $consulta = $conexion->Execute("SELECT IdContacto,NombreContacto FROM Contacto");
  set_var("id_contacto",-1);
  set_var("nombre_contacto","Seleccione un Contacto");
  parse("cargar_select_contactos");
  foreach($consulta as $contacto){
    set_var("id_contacto",$contacto['IdContacto']);
    set_var("nombre_contacto",$contacto['NombreContacto']);
    parse("cargar_select_contactos");
  }
  unset($consulta);
  set_var("via_link","#");
  set_var("via_ajax","onclick='xajax_form_pauta($id_pauta);'");
  $html=gparse("resul");
  $respuesta = new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function desasociar_contacto_pauta($id_contacto,$id_pauta){
  global $conexion;
  if($id_contacto!=null){
    $sql = "DELETE FROM ContactoPauta WHERE IdContacto=$id_contacto AND IdPauta=$id_pauta";
    $ok = $conexion->Execute($sql);
    if($ok){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('La asociación se elimino con exito')");
      $respuesta->script("xajax_contacto_pauta($id_pauta);");
    }else{
      $respuesta=new xajaxResponse();
      $msg = $conexion->ErrorMsg();
      $respuesta->script("alert('Se produjo un error, intente eliminar la asociación nuevamente')");
      $respuesta->script("xajax_contacto_pauta($id_pauta);");
    }
  }else{
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('No hay Contactos para Eliminar')");
  }
  return $respuesta;
}

function asociar_contacto_pauta($id_pauta,$id_contacto){
  global $conexion;
  $html ="";
  if($id_contacto==-1){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Debe seleccionar un contacto valido')");
    $respuesta->script("xajax_contacto_pauta($id_pauta);");
  }else{
    $cantidad = $conexion->GetRow("SELECT Count(*) FROM ContactoPauta WHERE IdContacto=$id_contacto AND IdPauta=$id_pauta");
    if($cantidad['Count(*)']>0){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('La asociación entre la Pauta y el Contacto ya existe')");
      $respuesta->script("xajax_contacto_pauta($id_pauta);");
    }else{
      $sql = "INSERT INTO ContactoPauta VALUES ($id_contacto,$id_pauta)";
      $ok = $conexion->Execute($sql);
      if($ok){
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('La asociación se cargo correctamente')");
        $respuesta->script("xajax_contacto_pauta($id_pauta);");
      }else{
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('Se produjo un error, intente eliminar la asociación nuevamente')");
        $respuesta->script("xajax_contacto_pauta($id_pauta);");
      }
    }
  }
  return $respuesta;
}


////////////////////////////////////////////////////
// Gestión de Temas
////////////////////////////////////////////////////


function form_temas($pagina,$nuevo_tema){
  global $conexion;
  $html="";
  set_file("temas","form_temas_admin.html");
  if(!$nuevo_tema){
    set_var("comentario", "");
    set_var("nombre_tema","");
  }else{
    set_var("comentario", "Se agrego el Tema: ");
    set_var("nombre_tema",$nuevo_tema);
  }
  $cantidad = $conexion->GetRow("SELECT Count(*) FROM Tema");
  if($cantidad['Count(*)']>0){
    $limite = 10;
    $inicio = ($pagina-1)*10;
    $consulta = $conexion->Execute("SELECT * FROM Tema LIMIT $inicio,$limite");
    foreach($consulta as $tema){
      set_var("tema",$tema['Tema']);
      set_var("idtema",$tema['IdTema']);
      parse("cargar_temas");
    }
    unset($consulta);
    if($pagina==1){
      set_var("prev","");
      set_var("pagina_prev","");
    }else{
      set_var("prev","Anterior");
      set_var("pagina_prev",($pagina-1));
    }
    set_var("pagina",$pagina);
    if($cantidad['Count(*)']>($pagina*10)){
      set_var("prox","Proxima");
      set_var("pagina_prox",($pagina+1));
    }else{
      set_var("prox","");
      set_var("pagina_prox","");
    }
    set_var("via_link","admin.php");
    set_var("via_ajax","");
  }else{
    set_var("tema","No existe ningún Tema");
    set_var("idtema","");
    parse("cargar_temas");
    set_var("prev","");
    set_var("pagina_prev","");
    set_var("pagina",$pagina);
    set_var("prox","");
    set_var("pagina_prox","");
    set_var("via_link","admin.php");
    set_var("via_ajax","");
  }
  $html=gparse("temas");
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function agregar_tema($nuevo_tema){
  global $conexion;
  if(empty($nuevo_tema)){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Debe escribir el Tema en el campo -Agregar un Tema-')");
    $respuesta->script("xajax_form_temas(1,'');");
  }else{
    // verificamos que el tema sea alfabetico
    if(!(is_alphanumeric($nuevo_tema,1,200))){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('El tema debe contener solo caracteres alfabéticos')");
    }else{
      // verificamos que no exista otro igual
      $cantidad = $conexion->GetRow("SELECT Count(*) FROM Tema WHERE Tema LIKE '$nuevo_tema'");
      if($cantidad['Count(*)']==0){
        // el tema es unico, lo guardo
        $id = $conexion->GenID("SeqTema");
        $ok = $conexion->Execute("INSERT INTO Tema VALUES($id,'$nuevo_tema')");
        if($ok){
          registrar_log_abm(1,7,$id,$nuevo_tema);
          $respuesta=new xajaxResponse();
          $respuesta->script("xajax_form_temas(1,'$nuevo_tema');");
        }else{
          $id = $id - 1;
          $conexion->Execute("UPDATE SeqTema SET id=$id");
          $respuesta=new xajaxResponse();
          $respuesta->script("alert('Se produjo un error al guardar el Tema, intentelo nuevamente')");
          $respuesta->script("xajax_form_temas(1,'');");
        }
      }else{
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('El Tema que intenta cargar ya Existe')");
        $respuesta->script("xajax_form_temas(1,'');");
      }
    }
  }
  return $respuesta;
}

function eliminar_tema($id_tema){
  if($id_tema!=null){
    global $conexion;
    $cantidad = $conexion->GetRow("SELECT Count(*) FROM Norma WHERE IdTema=$id_tema");
    if($cantidad['Count(*)']>0){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('No se puede eliminar el Tema debido a que tiene Normas asociadas')");
    }else{
      unset($cantidad);
      $cantidad = $conexion->GetRow("SELECT Count(*) FROM Pauta WHERE IdTema=$id_tema");
      if($cantidad['Count(*)']>0){
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('No se puede eliminar el Tema debido a que tiene Pautas asociadas')");
      }else{
        // el tema se puede eliminar
        $dato = $conexion->GetRow("SELECT Tema FROM Tema WHERE IdTema=$id_tema");
        $ok = $conexion->Execute("DELETE FROM Tema WHERE IdTema=$id_tema");
        if($ok){
          registrar_log_abm(2,7,$id_tema,$dato['Tema']);
          $respuesta=new xajaxResponse();
          $respuesta->script("alert('El Tema se elimino con exito')");
          $respuesta->script("xajax_form_temas(1,null);");
        }else{
          $respuesta=new xajaxResponse();
          $respuesta->script("alert('Error al eliminar el Tema, intentelo nuevamente')");
          $respuesta->script("xajax_form_temas(1,null);");
        }
      }
    }
  }else{
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Debe elegir un Tema para eliminar')");
  }
  return $respuesta;
}

function filtrar_temas($palabra){
  // vuelve a cargar el listado de temas segun la keyword ingresada
  if(!empty($palabra)){
    global $conexion;
    $html="";
    set_file("listado","listado_temas.html");
    $aux = "%".$palabra."%";
    $cantidad = $conexion->GetRow("SELECT Count(*) FROM Tema WHERE Tema LIKE '$aux'");
    if($cantidad['Count(*)']>0){
      $consulta = $conexion->Execute("SELECT * FROM Tema WHERE Tema LIKE '$aux'");
      foreach($consulta as $tema){
        set_var("tema",$tema['Tema']);
        set_var("idtema",$tema['IdTema']);
        parse("cargar_temas");
      }
      unset($consulta);
      set_var("prev","");
      set_var("pagina_prev","");
      set_var("pagina","");
      set_var("prox","");
      set_var("pagina_prox","");
      set_var("via_link","admin.php");
      set_var("via_ajax","");
    }else{
      set_var("tema","No existe ningún Tema que coincida con la palabra ingresada");
      set_var("idtema","");
      parse("cargar_temas");
      set_var("prev","");
      set_var("pagina_prev","");
      set_var("pagina","");
      set_var("prox","");
      set_var("pagina_prox","");
    }  
    $html=gparse("listado");
    $respuesta=new xajaxResponse();
    $respuesta->assign("listado_temas","innerHTML",$html);
  }else{
    $respuesta=new xajaxResponse();
    $respuesta->script("xajax_form_temas(1,null);");
  }
  return $respuesta;
}


////////////////////////////////////////////////////
// Gestión de Contactos
////////////////////////////////////////////////////


function form_agregar_contacto($nom_contacto){
  $html="";
  set_file("contacto","form_agregar_contacto.html");
  set_var("error","");
  if($nom_contacto == false){
    set_var("comentario","");
    set_var("nom_contacto","");
  }else{
    set_var("comentario","Se ha guardado el siguiente Contacto: ");
    set_var("nom_contacto",$nom_contacto);
  }
  $html=gparse("contacto");
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function guardar_contacto($nombre,$email,$tel,$ctx){
  global $conexion;
  if(empty($nombre)){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Debe ingresar un Nombre para el Contacto')");
  }else{
    if(empty($email) && empty($tel) && empty($ctx)){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('Debe ingresar un E-Mail, Telefono o Centrex para el Contacto')");
    }else{
      // verifico que los datos son alfanumericos
      if(!(is_alphanumeric($nombre,1,100) && (is_alphanumeric($tel,1,40)||$tel==null) && (is_alphanumeric($ctx,1,12)||$ctx==null))){
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('Recuerde que todos los datos solo pueden ser alfanuméricos')");
      }else{
        // email valido
        if(!is_email($email) && $email!=null){
          $respuesta=new xajaxResponse();
          $respuesta->script("alert('Ingrese una dirección de correo eletrónico válida')");
        }else{
          // verifico que el nombre de contacto no exista en la base
          $cantidad = $conexion->GetRow("SELECT Count(*) FROM Contacto WHERE NombreContacto LIKE '$nombre'");
          if($cantidad['Count(*)']>0){
            $respuesta=new xajaxResponse();
            $respuesta->script("alert('Ya existe un Contacto con ese nombre, ingrese otro nombre')");
          }else{
            // ya puedo guardarlo
            $id = $conexion->GenID("SeqContacto");
            $sql = "INSERT INTO Contacto VALUES($id,'$nombre','$email','$tel','$ctx')";
            if($conexion->Execute($sql)){
              // contacto guardado correctamente
              registrar_log_abm(1,1,$id,$nombre);
              $respuesta=new xajaxResponse();
              $respuesta->script("xajax_form_agregar_contacto('$nombre');");
            }else{
              $respuesta=new xajaxResponse();
              $respuesta->script("alert('Se produjo un error al guardar el Contacto, intentelo nuevamente')");
              $respuesta->script("xajax_form_agregar_contacto();");
            }
          }
        }
      }
    }
  }
  return $respuesta;
}

function form_buscar_contacto($pagina){
  global $conexion;
  $html="";
  set_file("contactos","form_buscar_contacto_admin.html");
  $cantidad = $conexion->GetRow("SELECT Count(*) FROM Contacto");
  if($cantidad['Count(*)']>0){
    $limite = 10;
    $inicio = ($pagina-1)*10;
    $consulta = $conexion->Execute("SELECT IdContacto,NombreContacto FROM Contacto LIMIT $inicio,$limite");
    foreach($consulta as $contacto){
      set_var("contacto",$contacto['NombreContacto']);
      set_var("idcontacto",$contacto['IdContacto']);
      parse("cargar_contactos");
    }
    unset($consulta);
    if($pagina==1){
      set_var("prev","");
      set_var("pagina_prev","");
    }else{
      set_var("prev","Anterior");
      set_var("pagina_prev",($pagina-1));
    }
    set_var("pagina",$pagina);
    if($cantidad['Count(*)']>($pagina*10)){
      set_var("prox","Proxima");
      set_var("pagina_prox",($pagina+1));
    }else{
      set_var("prox","");
      set_var("pagina_prox","");
    }
  }else{
    set_var("contacto","No existe ningún Contacto");
    set_var("idcontacto","");
    parse("cargar_contactos");
    set_var("prev","");
    set_var("pagina_prev","");
    set_var("pagina",$pagina);
    set_var("prox","");
    set_var("pagina_prox","");
  }
  set_var("via_link","admin.php");
  set_var("via_ajax","");
  $html=gparse("contactos");
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function filtrar_contactos($palabra){
  // vuelve a cargar el listado de contactos segun la keyword ingresada
  if(!empty($palabra)){
    global $conexion;
    $html="";
    set_file("listado","listado_contactos.html");
    $aux = "%".$palabra."%";
    $cantidad = $conexion->GetRow("SELECT Count(*) FROM Contacto WHERE NombreContacto LIKE '$aux'");
    if($cantidad['Count(*)']>0){
      $sql = "SELECT IdContacto,NombreContacto FROM Contacto WHERE NombreContacto LIKE '$aux'";
      $consulta = $conexion->Execute($sql);
      foreach($consulta as $contacto){
        set_var("contacto",$contacto['NombreContacto']);
        set_var("idcontacto",$contacto['IdContacto']);
        parse("cargar_contactos");
      }
      unset($consulta);
      set_var("prev","");
      set_var("pagina_prev","");
      set_var("pagina","");
      set_var("prox","");
      set_var("pagina_prox","");
      set_var("via_link","admin.php");
      set_var("via_ajax","");
    }else{
      set_var("contacto","No existe ningún Contacto que coincida con la palabra ingresada");
      set_var("idcontacto","");
      parse("cargar_contactos");
      set_var("prev","");
      set_var("pagina_prev","");
      set_var("pagina","");
      set_var("prox","");
      set_var("pagina_prox","");
    }  
    $html=gparse("listado");
    $respuesta=new xajaxResponse();
    $respuesta->assign("listado_contactos","innerHTML",$html);
  }else{
    $respuesta=new xajaxResponse();
    $respuesta->script("xajax_form_buscar_contacto(1);");
  }
  return $respuesta;
}

function eliminar_contacto($id_contacto){
  if($id_contacto!=null){
    global $conexion;
    if(false){
      $cantidad = $conexion->GetRow("SELECT Count(*) FROM ContactoNorma WHERE IdContacto=$id_contacto");
      if($cantidad['Count(*)']>0){
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('No se puede eliminar el Contacto debido a que tiene Normas asociadas')");
      }else{
        unset($cantidad);
        $cantidad = $conexion->GetRow("SELECT Count(*) FROM ContactoPauta WHERE IdContacto=$id_contacto");
        if($cantidad['Count(*)']>0){
          $respuesta=new xajaxResponse();
          $respuesta->script("alert('No se puede eliminar el Contacto debido a que tiene Pautas asociadas')");
        }else{
          // el contacto se puede eliminar
          $dato = $conexion->GetRow("SELECT NombreContacto FROM Contacto WHERE IdContacto=$id_contacto");
          $ok = $conexion->Execute("DELETE FROM Contacto WHERE IdContacto=$id_contacto");
          if($ok){
            registrar_log_abm(2,1,$id_contacto,$dato['NombreContacto']);
            $respuesta=new xajaxResponse();
            $respuesta->script("alert('El Contacto se elimino con exito')");
            $respuesta->script("xajax_form_buscar_contacto(1);");
          }else{
            $respuesta=new xajaxResponse();
            $respuesta->script("alert('Error al eliminar el Contacto, intentelo nuevamente')");
            $respuesta->script("xajax_form_buscar_contacto(1);");
          }
        }
      }
    }else{
      // forzar el borrado
      $mensaje = "";
      // eliminamos las normas asociadas
      $ok1 = $conexion->Execute("DELETE FROM ContactoNorma WHERE IdContacto=$id_contacto");
      if(!$ok1){$mensaje = $mensaje.$conexion->ErrorMsg();}
      // eliminamos las pautas asociadas
      $ok2 = $conexion->Execute("DELETE FROM ContactoPauta WHERE IdContacto=$id_contacto");
      if(!$ok2){$mensaje = $mensaje.$conexion->ErrorMsg();}
      // eliminamos el contacto
      $ok3 = $conexion->Execute("DELETE FROM Contacto WHERE IdContacto=$id_contacto");
      if(!$ok3){$mensaje = $mensaje.$conexion->ErrorMsg();}
      if($ok1 && $ok2 && $ok3){
        $mensaje = "El Contato se elimino con exitosamente";
      }else{
        $mensaje= "Error al eliminar alguno de los registros: ".$mensaje;
      }
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('$mensaje')");
      $respuesta->script("xajax_form_buscar_contacto(1);");
    }
  }else{
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Debe elegir un Contacto para eliminar')");
  }
  return $respuesta;
}

function form_modificar_contacto($id_contacto){
  global $conexion;
  $html="";
  set_file("contacto","form_modificar_contacto.html");
  set_var("error","");
  if(empty($id_contacto)){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Debe elegir un Contacto para modificar')");
  }else{
    $contacto = $conexion->GetRow("SELECT * FROM Contacto WHERE IdContacto=$id_contacto");
    set_var("idcontacto","$id_contacto"); 
    set_var("nombre",$contacto['NombreContacto']);
    set_var("email",$contacto['EMailContacto']);
    set_var("tel",$contacto['TelefonoContacto']);
    set_var("ctx",$contacto['Centrex']);
    $html=gparse("contacto");
    $respuesta=new xajaxResponse();
    $respuesta->assign("divcontenido","innerHTML",$html);
  }
  return $respuesta;
}

function modificar_contacto($id_contacto,$nombre,$email,$tel,$ctx){
  global $conexion;
  if(empty($nombre)){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Debe ingresar un Nombre para el Contacto')");
  }else{
    if(empty($email) && empty($tel) && empty($ctx)){
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('Debe ingresar un E-Mail, Telefono o Centrex para el Contacto')");
    }else{
      // verifico que los datos son alfanumericos
      if(!(is_alphanumeric($nombre,1,100) && (is_alphanumeric($tel,1,40)||$tel==null) && (is_alphanumeric($ctx,1,12)||$ctx==null))){
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('Recuerde que todos los datos solo pueden ser alfanuméricos')");
      }else{
        // email valido
        if(!is_email($email)){
          $respuesta=new xajaxResponse();
          $respuesta->script("alert('Ingrese una dirección de correo eletrónico válida')");
        }else{
          // verifico que el nombre de contacto no exista en la base
          $cantidad = $conexion->GetRow("SELECT Count(*) FROM Contacto WHERE NombreContacto LIKE '$nombre'");
          if($cantidad['Count(*)']>0){
            // si existe uno, verifico que el id sea distinto -> cambio nombre e ingresó uno existente
            $contacto = $conexion->GetRow("SELECT IdContacto FROM Contacto WHERE NombreContacto LIKE '$nombre'");
            if($id_contacto!=$contacto['IdContacto']){
              // el nuevo nombre de contacto fue utilizado previamente
              $respuesta=new xajaxResponse();
              $respuesta->script("alert('Ya existe un Contacto con ese Nombre')");
            }else{
              // el nombre de contacto no fue modificado
              $sql = "UPDATE Contacto SET EMailContacto='$email',
                                      TelefonoContacto='$tel',
                                      Centrex='$ctx' WHERE IdContacto=$id_contacto";
              if($conexion->Execute($sql)){
                $respuesta=new xajaxResponse();
                $respuesta->script("alert('El Contacto fue modificado')");
                $respuesta->script("xajax_form_buscar_contacto(1);");
              }else{
                $respuesta=new xajaxResponse();
                $respuesta->script("alert('Se produjo un error al guardar el Contacto, intentelo nuevamente')");
                $respuesta->script("xajax_form_modificar_contacto('$id_contacto');");
              }
            }
          }else{
            // el nuevo nombre de contacto no coincide con ninguno en la base, se puede guardar
            $sql = "UPDATE Contacto SET NombreContacto='$nombre',
                                    EMailContacto='$email',
                                    TelefonoContacto='$tel',
                                    Centrex='$ctx' WHERE IdContacto=$id_contacto";
            if($conexion->Execute($sql)){
              registrar_log_abm(3,1,$id_contacto,$nombre);
              $respuesta=new xajaxResponse();
              $respuesta->script("alert('El Contacto fue modificado')");
              $respuesta->script("xajax_form_buscar_contacto(1);"); 
            }else{
              $respuesta=new xajaxResponse();
              $respuesta->script("alert('Se produjo un error al guardar el Contacto, intentelo nuevamente')");
              $respuesta->script("xajax_form_modificar_contacto('$id_contacto');");
            } 
          } // end if cantidad mayor a 0
        } // email valido
      } // datos alfanumericos
    } // end if mail, tel o centrex
  } // end if principal
return $respuesta;
}


////////////////////////////////////////////////////
// Gestion de Usuarios
////////////////////////////////////////////////////


function form_lista_usuario_activo($pagina){
  global $conexion;
  $html = "";
  $cantidad = $conexion->GetRow("SELECT Count(*) FROM Usuario WHERE IdTipoUsuario IS NOT NULL");
  $limite = 10;
  $inicio = ($pagina-1)*10;
  $sql = "SELECT Nick,Nombre,Apellido FROM Usuario WHERE Nick!='root' AND IdTipoUsuario IS NOT NULL ORDER BY Nombre LIMIT $inicio,$limite";
  $consulta = $conexion->Execute($sql);
  if($consulta){
    set_file("listado","form_usuarios_activos.html");
    foreach($consulta as $usuario){
      set_var("nick",$usuario['Nick']);
      set_var("nombre",cortar_palabra($usuario['Nombre'],20));
      set_var("apellido",cortar_palabra($usuario['Apellido'],26));
      parse("cargar_usuarios");
    }
    unset($consulta);
    if($pagina==1){
      set_var("prev","");
      set_var("pagina_prev","");
    }else{
      set_var("prev","Anterior");
      set_var("pagina_prev",($pagina-1));
    }
    set_var("pagina",$pagina);
    if($cantidad['Count(*)']>($pagina*10)){
      set_var("prox","Proxima");
      set_var("pagina_prox",($pagina+1));
    }else{
      set_var("prox","");
      set_var("pagina_prox","");
    }
    set_var("via_link","admin.php");
    set_var("via_ajax","");
    $html = gparse("listado");
  }else{
    set_file("info","infoajax.html");
    set_var("mensaje","Se produjo un error al procesar su consulta, intentelo nuevamente");
    set_var("via_link","admin.php");
    set_var("via_ajax","");
    $html = gparse("info");
  }
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function filtrar_usuarios($username,$surname){
  // vuelve a cargar el listado de usuarios segun los filtros ingresados
  if(!(empty($username) && empty($surname))){
    global $conexion;
    $html="";
    $bandera = false;
    set_file("listado","listado_usuarios.html");
    if(empty($surname)){
      $aux = "%".$username."%";
      $cantidad = $conexion->GetRow("SELECT Count(*) FROM Usuario WHERE Nick!='root' AND Nick LIKE '$aux' AND IdTipoUsuario IS NOT NULL");
      if($cantidad['Count(*)']>0){
        $consulta = $conexion->Execute("SELECT * FROM Usuario WHERE Nick!='root' AND Nick LIKE '$aux' AND IdTipoUsuario IS NOT NULL");
        $bandera = true;
      }
    }else{
      if(empty($username)){
        $aux = "%".$surname."%";
        $cantidad = $conexion->GetRow("SELECT Count(*) FROM Usuario WHERE Apellido LIKE '$aux' AND IdTipoUsuario IS NOT NULL");
        if($cantidad['Count(*)']>0){
          $consulta = $conexion->Execute("SELECT * FROM Usuario WHERE Apellido LIKE '$aux' AND IdTipoUsuario IS NOT NULL");
          $bandera = true;
        }
      }else{
        $aux1 = "%".$username."%";
        $aux2 = "%".$surname."%";
        $cantidad = $conexion->GetRow("SELECT Count(*) FROM Usuario WHERE Nick LIKE '$aux1' AND Apellido LIKE '$aux2' AND IdTipoUsuario IS NOT NULL");
        if($cantidad['Count(*)']>0){
          $consulta = $conexion->Execute("SELECT * FROM Usuario WHERE Nick LIKE '$aux1' AND Apellido LIKE '$aux2' AND IdTipoUsuario IS NOT NULL");
          $bandera = true;
        }
      }
    }
    if($bandera){
      foreach($consulta as $usuario){
      set_var("nick",$usuario['Nick']);
      set_var("nombre",cortar_palabra($usuario['Nombre'],20));
      set_var("apellido",cortar_palabra($usuario['Apellido'],26));
      parse("cargar_usuarios");
      }
      unset($consulta);
      set_var("prev","");
      set_var("pagina_prev","");
      set_var("pagina","");
      set_var("prox","");
      set_var("pagina_prox","");
      set_var("via_link","admin.php");
      set_var("via_ajax","");
    }else{
      set_var("nick","");
      set_var("nombre","No existe ningún Usuario Activo que coincida con los datos ingresados");
      set_var("apellido","Verifique si no se encuentra Inactivo");
      parse("cargar_usuarios");
      set_var("prev","");
      set_var("pagina_prev","");
      set_var("pagina","");
      set_var("prox","");
      set_var("pagina_prox","");
    }  
    $html=gparse("listado");
    $respuesta=new xajaxResponse();
    $respuesta->assign("listado_usuarios","innerHTML",$html);
  }else{
    $respuesta=new xajaxResponse();
    $respuesta->script("xajax_form_lista_usuario_activo(1);");
  }
  return $respuesta;
}

function form_lista_usuario_inactivo(){
  global $conexion;
  $html = "";
  $cantidad = $conexion->GetRow("SELECT Count(*) FROM Usuario WHERE IdTipoUsuario IS NULL");
  if($cantidad['Count(*)']!=0){
    $sql = "SELECT Nick,Nombre,Apellido FROM Usuario WHERE IdTipoUsuario IS NULL";
    $consulta = $conexion->Execute($sql);
    if($consulta){
      set_file("listado","form_usuarios_inactivos.html");
      foreach($consulta as $usuario){
        set_var("nick",$usuario['Nick']);
        set_var("nombre",cortar_palabra($usuario['Nombre'],20));
        set_var("apellido",cortar_palabra($usuario['Apellido'],26));
        parse("cargar_usuarios");
      }
      set_var("via_link","admin.php");
      set_var("via_ajax","");
      $html = gparse("listado");
    }else{
      set_file("info","infoajax.html");
      set_var("mensaje","Se produjo un error al procesar su consulta, intentelo nuevamente");
      set_var("via_link","admin.php");
      set_var("via_ajax","");
      $html = gparse("info");
    }
  }else{
    // no hay usuarios inactivos
    set_file("info","infoajax.html");
    set_var("mensaje","No se registran Usuarios Inactivos");
    set_var("via_link","admin.php");
    set_var("via_ajax","");
    $html = gparse("info");
  }
  $respuesta=new xajaxResponse();
  $respuesta->assign("divcontenido","innerHTML",$html);
  return $respuesta;
}

function desactivar_usuario($nick){
  global $conexion;
  $html = "";
  // validacion previa
  $usr_actual = $_SESSION["usuario"];
  if($nick==$usr_actual){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('No puede desactivar su propio usuario, solicite este movimiento a otro Administrador!')");
    return $respuesta;
  }
  // fin validacion
  if(!empty($nick)){
    $tipo_ant = $conexion->GetRow("SELECT IdTipoUsuario FROM Usuario WHERE Nick='$nick'");
    $sql = "UPDATE Usuario SET IdTipoUsuario=null WHERE Nick='$nick'";
    $ok = $conexion->Execute($sql);
    if($ok){
      registrar_log_abm_usr(3,$nick,$tipo_ant['IdTipoUsuario'],0);
      $respuesta=new xajaxResponse();
      $respuesta->script("alert('El usuario fue desactivado con exito')");
      $respuesta->script("xajax_form_lista_usuario_activo(1);");
    }else{
      set_file("info","infoajax.html");
      set_var("mensaje","Error al desactivar usuario, intentelo nuevamente".$conexion->ErrorMsg());
      set_var("via_link","#");
      set_var("via_ajax","onclick='xajax_form_lista_usuario_activo(1);'");
      $html = gparse("info");
      $respuesta=new xajaxResponse();
      $respuesta->assign("divcontenido","innerHTML",$html);
    }
  }else{
    // mensaje de error
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Debe seleccionar un Usuario')");
  }
  return $respuesta;
}

function eliminar_usuario($nick){
  global $conexion;
  $html = "";

  // validacion previa
  $usr_actual = $_SESSION["usuario"];
  if($nick==$usr_actual){
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('No puede eliminar su propio usuario, solicite este movimiento a otro Administrador!')");
    return $respuesta;
  }
  // fin validacion

  if(!empty($nick)){
    $msg = "";
    $cantidad = $conexion->GetRow("SELECT Count(*) FROM Usuario WHERE Nick='$nick'");
    if($cantidad['Count(*)']==1){
      // lo elimino
      $tipo = $conexion->GetRow("SELECT IdTipoUsuario FROM Usuario WHERE Nick='$nick'");
      $sql = "DELETE FROM Usuario WHERE Nick='$nick'";
      $ok = $conexion->Execute($sql);
      if($ok){
        registrar_log_abm_usr(2,$nick,$tipo['IdTipoUsuario'],$tipo['IdTipoUsuario']);
        $respuesta=new xajaxResponse();
        $respuesta->script("alert('El Usuario fue eliminado con exito')");
        $respuesta->script("xajax_form_lista_usuario_activo(1);");
      }else{
        $msg = "Se produjo un error al eliminar el usuario".$conexion->ErrorMsg();
      }
    }else{
      $msg = "No se encontro o se encontrarion varios Usuarios con ese Nick".$cantidad['Count(*)'];
    }
    if(!empty($msg)){
      set_file("info","infoajax.html");
      set_var("mensaje",$msg);
      set_var("via_link","#");
      set_var("via_ajax","onclick='xajax_form_lista_usuario_activo(1);'");
      $html = gparse("info");
      $respuesta=new xajaxResponse();
      $respuesta->assign("divcontenido","innerHTML",$html);
    }
  }else{
    // mensaje de error
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Debe seleccionar un Usuario')");
  }
  return $respuesta;
}

function eliminar_usuario_inactivo($nick){
  global $conexion;
  $html = "";
  if(!empty($nick)){
    $msg = "";
    $cantidad = $conexion->GetRow("SELECT Count(*) FROM Usuario WHERE Nick='$nick'");
    if($cantidad['Count(*)']==1){
      // lo elimino
      $tipo = $conexion->GetRow("SELECT IdTipoUsuario FROM Usuario WHERE Nick='$nick'");
      $sql = "DELETE FROM Usuario WHERE Nick='$nick'";
      $ok = $conexion->Execute($sql);
      if($ok){
        registrar_log_abm_usr(2,$nick,$tipo['IdTipoUsuario'],$tipo['IdTipoUsuario']);
        $msg = "El Usuario fue eliminado con exito";
      }else{
        $msg = "Se produjo un error al eliminar el usuario".$conexion->ErrorMsg();
      }
    }else{
      $msg = "No se encontro o se encontrarion varios Usuarios con ese Nick".$cantidad['Count(*)'];
    }   
    set_file("info","infoajax.html");
    set_var("mensaje",$msg);
    set_var("via_link","admin.php");
    set_var("via_ajax","");
    $html = gparse("info");
    $respuesta=new xajaxResponse();
    $respuesta->assign("divcontenido","innerHTML",$html);    
  }else{
    // mensaje de error
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Debe seleccionar un Usuario')");
  }
  return $respuesta;
}

function form_modificar_usuario($nick){
  global $conexion;
  $html="";
  if(!empty($nick)){
    $sql="SELECT * FROM Usuario WHERE Nick='$nick'";
    $datos_usuario=$conexion->GetRow($sql);
    if($datos_usuario){
      set_file("form_usuario","form_modificar_usuario.html");
      set_var("nick",$nick);
      set_var("nombre",$datos_usuario['Nombre']);
      set_var("apellido",$datos_usuario['Apellido']);
      set_var("domicilio",$datos_usuario['Domicilio']);
      set_var("telefono",$datos_usuario['Telefono']);
      set_var("celular",$datos_usuario['Celular']);
      set_var("mail",$datos_usuario['EMail']);
      set_var("nrodoc",$datos_usuario['NroDocumento']);
      $fecha = fecha_normal($datos_usuario['FeNacimiento']);
      set_var("fenac",$fecha);
      set_var("pass","");
      set_var("error","");
      $consulta = $conexion->Execute("SELECT * FROM TipoDocumento");
      foreach($consulta as $tipo_doc){
        set_var("id_tipo_doc",$tipo_doc['IdTipoDoc']);
        set_var("tipo_doc",$tipo_doc['ValorTipoDoc']);
        set_var("selected","");
        if($tipo_doc['IdTipoDoc']==$datos_usuario['IdTipoDoc']){set_var("selected","selected=selected");}
        parse("cargar_tipo_doc");
      }
      unset($consulta);
      // llena el select_sexo
      $consulta = $conexion->Execute("SELECT * FROM Sexo");
      foreach($consulta as $sexo){
        set_var("id_sexo",$sexo['IdSexo']);
        set_var("sexo",$sexo['Sexo']);
        set_var("selected2","");
        if($sexo['IdSexo']==$datos_usuario['IdSexo']){set_var("selected2","selected=selected");}
        parse("cargar_sexo");
      }
      unset($consulta);
      $consulta = $conexion->Execute("SELECT * FROM TipoUsuario");
      foreach($consulta as $tipo_usuario){
        set_var("id_tipo_usuario",$tipo_usuario['IdTipoUsuario']);
        set_var("tipo_user",$tipo_usuario['TipoUsuario']);
        set_var("selected3","");
        if($tipo_usuario['IdTipoUsuario']==$datos_usuario['IdTipoUsuario'])
          {set_var("selected3","selected=selected");}
        parse("cargar_tipo_usuario");
      }
      unset($consulta);
      $html=gparse("form_usuario");
      }else{
        set_file("info","infoajax.html");
        $msj = "Se produjo un error al cargar los datos, por favor intentelo nuevamente";
        set_var("mensaje",$msj);
        set_var("via_link","#");
        set_var("via_ajax","onclick='xajax_form_lista_usuario_activo(1);'");
        $html=gparse("info");
      }
      $respuesta=new xajaxResponse();
      $respuesta->assign("medio","innerHTML",$html);
  }else{
    // mensaje de error
    $respuesta=new xajaxResponse();
    $respuesta->script("alert('Debe seleccionar un Usuario')");
  }
return $respuesta;
}

function modificar_usuario($form){
global $conexion;
$nick = $form['nick'];
$nombre = $form['nombre'];
$apellido = $form['apellido'];
$tipodoc = $form['select_tipo_doc'];
$nrodoc = $form['nrodocumento'];
$sexo = $form['select_sexo'];
$fenac = $form['fe_nac'];
$dom = $form['domicilio'];
$tel = $form['telefono'];
$cel = $form['celular'];
$email = $form['email'];
$pass1 = $form['password'];
$tipo_usuario = $form['select_tipo_usuario'];
$bandera = false; // si se guarda lo pasa a true
$msj = "";

// verificamos que no se haya perdido el nick
if($nick != null){
  // verificamos que haya algo en los campos obligatorios
  if(($nombre!=null)&&($apellido!=null)&&($nrodoc!=null)&&($fenac!=null)&&($email!=null)){
    // confirmamos que haya un tel o un cel
    if(is_alphanumeric($tel,1,20) || is_alphanumeric($cel,1,20)){
      // verificamos si nombre y apellido validos
      if(is_clean_text($nombre,1,50) && is_clean_text($apellido,1,30)){
        // verificamos si fecha valida
        if(fecha_valida($fenac)){
          // si hay algo en domicilio verificamos que no sea basura
          if(is_alphanumeric($dom,0,60) || $dom==null){
            // verificamos el documento
            if(is_numerico($nrodoc,6,8)){
              // verificamos qeu el nuevo dni no coincida con el de otro usuario
              $cant = $conexion->GetRow("SELECT Count(*) FROM Usuario WHERE Nick!='$nick' AND NroDocumento='$nrodoc'");
              if($cant['Count(*)']==0){
                  // verificamos si el mail es valido
                  if(is_email($email)){
                    // cambio de pass o no
                    if($pass1!=null){
                      if(is_alphanumeric($pass1,5,20)){
                        $pass1 = sha1($pass1);
                        $fenac = fecha_mysql($fenac);
                        $sql = "UPDATE Usuario SET Password='$pass1',
                                                Nombre='$nombre',
                                                Apellido='$apellido',
                                                NroDocumento='$nrodoc',
                                                FeNacimiento='$fenac',
                                                Domicilio='$dom',
                                                Telefono='$tel',
                                                Celular='$cel',
                                                EMail='$email',
                                                IdTipoDoc='$tipodoc',
                                                IdSexo='$sexo',
                                                IdTipoUsuario='$tipo_usuario' WHERE Nick='$nick'";
                        $ok = $conexion->Execute($sql);
                        if($ok){
                          registrar_log_abm_usr(3,$nick,$tipo_ant['IdTipoUsuario'],$tipo_usuario);
                          $bandera = true;
                          $msj = "El usuario se ha modificado correctamente";
                        }else {$msj = "Se produjo un error inesperado al guardar los datos, vuelva a intentarlo más tarde";}
                      }else{$msj = "La contraseña debe ser alfanumerica y debe contener entre 5 y 20 dígitos";}
                    }else{
                      $fenac = fecha_mysql($fenac);
                      $sql = "UPDATE Usuario SET Nombre='$nombre',
                                              Apellido='$apellido',
                                              NroDocumento='$nrodoc',
                                              FeNacimiento='$fenac',
                                              Domicilio='$dom',
                                              Telefono='$tel',
                                              Celular='$cel',
                                              EMail='$email',
                                              IdTipoDoc='$tipodoc',
                                              IdSexo='$sexo',
                                              IdTipoUsuario='$tipo_usuario' WHERE Nick='$nick'";
                      $ok = $conexion->Execute($sql);
                      if($ok){
                        registrar_log_abm_usr(3,$nick,$tipo_ant['IdTipoUsuario'],$tipo_usuario);
                        $bandera = true;
                        $msj = "El usuario se ha modificado correctamente";
                      }else{$msj = "Se produjo un error inesperado al guardar los datos,vuelva a intentarlo mas tarde";}
                    }
                  }else{$msj = "La dirección de correo electrónico ingresada es inválida";}
                }else{ $msj = "Ya existe otro usuario con ese número de documento. Verifique si lo ingreso correctamente.";}
              }else{$msj = "El Número de Documento ingresado es incorrecto";}
            }else{$msj = "El Domicilio solo puede contener datos alfanuméricos";}
          }else{$msj = "Debe ingresar una fecha válida, menor a la actual y con formato dd/mm/aaaa";}
        }else{$msj = "El Nombre y Apellido solo pueden contener caracteres alfabéticos";}
      }else{$msj = "Debe registrar un teléfono o un celular. Los datos ingresados deben ser alfanuméricos.";}
    }else{$msj = "Debe completar todos los campos marcados con (*)";}
  }else{$msj = "Ha ocurrido un error al procesar la información, vuelva a intentarlo mas tarde";}

if($bandera){
  set_file("info","infoajax.html");
  set_var("mensaje",$msj);
  set_var("via_link","admin.php");
  set_var("via_ajax","");
  $html=gparse("info");
  $respuesta=new xajaxResponse();
  $respuesta->assign("medio","innerHTML",$html);
}else{
  // mensaje de error
  $respuesta=new xajaxResponse();
  $respuesta->script("alert('$msj')");
}

return $respuesta;
}

////////////////////////////////////////////////////
// Procesar la solicitud
////////////////////////////////////////////////////

$ajax->processRequest();
?>
