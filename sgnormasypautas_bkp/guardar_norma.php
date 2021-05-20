<?php
require_once("template.php");
require_once("editar_fecha.php");
require_once("validacion.php");
require_once("conexion.php");
require_once("seguridad2.php");
require_once("registrar_log.php");
require_once("funciones.admin.ajax.php");

// variables globales
$numero = $_REQUEST['numero'];
$fecha = $_REQUEST['fecha'];
$titulo = $_REQUEST['titulo_norma'];
$descrip = $_REQUEST['descrip'];
$idnivel = $_REQUEST['select_nivel'];
$idtema = $_REQUEST['select_tema'];
$urldoc = "";
$bandera = true; // si hay error pasa a false
$mensaje = "";
$id_ant = "";

// verificamos que se haya ingresado dede guardar
if(isset($_REQUEST['guardar'])){
  // verificamos que haya datos en los campos obligatorios
  if(($fecha!=null)&&($titulo!=null)&&($descrip!=null)){
    // verificamos que haya seleccionado tema y nivel
    if(($idnivel!=-1)&&($idtema!=-1)){
      // verificamos que la fecha sea valida
      if(fecha_valida($fecha)){
        // validamos el titulo de la norma
        if(is_alphanumeric($titulo,1,60)){
          // validamos la descripcion
          if(is_alphanumeric($descrip,1,0)){
            // validamos el numero de la norma
            if(is_numerico($numero,1,11) || $numero==null){
              if($numero==null){$numero=-1;}
              $anio = get_anio($fecha);
              $anio = $anio."%";
              $sql = "";
              if($numero!=-1){
                $sql = "SELECT Count(*) FROM Norma WHERE IdNorma!='$id' AND NroNorma='$numero' AND FeNorma LIKE '$anio'";
              }else{
                $sql = "SELECT Count(*) FROM Norma WHERE IdNorma!='$id' AND TituloNorma='$titulo' AND FeNorma LIKE '$anio'";
              }
              $cant = $conexion->GetRow($sql);
              if($cant['Count(*)']==0){
                // guardar norma
                // genero un id para la norma
                $id = $conexion->GenID("SeqNorma");
                $id_ant = $id-1;
                // si tiene documento debo subirlo
                if(is_uploaded_file($_FILES['doc_norma']['tmp_name'])) {
                  $extension=explode(".",$_FILES['doc_norma']['name'],2);
                  if(!strcmp($extension[1],"pdf")){
                    $nombre="./docs/normas/norma".$id.".".$extension[1];
                    if(!copy($_FILES['doc_norma']['tmp_name'],$nombre)){
                      // no se pudo guardar el documento
                      $bandera = false;
                      $conexion->Execute("UPDATE SeqNorma SET id=$id_ant WHERE id=$id");
                      $mensaje = "Error al guardar el Documento, vuelva a intentarlo";
                    }else{
                      // el docuemento se guardo correctamente
                      $urldoc = "./docs/normas/norma".$id.".".$extension[1];
                    }
                  }else{
                    // el doc no es un archivo pdf
                    $bandera = false;
                    $conexion->Execute("UPDATE SeqNorma SET id=$id_ant WHERE id=$id");
                    $mensaje = "El Documento debe estar en formato PDF";
                  }
                }else{
                  $urldoc = null;
                }
              }else{
                // norma duplicada
                $bandera = false;
                $mensaje = "Ya existe una Norma con el mismo Número y Año. Si no tiene número el Título esta duplicado.";
              }
            }else{
              // el numero de la norma no es valido
              $bandera = false;
              $mensaje = "El Numeró de Norma deberá ser un entero positivo con un máximo de 11 dígitos";
            }
          }else{
            // descripcion no valida
          $bandera = false;
          $mensaje = "La Descripción de la Norma solo puede contener datos alfanumericos";
          }
        }else{
          // titulo no valido
          $bandera = false;
          $mensaje = "El Título de la Norma solo puede contener datos alfanumericos. Además debe tener un máximo de 60 caracteres";
        }
      }else{
        // fecha no valida
        $bandera = false;
        $mensaje = "Debe ingresar una fecha válida, menor a la actual y con formato dd/mm/aaaa";
      }
    }else{
      // no selecciono nivel o tema
      $bandera = false;
      $mensaje = "Debe seleccionar un Nivel y un Tema";
    }
  }else{
    // no completo fecha, titulo o descripción
    $bandera = false;
    $mensaje = "Debe Completar los campos con (*)";
  }
}else{
  // no presiono el botón guardar
  $bandera = false;
  $mensaje = "Error al procesar la información, intentelo nuevamente";
}

$ok = true; // si falla pasara a false
if($bandera){
  // guardamos la norma
  $owner = $_SESSION["usuario"];
  $fecha_new = fecha_mysql($fecha);
  if($urldoc!=null){
    $sql = "INSERT INTO Norma VALUES ($id,'$numero','$fecha_new','$titulo','$descrip','$urldoc','$owner',$idtema,$idnivel)";
  }else{
    $sql = "INSERT INTO Norma VALUES ($id,'$numero','$fecha_new','$titulo','$descrip',null,'$owner','$idtema','$idnivel')";
  }
  $ok = $conexion->Execute($sql);
  if($ok){
    registrar_log_abm(1,3,$id,$titulo);
    set_file("salida","info_opcion.html");
    set_var("javascript",$ajax->getJavascript("./"));
    set_var("mensaje","La Norma se cargo Correctamente");
    set_var("opcion", "Completar Información sobre la Nueva Norma");
    set_var("via_link2","#");
    set_var("via_ajax2","onclick='xajax_form_norma($id);'");
    set_var("via_link","admin.php");
    set_var("via_ajax","");
  }else{
    $conexion->Execute("UPDATE SeqNorma SET id=$id_ant WHERE id=$id");
    $mensaje = "Error al procesar la información, intentelo nuevamente".$conexion->ErrorMsg();
  }
}

if((!$bandera)||(!$ok)){
  // si no se pudo guardar o hubo un error volvemos al form de alta nomra
  set_file("salida","form_agregar_norma_completo.html");
  set_var("javascript",$ajax->getJavascript("./"));
  set_var("error",$mensaje);
  $consulta = $conexion->Execute("SELECT * FROM Nivel");
  if($idnivel==-1){
    set_var("id_nivel",-1);
    set_var("nivel","Seleccione un Nivel");
    set_var("selected1","selected=selected");
    parse("cargar_nivel");
    foreach($consulta as $nivel){
      set_var("id_nivel",$nivel['IdNivel']);
      set_var("nivel",$nivel['Nivel']);
      set_var("selected1","");
      parse("cargar_nivel");
    }
  }else{
    foreach($consulta as $nivel){
      set_var("id_nivel",$nivel['IdNivel']);
      set_var("nivel",$nivel['Nivel']);
      if($nivel['IdNivel']==$idnivel){set_var("selected1","selected=selected");}
      else{set_var("selected1","");}
      parse("cargar_nivel");
    }
  }
  unset($consulta);
  $consulta = $conexion->Execute("SELECT * FROM Tema");  
  if($idtema==-1){
    set_var("id_tema",-1);
    set_var("tema","Seleccione un Tema");
    set_var("selected2","selected=selected");
    parse("cargar_tema");
    foreach($consulta as $tema){
      set_var("id_tema",$tema['IdTema']);
      set_var("tema",$tema['Tema']);
      set_var("selected2","");
      parse("cargar_tema");
    }
  }else{
    foreach($consulta as $tema){
      set_var("id_tema",$tema['IdTema']);
      set_var("tema",$tema['Tema']);
      if($tema['IdTema']==$idtema){set_var("selected2","selected=selected");}
      else{set_var("selected2","");}
      parse("cargar_tema");
    }
  }
  unset($consulta);
  if($numero==-1){set_var("nro_norma","");}
  else{set_var("nro_norma",$numero);}
  set_var("fe_norma",$fecha);
  set_var("titu_norma",$titulo);
  set_var("descrip_norma",$descrip);
}


pparse("salida");
?>