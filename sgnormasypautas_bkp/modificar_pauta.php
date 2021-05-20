<?php
require_once("template.php");
require_once("editar_fecha.php");
require_once("validacion.php");
require_once("conexion.php");
require_once("seguridad2.php");
require_once("registrar_log.php");
require_once("funciones.admin.ajax.php");

if(isset($_REQUEST['modificar'])){
  $fecha = $_REQUEST['fecha'];
  $nombre_pauta = $_REQUEST['nombre_pauta'];
  $idtema = $_REQUEST['select_tema'];
  $id = $_REQUEST['idpauta'];
  $urldoc = "";
  $owner = $_SESSION["usuario"];
  set_file("info","info_opcion.html");
  set_var("javascript",$ajax->getJavascript("./"));
  if(($nombre_pauta!=null)&&($fecha!=null)){
    // verificamos que la fecha sea valida
    if(fecha_valida($fecha)){
      // nombre de la pauta alfanumerico
      $fecha = fecha_mysql($fecha);
      if(is_alphanumeric($nombre_pauta,1,100)){
        $cant = $conexion->GetRow("SELECT Count(*) FROM Pauta WHERE NombrePauta='$nombre_pauta' AND IdTema='$idtema' AND IdPauta!='$id'");
        if($cant['Count(*)']==0){
          // guardar pauta
          // si tiene documento debo subirlo
          if(is_uploaded_file($_FILES['doc_pauta']['tmp_name'])){
            $extension=explode(".",$_FILES['doc_pauta']['name'],2);
            if(!strcmp($extension[1],"pdf")){
              $urldoc = "./docs/pautas/pauta".$id.".".$extension[1];
              if(!copy($_FILES['doc_pauta']['tmp_name'],$urldoc)){
                // no se pudo guardar el documento
                set_var("mensaje","Error al guardar el Documento, vuelva a intentarlo");
                set_var("opcion", "Completar Información sobre la Pauta");
                set_var("via_link2","#");
                set_var("via_ajax2","onclick='xajax_form_pauta($id);'");
                set_var("via_link","admin.php");
                set_var("via_ajax","");
              }else{
                // el docuemento se guardo correctamente
                $sql = "UPDATE Pauta SET FePauta='$fecha',
                                    NombrePauta='$nombre_pauta',
                                    DocPauta='$urldoc',
                                    IdTema='$idtema',
                                    Owner='$owner' WHERE IdPauta=$id";
                $ok = $conexion->Execute($sql);
                if($ok){
                  registrar_log_abm(3,4,$id,$nombre_pauta);
                  set_var("mensaje","La Pauta se modifico Correctamente");
                  set_var("opcion", "Completar Información sobre la Pauta");
                  set_var("via_link2","#");
                  set_var("via_ajax2","onclick='xajax_form_pauta($id);'");
                  set_var("via_link","admin.php");
                  set_var("via_ajax","");
                }else{
                  set_var("mensaje","Error al procesar la información, intentelo nuevamente".$conexion->ErrorMsg());
                  set_var("opcion", "Volver a intentar modificar la Pauta");
                  set_var("via_link2","#");
                  set_var("via_ajax2","onclick='xajax_form_pauta($id);'");
                  set_var("via_link","admin.php");
                  set_var("via_ajax","");
                }
              }
            }else{
              // el doc no es un archivo pdf
              set_var("mensaje","El Documento debe estar en formato PDF");
              set_var("opcion", "Volver a intentar modificar la Pauta");
              set_var("via_link2","#");
              set_var("via_ajax2","onclick='xajax_form_pauta($id);'");
              set_var("via_link","admin.php");
              set_var("via_ajax","");
            }
          }else{
            $sql = "UPDATE Pauta SET FePauta='$fecha',
                                    NombrePauta='$nombre_pauta',
                                    IdTema='$idtema',
                                    Owner='$owner' WHERE IdPauta=$id";
            $ok = $conexion->Execute($sql);
            if($ok){
              registrar_log_abm(3,4,$id,$nombre_pauta);
              set_var("mensaje","La Pauta se modifico Correctamente");
              set_var("opcion", "Completar Información sobre la Pauta");
              set_var("via_link2","#");
              set_var("via_ajax2","onclick='xajax_form_pauta($id);'");
              set_var("via_link","admin.php");
              set_var("via_ajax","");
            }else{
              set_var("mensaje","Error al procesar la información, intentelo nuevamente".$conexion->ErrorMsg());
              set_var("opcion", "Volver a intentar modificar la Pauta");
              set_var("via_link2","#");
              set_var("via_ajax2","onclick='xajax_form_pauta($id);'");
              set_var("via_link","admin.php");
              set_var("via_ajax","");
            }
          }
        }else{
          // nombre y tema duplicado
          set_var("mensaje","Ya existe una Pauta con el mismo Nombre y Tema");
          set_var("opcion", "Volver a intentar modificar la Pauta");
          set_var("via_link2","#");
          set_var("via_ajax2","onclick='xajax_form_pauta($id);'");
          set_var("via_link","admin.php");
          set_var("via_ajax","");
        }
      }else{
        // error en el nombre de la pauta
        set_var("mensaje","El Nombre de la Pauta solo puede contener datos alfanumericos. Además debe tener un máximo de 100 caracteres");
        set_var("opcion", "Volver a intentar modificar la Pauta");
        set_var("via_link2","#");
        set_var("via_ajax2","onclick='xajax_form_pauta($id);'");
        set_var("via_link","admin.php");
        set_var("via_ajax","");
      }
    }else{
      // fecha no valida
      set_var("mensaje","Debe ingresar una fecha válida, menor a la actual y con formato dd/mm/aaaa");
      set_var("opcion", "Volver a intentar modificar la Pauta");
      set_var("via_link2","#");
      set_var("via_ajax2","onclick='xajax_form_pauta($id);'");
      set_var("via_link","admin.php");
      set_var("via_ajax","");
    }
  }else{
    // no completo fecha o nombre
    set_var("mensaje","Debe Completar los campos con (*)");
    set_var("opcion", "Volver a intentar modificar la Pauta");
    set_var("via_link2","#");
    set_var("via_ajax2","onclick='xajax_form_pauta($id);'");
    set_var("via_link","admin.php");
    set_var("via_ajax","");
  }
}else{
  // no presiono el botón guardar
  set_var("mensaje","Error al procesar la información, intentelo nuevamente");
  set_var("opcion", "Volver a intentar modificar la Pauta");
  set_var("via_link2","#");
  set_var("via_ajax2","onclick='xajax_form_pauta($id);'");
  set_var("via_link","admin.php");
  set_var("via_ajax","");
}
pparse("info");
?>