<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/estilos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<?php
require('conection.php');
if (!$conn) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $password = password_hash($_POST["clave"], PASSWORD_DEFAULT); //Encriptacion
    $dni = $_POST["dni"];
    $mail = $_POST["mail"];
    $domicilio = $_POST["domicilio"];
    $telefono = $_POST["telefono"];
    $rol = $_POST["rol"];

    $sql = "INSERT INTO persona (nombre, apellido, mail, clave, DNI, rol, domicilio, telefono) 
            VALUES ('$nombre','$apellido','$mail','$password','$dni','$rol','$domicilio','$telefono')";


    if (mysqli_query($conn, $sql)) {
        $mensaje = "¡Registro exitoso!";
    } else {
        $mensaje = "Error: " . mysqli_error($conn);
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
    <div class="container contenedor">
        <div class="row justify-content-md-center">
            <div class="col text-end">
                <a href="../index.php" class="btn btn-light rounded-circle shadow-sm ">
                    <i class="bi bi-arrow-left"></i>
                </a>
            </div>
            <div class="container col-8">

                <div class="formulario-box shadow">
                    <h2 class="text-center mb-4">Registrate</h2>
                    <?php if ($mensaje){
                    echo "<div class='alert alert-info'>";
                    echo $mensaje;
                    echo "</div>";
                    } ?>
                    <form action="registro.php" method="post">
                        <div class="mb-3">
                            <label for="dni" class="form-label">Número de Documento (DNI):</label>
                            <input type="number" class="form-control" name="dni" id="dni" required min="1000000" max="99999999">
                        </div>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido" class="form-label">Apellido:</label>
                            <input type="text" class="form-control" name="apellido" id="apellido" required>
                        </div>
                        <div class="mb-3">
                            <label for="mail" class="form-label">E-mail:</label>
                            <input type="email" class="form-control" name="mail" id="mail" required>
                        </div>
                        <div class="mb-3">
                            <label for="domicilio" class="form-label">Domicilio:</label>
                            <input type="text" class="form-control" name="domicilio" id="domicilio">
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono:</label>
                            <input type="text" class="form-control" name="telefono" id="telefono">
                        </div>
                        <div class="mb-3">
                            <label for="clave" class="form-label">Clave:</label>
                            <input type="password" class="form-control" name="clave" id="clave" required>
                        </div>
                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol:</label>
                                <select class="form-select" name="rol" id="rol" required>
                                    <option value="" disabled selected>Selecciona un rol</option>
                                    <option value="0">Postulante</option>
                                    <option value="2">Jefe de cátedra</option>
                                </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Registrarse</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</body>

</html>