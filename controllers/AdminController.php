<?php

namespace Controllers;

use Model\AdminCita;
use MVC\Router;

class AdminController{

    public static function index( Router $router ){
       // session_start();

       isAdmin();

       $fecha=$_GET['fecha'] ?? date('Y-m-d');

       $fechas=explode('-', $fecha);

      
       if( !checkdate($fechas[1], $fechas[2], $fechas[0])){
        header('Location: /404'); 
       }

      

    //    debuguear($fecha);

       //Consultar la BD

       $consulta= "select citas.id, citas.hora, CONCAT(usuarios.nombre, ' ', usuarios.apellido) as cliente, usuarios.email, usuarios.telefono, servicios.nombre as servicio, servicios.precio from citas
        left OUTER JOIN usuarios on citas.usuariosid=usuarios.id
        LEFT OUTER JOIN citasservicios on citasservicios.citaId=citas.id
        LEFT OUTER JOIN servicios on servicios.id=citasservicios.servicioId
        where fecha='${fecha}'
        ";

        $citas=AdminCita::SQL($consulta);

        // debuguear($citas);

        $router->render('admin/index',[
            'nombre' => $_SESSION['nombre'],
            'citas' => $citas,
            'fecha' => $fecha
        ]);
    }
}