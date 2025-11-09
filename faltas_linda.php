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
            <h2>Registro de faltas</h2>
            <h5>Tabla elaborada por Linda Herazo</h5>

            <label>Código Falta:</label>
            <input type="number" name="cod_falta"><br>
            

            <label>ID Tipo Falta:</label>
            <input type="number" name="id_tipofalta"><br>

            <label>Falta:</label>
            <input type="text" name="falta"><br>

            <label>Descripción Falta:</label>
            <textarea name="descripcion_falta" style="width:400px; height:80px;"></textarea><br><br>

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
                    style="background-color:#17a2b8; color:white; border:none; padding:8px 15px; border-radius:5px;">
                Mostrar
            </button>
        </form><br><br>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Código Falta</th>
                    <th scope="col">ID Tipo Falta</th>
                    <th scope="col">Falta</th>
                    <th scope="col">Descripción Falta</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $conexion = new mysqli('localhost', 'root', '', 'registro_faltas');

                $cod  = $_POST['cod_falta'] ?? '';
                $id   = $_POST['id_tipofalta'] ?? '';
                $falta = $_POST['falta'] ?? '';
                $descripcion = $_POST['descripcion_falta'] ?? '';

                // Insertar
                if(isset($_POST['insertar'])){
                    if(!empty($id) && !empty($falta) && !empty($descripcion)){
                        $insertar = mysqli_query($conexion, 
                            "INSERT INTO faltas (id_tipofalta, falta, descripcion_falta) 
                             VALUES ('$id', '$falta', '$descripcion')");
                        if($insertar){
                            echo "<p style='color:green;'>Registro insertado correctamente.</p>";
                        } else {
                            echo "<p style='color:red;'>Error al insertar: " . mysqli_error($conexion) . "</p>";
                        }
                    } else {
                        echo "<p style='color:red;'>Debes llenar todos los campos antes de insertar.</p>";
                    }
                }

                // Actualizar
                if(isset($_POST['actualizar'])){
                    if(!empty($cod)){
                        $actualizar = mysqli_query($conexion, 
                            "UPDATE faltas SET 
                             id_tipofalta='$id',
                             falta='$falta',
                             descripcion_falta='$descripcion'
                             WHERE cod_falta='$cod'");
                        if($actualizar){
                            echo "<p style='color:green;'>Registro actualizado correctamente.</p>";
                        } else {
                            echo "<p style='color:red;'>Error al actualizar: " . mysqli_error($conexion) . "</p>";
                        }
                    } else {
                        echo "<p style='color:red;'>Debes ingresar el código de falta para actualizar.</p>";
                    }
                }

                // Eliminar
                if(isset($_POST['eliminar'])){
                    if(!empty($cod)){
                        $eliminar = mysqli_query($conexion, "DELETE FROM faltas WHERE cod_falta='$cod'");
                        if($eliminar){
                            echo "<p style='color:orange;'>Registro eliminado correctamente.</p>";
                        } else {
                            echo "<p style='color:red;'>Error al eliminar: " . mysqli_error($conexion) . "</p>";
                        }
                    } else {
                        echo "<p style='color:red;'>Debes ingresar el código de falta para eliminar.</p>";
                    }
                }

                // Mostrar
                if(isset($_POST['mostrar'])){
                    $mostrar = mysqli_query($conexion, "SELECT * FROM faltas");
                    while($ver = mysqli_fetch_array($mostrar)){
                        echo "<tr>";
                        echo "<td>" . $ver['cod_falta'] . "</td>";
                        echo "<td>" . $ver['id_tipofalta'] . "</td>";
                        echo "<td>" . $ver['falta'] . "</td>";
                        echo "<td>" . $ver['descripcion_falta'] . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
