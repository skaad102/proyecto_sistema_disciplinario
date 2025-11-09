<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <form action="" method="post">
        <h1>DISEÑO DE UN SOFTWARE PARA LA GESTION DE FALTAS ACADEMICAS Y DISCIPLINARIAS</h1>
        <h2>Registro de seguimiento</h2>
        <h5>Tabla elaborada por Danna  Agudelo</h5>

        <label>Código Seguimiento:</label>
        <input type="number" name="cod_seguimiento"><br>
       

        <label>Id registro falta:</label>
        <input type="number" name="id_registrofalta"><br>

        <label>Fecha de seguimiento:</label>
        <input type="text" name="fecha_seguimiento"><br>

        <label>Observaciones:</label><br>
        <textarea name="observaciones" style="width:100%; max-width:600px; height:100px;"></textarea><br><br>

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
    </form>

    <br>

    <?php
    // Conexión (usa la misma que tienes)
    $conexion = new mysqli('LOCALHOST', 'root', '', 'registro_faltas');
    if ($conexion->connect_errno) {
        echo "<p style='color:red;'>Error de conexión: " . $conexion->connect_error . "</p>";
        exit;
    }

    // recoger valores del POST
    $cod         = $_POST['cod_seguimiento'] ?? '';
    $id_regfalta = $_POST['id_registrofalta'] ?? '';
    $fecha       = $_POST['fecha_seguimiento'] ?? '';
    $observacion = $_POST['observaciones'] ?? '';

    if (isset($_GET['msg'])) {
        if ($_GET['msg'] == 'insertado') echo "<p style='color:green;'>Registro insertado correctamente ✅</p>";
        if ($_GET['msg'] == 'actualizado') echo "<p style='color:green;'>Registro actualizado correctamente ✅</p>";
        if ($_GET['msg'] == 'eliminado') echo "<p style='color:orange;'>Registro eliminado correctamente ✅</p>";
    }

    // ---------- INSERTAR ----------
    if (isset($_POST['insertar'])) {
        if (!empty($id_regfalta) && !empty($fecha) && !empty($observacion)) {
            $sql = "INSERT INTO seguimiento (id_registrofalta, fecha_seguimiento, observaciones)
                    VALUES ('$id_regfalta', '$fecha', '$observacion')";
            $res = mysqli_query($conexion, $sql);
            if ($res) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?msg=insertado");
                exit;
            } else {
                echo "<p style='color:red;'>Error al insertar: " . mysqli_error($conexion) . "</p>";
            }
        } else {
            echo "<p style='color:red;'>Debes llenar los campos obligatorios (id_registrofalta, fecha, observaciones).</p>";
        }
    }

    // ---------- ACTUALIZAR ----------
    if (isset($_POST['actualizar'])) {
        if (!empty($cod)) {
            $sql = "UPDATE seguimiento SET
                    id_registrofalta='$id_regfalta',
                    fecha_seguimiento='$fecha',
                    observaciones='$observacion'
                    WHERE cod_seguimiento='$cod'";
            $res = mysqli_query($conexion, $sql);
            if ($res) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?msg=actualizado");
                exit;
            } else {
                echo "<p style='color:red;'>Error al actualizar: " . mysqli_error($conexion) . "</p>";
            }
        } else {
            echo "<p style='color:red;'>Debes ingresar el código de seguimiento para actualizar.</p>";
        }
    }

    // ---------- ELIMINAR ----------
    if (isset($_POST['eliminar'])) {
        if (!empty($cod)) {
            $sql = "DELETE FROM seguimiento WHERE cod_seguimiento='$cod'";
            $res = mysqli_query($conexion, $sql);
            if ($res) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?msg=eliminado");
                exit;
            } else {
                echo "<p style='color:red;'>Error al eliminar: " . mysqli_error($conexion) . "</p>";
            }
        } else {
            echo "<p style='color:red;'>Debes ingresar el código de seguimiento para eliminar.</p>";
        }
    }
    ?>

    <!-- Tabla (se rellena cuando presionas Mostrar) -->
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">Cod seguimiento</th>
                <th scope="col">Id registro falta</th>
                <th scope="col">Fecha seguimiento</th>
                <th scope="col">Observaciones</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (isset($_POST['mostrar'])) {
            $sql = "SELECT * FROM seguimiento";
            $res = mysqli_query($conexion, $sql);
            if (!$res) {
                echo "<tr><td colspan='4' style='color:red;'>Error en la consulta: " . mysqli_error($conexion) . "</td></tr>";
            } else {
                if (mysqli_num_rows($res) == 0) {
                    echo "<tr><td colspan='4'>No hay registros</td></tr>";
                } else {
                    while ($fila = mysqli_fetch_assoc($res)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($fila['cod_seguimiento']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['id_registrofalta']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['fecha_seguimiento']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['observaciones']) . "</td>";
                        echo "</tr>";
                    }
                }
            }
        }
        ?>
        </tbody>
    </table>

</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
