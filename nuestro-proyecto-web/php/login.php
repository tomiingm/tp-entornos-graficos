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
<body class="bg-body-secondary d-flex align-items-center vh-100">
    <!-- <div class="container contenedor">
        <div class="row justify-content-md-center">
            <div class="col text-end">
                <a href="../index.php" class="btn btn-light rounded-circle shadow-sm ">
                    <i class="bi bi-arrow-left"></i>
                </a>
            </div>
            <div class="container col-8">

                <div class="formulario-box shadow">
                    <h2 class="text-center mb-4">Iniciar Sesión</h2>
                    <?php if ($mensaje) { ?>
                    <div class="alert alert-danger">
                    <?= htmlspecialchars($mensaje) ?>
                    </div>
                    <?php } ?>
                    <form action="login.php" method="post">
                        <div class="mb-3">
                            <label for="dni" class="form-label ">Número de Documento (DNI):</label>
                            <input type="number" class="form-control sin-flechas" name="dni" id="dni" required min="1000000" max="99999999" >
                        </div>
                        <div class="mb-3">
                            <label for="clave" class="form-label">Clave:</label>
                            <input type="password" class="form-control" name="clave" id="clave" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Iniciar Sesion</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div> -->


    <div class="container col-xl-10 col-xxl-8 px-4"> 
    <div class="row align-items-center g-lg-5"> 
        
        <div class="col-lg-7 text-center text-lg-start"> 
            <img src="/nuestro-proyecto-web/assets/images/UTN-Logo-M.png" alt="Logo Universidad" class="logo-facu" >
        </div> 
        <div class="col-md-10 mx-auto col-lg-5">
            
            <form action="login.php" method="post" class="p-4 p-md-5 border rounded-3 bg-body-tertiary"> 
                <div class="form-floating mb-3" > 
                    <input type="number" class="form-control" name="dni" id="dni" required min="1000000" max="99999999"  placeholder="Documento (DNI)"> 
                    <label for="dni">Número de Documento (DNI)</label> 
                </div> 
                    <div class="form-floating mb-3"> 
                        <input id="clave" type="password" class="form-control" name="clave" placeholder="Contraseña" required> 
                        <label for="clave">Contraseña</label> 
                    </div> <div class="checkbox mb-3"> 
                        <label> <input type="checkbox" value="remember-me"> Recordar mis datos</label>
                    </div> 
                    <button class="w-100 btn btn-lg btn-primary" type="submit">Inicia Sesión</button> 
                    <hr class="my-4"> <small class="text-body-secondary">La Facultad no es un lujo, es un derecho. Fachito.</small> 
                </form> 
            </div> 
        </div> 
    </div>
</body>


</html>