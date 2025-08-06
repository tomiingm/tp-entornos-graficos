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
$sql = "SELECT * FROM persona WHERE ID = $user_id";
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
    <link rel="stylesheet" href="../css/estilos.css">
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



echo "<div class='container mt-5 border border-light-subtle p-5 rounded-5 shadow-lg'>";
    echo "<div class='container d-flex align-items-center p-0'>";
    echo "<div class='flex-grow-1'>";
        echo "<h1 class='display-1'> " . $usuario['apellido'] . "," ."</h1>";
        echo "<h1 class='display-1'> " . $usuario['nombre'] ."</h1>";
    echo "</div>";
    echo "<div>
        <form action='subirFoto.php' method='post' enctype='multipart/form-data' id='form-upload''>
            
            <input type='hidden' name='userId' value= " . $usuario['ID'] . " '>

            <input type='file' name='profilePhoto' id='file-input' style='display: none; cursor:pointer;' accept='image/png, image/jpeg'; >
            <img src= " . $usuario['fotoperfil'] . " id='profile-image' alt='Foto de perfil' class='rounded-circle cursor:pointer profile-image foto-perfil'>
        </form>
            
        </div>";
    echo "</div>";

    echo "<hr style='border: 1px solid; color: black'>";

    echo "<div class='p-2'>";
    echo "<h3 class='display-5'>Datos Personales</h3>";
        echo "<p> Documento: " . $usuario['DNI']. "</p>";
        echo "<p> Correo: " . $usuario['mail']. "</p>";
        echo "<p> Telefono: " . $usuario['telefono']. "</p>";
        echo "<p> Domicilio: " . $usuario['domicilio']. "</p>";
    echo "</div>";
    


echo "<form method='POST' enctype='multipart/form-data'>
        <div class='mb-3'>
            <label for='cv' class='form-label'>Seleccionar CV (.pdf):</label>
            <input class='form-control' type='file' name='cv' id='cv' accept='.pdf' required>
        </div>
        <button type='submit' class='btn btn-success'>Subir CV</button>
    </form>";

if (isset($mensaje))
{
    echo "<div class='alert alert-info'><?php echo $mensaje; ?></div>";
}
    
if (!empty($usuario['cv']) && file_exists($usuario['cv'])) {
    echo "<div class='mt-3'>
            <a href=".$usuario['cv']." class='btn btn-outline-primary' download>
            Descargar CV
        </a>
    </div>";

    echo "<div class='mt-3'>
        <p class='text-muted'>Aún no subiste tu CV.</p>
    </div>";
};
    
echo "<a href='../php/cerrarsesion.php' class='btn btn-danger px-4 mt-3'>Cerrar Sesión</a>";

echo "</div>";
?>



<script>
    // Obtenemos los elementos del DOM
    const profileImage = document.getElementById('profile-image');
    const fileInput = document.getElementById('file-input');
    const uploadForm = document.getElementById('form-upload');

    // 1. Cuando se hace clic en la imagen...
    profileImage.addEventListener('click', () => {
        // ...simulamos un clic en el input de archivo oculto.
        fileInput.click();
    });

    // 2. Cuando el usuario selecciona un archivo...
    fileInput.addEventListener('change', () => {
        // ...enviamos el formulario automáticamente.
        uploadForm.submit();
    });
</script>

</body>
</html>
