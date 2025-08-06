<?php
require('conection.php');
session_start();

// Validar sesión de jefe
if (!isset($_SESSION["usuario_id"]) || $_SESSION["rol"] != 2) {
  echo "Acceso no autorizado.";
  exit;
}

$idJefe = $_SESSION["usuario_id"];

// Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $titulo = $_POST['titulo'];
  $descripcion = $_POST['descripcion'];
  $estado = "abierta"; // estado fijo
  $fecha_ini = $_POST['fecha_ini'];
  $fecha_fin = $_POST['fecha_fin'];

  $sql = "INSERT INTO vacante (titulo, descripcion, estado, fecha_ini, fecha_fin, ID_Jefe)
          VALUES (?, ?, ?, ?, ?, ?)";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "sssssi", $titulo, $descripcion, $estado, $fecha_ini, $fecha_fin, $idJefe);
  $ok = mysqli_stmt_execute($stmt);

  if ($ok) {
    header("Location: vacantes.php");
    exit;
  } else {
    echo "Error al crear la vacante: " . mysqli_error($conn);
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Vacante</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

  <h2 class="mb-4">Crear Nueva Vacante</h2>

  <form method="POST" action="">
    <div class="mb-3">
      <label class="form-label">Título</label>
      <input type="text" name="titulo" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Descripción</label>
      <textarea name="descripcion" class="form-control" rows="4" required></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Fecha de inicio</label>
      <input type="date" name="fecha_ini" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Fecha de finalización</label>
      <input type="date" name="fecha_fin" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success">Crear Vacante</button>
    <a href="vacantes.php" class="btn btn-secondary">Cancelar</a>
  </form>

</body>
</html>
