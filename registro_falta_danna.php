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
        <h2>formulario de registro falta</h2>
        <h5>Tabla elaborada por Danna Agudelo</h5>

        <label for="">Código de registro:</label>
        <input type="number" name="cod_registro"><br>

        <label for="">fecha de registro:</label>
        <input type="date" name="fecha_registro"><br>

        <label for="">hora de registro:</label>
        <input type="time" name="hora_registro"><br>

        <label for="">id de estudiante:</label>
        <input type="number" name="id_estudiante"><br>

        <label for="">id de docente:</label>
        <input type="number" name="id_docente"><br>

        <label for="">id de curso:</label>
        <input type="number" name="id_curso"><br>

        <label for="">id tido de falta:</label>
        <input type="number" name="id_tipofalta"><br>

        <label for="">id falta:</label>
        <input type="number" name="id_falta"><br>

        <label for="">descripcion de falta:</label>
        <textarea name="descripcion_falta" style="width:100%; max-width:600px; height:100px;"></textarea><br>

        <label for="">descargos de falta:</label>
        <textarea name="descargos_falta" style="width:100%; max-width:600px; height:100px;"></textarea><br>

        <label for="">correctivos disciplinarios:</label>
        <textarea name="correctivos_disciplinarios" style="width:100%; max-width:600px; height:100px;"></textarea><br>

        <label for="">compromisos:</label>
        <textarea name="compromisos" style="width:100%; max-width:600px; height:100px;"></textarea><br>

        <label for="">observaciones:</label>
        <textarea name="observaciones" style="width:100%; max-width:600px; height:100px;"></textarea><br>


        <!-- Botones -->
        <button type="submit" name="insertar" class="btn" 
            style="background-color:#28a745; color:white; border:none; padding:8px 15px; border-radius:5px;">
            Insertar
        </button>

        <button type="submit" name="mostrar" class="btn" 
            style="background-color:#5bc0de; color:white; border:none; padding:8px 15px; border-radius:5px;">
            Mostrar
        </button>

        <button type="submit" name="actualizar" class="btn" 
            style="background-color:#007bff; color:white; border:none; padding:8px 15px; border-radius:5px;">
            Actualizar
        </button>

        <button type="submit" name="eliminar" class="btn" 
            style="background-color:#ffc107; color:black; border:none; padding:8px 15px; border-radius:5px;">
            Eliminar
        </button>

        
    </form><br><br>

    <!-- Tabla -->
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">Código de registro</th>
                <th scope="col">fecha de registro</th>
                <th scope="col">hora de registro</th>
                <th scope="col">id de estudiante</th>
                <th scope="col">id de docente</th>
                <th scope="col">id de curso</th>
                <th scope="col">id tipo de falta</th>
                <th scope="col">id falta</th>
                <th scope="col">descripcion de falta</th>
                <th scope="col">descargos falta</th>
                <th scope="col">correctivos disciplinarios</th>
                <th scope="col">compromisos</th>
                <th scope="col">observaciones</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $conexion=new mysqli('LOCALHOST', 'root', '', 'registro_faltas');

        $cod=$_POST['cod_registro '] ??'';
        $fecha=$_POST['fecha_registro'] ??'';
        $hora=$_POST['hora_registro'] ??'';
        $estudiante=$_POST['id_estudiante'] ??'';
        $docente=$_POST['id_docente'] ??'';
        $curso=$_POST['id_curso'] ??'';
        $tipofalta=$_POST['id_tipofalta'] ??'';
        $falta=$_POST['id_falta'] ??'';
        $descripcion=$_POST['descripcion_falta'] ??'';
        $descargos=$_POST['descargos_falta'] ??'';
        $correctivos=$_POST['correctivos_disciplinarios'] ??'';
        $compromisos=$_POST['compromisos'] ??'';
        $observaciones=$_POST['observaciones'] ??'';

        // Insertar
          
        
        ?>
        </tbody>
    </table>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
