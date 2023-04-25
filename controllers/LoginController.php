<?php 

    namespace Controllers;

use Clases\Email;
use Model\Usuario;
use MVC\Router;

    class LoginController {


/*----Login----*//*---------------------------------------------------*/   
        public static function login(Router $router) {

            $alertas = [];

            $auth = new Usuario;

            if($_SERVER['REQUEST_METHOD'] === 'POST'){

                $auth = new Usuario($_POST);

                $alertas = $auth->validarLogin();

                if(empty($alertas)) {

                    //Comprobar que exista el usuario
                    $usuario = Usuario::where('email', $auth->email);

                    if($usuario) {
                        //Verificar el password
                        if($usuario->comprobar_verifcadoPass($auth->password)) {

                            //Autenticar al usuario

                            $_SESSION['id'] = $usuario->id;
                            $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                            $_SESSION['email'] = $usuario->email;
                            $_SESSION['login'] = true;


                            //Redireccionamiento
                            if($usuario->admin === "1") {

                                $_SESSION['admin'] = $usuario->admin ?? null;
                                header('Location: /admin');

                                debuguear($_SESSION);

                            }
                            else {

                                header('Location: /cita');

                            }

                        }

                    }
                    else {

                        Usuario::setAlerta('error', 'Usuario no encontrado');

                    }
                }
            }

            $alertas = Usuario::getAlertas();

            $router->render('auth/login', [

                'alertas' => $alertas,
                'auth' => $auth
                
            ]);

        }


/*----Logout----*//*---------------------------------------------------*/           
        public static function logout() {

            $_SESSION = [];

            header('Location: /');

        }


/*----Olvide----*//*---------------------------------------------------*/   
        public static function olvide(Router $router) {

            $alertas = [];

            if($_SERVER['REQUEST_METHOD'] === 'POST'){

                $auth = new Usuario($_POST);
                $alertas = $auth->validarEmail();

                if(empty($alerta)) {

                    $usuario = Usuario::where('email', $auth->email);
                    
                    if($usuario && $usuario->confirmado === "1") {

                        //Generar un token
                        $usuario->crearToken();
                        $usuario->guardar();

                        //Enviar el email
                        $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                        $email->enviarInstrucciones();

                        Usuario::setAlerta('exito', 'Revisa tu email');

                    }
                    else {

                       Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                       
                    }
                }
            }

            $alertas = Usuario::getAlertas();

            $router->render('auth/olvide-password', [

                'alertas' => $alertas

            ]);

        }


/*----Recuperar----*//*---------------------------------------------------*/
        public static function recuperar(Router $router) {

            $alertas = [];

            $error = false;

            $token = s($_GET['token']);

            //Buscar usuario por su token

            $usuario = Usuario::where('token', $token); 

            if(empty($usuario)){

                Usuario::setAlerta('error', 'Token no válido');
                $error = true;

            }

            if($_SERVER['REQUEST_METHOD'] === 'POST') {

                //Leer el nuevo password y guardarlo
                $password = new Usuario($_POST);
                $alertas = $password->validarPassword();

                if(empty($alertas)) {

                    $usuario->password = null;

                    $usuario->password = $password->password;
                    $usuario->hashPassword();
                    $usuario->token = null;

                    $resultado = $usuario->guardar();
                    if($resultado) {

                        header('Location: /');

                    }
                }
            }

            $alertas = Usuario::getAlertas();

            $router->render('auth/recuperar-password', [

                'alertas' => $alertas,
                'error' => $error
                
            ]);

        }


/*----Crear----*//*---------------------------------------------------*/    
        public static function crear(Router $router) {

            $usuario = new Usuario($_POST);

            //Alertas vacias
            $alertas = [];

            if($_SERVER['REQUEST_METHOD'] === 'POST'){

                $usuario->sincronizar($_POST);
                $alertas = $usuario->validarNuevaCuenta();

                //Revisar qu alerta este vacio
                if(empty($alertas)){

                    //Verificar que el usuario no este registrado
                    $resultado = $usuario->existeUsuario();

                    if($resultado->num_rows){

                        $alertas = Usuario::getAlertas();
                        
                    }
                    
                    else {
                        //Hashear el password
                        $usuario->hashPassword();

                        //Generar un token unico
                        $usuario->crearToken();

                        //Enviar el email
                        $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                        $email->enviarConfirmacion();

                        //Crear el usuario
                        $resultado = $usuario->guardar();    

                        if($resultado) {

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


/*----Mensaje----*//*---------------------------------------------------*/
        public static function mensaje(Router $router) { 

            $router->render('auth/mensaje', [

               
            ]);

        }


/*----Confirmar----*//*---------------------------------------------------*/
        public static function confirmar(Router $router) { 

            $alertas = [];

            $token = s($_GET['token']);
                        
            if(!$token) header('Location: /');

            //Encontrar al usuario con este token
            $usuario = Usuario::where('token', $token);   

            if(empty($usuario)) {
                //Mostrar mensaje de error
     
                Usuario::setAlerta('error', 'Token no válido');

            }
            else {
                //Modificar a usuario confirmado
                $usuario->confirmado = "1";
                $usuario->token = "";
                $usuario->guardar();
                Usuario::setAlerta('exito', 'Token válido, confirmando usuario...');           
            }

            //Obtener alertas
            $alertas = Usuario::getAlertas();

            $router->render('auth/confirmar', [

                'usuario' => $usuario,
                'alertas' => $alertas

            ]);

        }

    }
