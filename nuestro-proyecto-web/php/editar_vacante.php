<?php
require('conection.php');
session_start();

// Validación: ¿viene el ID por GET?
if (!isset($_GET['id'])) {
  echo "ID de vacante no especificado.";
  exit;
}

$id = $_GET['id'];

// Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $titulo = $_POST['titulo'];
  $descripcion = $_POST['descripcion'];
  $estado = $_POST['estado'];
  $fecha_ini = $_POST['fecha_ini'];
  $fecha_fin = $_POST['fecha_fin'];

  $sql = "UPDATE vacante SET titulo=?, descripcion=?, estado=?, fecha_ini=?, fecha_fin=? WHERE ID=?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "sssssi", $titulo, $descripcion, $estado, $fecha_ini, $fecha_fin, $id);
  mysqli_stmt_execute($stmt);

  if (mysqli_stmt_affected_rows($stmt) >= 0) {
    header("Location: vacantes.php");
    exit;
  } else {
    echo "Error al actualizar: " . mysqli_error($conn);
  }
}

// Traer los datos actuales de la vacante
$sql = "SELECT * FROM vacante WHERE ID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$vacante = mysqli_fetch_assoc($resultado);

if (!$vacante) {
  echo "Vacante no encontrada.";
  exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Vacante</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

  <h2 class="mb-4">Editar Vacante</h2>

  <form method="POST" action="">
    <div class="mb-3">
      <label class="form-label">Título</label>
      <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($vacante['titulo']) ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Descripción</label>
      <textarea name="descripcion" class="form-control" rows="4" required><?= htmlspecialchars($vacante['descripcion']) ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Estado</label>
      <select name="estado" class="form-select" required>
        <option value="abierta" <?= $vacante['estado'] === 'abierta' ? 'selected' : '' ?>>Abierta</option>
        <option value="cerrada" <?= $vacante['estado'] === 'cerrada' ? 'selected' : '' ?>>Cerrada</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Fecha de inicio</label>
      <input type="date" name="fecha_ini" class="form-control" value="<?= $vacante['fecha_ini'] ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Fecha de finalización</label>
      <input type="date" name="fecha_fin" class="form-control" value="<?= $vacante['fecha_fin'] ?>" required>
    </div>

    <button type="submit" class="btn btn-primary">Guardar cambios</button>
    <a href="vacantes.php" class="btn btn-secondary">Cancelar</a>
  </form>

</body>
</html>