<?php

namespace Classes;
use PHPMailer\PHPMailer\PHPMailer;

class Email{

    public $email;
    public $nombre;
    public $token;


    public function __construct($email, $nombre, $token){
        
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    

    public function enviarConfirmacion(){
        //Crear el objeto de email
        $mail = new PHPMailer();
        $mail-> isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Port = $_ENV['EMAIL_PORT'];                
        $mail->Username = $_ENV['EMAIL_USER'];                     //SMTP username
        $mail->Password = $_ENV['EMAIL_PASSWORD'];

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'Confirma tu cuenta';

        //setHTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido="<html>";
        $contenido.="<p><strong>Hola " . $this->nombre ."</strong>. Has creado una cuenta en AppSalon.com. Para confirmar tu cuenta, haz clic en el siguiente enlace:</p>";
        $contenido.="<p> Presiona aqui: <a href='" . $_ENV['APP_URL'] . "/confirmar-cuenta?token=". $this->token ."'> Confirmar cuenta</a> ";
        $contenido.="<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje </p>";
        $contenido.="</html>";

        $mail->Body = $contenido;

        //Enviar MAIL
        $mail->send();
    }

    public function enviarInstrucciones(){
        $mail = new PHPMailer();
        $mail-> isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Port = $_ENV['EMAIL_PORT'];                       
        $mail->Username = $_ENV['EMAIL_USER'];                     //SMTP username
        $mail->Password = $_ENV['EMAIL_PASSWORD'];

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'Reestablece tu contraseña';

        //setHTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido="<html>";
        $contenido.="<p><strong>Hola " . $this->nombre ."</strong>. Has solicitado reestablecer tu contraseña. Para establecer una nueva contraseña, haz clic en el siguiente enlace:</p>";
        $contenido.="<p> Presiona aqui: <a href='" . $_ENV['APP_URL'] . "/recuperar?token=". $this->token ."'> Reestablecer contraseña</a> ";
        $contenido.="<p>Si tu no solicitaste este cambio, puedes ignorar el mensaje </p>";
        $contenido.="</html>";

        $mail->Body = $contenido;

        //Enviar MAIL
        $mail->send();
    }
}