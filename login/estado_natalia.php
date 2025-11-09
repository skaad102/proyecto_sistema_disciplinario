 <!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado estudiante Natalia</title>
    <link rel="stylesheet" href="css\bootstrap.min.css">
</head>
<body>
    <div class="container">
    <form action="" method="POST">
        <h1>DISEÑO DE UN SOFTWARE PARA LA GESTION DE FALTAS ACADEMICAS Y DISCIPLINARIAS</h1>
        <h2>ESTADO ESTUDIANTE</h2>
         <h5>Tabla elaborada por NATALIA SANTOS</h5>
        <label for="cod_estado">Cod estado:</label>
        <input type="text" name="cod_estado" id="cod_estado"> <br><br>

        <select name="estado" id="estado">
            <option value="">Elige una opción</option>
            <option value="ACTIVO">ACTIVO</option>
            <option value="INACTIVO">INACTIVO</option>
            <option value="GRADUADO">GRADUADO</option>
        </select>
        <br><br>

         <input type="submit" value="GUARDAR" name="guardar" class="btn btn-primary">
         <input type="submit" value="VER" name="ver" class="btn btn-info">
         <input type="submit" value="EDITAR" name="editar" class="btn btn-secondary">
         <input type="submit" value="ELIMINAR" name="eliminar" class="btn btn-danger">
    </form> <br><br>
     <table class="table table-hover"> 
        <thead>
         <tr>
          <th scope="col">Cod estado</th>
         <th scope="col">Estado</th>
</tr>
</thead>
<?php
$conexion=new mysqli('localhost', 'root', '', 'registro_faltas');

$cod_estado = isset($_POST['cod_estado']) ? $_POST['cod_estado'] : "";
$estado = isset($_POST['estado']) ? $_POST['estado'] : "";

// GUARDAR
if (isset($_POST['guardar'])){
    $insertar="INSERT INTO estado (estado) VALUES ('$estado')";
    $sql=mysqli_query($conexion, $insertar);
    echo "✅ Registro Insertado Correctamente";
}

// VER
if (isset($_POST['ver'])) {
    $consultar = "SELECT * FROM estado";
    $sql=mysqli_query($conexion, $consultar);
    while ($ver=mysqli_fetch_array($sql)){
       echo "<tr><td>";
		echo $ver['cod_estado'];
		echo "<td>";
		echo $ver['estado'];
		echo "<td></tr>";
    }
}

// EDITAR
if (isset($_POST['editar'])) {
    $editar = "UPDATE estado SET estado='$estado' WHERE cod_estado='$cod_estado'";
    $sql = mysqli_query($conexion, $editar);
    echo "✏ Registro editado correctamente";
}

// ELIMINAR
if (isset($_POST['eliminar'])) {
    $eliminar = "DELETE FROM estado WHERE cod_estado='$cod_estado'";
    $sql = mysqli_query($conexion, $eliminar);
    echo "🗑 Registro eliminado correctamente";
}
?>
</div>
<script src="bootstrap.bundle.min.js"></script>
</body>
</html>