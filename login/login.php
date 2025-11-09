<?php
session_start();

$conexion=new mysqli('LOCALHOST','root','','registro_faltas');

$usuario=$_POST['usuario'];
$clave=$_POST['clave'];

$consultar="SELECT * FROM usuario WHERE usuario='$usuario' AND clave='$clave'";
$sql=mysqli_query($conexion, $consultar);

if(mysqli_num_rows($sql)>0){
    $_SESSION['usuario']=$usuario;
    header('Location: admin.php');
    } else {
        echo"<h3>Usuario o contraseña estan equivocadas</h3>";
}
?>
<a href="index.php">Intentar Nuevamente</a>