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
$id = $_REQUEST['idnorma'];
$urldoc = "";
$owner = $_SESSION["usuario"];
$bandera = true; // por error se pone en false
$mensaje = "";
set_file("info","info_opcion.html");
set_var("javascript",$ajax->getJavascript("./"));

// verificamos se presiono el boton modificar
if(isset($_REQUEST['modificar'])){
  // verificamos si hay algo en los campos obligatorios
  if(($fecha!=null)&&($titulo!=null)&&($descrip!=null)){
    // verificamos que la fecha sea valida
    if(fecha_valida($fecha)){
      $fecha = fecha_mysql($fecha);
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
              // si tiene documento debo subirlo
              if(is_uploaded_file($_FILES['doc_norma']['tmp_name'])) {
                $extension=explode(".",$_FILES['doc_norma']['name'],2);
                if(!strcmp($extension[1],"pdf")){
                  $urldoc="./docs/normas/norma".$id.".".$extension[1];
                  if(!copy($_FILES['doc_norma']['tmp_name'],$urldoc)){
                    // no se pudo guardar el documento
                    set_var("mensaje","Error al guardar el Documento, vuelva a intentarlo");
                    set_var("opcion", "Volver a Intentarlo");
                    set_var("via_link2","#");
                    set_var("via_ajax2","onclick='xajax_form_norma($id);'");
                    set_var("via_link","admin.php");
                    set_var("via_ajax","");
                  }else{
                    // el docuemento se guardo correctamente
                    $sql = "UPDATE Norma SET NroNorma='$numero',
                                        FeNorma='$fecha',
                                        TituloNorma='$titulo',
                                        DescripcionNorma='$descrip',
                                        DocNorma='$urldoc',
                                        Owner='$owner',
                                        IdNivel='$idnivel',
                                        IdTema='$idtema' WHERE IdNorma=$id";
                    $ok = $conexion->Execute($sql);
                    if($ok){
                      registrar_log_abm(3,3,$id,$titulo);
                      set_var("mensaje","La Norma se modifico Correctamente");
                      set_var("opcion", "Completar Información sobre la Nueva Norma");
                      set_var("via_link2","#");
                      set_var("via_ajax2","onclick='xajax_form_norma($id);'");
                      set_var("via_link","admin.php");
                      set_var("via_ajax","");
                    }else{
                      $bandera = false;
                      $mensaje = "Error al procesar la información, intentelo nuevamente".$conexion->ErrorMsg();
                    }
                  }
                }else{
                  // el doc no es un archivo pdf
                  $bandera = false;
                  $mensaje = "El Documento debe estar en formato PDF";
                }
              }else{
                $sql = "UPDATE Norma SET NroNorma='$numero',
                                        FeNorma='$fecha',
                                        TituloNorma='$titulo',
                                        DescripcionNorma='$descrip',
                                        Owner='$owner',
                                        IdNivel='$idnivel',
                                        IdTema='$idtema' WHERE IdNorma=$id";
                $ok = $conexion->Execute($sql);
                if($ok){
                  registrar_log_abm(3,3,$id,$titulo);
                  set_var("mensaje","La Norma se modifico Correctamente");
                  set_var("opcion", "Completar Información sobre la Nueva Norma");
                  set_var("via_link2","#");
                  set_var("via_ajax2","onclick='xajax_form_norma($id);'");
                  set_var("via_link","admin.php");
                  set_var("via_ajax","");
                }else{
                  $bandera = false;
                  $mensaje = "Error al procesar la información, intentelo nuevamente".$conexion->ErrorMsg();
                }
              }
            }else{
              // dato duplicado
              $bandera = false;
              $mensaje = "Ya existe una Norma con el mismo Número y Año. Si no tiene número el Título esta duplicado.";
            }
          }else{
            // el numero de la norma no es valido
            $bandera = false;
            $mensaje = "El Numeró de Norma deberá ser un entero positivo";
          }
        }else{
          // descripcion no valida
          $bandera = false;
          $mensaje = "La Descripción de la Norma solo puede contener datos alfanumericos";
        }
      }else{
        // el titulo no es valido
        $bandera = false;
        $mensaje = "El Título de la Norma solo puede contener datos alfanumericos. Además debe tener un máximo de 60 caracteres";
      }
    }else{
      // la fecha no es valida
      $bandera = false;
      $mensaje = "Debe ingresar una fecha válida, menor a la actual y con formato dd/mm/aaaa";
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

if(!$bandera){
  set_var("mensaje",$mensaje);
  set_var("opcion", "Completar Información sobre la Nueva Norma");
  set_var("via_link2","#");
  set_var("via_ajax2","onclick='xajax_form_norma($id);'");
  set_var("via_link","admin.php");
  set_var("via_ajax","");
}

pparse("info");
?>