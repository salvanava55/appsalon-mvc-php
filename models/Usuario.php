<?php

namespace Model;

class Usuario extends ActiveRecord{

    //BAse de datos
    protected static $tabla='usuarios';
    protected static $columnasDB=['id', 'nombre', 'apellido', 'email','password','telefono','admin','confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args=[])
    {
        $this->id=$args['id'] ?? null;
        $this->nombre=$args['nombre'] ?? null;
        $this->apellido=$args['apellido'] ?? null;
        $this->email=$args['email'] ?? null;
        $this->password=$args['password'] ?? null;
        $this->telefono=$args['telefono'] ?? null;
        $this->admin=$args['admin'] ?? 0;
        $this->confirmado=$args['confirmado'] ?? 0;
        $this->token=$args['token'] ?? null;
        

    }

    //Mensajes de validacion para al creacion de cuenta

    public function validarNuevaCuenta(){
        if(!$this->nombre){ //Thos hace referencia al nombre que se instancia en la funcion crear de LoginController
            self::$alertas['error'][]='El nombre es Obligratorio';

        }
        if(!$this->apellido){
            self::$alertas['error'][]='El apellido es Obligratorio';

        }
        if(!$this->email){
            self::$alertas['error'][]='El email es Obligratorio';

        }
        if(!$this->password){
            self::$alertas['error'][]='El password es Obligratorio';

        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][]='El password debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][]='El email es obligatorio';
        }

        if(!$this->password){
            self::$alertas['error'][]='El password es obligatorio';
        }

        return self::$alertas;
    }

    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][]='El email es obligatorio';
        }
        return self::$alertas;
    }

    public function validarPassword(){
        if(!$this->password){
            self::$alertas['error'][]="El password es obligatorio";
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][]="El password debe tener almentos 6 caracteres";
        }
        return self::$alertas;
    }

    //Revisa si el usuario ya existe
    public function existeUsuario(){
        $query=" SELECT * FROM ". self::$tabla. " WHERE email= '" .$this->email. "' LIMIT 1";
       
        $resultado= self::$db->query($query);

        if($resultado->num_rows){
            self::$alertas['error'][]='El Usuario ya esta registrado';
        }

        return $resultado;
    }


    public function hashPassword(){
        $this->password=password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken(){
        $this->token=uniqid();
    }

    public function comprobarPasswordAndVerificado($password){
        $resultado=password_verify($password, $this->password);
        if(!$resultado || !$this->confirmado){
            self::$alertas['error'][]='Password Incorrecto o tu cuenta no ha sido confirmada';
        }
        else{
            return true;
        }
    }

}