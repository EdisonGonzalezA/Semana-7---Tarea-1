<?php
// Modelo de UnidadDeMedida
require_once('../config/config.php');

class IVA
{
    public function todos() // select * from unidad_medida
    {
        $con = new ClaseConectar();
        $con = $con->ProcedimientoParaConectar();
        $cadena = "SELECT * FROM `IVA`";
        $datos = mysqli_query($con, $cadena);
        $con->close();
        return $datos;
    }
    public function uno($idIVA) // select * from unidad_medida where id = $idUnidad
    {
        $con = new ClaseConectar();
        $con = $con->ProcedimientoParaConectar();
        $cadena = "SELECT * FROM `IVA` WHERE `idIVA` = $idIVA";
        $datos = mysqli_query($con, $cadena);
        $con->close();
        return $datos;
    }
}
