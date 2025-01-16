<div class="barra">
    <p>Bienvenido <b><?php echo $_SESSION['nombre'] ?? ''; ?></b></p>

    <a class="boton" href="/logout"> Cerrar sesión</a>
</div>

<?php if(isset($_SESSION['admin'])){ ?>
    <div class="barra-servicios">
        <a class="boton" href="/admin">Ver Citas</a>
        <a class="boton" href="/servicios">Ver Servicios</a>
        <a class="boton" href="/servicios/crear">Nuevo servicio</a>
    </div>

<?php }?>