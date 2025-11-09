<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudiante Natalia</title>
    <link rel="stylesheet" href="css\bootstrap.min.css">
</head>
<body>
    <div class="container">
    <form action="" method="POST">
	   <h1>DISEÑO DE UN SOFTWARE PARA LA GESTION DE FALTAS ACADEMICAS Y DISCIPLINARIAS</h1>
       <h2>Registro estudiante</h2>
             <h5>Tabla elaborada por NATALIA SANTOS</h5>
             <label for="">Codigo estudiante:</label>
             
             <input type="text" name="cod_estudiante" id="" autocomplete="off"> <br>
             <label for="">Id tipo documento:</label>

             <input type="text" name="id_tipo_documento" id=""> <br>
             <label for="">Documento:</label>

             <input type="text" name="documento" id=""> <br>
             <label for="">Nombre estudiante:</label>

             <input type="text" name="nombre_estudiante" id=""> <br>
             <label for="">Apellido estudiante:</label>

             <input type="text" name="apellido_estudiante" id=""> <br>
             <label for="">Fecha nacimiento:</label>

             <input type="date" name="fecha_nacimiento" id=""> <br>
             <label for="">Telefono estudiante:</label>

             <input type="tel" name="telefono_estudiante" id=""> <br>
              <label for="">Correo estudiante:</label>

             <input type="email" name="correo_estudiante" id=""> <br>
             <label for="">Direccion estudiante:</label>
             
             <input type="text" name="direccion_estudiante" id=""> <br>

<input type="submit" value="GUARDAR" name="guardar" class="btn btn-primary">
<input type="submit" value="VER" name="ver"  class="btn btn-info">
<input type="submit" value="EDITAR" name="editar"  class="btn btn-secondary">
<input type="submit" value="ELIMINAR" name="eliminar"  class="btn btn-danger">
	</form> <br><br>
    <table class="table table-hover"> 
        <thead>
         <tr>
          <th scope="col">Codigo estudiante</th>
         <th scope="col">Id tipo documento</th>
         <th scope="col">Documento</th>
         <th scope="col">Nombre estudiantes</th>
         <th scope="col">Apellido estudiante</th>
         <th scope="col">Fecha nacimiento</th>
         <th scope="col">Telefono estudiante</th>
         <th scope="col">Correo estudiante</th>
         <th scope="col">Direccion estudiante</th>
</tr>
</thead>
<?php  
$conexion = new mysqli('localhost', 'root', '', 'registro_faltas');

$cod_estudiante=$_POST['cod_estudiante']?? '';
$id_tipo_documento=$_POST['id_tipo_documento']?? '';
$documento=$_POST['documento']?? '';
$nombre_estudiante=$_POST['nombre_estudiante']?? '';
$apellido_estudiante=$_POST['apellido_estudiante']?? '';
$fecha_nacimiento=$_POST['fecha_nacimiento']?? '';
$telefono_estudiante=$_POST['telefono_estudiante']?? '';
$correo_estudiante=$_POST['correo_estudiante']?? '';
$direccion_estudiante=$_POST['direccion_estudiante']?? '';

if(isset($_POST['guardar'])){
$insertar="INSERT INTO registro_faltas (id_tipo_documento, documento, nombre_estudiante, apellido_estudiante, fecha_nacimiento,telefono_estudiante, correo_estudiante, direccion_estudiante) Values ('$id_tipo_documento','$documento','$nombre_estudiante','$apellido_estudiante','$fecha_nacimiento', '$telefono_estudiante','$correo_estudiante','$direccion_estudiante')"; 
$sql = mysqli_query($conexion, $insertar);
echo "✅ Registro Insertado Correctamente";
}

if (isset($_POST['ver'])) {
	$consultar = "SELECT * FROM estudiante";
	$sql=mysqli_query($conexion, $consultar);
	while ($ver=mysqli_fetch_array($sql)){
        echo "<tr><td>";
		echo $ver['cod_estudiante'];
		echo "<td>";
		echo $ver['id_tipo_documento'];
		echo "<td>";
        echo $ver['documento'];
		echo "<td>";
		echo $ver['nombre_estudiante'];
		echo "<td>";
        echo $ver['apellido_estudiante'];
		echo "<td>";
        echo $ver['fecha_nacimiento'];
		echo "<td>";
        echo $ver['telefono_estudiante'];
		echo "<td>";
        echo $ver['correo_estudiante'];
		echo "<td>";
        echo $ver['direccion_estudiante'];
		echo "</td></tr>";
	
	}

}

if (isset($_POST['editar'])){
	$editar="UPDATE registro_faltas SET id_tipo_documento='$id_tipo_documento',documento='$documento',nombre_estudiante='$nombre_estudiante',apellido_estudiante= '$apellido_estudiante',fecha_nacimiento= '$fecha_nacimiento',telefono_estudiante= '$telefono_estudiante',correo_estudiante= '$correo_estudiante',direccion_estudiante= '$direccion_estudiante'
	WHERE cod_estudiante='$cod_estudiante' ";
	$sql=mysqli_query($conexion, $editar);
}
if (isset($_POST['eliminar'])) {
	$eliminar="DELETE FROM registro_faltas WHERE cod_estudiante='$cod_estudiante' ";
$sql=mysqli_query($conexion, $eliminar);

}
?>
</table>
</div>
<script src="bootstrap.bundle.min.js"></script>
</body>
</html> 