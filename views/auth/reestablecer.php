<div class="contenedor reestablecer">

    <?php include_once __DIR__ . "/../templates/nombre-sitio.php"; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Coloca tu Nuevo Password</p>

        <?php include_once __DIR__ . '/../templates/alertas.php' ?>

        <?php if ($mostrar) { ?>

            <form method="POST" class="formulario">

                <div class="campo">
                    <label for="password"> Password </label>
                    <input type="password" placeholder="Tu Password" name="password" id="password">
                </div>

                <div class="right">
                    <input type="submit" class="boton" value="Guardar Password">

                </div>
            </form>

        <?php }; ?>

        <div class="acciones">
            <a href="/crear">¿Aún no tienes una cuenta? Crea una</a>
            <a href="/olvide">¿Olvisate tu Password?</a>
        </div>
    </div> <!-- .contenedor-sm -->
</div>