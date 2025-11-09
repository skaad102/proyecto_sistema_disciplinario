<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <form action="" method="post">
        <h1>DISEÑO DE UN SOFTWARE PARA LA GESTION DE FALTAS ACADEMICAS Y DISCIPLINARIAS</h1>
        <h2>Registro de docente</h2>
        <h5>Tabla elaborada por Danna Agudelo</h5>

        <label for="">Código Docente:</label>
        <input type="number" name="cod_docente"><br>
        

        <!-- CORRECCIÓN: name cambiado a id_tipo_documento para que coincida con PHP -->
        <label for="id_tipo_documento">Tipo de Documento:</label>
        <select name="id_tipo_documento" id="id_tipo_documento" required>
            <option value="1">Cédula de ciudadanía</option>
            <option value="2">Tarjeta de identidad</option>
            <option value="3">Cédula de extranjería</option>
            <option value="4">Pasaporte</option>
        </select><br>

        <label for="">Documento:</label>
        <input type="text" name="documento"><br>

        <label for="">Nombre:</label>
        <input type="text" name="nombre_docente"><br>

        <label for="">Apellido:</label>
        <input type="text" name="apellido_docente"><br>

        <label for="">Teléfono:</label>
        <input type="text" name="telefono_docente"><br>

        <label for="">Correo:</label>
        <input type="text" name="correo_docente"><br>

        <label for="id_asignatura">Asignatura:</label>
        <select name="id_asignatura" id="id_asignatura" required>
            <option value="1">Matemáticas</option>
            <option value="2">Castellano</option>
            <option value="3">Inglés</option>
        </select><br>

        <label for="">ID Usuario:</label>
        <input type="number" name="id_usuario"><br><br>

        <!-- Botones -->
        <button type="submit" name="insertar" class="btn" 
            style="background-color:#28a745; color:white; border:none; padding:8px 15px; border-radius:5px;">
            Insertar
        </button>

        <button type="submit" name="actualizar" class="btn" 
            style="background-color:#007bff; color:white; border:none; padding:8px 15px; border-radius:5px;">
            Actualizar
        </button>

        <button type="submit" name="eliminar" class="btn" 
            style="background-color:#ffc107; color:black; border:none; padding:8px 15px; border-radius:5px;">
            Eliminar
        </button>

        <button type="submit" name="mostrar" class="btn" 
            style="background-color:#5bc0de; color:white; border:none; padding:8px 15px; border-radius:5px;">
            Mostrar
        </button>
    </form><br><br>

    <!-- Tabla -->
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">Código Docente</th>
                <th scope="col">Tipo Documento</th>
                <th scope="col">Documento</th>
                <th scope="col">Nombre</th>
                <th scope="col">Apellido</th>
                <th scope="col">Teléfono</th>
                <th scope="col">Correo</th>
                <th scope="col">ID Asignatura</th>
                <th scope="col">ID Usuario</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $conexion=new mysqli('LOCALHOST', 'root', '', 'registro_faltas');

        $cod=$_POST['cod_docente'] ??'';
        $idTipo=$_POST['id_tipo_documento'] ??'';
        $documento=$_POST['documento'] ??'';
        $nombre=$_POST['nombre_docente'] ??'';
        $apellido=$_POST['apellido_docente'] ??'';
        $telefono=$_POST['telefono_docente'] ??'';
        $correo=$_POST['correo_docente'] ??'';
        $idAsig=$_POST['id_asignatura'] ??'';
        $idUsuario=$_POST['id_usuario'] ??'';

        // Insertar
        if(isset($_POST['insertar'])){
            if(!empty($idTipo) && !empty($documento) && !empty($nombre) && !empty($apellido)){
                $insertar = mysqli_query($conexion, 
                    "INSERT INTO docente (id_tipo_documento, documento, nombre_docente, apellido_docente, telefono_docente, correo_docente, id_asignatura, id_usuario) 
                    VALUES ('$idTipo','$documento','$nombre','$apellido','$telefono','$correo','$idAsig','$idUsuario')");
                if($insertar){
                    header("Location: ".$_SERVER['PHP_SELF']."?ok=1");
                    exit;
                } else {
                    echo "Error al insertar: " . mysqli_error($conexion);
                }
            } else {
                echo "Debes llenar los campos obligatorios antes de insertar.";
            }
        }

        // Actualizar
        if(isset($_POST['actualizar'])){
            if(!empty($cod)){
                $actualizar = mysqli_query($conexion,
                    "UPDATE docente SET 
                    id_tipo_documento='$idTipo',
                    documento='$documento',
                    nombre_docente='$nombre',
                    apellido_docente='$apellido',
                    telefono_docente='$telefono',
                    correo_docente='$correo',
                    id_asignatura='$idAsig',
                    id_usuario='$idUsuario'
                    WHERE cod_docente='$cod'");
                if($actualizar){
                    echo "Registro actualizado correctamente.";
                } else {
                    echo "Error al actualizar: " . mysqli_error($conexion);
                }
            } else {
                echo "Debes ingresar el código de docente para actualizar.";
            }
        }

        // Eliminar
        if(isset($_POST['eliminar'])){
            if(!empty($cod)){
                $eliminar = mysqli_query($conexion, "DELETE FROM docente WHERE cod_docente='$cod'");
                if($eliminar){
                    echo "Registro eliminado correctamente.";
                } else {
                    echo "Error al eliminar: " . mysqli_error($conexion);
                }
            } else {
                echo "Debes ingresar el código de docente para eliminar.";
            }
        }

        // Mostrar
        if(isset($_POST['mostrar'])){
            $resultado = mysqli_query($conexion, "SELECT * FROM docente");
            if(mysqli_num_rows($resultado) > 0){
                while($fila = mysqli_fetch_assoc($resultado)){
                    echo "<tr>";
                    echo "<td>".$fila['cod_docente']."</td>";
                    echo "<td>".$fila['id_tipo_documento']."</td>";
                    echo "<td>".$fila['documento']."</td>";
                    echo "<td>".$fila['nombre_docente']."</td>";
                    echo "<td>".$fila['apellido_docente']."</td>";
                    echo "<td>".$fila['telefono_docente']."</td>";
                    echo "<td>".$fila['correo_docente']."</td>";
                    echo "<td>".$fila['id_asignatura']."</td>";
                    echo "<td>".$fila['id_usuario']."</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No hay registros</td></tr>";
            }
        }
        ?>
        </tbody>
    </table>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
