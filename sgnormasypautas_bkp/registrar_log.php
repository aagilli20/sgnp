<?php
require_once("template.php");
require_once("seguridad.php");
require_once("conexion.php");


////////////////////////////////////////////////////
// Registrar log de usuario
////////////////////////////////////////////////////

function registrar_log_usr($id_tabla,$id_registro,$dato){
    global $conexion;
    $nick = $_SESSION["usuario"];
    $fecha = date("Y-m-d");
    $id = $conexion->GenID("SeqLogUsr");
    $conexion->Execute("INSERT INTO LogUsr VALUES ($id,'$fecha',$id_tabla,$id_registro,'$dato','$nick')");
    return true;
}

////////////////////////////////////////////////////
// Registrar log de Administrador
////////////////////////////////////////////////////

function registrar_log_abm($id_tipo_accion,$id_tabla,$id_registro,$dato){
    global $conexion;
    $nick = $_SESSION["usuario"];
    $fecha = date("Y-m-d");
    $id = $conexion->GenID("SeqLogAbm");
    $sql = "INSERT INTO LogAbm VALUES ($id,'$fecha','$id_registro','$dato','$id_tabla','$id_tipo_accion','$nick')";
    $conexion->Execute($sql);
    return true;
}

////////////////////////////////////////////////////
// Registrar log Administracion de Usuarios
////////////////////////////////////////////////////

function registrar_log_abm_usr($id_tipo_accion,$nick,$id_tipo_ant,$id_tipo_pos){
    global $conexion;
    $usr = $_SESSION["usuario"];
    $fecha = date("Y-m-d");
    $id = $conexion->GenID("SeqLogAbmUsr");
    if($id_tipo_pos!=0){
      $sql = "INSERT INTO LogAbmUsr VALUES ($id,'$fecha','$nick',$id_tipo_ant,$id_tipo_pos,$id_tipo_accion,'$usr')";
    }else{
      $sql = "INSERT INTO LogAbmUsr VALUES ($id,'$fecha','$nick',$id_tipo_ant,null,$id_tipo_accion,'$usr')";
    }
    $conexion->Execute($sql);
    return true;
}

?>