<?php
session_start();

if (!isset($_SESSION["usuario_id"]) || !in_array($_SESSION["rol"], [1, 2])) {
    header("Location: login.php");
    exit();
}

require("conection.php");

// Validar que se pasaron los parámetros
if (!isset($_GET["id_persona"]) || !isset($_GET["id_vacante"])) {
    echo "Parámetros incompletos.";
    exit();
}

$idPersona = intval($_GET["id_persona"]);
$idVacante = intval($_GET["id_vacante"]);

// Buscar el correo de la persona
$sql = "SELECT mail FROM persona WHERE ID = $idPersona";
$res = mysqli_query($conn, $sql);
if (!$res || mysqli_num_rows($res) == 0) {
    echo "No se encontró la persona.";
    exit();
}
$persona = mysqli_fetch_assoc($res);
$correo_destino = $persona["mail"];


?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Notificar Postulante</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../css/estilosvacantes.css" rel="stylesheet">
</head>
<body >
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContenido" aria-controls="navbarContenido" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarContenido">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link px-4" href="../index.php">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link rounded-pill active px-4 bg-secondary text-white" href="../php/vacantes.php">Vacantes</a>
        </li>
        <?php if (isset($_SESSION["usuario_id"])): ?>
          <li class="nav-item">
            <a class="nav-link px-4" href="../php/perfil.php">Perfil</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<?php
// Procesar envío de correo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["asunto"], $_POST["mensaje"])) {
    $asunto = trim($_POST["asunto"]);
    $mensaje = trim($_POST["mensaje"]);
    $remitente = $_SESSION["mail"]; 

    $headers = "From: $remitente";

    if (mail($correo_destino, $asunto, nl2br($mensaje), $headers)) {
        echo "<div class='alert alert-success'>Correo enviado con éxito a $correo_destino</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al enviar el correo.</div>";
    }
}
?>
    <div class="caja_nueva_vacante">

<h2>Notificar Postulante</h2>
<p><strong>Destinatario:</strong> <?= htmlspecialchars($correo_destino) ?></p>

<form method="POST">
    <div class="mb-3">
        <label for="asunto" class="form-label">Asunto</label>
        <input type="text" class="form-control" id="asunto" name="asunto" required>
    </div>
    <div class="mb-3">
        <label for="mensaje" class="form-label">Cuerpo del mensaje</label>
        <textarea class="form-control" id="mensaje" name="mensaje" rows="6" required></textarea>
    </div>
    <a href="resultados.php?id=<?= $idVacante ?>" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">Enviar</button>
</form>
    </div>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</html>
