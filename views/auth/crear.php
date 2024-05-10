<div class="contenedor crear">
    <?php include_once __DIR__ . "/../templates/nombre-sitio.php"; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Crea tu cuenta en UpTask</p>

        <?php include_once __DIR__ . '/../templates/alertas.php' ?>

        <form action="/crear" method="POST" class="formulario">

            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" placeholder="Tu nombre" name="nombre" id="nombre" value="<?php echo $usuario->nombre; ?>">
            </div>

            <div class="campo">
                <label for="email">Email</label>
                <input type="email" placeholder="Tu Email" name="email" id="email" value="<?php echo $usuario->email; ?>">
            </div>


            <div class=" campo">
                <label for="password">Password</label>
                <input type="password" placeholder="Tu Password" name="password" id="password">
            </div>

            <div class="campo">
                <label for="password2">Repetir Password</label>
                <input type="password" placeholder="Repite tu Password" name="password2" id="password2">
            </div>

            <div class="right">
                <input type="submit" class="boton" value="Crear Cuenta">
            </div>
        </form>

        <div class="acciones">
            <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
            <a href="/olvide">¿Olvisate tu Password?</a>
        </div>
    </div> <!-- .contenedor-sm -->
</div>