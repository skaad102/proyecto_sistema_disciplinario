<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Asignaturas</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background-color: white;
            min-height: 100vh;
            padding: 30px 0;
        }
        .container {
            background-color: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            max-width: 900px;
        }
        h1 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        h2 {
            color: #3498db;
            font-size: 20px;
            margin-top: 20px;
            margin-bottom: 15px;
        }
        h5 {
            color: #7f8c8d;
            font-size: 14px;
            font-style: italic;
            margin-bottom: 30px;
        }
        label {
            font-weight: 600;
            color: #34495e;
            margin-top: 10px;
            display: block;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #3498db;
        }
        .btn-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .btn-custom {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(52, 152, 219, 0.3);
        }
        .btn-custom:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(52, 152, 219, 0.4);
        }
        .btn-custom:active {
            transform: translateY(0);
        }
        .table {
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .table thead {
            background-color: #3498db;
            color: white;
        }
        .table thead th {
            font-weight: 600;
            padding: 15px;
            border: none;
        }
        .table tbody tr {
            transition: background-color 0.2s;
        }
        .table tbody tr:hover {
            background-color: #ebf5fb;
        }
        .table tbody td {
            padding: 12px 15px;
            vertical-align: middle;
        }
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body>
<div class="container">
    <form action="" method="post">
        <h1>DISEÑO DE UN SOFTWARE PARA LA GESTIÓN DE FALTAS ACADÉMICAS Y DISCIPLINARIAS</h1>
        <h2>Registro de asignatura</h2>
        <h5>Tabla elaborada por Linda Herazo</h5>

        <label for="cod_asignatura">Código Asignatura:</label>
        <input type="number" name="cod_asignatura" id="cod_asignatura" placeholder="Ingrese el código">

        <label for="nombre_asignatura">Nombre de la Asignatura:</label>
        <input type="text" name="nombre_asignatura" id="nombre_asignatura" placeholder="Ingrese el nombre de la asignatura">

        <!-- Botones -->
        <div class="btn-container">
            <button type="submit" name="insertar" class="btn-custom">
                Insertar
            </button>

            <button type="submit" name="actualizar" class="btn-custom">
                Actualizar
            </button>

            <button type="submit" name="eliminar" class="btn-custom">
                Eliminar
            </button>

            <button type="submit" name="mostrar" class="btn-custom">
                Mostrar
            </button>
        </div>
    </form>

    <?php
    $conexion = new mysqli('localhost', 'root', '', 'registro_faltas');

    $cod = $_POST['cod_asignatura'] ?? '';
    $nombre = $_POST['nombre_asignatura'] ?? '';

    // Insertar
    if(isset($_POST['insertar'])){
        if(!empty($nombre)){
            $insertar = mysqli_query($conexion, 
                "INSERT INTO asignatura (nombre_asignatura) VALUES ('$nombre')");
            if($insertar){
                echo "<div class='alert alert-success'>Registro insertado correctamente.</div>";
            } else {
                echo "<div class='alert alert-error'>Error al insertar: " . mysqli_error($conexion) . "</div>";
            }
        } else {
            echo "<div class='alert alert-error'>Debes llenar el campo del nombre antes de insertar.</div>";
        }
    }

    // Actualizar
    if(isset($_POST['actualizar'])){
        if(!empty($cod)){
            $actualizar = mysqli_query($conexion,
                "UPDATE asignatura SET nombre_asignatura='$nombre' WHERE cod_asignatura='$cod'");
            if($actualizar){
                echo "<div class='alert alert-success'>✓ Registro actualizado correctamente.</div>";
            } else {
                echo "<div class='alert alert-error'>✗ Error al actualizar: " . mysqli_error($conexion) . "</div>";
            }
        } else {
            echo "<div class='alert alert-error'>✗ Debes ingresar el código de asignatura para actualizar.</div>";
        }
    }

    // Eliminar
    if(isset($_POST['eliminar'])){
        if(!empty($cod)){
            $eliminar = mysqli_query($conexion, "DELETE FROM asignatura WHERE cod_asignatura='$cod'");
            if($eliminar){
                echo "<div class='alert alert-success'>✓ Registro eliminado correctamente.</div>";
            } else {
                echo "<div class='alert alert-error'>✗ Error al eliminar: " . mysqli_error($conexion) . "</div>";
            }
        } else {
            echo "<div class='alert alert-error'>✗ Debes ingresar el código de asignatura para eliminar.</div>";
        }
    }
    ?>

    <!-- Tabla -->
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">Código Asignatura</th>
                <th scope="col">Nombre Asignatura</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Mostrar
        if(isset($_POST['mostrar'])){
            $resultado = mysqli_query($conexion, "SELECT * FROM asignatura");
            if(mysqli_num_rows($resultado) > 0){
                while($fila = mysqli_fetch_assoc($resultado)){
                    echo "<tr>";
                    echo "<td>".$fila['cod_asignatura']."</td>";
                    echo "<td>".$fila['nombre_asignatura']."</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2' style='text-align:center; color:#7f8c8d;'>No hay registros para mostrar</td></tr>";
            }
        }
        ?>
        </tbody>
    </table>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>