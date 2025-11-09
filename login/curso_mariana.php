<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css\bootstrap.min.css">
</head>
<body>
    <div class="container">
    <form action="" method="POST">
        <h1>DISEÑO DE UN SOFTWARE PARA LA GESTION DE FALTAS ACADEMICAS Y DISCIPLINARIAS</h1>
        <h2>REGISTRO CURSO </h2>
         <h5>Tabla elaborada por Mariana  Burgos</h5>
        <label for="cod_curso">Cod curso:</label>
        <input type="text" name="cod_curso" id="cod_curso"> <br>
        
         <label for="id_grado">id grado:</label>
        <select name="id_grado" id="id_grado">
            <option value="">Elige una opción</option>
            <option value="SEXTO">sexto</option>
            <option value="SEPTIMO">septimo</option>
            <option value="OCTAVO">octavo</option>
            <option value="NOVENO">noveno</option>
            <option value="DECIMO">decimo</option>
            <option value="ONCE">once</option>
        </select>
        <br>
        <label for="curso">curso:</label>
        <input type="text" name="curso" id="curso">
         <br>

         <label for="id_docente">id docente:</label>
        <select name="id_docente" id="id_docente">
            <option value="">Elige una opción</option>
            <option value="DAVID PALMET">david palmet</option>
            <option value="CINTYA HERNANDEZ">cintya hernandeez</option>
            <option value="MARY  MORENO">mary moreno</option>
            <option value="JEISON CORRALES">jeison corrales</option>
            <option value="ANGELICA SALCEDO">angelica salcedo</option>
            <option value="JANES SUAREZ">janez suarez</option>
        </select>
        <br><br>

         <input type="submit" value="GUARDAR" name="guardar" class="btn btn-primary">
         <input type="submit" value="MOSTRAR" name="mostrar" class="btn btn-warning">
         <input type="submit" value="EDITAR" name="editar" class="btn btn-danger">
         <input type="submit" value="BORRAR" name="borrar" class="btn btn-info">
    </form> <br><br>
     <table class="table table-hover"> 
        <thead>
         <tr>
          <th scope="col">Cod curso</th>
         <th scope="col">id_grado</th>
         <th scope="col">Curso</th>
         <th scope="col">id_docente</th>
</tr>
</thead>
<?php
$conexion=new mysqli('localhost', 'root', '', 'registro_faltas');

$cod_estado = isset($_POST['cod_curso']) ? $_POST['cod_curso'] : "";
$id_grado = isset($_POST['id_grado']) ? $_POST['id_grado'] : "";
$curso = isset($_POST['curso']) ? $_POST['curso'] : "";
$id_docente = isset($_POST['id_docente']) ? $_POST['id_docente'] : "";

// GUARDAR
if (isset($_POST['guardar'])){
    $insertar="INSERT INTO curso (curso) VALUES ('$curso')";
    $sql=mysqli_query($conexion, $insertar);
    echo "✅ Registro Insertado Correctamente";
}

// VER
if (isset($_POST['mostrar'])) {
    $consultar = "SELECT * FROM curso";
    $sql=mysqli_query($conexion, $consultar);
    while ($ver=mysqli_fetch_array($sql)){
       echo "<tr><td>";
		echo $ver['cod_curso'];
		echo "</td> <td>";
		echo $ver['id_grado'];
        echo "</td> <td>";
        echo $ver['curso'];
		echo "</td> <td>";
		echo $ver['id_docente'];
		echo "<td></tr>";
    }
}

// EDITAR
if (isset($_POST['editar'])) {
    $editar = "UPDATE curso SET curso='$curso' WHERE cod_curso='$cod_curso', id_grado='$id_grado', curso='$curso', id_docente='$id_docente'";
    $sql = mysqli_query($conexion, $editar);
    echo "✏ Registro editado correctamente";
}

// ELIMINAR
if (isset($_POST['eliminar'])) {
    $eliminar = "DELETE FROM curso WHERE cod_curso='$cod_curso', id_grado='$id_grado', curso='$curso', id_docente='$id_docente'";
    $sql = mysqli_query($conexion, $eliminar);
    echo "🗑 Registro eliminado correctamente";
}
?>
</div>
<script src="bootstrap.bundle.min.js"></script>
</body>
</html>