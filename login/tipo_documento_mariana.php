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
            <h2>Registro de documento</h2>
            <h5>Tabla elaborada por Mariana Burgos</h5>
            <label for=""> codigo tipo de documento</label>
            <input type="text" name="cod_tipo" id=""><br>
            <label for="">tipo de documento</label>
            <input type="text" name="tipo_documento" id=""><br><br>
            <input type="submit" value="INSERTAR" name="insertar" class="btn btn-danger">
            <input type="submit" value="MOSTRAR" name="mostrar" class="btn btn-warning">
            <input type="submit" value="ACTUALIZAR" name="actualizar" class="btn btn-success">
            <input type="submit" value="ELIMINAR" name="eliminar" class="btn btn-primary">
        </form><br><br>
        <table class="table table-hover">
            <thead>
    <tr>
      <th scope="col">codigo</th>
      <th scope="col">tipo documento</th>
    </tr>
  </thead>
        <?php
        $conexion=new mysqli('LOCALHOST','root','','registro_faltas');

        $cod=$_POST['cod_tipo'];
        $tipo=$_POST['tipo_documento'];

        if(isset($_POST['insertar'])){
            echo "ok, insertar";
            $insertar=mysqli_query($conexion, "INSERT INTO tipo_documento (tipo_documento) VALUE ('$tipo') ");
        }

        if(isset($_POST['mostrar'])){
            $mostrar=mysqli_query($conexion, "SELECT*FROM tipo_documento");
            while($ver=mysqli_fetch_array($mostrar)){
                echo "<tr> <td>";
                echo $ver['cod_tipodocumento'];
                echo "</td> <td>";
                echo $ver['tipo_documento'];
                echo "</td> </tr>";
            }
        }

        if(isset($_POST['actualizar'])){
             $actualizar=mysqli_query($conexion, "UPDATE tipo_documento SET tipo_documento='$tipo' WHERE cod_tipodocumento='$cod' ");
            
            }
        

        if(isset($_POST['eliminar'])){
            $eliminar=mysqli_query($conexion, "DELETE FROM tipo_documento WHERE cod_tipodocumento='$cod'");
        }
        
        ?>
        </table>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>