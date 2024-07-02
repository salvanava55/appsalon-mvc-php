<?php 

namespace Controllers;

use MVC\Router;

class CitaController{

   
    public static function index( Router $router){

        // session_start();

        // isAuth();
        
        if(!$_SESSION['nombre']){
            //session_start();
            header('Location: /');
        }
        
       
        $router->render('cita/index', [
            'nombre' => $_SESSION['nombre'],
            'id' => $_SESSION['id']
        ]);
    }
}