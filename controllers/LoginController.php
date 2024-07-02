<?php 

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{

    public static function login(Router $router){
        // echo "Desde Login";
        $alertas=[];

        if($_SERVER['REQUEST_METHOD']==='POST'){
            //echo "Desde Post";
            $auth=new Usuario($_POST);
            $alertas=$auth->validarLogin();
            
            if(empty($alertas)){
               //Comprobar que exista el usuario
               $usuario=Usuario::where('email', $auth->email);

               if($usuario){
                //verificar usuario
               if($usuario->comprobarPasswordAndVerificado($auth->password)){
                    //Autenticar el usuario
                    session_start();
                    $_SESSION['id']=$usuario->id;
                    $_SESSION['nombre']=$usuario->nombre . " " . $usuario->apellido;  
                    $_SESSION['email']=$usuario->email;
                    $_SESSION['login']=true;
                    //Redireccionamiento
                    
                    if($usuario->admin==1){
                        $_SESSION['admin']=$usuario->admin ?? null;
                        header('Location: /admin');
                    }
                    else{
                        header('Location: /cita');
                    }
                    debuguear($_SESSION);
               }
               }
               else{
                Usuario::setAlerta('error', 'Usuario no encontrado');
               }
               
            }
        }

        $alertas=Usuario::getAlertas();

        $router->render('auth/login', ['alertas'=>$alertas]); //Carpeta auth dentro de views y el archivo login.php
   
    }

    public static function logout(){
        // echo "Desde Logout";
        session_start();

        $_SESSION=[];

        header('Location: /');
    }

    public static function olvide(Router $router){
       // echo "Desde olvide";
       $alertas=[];

       if($_SERVER['REQUEST_METHOD']==='POST'){
        $auth=new Usuario($_POST);

        $alertas= $auth->validarEmail();
        if(empty($alertas)){
            $usuario=Usuario::where('email',$auth->email);

            if($usuario && $usuario->confirmado==="1"){
                //Generar un token de un solo uso
                $usuario->crearToken();
                $usuario->guardar();

                //Enviar el email
                $email=new Email($usuario->email, $usuario->nombre, $usuario->token);
                $email->enviarInstrucciones();

                //Alerta de exito
                Usuario::setAlerta('exito', 'Revisa tu email');
                
            }
            else{
                Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
               
            }
        }
       }
       $alertas=Usuario::getAlertas();
       $router->render('auth/olvide-password',[
            'alertas' => $alertas
       ]);
    }

    public static function recuperar(Router $router){
        //echo "Desde recuperar";

        $alertas=[];
        $error=false;

        $token=s($_GET['token']);

        //Buscar usuario por su token
        $usuario=Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error','Token no valido');
            $error=true;
        }

        if($_SERVER['REQUEST_METHOD']==='POST'){
            //lEER EL NUEVO PASSWORD Y GUARDARLO
                $password=new Usuario($_POST);
               $alertas= $password->validarPassword();
               if(empty($alertas)){
                $usuario->password=null;
                $usuario->password=$password->password;
                $usuario->hashPassword();
                $usuario->token=null;
                $resultado=$usuario->guardar();
                if($resultado){
                    header('Location: /');
                }
               }
        }

        $alertas=Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' =>$error
        ]);
    }

    public static function crear(Router $router){
        //echo "Desde crear";
        $usuario=new Usuario;

        $alertas=[];
        
        if($_SERVER['REQUEST_METHOD']==='POST'){
            //echo "Enviaste el formulario";
           
            //debuguear($usuario);
            $usuario->sincronizar($_POST);
            $alertas=$usuario->validarNuevaCuenta();

            //Revisar que alerta este vacio
            if(empty($alertas)){
                //echo "Pasaste la validacion";
                
                //Verificar que el usuario no este registrado
                $resultado=$usuario->existeUsuario();
                if($resultado->num_rows){
                    $alertas=Usuario::getAlertas();
                }
                else{
                    //No esta registrado

                //Hasear el password
                $usuario->hashPassword();

                //Generar un token unico
                $usuario->crearToken();

                //Enviar un Email
              $email=new Email($usuario->nombre, $usuario->email, $usuario->token); 
                    // debuguear($email);
                    $email->enviarConfirmacion();

                    //Crear el usuario
                    $resultado=$usuario->guardar();
                    if($resultado){
                        header('Location: /mensaje');
                    }
                }
            }

        }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router) {
        $router->render('auth/mensaje'); 
    }

    public static function confirmar(Router $router){
        $alertas=[];

        $token=s($_GET['token']);
        $usuario=Usuario::where('token',$token);

        if(empty($usuario)){
            //Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no valido');
        }
        else{
            //Modificar usuario confirmado
            
            $usuario->confirmado="1";
            $usuario->token=null;
            $usuario->guardar();
            Usuario::setAlerta('exito','Cuenta comprobada correctamente');
           
        }

        $alertas=Usuario::getAlertas();
        $router->render('auth/confirmar-cuenta',[
            'alertas' => $alertas
        ]);
    }
}