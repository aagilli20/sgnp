<?php
require_once("template.php");
require_once("conexion.php");
require_once("editar_fecha.php");
require_once("seguridad2.php");
require_once("validacion.php");
require_once("registrar_log.php");
require_once("funciones.admin.ajax.php");


if(isset($_REQUEST['modificar'])){
  $nick = $_REQUEST['nick'];
  $nombre = $_REQUEST['nombre'];
  $apellido = $_REQUEST['apellido'];
  $tipodoc = $_REQUEST['select_tipo_doc'];
  $nrodoc = $_REQUEST['nrodocumento'];
  $sexo = $_REQUEST['select_sexo'];
  $fenac = fecha_mysql($_REQUEST['fe_nac']);
  $dom = $_REQUEST['domicilio'];
  $tel = $_REQUEST['telefono'];
  $cel = $_REQUEST['celular'];
  $email = $_REQUEST['email'];
  $pass1 = $_REQUEST['password'];
  $tipo_usuario = $_REQUEST['select_tipo_usuario'];
  $tipo_ant = $conexion->GetRow("SELECT IdTipoUsuario FROM Usuario WHERE Nick='$nick'");
  if($nick != null){
   if(($nombre!=null)&&($apellido!=null)&&($nrodoc!=null)&&($fenac!=null)&&($email!=null)){
      if(($tel!=null)||($cel!=null)){
        if($pass1!=null){
          if(is_alphanumeric($pass1,4,20)){
            $pass1 = sha1($pass1);
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
              $msj = "El usuario se ha modificado correctamente";
            }else { 
              $msj = "Se produjo un error inesperado al guardar los datos, vuelva a intentarlo más tarde";
            }
          }else{ $msj = "La contraseña debe ser alfanumerica y debe contener entre 4 y 20 dígitos";}
        }else{
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
            $msj = "El usuario se ha modificado correctamente";
          }else { 
            $msj = "Se produjo un error inesperado al guardar los datos,vuelva a intentarlo mas tarde";
          }
        }
      } else { $msj = "Debe registrar un teléfono o un celular";}
    } else { $msj = "Debe completar todos los campos marcados con (*)";}
  } else { $msj = "Ha ocurrido un error al procesar la información, vuelva a intentarlo mas tarde";}
} else { 
   $msj = "Se produjo un error inesperado, vuelva a intentarlo mas tarde";
}
// 

set_file("info","info_opcion.html");
set_var("javascript",$ajax->getJavascript("./"));
set_var("mensaje","$msj");
set_var("opcion", "Volver a Usuarios Activos");
set_var("via_link2","#");
set_var("via_ajax2","onclick='xajax_form_lista_usuario_activo(1);'");
set_var("via_link","admin.php");
set_var("via_ajax","");
pparse("info");
?>