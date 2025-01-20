<?php 

namespace Model;

class Usuario extends ActiveRecord{
    //DB

    protected static $tabla= 'usuarios';
    protected static $columnasDB =['id', 'nombre', 'apellido', 'email', 'telefono', 'admin', 'confirmado', 'token', 'password'];


    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;
    public $password;

    public function __construct($args = []){
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? 0;
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->token = $args['token'] ?? '';
        $this->password = $args['password'] ?? '';
    }
    
    //Mensajes de validación para la creación de una cuenta

    
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas ['error'][]= ' El nombre del usuario es obligatorio';
        }
        if(!$this->apellido){
            self::$alertas ['error'][]= ' El apellido del usuario es obligatorio';
        }
        if(!$this->email){
            self::$alertas ['error'][]= ' El email del usuario es obligatorio';
        }
        if(!$this->telefono){
            self::$alertas ['error'][]= ' El telefono del usuario es obligatorio';
        }
        if(strlen($this->telefono)>10){
            self::$alertas ['error'][]= ' El telefono del usuario no debe contener mas de 10 digitos';
        }
        if(!$this->password){
            self::$alertas ['error'][]= ' El password del usuario es obligatorio';
        }
        if(strlen($this->password)<6){
            self::$alertas['error'][]= 'El password debe tener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    public function validarLogin(){
        if(!$this->email){
            self::$alertas ['error'][]= ' El email del usuario es obligatorio';
        }
        if(!$this->password){
            self::$alertas ['error'][]= ' El password del usuario es obligatorio';
        }
        return self::$alertas;
    }
    public function validarEmail(){
        if(!$this->email){
            self::$alertas ['error'][]= ' El email del usuario es obligatorio';
        }
        return self::$alertas;
    }

    public function validarPassword(){
        if(!$this->password){
            self::$alertas ['error'][]= ' El password del usuario es obligatorio';
        }
        if(strlen($this->password)<6){
            self::$alertas['error'][]= 'El password debe tener al menos 6 caracteres';
        }
        return self::$alertas;
    }
    public static function where($columna, $valor) {
        // Lógica para buscar un usuario en la base de datos
        $query = "SELECT * FROM usuarios WHERE $columna = '$valor'";
        $resultado = self::$db->query($query);
        return $resultado->fetch_object(self::class);
    }
    
    //Revisa si el usuario ya existe
    public function existeUsuario(){
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";

        $resultado = self::$db->query($query);

        if($resultado->num_rows){
            self::$alertas['error'][]= 'El usuario ya está registrado';
        }else{
            
        }
        return $resultado;
    }

    public function hashPassword(){
        $this->password = password_hash( $this->password, PASSWORD_BCRYPT);
    }

    public function crearToken(){
        $this->token= uniqid();
    }

    public function comprobarPasswordAndVerificado($password){
        $resultado = password_verify($password, $this->password);
        
        if(!$resultado || !$this->confirmado){
            self::$alertas['error'][]='El usuario no ha verificado su cuenta o la contraseña es incorrecta';
        }else{
            return true;
        }
    }
}
