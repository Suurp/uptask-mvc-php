<div class="contenedor login">

    <?php include_once __DIR__ . "/../templates/nombre-sitio.php"; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Iniciar Sesión</p>

        <?php include_once __DIR__ . "/../templates/alertas.php"; ?>

        <form action="/" method="POST" class="formulario" novalidate>

            <div class="campo">
                <label for="email">Email</label>
                <input type="email" placeholder="Tu Email" name="email" id="email">
            </div>

            <div class="campo">
                <label for="password"> Password </label>
                <input type="password" placeholder="Tu Password" name="password" id="password">
            </div>

            <div class="right">
                <input type="submit" class="boton" value="Iniciar Sesión">

            </div>
        </form>

        <div class="acciones">
            <a href="/crear">¿Aún no tienes una cuenta? Crea una</a>
            <a href="/olvide">¿Olvisate tu Password?</a>
        </div>
    </div> <!-- .contenedor-sm -->
</div>