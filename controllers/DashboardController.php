<?php

namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
use MVC\Router;

class DashboardController
{

    public static function index(Router $router)
    {
        session_start();
        isAuth();

        $id = $_SESSION['id'];

        $proyectos = Proyecto::belongsTo('propietarioId', $id);

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos,
            'nombre' => $_SESSION['nombre'],
        ]);
    }

    public static function crear_proyecto(Router $router)
    {
        session_start();

        isAuth();

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === "POST") {

            $proyecto = new Proyecto($_POST);

            // Validacion
            $alertas = $proyecto->validarProyecto();

            if (empty($alertas)) {
                // Generar URL unica
                $hash = md5(uniqid());
                $proyecto->url = $hash;

                // Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];

                // Guardar proyecto
                $proyecto->guardar();


                // Redireccionar
                header('Location: /proyecto?id=' . $proyecto->url);
            }
        }


        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'nombre' => $_SESSION['nombre'],
            'alertas' => $alertas,
        ]);
    }

    public static function proyecto(Router $router)
    {
        session_start();

        isAuth();

        $token = $_GET['id'];

        if (!$token) {
            header('Location: /dashboard');
        }
        // Revisar que la persona que visita el proyecto, es quien lo creo
        $proyecto = Proyecto::where('url', $token);
        if ($proyecto->propietarioId !== $_SESSION['id']) {
            header('Location: /dashboard');
        }

        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto,
            'nombre' => $_SESSION['nombre'],

        ]);
    }

    public static function perfil(Router $router)
    {
        session_start();

        isAuth();
        $alertas = [];
        $usuario = Usuario::find($_SESSION['id']);

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validar_perfil();

            if (empty($alertas)) {
                // Verificar que el email no pertenezca a otro usuario
                $existeUsuario = Usuario::where('email', $usuario->email);

                if ($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    Usuario::setAlerta('error', 'Email no valido, ya pertenece a otra cuenta');
                } else {
                    // Guardar el registro
                    $usuario->guardar();
                    Usuario::setAlerta('exito', 'Guardado Correctamente');

                    // Asignar el nuevo nombre a la barra
                    $_SESSION['nombre'] = $usuario->nombre;
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'nombre' => $_SESSION['nombre'],
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }
    public static function cambiar_password(Router $router)
    {
        session_start();

        isAuth();
        $alertas = [];


        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $usuario = Usuario::find($_SESSION['id']);

            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();

            if (empty($alertas)) {
                $resultado = $usuario->comprobar_password();

                if ($resultado) {

                    $usuario->password = $usuario->password_nuevo;

                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    $usuario->hashPassword();

                    $resultado = $usuario->guardar();

                    if ($resultado) {
                        Usuario::setAlerta('exito', 'Password Actualizado Correctamente');
                    }
                } else {
                    Usuario::setAlerta('error', 'Password Actual Incorrecto');
                }
            }
        }

        $alertas = Usuario::getAlertas();


        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar Password',
            'nombre' => $_SESSION['nombre'],
            'alertas' => $alertas
        ]);
    }
}
