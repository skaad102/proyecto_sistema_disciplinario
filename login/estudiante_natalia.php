<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estudiantes</title>
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
            max-width: 1100px;
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
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
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
        .btn-success {
            background-color: #27ae60;
            box-shadow: 0 4px 6px rgba(39, 174, 96, 0.3);
        }
        .btn-success:hover {
            background-color: #229954;
        }
        .btn-warning {
            background-color: #f39c12;
            box-shadow: 0 4px 6px rgba(243, 156, 18, 0.3);
        }
        .btn-warning:hover {
            background-color: #d68910;
        }
        .btn-danger {
            background-color: #e74c3c;
            box-shadow: 0 4px 6px rgba(231, 76, 60, 0.3);
        }
        .btn-danger:hover {
            background-color: #c0392b;
        }
        .table-responsive {
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .table {
            margin-bottom: 0;
        }
        .table thead {
            background-color: #3498db;
            color: white;
        }
        .table thead th {
            font-weight: 600;
            padding: 15px 10px;
            border: none;
            font-size: 13px;
        }
        .table tbody tr {
            transition: background-color 0.2s;
        }
        .table tbody tr:hover {
            background-color: #ebf5fb;
        }
        .table tbody td {
            padding: 12px 10px;
            vertical-align: middle;
            font-size: 13px;
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
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .table thead th,
            .table tbody td {
                font-size: 11px;
                padding: 8px 5px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <form action="" method="POST">
        <h1>DISEÑO DE UN SOFTWARE PARA LA GESTIÓN DE FALTAS ACADÉMICAS Y DISCIPLINARIAS</h1>
        <h2>Registro de Estudiantes</h2>
        <h5>Tabla elaborada por Natalia Santos</h5>

        <div class="form-row">
            <div class="form-group">
                <label for="cod_estudiante">Código Estudiante:</label>
                <input type="number" name="cod_estudiante" id="cod_estudiante" placeholder="Ingrese el código" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="id_tipo_documento">ID Tipo Documento:</label>
                <input type="number" name="id_tipo_documento" id="id_tipo_documento" placeholder="Ingrese el ID del tipo de documento">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="documento">Documento:</label>
                <input type="text" name="documento" id="documento" placeholder="Ingrese el documento">
            </div>

            <div class="form-group">
                <label for="nombre_estudiante">Nombre Estudiante:</label>
                <input type="text" name="nombre_estudiante" id="nombre_estudiante" placeholder="Ingrese el nombre">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="apellido_estudiante">Apellido Estudiante:</label>
                <input type="text" name="apellido_estudiante" id="apellido_estudiante" placeholder="Ingrese el apellido">
            </div>

            <div class="form-group">
                <label for="fecha_nacimiento">Fecha Nacimiento:</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="telefono_estudiante">Teléfono Estudiante:</label>
                <input type="tel" name="telefono_estudiante" id="telefono_estudiante" placeholder="Ingrese el teléfono">
            </div>

            <div class="form-group">
                <label for="correo_estudiante">Correo Estudiante:</label>
                <input type="email" name="correo_estudiante" id="correo_estudiante" placeholder="Ingrese el correo">
            </div>
        </div>

        <label for="direccion_estudiante">Dirección Estudiante:</label>
        <input type="text" name="direccion_estudiante" id="direccion_estudiante" placeholder="Ingrese la dirección">

        <!-- Botones -->
        <div class="btn-container">
            <button type="submit" name="guardar" class="btn-custom btn-success">
                ✓ Guardar
            </button>

            <button type="submit" name="ver" class="btn-custom">
                👁 Ver
            </button>

            <button type="submit" name="editar" class="btn-custom btn-warning">
                ✏ Editar
            </button>

            <button type="submit" name="eliminar" class="btn-custom btn-danger">
                🗑 Eliminar
            </button>
        </div>
    </form>

    <?php
    $conexion = new mysqli('localhost', 'root', '', 'registro_faltas');

    $cod_estudiante = $_POST['cod_estudiante'] ?? '';
    $id_tipo_documento = $_POST['id_tipo_documento'] ?? '';
    $documento = $_POST['documento'] ?? '';
    $nombre_estudiante = $_POST['nombre_estudiante'] ?? '';
    $apellido_estudiante = $_POST['apellido_estudiante'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $telefono_estudiante = $_POST['telefono_estudiante'] ?? '';
    $correo_estudiante = $_POST['correo_estudiante'] ?? '';
    $direccion_estudiante = $_POST['direccion_estudiante'] ?? '';

    // Insertar
    if(isset($_POST['guardar'])){
        if(!empty($documento) && !empty($nombre_estudiante)){
            $insertar = "INSERT INTO estudiante (id_tipo_documento, documento, nombre_estudiante, apellido_estudiante, fecha_nacimiento, telefono_estudiante, correo_estudiante, direccion_estudiante) 
                        VALUES ('$id_tipo_documento', '$documento', '$nombre_estudiante', '$apellido_estudiante', '$fecha_nacimiento', '$telefono_estudiante', '$correo_estudiante', '$direccion_estudiante')"; 
            $sql = mysqli_query($conexion, $insertar);
            if($sql){
                echo "<div class='alert alert-success'>✓ Registro insertado correctamente.</div>";
            } else {
                echo "<div class='alert alert-error'>✗ Error al insertar: " . mysqli_error($conexion) . "</div>";
            }
        } else {
            echo "<div class='alert alert-error'>✗ Debes llenar al menos el documento y nombre del estudiante.</div>";
        }
    }

    // Editar
    if(isset($_POST['editar'])){
        if(!empty($cod_estudiante)){
            $editar = "UPDATE estudiante SET 
                      id_tipo_documento='$id_tipo_documento',
                      documento='$documento',
                      nombre_estudiante='$nombre_estudiante',
                      apellido_estudiante='$apellido_estudiante',
                      fecha_nacimiento='$fecha_nacimiento',
                      telefono_estudiante='$telefono_estudiante',
                      correo_estudiante='$correo_estudiante',
                      direccion_estudiante='$direccion_estudiante'
                      WHERE cod_estudiante='$cod_estudiante'";
            $sql = mysqli_query($conexion, $editar);
            if($sql){
                echo "<div class='alert alert-success'>✓ Registro actualizado correctamente.</div>";
            } else {
                echo "<div class='alert alert-error'>✗ Error al actualizar: " . mysqli_error($conexion) . "</div>";
            }
        } else {
            echo "<div class='alert alert-error'>✗ Debes ingresar el código del estudiante para editar.</div>";
        }
    }

    // Eliminar
    if(isset($_POST['eliminar'])){
        if(!empty($cod_estudiante)){
            $eliminar = "DELETE FROM estudiante WHERE cod_estudiante='$cod_estudiante'";
            $sql = mysqli_query($conexion, $eliminar);
            if($sql){
                echo "<div class='alert alert-success'>✓ Registro eliminado correctamente.</div>";
            } else {
                echo "<div class='alert alert-error'>✗ Error al eliminar: " . mysqli_error($conexion) . "</div>";
            }
        } else {
            echo "<div class='alert alert-error'>✗ Debes ingresar el código del estudiante para eliminar.</div>";
        }
    }
    ?>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Código</th>
                    <th scope="col">ID Tipo Doc</th>
                    <th scope="col">Documento</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Apellido</th>
                    <th scope="col">Fecha Nac.</th>
                    <th scope="col">Teléfono</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Dirección</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // Mostrar
            if(isset($_POST['ver'])){
                $consultar = "SELECT * FROM estudiante";
                $resultado = mysqli_query($conexion, $consultar);
                if(mysqli_num_rows($resultado) > 0){
                    while($ver = mysqli_fetch_assoc($resultado)){
                        echo "<tr>";
                        echo "<td>".$ver['cod_estudiante']."</td>";
                        echo "<td>".$ver['id_tipo_documento']."</td>";
                        echo "<td>".$ver['documento']."</td>";
                        echo "<td>".$ver['nombre_estudiante']."</td>";
                        echo "<td>".$ver['apellido_estudiante']."</td>";
                        echo "<td>".$ver['fecha_nacimiento']."</td>";
                        echo "<td>".$ver['telefono_estudiante']."</td>";
                        echo "<td>".$ver['correo_estudiante']."</td>";
                        echo "<td>".$ver['direccion_estudiante']."</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' style='text-align:center; color:#7f8c8d;'>No hay registros para mostrar</td></tr>";
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>