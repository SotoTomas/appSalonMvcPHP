<h1 class="nombre-pagina">Login</h1>
<p class="descripcion-pagina">Inicia sesión con tus datos</p>

<?php include_once __DIR__ . '/../templates/alertas.php' ?>

<form class="formulario" method="POST" action="/">
    <div class="campo">
        <label for="email"> Correo electrónico:</label>
        <input 
        type="email" 
        id="email" 
        name="email" 
        placeholder="Ingrese su correo electrónico"/>
    </div>
    
    <div class="campo">
        <label for="password"> Contraseña: </label>
        <input 
        type="password" 
        id="password" 
        name="password" 
        placeholder="Ingrese su contraseña"/>
    </div>

        <input type="submit" value="Iniciar sesión" class="boton"/>
    </form>

    <div class="acciones">
        <a href="/crear-cuenta">¿Aun no tienes una cuenta? Crear una</a>
        <a href="/olvide">¿Has olvidado tu contraseña?</a>
    </div>