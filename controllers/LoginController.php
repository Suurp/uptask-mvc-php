<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController
{

    public static function login(Router $router)
    {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if (empty($alertas)) {
                // Verificar si el usuario existe
                $usuario = Usuario::where('email', $usuario->email);

                if (!$usuario || !$usuario->confirmado === "1") {
                    // No se encontro el usuario con el token
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                } else {
                    // Usuario existe
                    if (password_verify($_POST['password'], $usuario->password)) {
                        // Iniciar sesión
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        header('Location: /dashboard');
                    } else {
                        Usuario::setAlerta('error', 'Password incorrecto');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas,
        ]);
    }

    public static function logout()
    {
        session_start();

        $_SESSION = [];
        header('Location: /');
    }

    public static function crear(Router $router)
    {

        $usuario = new Usuario;
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === "POST") {

            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if (empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);

                if ($existeUsuario) {
                    Usuario::setAlerta('error', 'Este Usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear Password
                    $usuario->hashPassword();

                    // Eliminar password2
                    unset($usuario->password2);

                    // Generar Token
                    $usuario->crearToken();

                    // Crear usuario
                    $resultado = $usuario->guardar();

                    // Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    if ($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }
        }

        $router->render('auth/crear', [
            'titulo' => 'Crear Cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas,
        ]);
    }

    public static function olvide(Router $router)
    {

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if (empty($alertas)) {
                // Buscar usuario
                $usuario = Usuario::where('email', $usuario->email);

                if ($usuario && $usuario->confirmado === "1") {
                    // Generar nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);
                    // Actualizar usuario
                    $usuario->guardar();
                    // Enviar 
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    // Alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide', [
            'titulo' => 'Reestaurar Password',
            'alertas' => $alertas,
        ]);
    }

    public static function reestablecer(Router $router)
    {

        $token = s($_GET['token']);
        $mostrar = true;

        if (!$token) {
            header('Location: /');
        }

        // Encontrar al usuario
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            // No se encontro el usuario con el token
            Usuario::setAlerta('error', 'Token No Válido');
            $mostrar = false;
        }

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            // Obtenemos el password que introduce el usuario
            $usuario->sincronizar($_POST);

            // Validar el password
            $alertas = $usuario->validarPassword();

            if (empty($alertas)) {
                // Hashear el nuevo password
                $usuario->hashPassword();
                // Eliminar Token
                $usuario->token = null;
                // Guardar en BD
                $resultado = $usuario->guardar();
                // Redireccionar
                if ($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer',
            'alertas' => $alertas,
            'mostrar' => $mostrar,
        ]);
    }

    public static function mensaje(Router $router)
    {
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Exitosamente',
        ]);
    }

    public static function confirmar(Router $router)
    {
        $token = s($_GET['token']);

        if (!$token) {
            header('Location: /');
        }

        // Encontrar al usuario
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            // No se encontro el usuario con el token
            Usuario::setAlerta('error', 'Token No Válido');
        } else {
            // Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);

            // Guardar en la BD
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Tu cuenta ha sido confirmada correctamente');
        }

        $alertas = Usuario::getAlertas();


        $router->render('auth/confirmar', [
            'titulo' => 'Cuenta Confirmada Exitosamente',
            'alertas' => $alertas,
        ]);
    }
}
