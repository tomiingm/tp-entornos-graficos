<?php
session_start();

// Redirigir si no está logueado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

require("conection.php");

$user_id = (int) $_SESSION["usuario_id"];

// Buscar la vacante en la base de datos
$sql = "SELECT * FROM persona WHERE id = $user_id";
$resultado = mysqli_query($conn, $sql);

if (!$resultado || mysqli_num_rows($resultado) == 0) {
    echo "Usuario no encontrado";
    exit();
}

if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'exito') {
    $mensaje = "CV subido correctamente.";
}

$usuario = mysqli_fetch_assoc($resultado);

// Procesar el formulario si se envió
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['cv'])) {
    $tipo = mime_content_type($_FILES['cv']['tmp_name']);
    
    if ($tipo !== 'application/pdf') {
        $mensaje = "El archivo debe ser un PDF válido.";
    } else {
        $nombre_cv = "cv_" . $usuario["DNI"] . ".pdf";
        $ruta_destino = "../uploads/" . $nombre_cv;

        if (move_uploaded_file($_FILES['cv']['tmp_name'], $ruta_destino)) {
            $sql = "UPDATE PERSONA SET cv = '$ruta_destino' WHERE DNI = " . $usuario["DNI"];
            if (mysqli_query($conn, $sql)) {
                header("Location: perfil.php?mensaje=exito");
                exit();
                } else {
                $mensaje = "Error al guardar la ruta del CV en la base de datos.";
}
        } else {
            $mensaje = "Error al subir el archivo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Perfil</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <div class="collapse navbar-collapse justify-content-center">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link px-4" href="../index.php">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-4" href="../php/vacantes.php">Vacantes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link  rounded-pill active px-4 bg-secondary text-white" href="php/#">Perfil</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<?php
echo "<p> Nombre: " . $usuario['nombre']. "<p>";
echo "<p> Apellido: " . $usuario['apellido']. "<p>";
echo "<p> Documento: " . $usuario['DNI']. "<p>";
echo "<p> Correo: " . $usuario['mail']. "<p>";
?>

<!-- Botón para subir CV -->
<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="cv" class="form-label">Seleccionar CV (.pdf):</label>
        <input class="form-control" type="file" name="cv" id="cv" accept=".pdf" required>
    </div>
    <button type="submit" class="btn btn-success">Subir CV</button>
</form>

<?php if (isset($mensaje)) : ?>
    <div class="alert alert-info"><?php echo $mensaje; ?></div>
<?php endif; ?>

<!-- Botón de descarga de CV -->
<?php if (!empty($usuario['cv']) && file_exists($usuario['cv'])): ?>
    <div class="mt-3">
        <a href="<?php echo $usuario['cv']; ?>" class="btn btn-outline-primary" download>
            Descargar CV
        </a>
    </div>
<?php else: ?>
    <div class="mt-3">
        <p class="text-muted">Aún no subiste tu CV.</p>
    </div>
<?php endif; ?>

<!-- Botón de cerrar sesión -->
<?php
echo "<a href='../php/cerrarsesion.php' class='btn btn-danger px-4 mt-3'>Cerrar Sesión</a>";
?>

</body>
</html>
