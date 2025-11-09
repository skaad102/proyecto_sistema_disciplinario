<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="bootstrap/bootstrap-icons.min.css">
</head>
<body>
    <!--contenedor general-->
<div class="container-fluid p-0" >
    <!--fila general-->
<div class="row g-0 min-vh-100">
    <!--columna 1-->
<div class="col-12 col-md-6 d-flex justify-content-center align-items-center" style="background-image: url('img/fondo2.jpg'); background-size: cover; 
            background-repeat: no-repeat; 
            background-position: center;"> 
<img src="img/logo.png" style="width: 50%;" alt="">
</div>
<!--columna 2-->
<div class="col-12 col-md-6 d-flex justify-content-center align-items-center" > 
    <div class="formulario" style="width: 90%;">

<div class="row mt-2 d-flex justify-content-center">
<div class="col-12 col-md-8 text-center">
<form action="login.php" method="POST">
    <h2>Login- iniciar sesion</h2>
</div>
</div>

<div class="row mt-2 d-flex justify-content-center">
<div class="col-12 col-md-2">
     <label for="" class="form-label">Usuario</label>
</div>
<div class="col-12 col-md-6">
    <input type="text" name="usuario" id="" class="form-control">
</div>
</div>

<div class="row mt-2 d-flex justify-content-center">
<div class="col-12 col-md-2">
     <label for="" class="form-label">clave</label>
</div>
<div class="col-12 col-md-6">
    <input type="password" name="clave" id="" class="form-control">
</div>
</div>

<div class="row mt-2 d-flex justify-content-center">
<div class="col-12 col-md-8 text-center">
    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i> iniciar</button>
                   </div>
               </div>
           </form>
         </div>
      </div>
     </div>
 </div>
<script src="bootstrap/bootstrap.bundle.min.js"></script>    
</body>
</html>  