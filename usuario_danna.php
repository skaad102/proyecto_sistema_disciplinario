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
        <h2>Registro de usuario</h2>
        <h5>Tabla elaborada por Danna Agudelo</h5>

        <label>Código Usuario:</label>
        <input type="number" name="cod_usuario"><br>
       

        <label>Usuario:</label>
        <input type="text" name="usuario"><br>

        <label>Clave:</label>
        <input type="text" name="clave"><br>

        <label>ID Rol:</label>
        <input type="number" name="id_rol"><br><br>

        <!-- Botones -->
        <button type="submit" name="insertar" class="btn" 
                style="background-color:#007bff; color:white; border:none; padding:8px 15px; border-radius:5px;">
            Insertar
        </button>

        <button type="submit" name="actualizar" class="btn" 
                style="background-color:#28a745; color:white; border:none; padding:8px 15px; border-radius:5px;">
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
    <?php
        $conexion = new mysqli('LOCALHOST', 'root', '', 'registro_faltas');
        if ($conexion->connect_errno) {
            echo "<p style='color:red;'>Error de conexión: " . $conexion->connect_error . "</p>";
            exit;
        }

        $cod     = $_POST['cod_usuario'] ?? '';
        $id      = $_POST['id_rol'] ?? '';
        $clave   = $_POST['clave'] ?? '';
        $usuario = $_POST['usuario'] ?? '';

        // insertar
        if(isset($_POST['insertar'])){
            if(!empty($id) && !empty($clave) && !empty($usuario)){
                $insertar = mysqli_query($conexion, 
                    "INSERT INTO usuario (id_rol, clave, usuario) 
                    VALUES ('$id', '$clave', '$usuario')");
                
                if($insertar){
                    echo "<p style='color:green;'>Registro insertado correctamente.</p>";
                } else {
                    echo "<p style='color:red;'>Error al insertar: " . mysqli_error($conexion) . "</p>";
                }
            } else {
                echo "<p style='color:red;'>Debes llenar todos los campos obligatorios antes de insertar.</p>";
            }
        }

        // actualizar
        if(isset($_POST['actualizar'])){
            if(!empty($cod)){
                $actualizar = mysqli_query($conexion, 
                    "UPDATE usuario SET 
                     id_rol='$id', 
                     clave='$clave', 
                     usuario='$usuario'
                     WHERE cod_usuario='$cod' ");
                
                if($actualizar){
                    echo "<p style='color:green;'>Registro actualizado correctamente.</p>";
                } else {
                    echo "<p style='color:red;'>Error al actualizar: " . mysqli_error($conexion) . "</p>";
                }
            } else {
                echo "<p style='color:red;'>Debes ingresar el código de usuario para actualizar.</p>";
            }
        }

        // eliminar
        if(isset($_POST['eliminar'])){
            if(!empty($cod)){
                $eliminar = mysqli_query($conexion, "DELETE FROM usuario WHERE cod_usuario='$cod'");
                if($eliminar){
                    echo "<p style='color:orange;'>Registro eliminado correctamente.</p>";
                } else {
                    echo "<p style='color:red;'>Error al eliminar: " . mysqli_error($conexion) . "</p>";
                }
            } else {
                echo "<p style='color:red;'>Debes ingresar el código de usuario para eliminar.</p>";
            }
        }
    ?>

    <!-- Tabla SOLO si presiona mostrar -->
    <table class="table table-hover">
      <thead>
        <tr>
            <th scope="col">Código Usuario</th>
            <th scope="col">Usuario</th>
            <th scope="col">Clave</th>
            <th scope="col">ID Rol</th>
        </tr>
      </thead>
      <tbody>
      <?php
        if(isset($_POST['mostrar'])){
            $mostrar = mysqli_query($conexion, "SELECT * FROM usuario");
            if ($mostrar){
                while($ver = mysqli_fetch_array($mostrar)){
                    echo "<tr>";
                    echo "<td>" . $ver['cod_usuario'] . "</td>";
                    echo "<td>" . $ver['usuario'] . "</td>";
                    echo "<td>" . $ver['clave'] . "</td>";
                    echo "<td>" . $ver['id_rol'] . "</td>";
                    echo "</tr>";
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
