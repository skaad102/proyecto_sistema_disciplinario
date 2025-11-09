<!DOCTYPE html>
<html lang="es">
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
        <h2>Registro de tipo de falta</h2>
        <h5>Tabla elaborada por Linda Herazo</h5>

        <label for="">Código TipoFalta:</label>
        <input type="number" name="cod_tipofalta"><br>
        

        <label for="">Tipo de Falta:</label>
        <input type="text" name="tipo_falta"><br>

        <label for="">Descripción:</label><br>
        <textarea name="descripcion_tipo" rows="4" cols="50"></textarea><br><br>

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
                <th scope="col">Código</th>
                <th scope="col">Tipo de Falta</th>
                <th scope="col">Descripción</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $conexion = new mysqli('localhost', 'root', '', 'registro_faltas');

        $cod = $_POST['cod_tipofalta'] ?? '';
        $tipo = $_POST['tipo_falta'] ?? '';
        $desc = $_POST['descripcion_tipo'] ?? '';

        // Insertar
        if(isset($_POST['insertar'])){
            if(!empty($tipo)){
                $insertar = mysqli_query($conexion,
                    "INSERT INTO tipo_falta (tipo_falta, descripcion_tipo) VALUES ('$tipo','$desc')");
                if($insertar){
                    header("Location: ".$_SERVER['PHP_SELF']."?ok=1");
                    exit;
                } else {
                    echo "Error al insertar: " . mysqli_error($conexion);
                }
            } else {
                echo "Debes llenar el campo del tipo antes de insertar.";
            }
        }

        // Actualizar
        if(isset($_POST['actualizar'])){
            if(!empty($cod)){
                $actualizar = mysqli_query($conexion,
                    "UPDATE tipo_falta SET tipo_falta='$tipo', descripcion_tipo='$desc' WHERE cod_tipofalta='$cod'");
                if($actualizar){
                    echo "Registro actualizado correctamente.";
                } else {
                    echo "Error al actualizar: " . mysqli_error($conexion);
                }
            } else {
                echo "Debes ingresar el código para actualizar.";
            }
        }

        // Eliminar
        if(isset($_POST['eliminar'])){
            if(!empty($cod)){
                $eliminar = mysqli_query($conexion, "DELETE FROM tipo_falta WHERE cod_tipofalta='$cod'");
                if($eliminar){
                    echo "Registro eliminado correctamente.";
                } else {
                    echo "Error al eliminar: " . mysqli_error($conexion);
                }
            } else {
                echo "Debes ingresar el código para eliminar.";
            }
        }

        // Mostrar
        if(isset($_POST['mostrar'])){
            $resultado = mysqli_query($conexion, "SELECT * FROM tipo_falta");
            if(mysqli_num_rows($resultado) > 0){
                while($fila = mysqli_fetch_assoc($resultado)){
                    echo "<tr>";
                    echo "<td>".$fila['cod_tipofalta']."</td>";
                    echo "<td>".$fila['tipo_falta']."</td>";
                    echo "<td>".$fila['descripcion_tipo']."</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No hay registros</td></tr>";
            }
        }
        ?>
        </tbody>
    </table>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
