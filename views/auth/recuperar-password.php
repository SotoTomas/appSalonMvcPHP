<h1 class="nombre-pagina">Cambiar Contraseña</h1>
<p class="descripcion-pagina">Coloca tu nueva contraseña a continuación</p>

<?php include_once __DIR__ . '/../templates/alertas.php' ?>
<?php 
    if($error) return;
?>
<form class="formulario" method="post">
    <div class="campo">
        <label for="password">Nueva Contraseña:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <input type ="submit" class="boton" value="Cambiar Contraseña">

 <div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Iniciar sesión</a>
    <a href="/crear-cuenta">¿Aun no tienes una cuenta? Crear una.</a>
</div>
</form>