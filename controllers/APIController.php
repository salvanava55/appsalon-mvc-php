<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController{
    
    public static function index(){
       // echo "Desde API/Index";
       $servicios=Servicio::all();
       //debuguear($servicios);
       echo json_encode($servicios);
    }

    public static function guardar(){
        //Almacena la cita y devuelve el ID
        $cita=new Cita($_POST);
        $resultado=$cita->guardar();

        $id=$resultado['id'];


        //Almacena la Cita y el servicio

        //Almacena los servicios con el ID de la cita
        $idServicios=explode(",", $_POST['servicios']);

        foreach($idServicios as $idServicio){
            $args=[
                'citaId' => $id,
                'servicioId' => $idServicio
            ];
            $citaServicio=new CitaServicio($args);
            $citaServicio->guardar();
        }

        $respuesta=[
            'resultado' => $resultado
        ];

    //  var_dump($resultado);
        
        echo json_encode($respuesta);
    }

    public static function eliminar(){
        // echo "Eliminando cita";
        if($_SERVER['REQUEST_METHOD']==='POST'){
            $id=$_POST['id'];
            $cita=Cita::find($id);
            $cita->eliminar();
            header('Location:'. $_SERVER['HTTP_REFER']);
        }
    }
}

?>