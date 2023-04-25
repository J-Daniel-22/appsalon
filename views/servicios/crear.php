<h1 class="nombre-pagina">Nuevo Servicios</h1>
<p class="descripcion-pagina">llena todos los campos para agregar nuevo servicios</p>

<?php 

    include_once __DIR__ . '/../templates/barra.php';
    include_once __DIR__ . '/../templates/alertas.php'; 

?>

<form action="/servicios/crear" class="formulario" method="POST">

    <?php include_once __DIR__ . '/formulario.php'; ?>

    <input type="submit" class="boton" value="Guardar Servicio">
</form>