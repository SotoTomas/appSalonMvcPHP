<?php

namespace Controllers;

use Model\Usuario;
use MVC\Router;
use Classes\Email;

class LoginController{
    public static function login(Router $router){
        $alertas = [];


        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if(empty($alertas)){
                //Comprobar que existe el usuario
                $usuario = Usuario::where('email', $auth->email);

                if($usuario){
                    //Verificar el password
                    if($usuario->comprobarPasswordAndVerificado($auth->password)){
                        //autenticar el usuario
                        session_start();

                        $_SESSION['id']= $usuario->id;
                        $_SESSION['nombre']= $usuario->nombre . " " . $usuario-> apellido;
                        $_SESSION['email']= $usuario->email;
                        $_SESSION['login']= true;

                        //redireccionamiento
                        if($usuario->admin === '1'){
                            $_SESSION['admin']= $usuario->admin ?? null;

                            header('Location: /admin');

                        }else{
                            header('Location: /cita');
                        }

                            debuguear($_SESSION);
                        }
                }else{
                    Usuario::setAlerta( 'error', 'Usuario no existe' );
                }
            }
        }

        
            $alertas = Usuario::getAlertas();

            $router->render('auth/login',[
                'alertas' => $alertas
            ]);
        
    }
    public static function logout(){
       session_start();
       $_SESSION = [];
       
       header( 'Location: /' );
        
    }

    public static function olvide(Router $router){

        $alertas = [];

        if($_SERVER['REQUEST_METHOD']==='POST'){
            $auth = new Usuario($_POST);
            $alertas= $auth->validarEmail();

            if(empty($alertas)){
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado ==='1'){
                    //existe
                    //Generar Token
                    $usuario->crearToken();
                    $usuario->guardar();

                    //Enviar el mail
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    //Alerta exito
                    Usuario::setAlerta('exito', 'Revisa tu Email');
                }else{
                    //no confirmado 
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
            }
        }
        
        $alertas= Usuario::getAlertas();
        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
        
    }

    public static function recuperar(Router $router){
        $error = false;
        $alertas= [];
        $token= s($_GET['token'] ?? "");

        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token no válido');
            $error= true;
        }

       if($_SERVER['REQUEST_METHOD'] === 'POST'){
        //Leer el nuevo password y guardarlo
        $password = new Usuario($_POST);
        $password-> validarPassword();
        if(empty($alertas)){
            $usuario->password= null;
            $usuario->password= $password->password;
            $usuario->hashPassword();
            $usuario->token= null;

            $resultado= $usuario->guardar();

            if($resultado){
                Usuario::setAlerta('exito', ' Password actualizado con éxito');
            }
        }
       } 

        $alertas= Usuario::getAlertas();

        $router->render('auth/recuperar-password', [
            'alertas'=> $alertas,
            'error'=> $error
        ]);
    }


    public static function crear(Router $router){
        
        $usuario = new Usuario($_POST);
        
        $alertas = [];

        if( $_SERVER['REQUEST_METHOD'] == 'POST'){
            
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            //Revisar que alerta este vacio
            if(empty($alertas)){
                $resultado= $usuario->existeUsuario();
                if($resultado->num_rows){
                    $alertas = Usuario::getAlertas();
                }else{
                    //Hashear password
                    $usuario->hashPassword();

                    //generar un toke nunico
                    $usuario-> crearToken();

                    //Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    
                    $email->enviarConfirmacion();

                    //Crear el usuario
                    $resultado= $usuario->guardar();
                    if($resultado){
                        header('Location: /mensaje');
                    }
                    // debuguear($usuario);
                }
            }
        }
        

        $router ->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
        
    }

    public static function mensaje(Router $router){

        $router ->render('auth/mensaje');
    }

    public static function confirmar(Router $router){

        $alertas= [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);
        
        if(empty($usuario)){
            //mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no valido');
        }else{
            //modificar al usuario confirmado
            Usuario::setAlerta('exito', 'Cuenta confirmada correctamente');

            $usuario->confirmado = "1";
            $usuario->token = null;
            $usuario->guardar();
        }
        
        $alertas = Usuario::getAlertas();


        $router->render('auth/confirmar-cuenta',[
            'alertas'=> $alertas
        ]);
    }
}