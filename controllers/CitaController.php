<?php
namespace Controllers;

use Model\Usuario;
use MVC\Router;
use Classes\Email;


class CitaController{
    public static function index(Router $router){
        if(!session_start()){
            session_start();
        };

        isAuth();

        $router->render('cita/index', [
            'usuario'=> $_SESSION['nombre'],
            'id'=>$_SESSION['id']
        ]);
    }
}