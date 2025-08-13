<?php session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/nuestro-proyecto-web/css/estilos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="/nuestro-proyecto-web/assets/images/utn.ico" type="image/x-icon">
    <title>Inicia Sesión - UTN</title>
</head>

<?php

require('conection.php');
if (!$conn) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni = $_POST["dni"];
    $clave = $_POST["clave"];

    $sql = "SELECT * FROM persona WHERE DNI = '$dni'";
    $resultado = mysqli_query($conn, $sql);

    if (mysqli_num_rows($resultado) == 1) {
        $usuario = mysqli_fetch_assoc($resultado);

        if (password_verify($clave, $usuario["clave"])) {
            $_SESSION["mail"]= $usuario["mail"];
            $_SESSION["dni"]= $usuario["dni"];
            $_SESSION["usuario_id"] = $usuario["ID"];
            $_SESSION["nombre"] = $usuario["nombre"];
            $_SESSION["apellido"] = $usuario["apellido"];
            $_SESSION["rol"] = $usuario["rol"] ;

            header("Location: vacantes.php"); // REDIRIGIR
            exit();
        } else {
            $mensaje = "Clave incorrecta.";
        }
    } else {
        $mensaje = "Usuario no encontrado.";
    }
}

?>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
      <div class="collapse navbar-collapse justify-content-center">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link active rounded-pill px-4 bg-secondary text-white" href="../index.php">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-4" href="vacantes.php">Vacantes</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class=" d-flex align-items-center vh-100">
    <div class="container col-xl-10 col-xxl-8 px-4"> 
    <div class="row align-items-center g-lg-5"> 
        
        <div class="col-lg-7 text-center text-lg-start"> 
            <img src="/nuestro-proyecto-web/assets/images/UTN-Logo-M.png" alt="Logo Universidad" class="logo-facu" id="image-utn" >
        </div> 

        <div class="col-md-10 mx-auto col-lg-5">
            
            <form action="login.php" method="post" class="p-4 p-md-5 border rounded-3 bg-body-tertiary shadow-lg"> 
                <div class="form-floating mb-3" > 
                    <input type="number" class="form-control" name="dni" id="dni" required min="1000000" max="99999999"  placeholder="Documento (DNI)"> 
                    <label for="dni">Número de Documento (DNI)</label> 
                </div>
                <?php if ($mensaje) { ?>
                    <div class="text-danger">
                    <?= htmlspecialchars($mensaje) ?>
                    </div>
                    <?php } ?>
                    <div class="form-floating mb-3"> 
                        <input id="clave" type="password" class="form-control" name="clave" placeholder="Contraseña" required> 
                        <label for="clave">Contraseña</label> 
                    </div> <div class="checkbox mb-3"> 
                        <label> <input type="checkbox" value="remember-me"> Recordar mis datos</label>
                    </div> 
                    <button class="w-100 btn btn-lg btn-primary" type="submit">Iniciar Sesión</button> 
                </form> 
            </div> 
        </div> 
    </div>
                </div>
</body>


</html>