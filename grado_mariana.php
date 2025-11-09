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
            <h2>Registro de grados</h2>
            <h5>Tabla elaborada por Mariana Burgos</h5>
            <label for=""> codigo de grado</label>
            <input type="text" name="cod_grado" id=""><br>
            <label for=""> grado</label>
            <input type="text" name="grados" id=""><br><br>
            <input type="submit" value="INSERTAR" name="insertar" class="btn btn-warning">
            <input type="submit" value="MOSTRAR" name="mostrar" class="btn btn-info">
            <input type="submit" value="ACTUALIZAR" name="actualizar" class="btn btn-success">
            <input type="submit" value="ELIMINAR" name="eliminar" class="btn btn-danger">
        </form><br><br>
        <table class="table table-hover">
            <thead>
    <tr>
      <th scope="col">codigo</th>
      <th scope="col">grado</th>
    </tr>
  </thead>
        <?php
        $conexion=new mysqli('LOCALHOST','root','','registro_faltas');

        $cod=$_POST['cod_grado'];
        $grados=$_POST['grados']; 

        if(isset($_POST['insertar'])){
            echo "ok, insertar";
            $insertar=mysqli_query($conexion, "INSERT INTO grado (grados) VALUES ('$grados') ");
        }

        if(isset($_POST['mostrar'])){
            $mostrar=mysqli_query($conexion, "SELECT*FROM grado");
            while($mirar=mysqli_fetch_array($mostrar)){
                echo "<tr> <td>";
                echo $mirar['cod_grado'];
                echo "</td> <td>";
                echo $mirar['grados'];
                echo "</td> </tr>";
            }
        }

        if(isset($_POST['actualizar'])){
             $actualizar=mysqli_query($conexion, "UPDATE grado SET grados='$grados' WHERE cod_grado='$cod' ");
            
            }
        

        if(isset($_POST['eliminar'])){
            $eliminar=mysqli_query($conexion, "DELETE FROM grado WHERE cod_grado='$cod'");
        }
        
        ?>
        </table>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>