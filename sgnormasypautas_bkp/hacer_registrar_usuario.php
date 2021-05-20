<?php
require_once("template.php");
require_once("conexion.php");
require_once("editar_fecha.php");
require_once("validacion.php");

if(isset($_REQUEST['aceptar'])){
  $nick = $_REQUEST['nick'];
  $nombre = $_REQUEST['nombre'];
  $apellido = $_REQUEST['apellido'];
  $tipodoc = $_REQUEST['select_tipo_doc'];
  $nrodoc = $_REQUEST['nrodocumento'];
  $sexo = $_REQUEST['select_sexo'];
  $fenac = $_REQUEST['fe_nac'];
  $dom = $_REQUEST['domicilio'];
  $tel = $_REQUEST['telefono'];
  $cel = $_REQUEST['celular'];
  $email = $_REQUEST['email'];
  $pass1 = $_REQUEST['pass1'];
  $pass2 = $_REQUEST['pass2'];
  // verificamos que haya ingresado la misma contraseña dos veces
  if((!strcmp($pass1,$pass2)) && ($pass1!=null)){
    // verificamos que haya algo en los campos obligatorios
    if(($nick!=null)&&($nombre!=null)&&($apellido!=null)&&($nrodoc!=null)&&($fenac!=null)&&($email!=null)){
      // verificamos si ingreso un telefono o un celular
      if(is_alphanumeric($tel,1,20) || is_alphanumeric($cel,1,20)){
        // verificamos que el apellido y nombre sean validos
        if(is_clean_text($nombre,1,50) && is_clean_text($apellido,1,30)){
          // verificamos que el domicilio sea valido
          if(is_alphanumeric($dom,1,60) || $dom==null){
            if($sexo=='1'){$foto = "./imgs/avatars/witch_avatar.png";}
            else {$foto = "./imgs/avatars/v_avatar.png";}
            // verficamos si el password es valido
            if(is_alphanumeric($pass1,5,20)){
              $pass1 = sha1($pass1);
              // verificamos que el nick sea valido
              if(is_alphanumeric($nick,5,20)){
                // verificamos que no exista otro usuairo con el mismo nick
                $cant = $conexion->GetRow("SELECT Count(*) FROM Usuario WHERE Nick='$nick'");
                if($cant['Count(*)']==0){
                  // verficamos que la fecha ingresada sea valida
                  if(fecha_valida($fenac)){
                    $fesql = fecha_mysql($fenac);
                    // verificamos si ingreso un email valido
                    if(is_email($email)){
                      // verficamos si ingreso un dni valido
                      if(is_numerico($nrodoc,6,8)){
                      // verificamos que el dni no este duplicado
                        unset($cant);
                        $cant = $conexion->GetRow("SELECT Count(*) FROM Usuario WHERE NroDocumento='$nrodoc'");
                        if($cant['Count(*)']==0){
                          // podemos guardar el usuario
                          $sql = "INSERT INTO Usuario (Nick,Password,Nombre,Apellido,NroDocumento,FeNacimiento,Domicilio,
                                                  Telefono,Celular,EMail,UrlFoto,IdTipoDoc,IdSexo) VALUES
                                                  ('$nick','$pass1','$nombre','$apellido','$nrodoc','$fesql','$dom',
                                                  '$tel','$cel','$email','$foto','$tipodoc','$sexo')";
                          $ok = $conexion->Execute($sql);
                          if($ok){
                            $msj = "El usuario se ha creado correctamente, aguarde la autorización de un Administrador";
                            set_file("info","informacion.html");
                            set_var("javascript","");
                            set_var("mensaje",$msj);
                            set_var("via_link","index.php");
                            set_var("via_ajax","");
                            pparse("info");
                          }else{
                            $msj = "Se produjo un error inesperado al guardar los datos, intente registrarse nuevamente".$conexion->ErrorMsg();
                          }
                        }else{ $msj = "Ya existe otro usuario con ese número de documento. Verifique si lo ingreso correctamente o comuniquese con un Administrador del Sistema";}
                      }else{ $msj = "El Número de Documento ingresado es incorrecto";}
                    }else{ $msj = "Debe ingresar un E-Mail Valido";}
                  }else{ $msj = "Debe ingresar una fecha válida, menor a la actual y con formato dd/mm/aaaa";}
                }else{$msj = "El Nick que eligió ya existe, ingrese otro distinto";}
              }else{
                $msj = "El Nick solo puede contener datos alfanumericos. Además debe tener un mínimo de 5 y un máximo de 20 caracteres";
              }
            }else{$msj = "La contraseña debe ser alfanumerica y contener entre 5 y 20 dígitos";}
          }else{$msj = "El Domicilio solo puede contener datos alfanuméricos";}
        }else{$msj = "El Nombre y Apellido solo pueden contener datos alfabéticos";}
      }else{ $msj = "Debe registrar un teléfono o un celular. Los datos ingresados deben ser alfanuméricos.";}
    }else{ $msj = "Debe completar todos los campos marcados con (*)";}
  }else{ $msj = "Debe ingresar dos veces la misma contraseña";}
}else{ 
  $msj = "Se produjo un error inesperado, intente registrarse nuevamente";
}

if(!$ok){
  // el usuario no fue guardado
  set_file("registrar","registrar_usuario.html");
  set_var("nick",$nick);
  set_var("nombre",$nombre);
  set_var("apellido",$apellido);
  set_var("nrodoc",$nrodoc);
  if(fecha_valida($fenac)){set_var("fe_nac",$fenac);}
  else{set_var("fe_nac","dd/mm/aaaa");}
  set_var("domicilio",$dom);
  set_var("mail",$email);
  set_var("tel",$tel);
  set_var("cel",$cel);
  set_var("error",$msj);
  // llena el select_tipo_doc
  $consulta = $conexion->Execute("SELECT * FROM TipoDocumento");
  foreach($consulta as $registro){
    set_var("id_tipo_doc",$registro['IdTipoDoc']);
    set_var("tipo_doc",$registro['ValorTipoDoc']);
    set_var("selected1","");
    if($registro['IdTipoDoc']==$tipodoc){set_var("selected1","selected=selected");}
    parse("cargar_tipo_doc");
  }
  unset($consulta);
  // llena el select_sexo
  $consulta = $conexion->Execute("SELECT * FROM Sexo");
  foreach($consulta as $registro){
    set_var("id_sexo",$registro['IdSexo']);
    set_var("sexo",$registro['Sexo']);
    set_var("selected2","");
    if($registro['IdSexo']==$sexo){set_var("selected2","selected=selected");}
    parse("cargar_sexo");
  }
  unset($consulta);
  pparse("registrar");
}

?>