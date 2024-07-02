<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email{

    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email=$email;
        $this->nombre=$nombre;
        $this->token=$token;
    }

    public function enviarConfirmacion(){
        //Crear el objeto de email

        $mail=new PHPMailer();
        $mail->isSMTP();
        $mail->Host=$_ENV['EMAIL_HOST'];
        $mail->SMTPAuth=true;
        $mail->Port=$_ENV['EMAIL_PORT']; //587 995
        $mail->SMTPSecure = 'ssl';
        $mail->Username=$_ENV['EMAIL_USER'];
        $mail->Password=$_ENV['EMAIL_PASSWORD'];

        $mail->setFrom('chavatest55@gmail.com');
        $mail->addAddress('salvanava55@gmail.com', 'ChavaCMX');
        $mail->Subject='Confirma tu correo';

        //Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet='UTF-8';

        $contenido="<html>";
        $contenido.="<p><trong>Hola " . $this->nombre . "</strong> Has creado tu cuenta en ..., solo debes confirmar presionando el siguiente enlace</p>";
        $contenido.= "<p>Presiona aqui: <a href='" . $_ENV['APP_URL'] . "/confirmar-cuenta?token=".$this->token."'> Confirmar Cuenta </a> </p>";
        $contenido.= "<p> Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
        $contenido.= "</html>";

        $mail->Body=$contenido;

        //Enviar el email

        $mail->send();
    }

    public function enviarInstrucciones(){
        $mail=new PHPMailer();
        $mail->isSMTP();
        $mail->Host=$_ENV['EMAIL_HOST'];
        $mail->SMTPAuth=true;
        $mail->Port=$_ENV['EMAIL_PORT']; //587 995
        $mail->SMTPSecure = 'ssl';
        $mail->Username=$_ENV['EMAIL_USER'];
        $mail->Password=$_ENV['EMAIL_PASSWORD'];

        $mail->setFrom('chavatest55@gmail.com');
        $mail->addAddress('salvanava55@gmail.com', 'ChavaCMX');
        $mail->Subject='Reestablece tu password';

        //Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet='UTF-8';

        $contenido="<html>";
        $contenido.="<p><trong>Hola " . $this->nombre . "</strong>Has solicitado restablecer tu password, sigue el siguiente enlace para hacerlo. </p>";
        $contenido.= "<p>Presiona aqui: <a href='" . $_ENV['APP_URL'] . "/recuperar?token=".$this->token."'> Restablecer Password </a> </p>";
        $contenido.= "<p> Si tu no solicitaste este cambio, puedes ignorar el mensaje</p>";
        $contenido.= "</html>";

        $mail->Body=$contenido;

        //Enviar el email

        $mail->send();
    }
}