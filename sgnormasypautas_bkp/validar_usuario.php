<?php
require_once("template.php");
require_once("conexion.php");

if(isset($_REQUEST['ingresar'])){
  $nick = $_REQUEST['nick'];
  $pass = sha1($_REQUEST['pass']);
  $sql = "SELECT count(*) from Usuario where Nick='$nick' and Password='$pass'";
  $ok = $conexion->GetOne($sql);
  $tipo_usuario = $conexion->GetRow("SELECT IdTipoUsuario FROM Usuario WHERE Nick='$nick'");
  if($ok){
    if($tipo_usuario['IdTipoUsuario']!=null){
      // usuario logueado correctamente
      session_start();
      $_SESSION["logueado"] = true;
      $_SESSION["usuario"] = $nick;
      $_SESSION["tipo"] = $tipo_usuario['IdTipoUsuario'];
      header("location:index.php");
    } else {
      set_file("info","informacion.html");
      set_var("javascript","");
      set_var("mensaje","El usuario no se encuentra habilitdo, comuniquese con el Administrador del Sistema para solucionar el inconveniente");
      set_var("via_link","index.php");
      set_var("via_ajax","");
      pparse("info");
    }
  } else {
    // error de usuario o contraseña
    set_file("login","login.html");
    set_var("error", "El usuario o la contraseña son incorrectos. Si olvido la contraseña comuniquese con el Administrador del Sistema.");
    pparse("login");
  }
} else{
  set_file("info","informacion.html");
  set_var("javascript","");
  set_var("mensaje","Se produjo un error inesperado, intente ingresar nuevamente");
  set_var("via_link","index.php");
  set_var("via_ajax","");
  pparse("info");
}
?>