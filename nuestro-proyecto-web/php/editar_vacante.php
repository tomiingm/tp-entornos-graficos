<?php
require('conection.php');
session_start();

if (!isset($_GET['id'])) {
  echo "ID de vacante no especificado.";
  exit;
}

$id = (int) $_GET['id'];
$hoy = date('Y-m-d');

$errores = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $titulo = trim($_POST['titulo']);
  $descripcion = trim($_POST['descripcion']);
  $estado = $_POST['estado'];
  $fecha_ini = $_POST['fecha_ini'];
  $fecha_fin = $_POST['fecha_fin'];

  // === Validaciones de fecha (SERVIDOR) ===
  // Igual que en "crear": bloquear inicio en pasado
  if ($fecha_ini < $hoy) {
    $errores[] = "La fecha de inicio no puede ser anterior a hoy.";
  }
  if ($fecha_fin < $fecha_ini) {
    $errores[] = "La fecha de fin no puede ser anterior a la fecha de inicio.";
  }

  if (empty($errores)) {
    $sql = "UPDATE vacante SET titulo=?, descripcion=?, estado=?, fecha_ini=?, fecha_fin=? WHERE ID=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $titulo, $descripcion, $estado, $fecha_ini, $fecha_fin, $id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) >= 0) {
      header("Location: vacantes.php");
      exit;
    } else {
      $errores[] = "Error al actualizar: " . mysqli_error($conn);
    }
  }
}

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
  <link href="../css/estilosvacantes.css" rel="stylesheet">
  <link rel="icon" href="/nuestro-proyecto-web/assets/images/utn.ico" type="image/x-icon">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <div class="collapse navbar-collapse justify-content-center">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link px-4" href="../index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link active rounded-pill px-4 bg-secondary text-white" href="vacantes.php">Vacantes</a></li>
        <?php if (isset($_SESSION['usuario_id'])): ?>
          <li class="nav-item"><a class="nav-link px-4" href="../php/perfil.php">Perfil</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  <div class="caja_nueva_vacante shadow-lg">

    <h2 class="mb-4">Editar Vacante</h2>

    <?php if (!empty($errores)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errores as $e): ?>
          <div><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="" class="mt-4" novalidate>
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
          <option value="abierta"     <?= $vacante['estado'] === 'abierta' ? 'selected' : '' ?>>Abierta</option>
          <option value="cerrada"     <?= $vacante['estado'] === 'cerrada' ? 'selected' : '' ?>>Cerrada</option>
          <option value="en revision" <?= $vacante['estado'] === 'en revision' ? 'selected' : '' ?>>En revisión</option>
          <option value="sin abrir"   <?= $vacante['estado'] === 'sin abrir' ? 'selected' : '' ?>>Sin abrir</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Fecha de inicio</label>
        <input
          type="date"
          name="fecha_ini"
          id="fecha_ini"
          class="form-control"
          value="<?= htmlspecialchars($vacante['fecha_ini']) ?>"
          min="<?= $hoy ?>"
          required>
      </div>

      <div class="mb-3">
        <label class="form-label">Fecha de finalización</label>
        <input
          type="date"
          name="fecha_fin"
          id="fecha_fin"
          class="form-control"
          value="<?= htmlspecialchars($vacante['fecha_fin']) ?>"
          min="<?= htmlspecialchars($vacante['fecha_ini']) ?>"
          required>
      </div>

      <button type="submit" class="btn btn-primary">Guardar cambios</button>
      <a href="vacantes.php" class="btn btn-secondary">Cancelar</a>
    </form>
  </div>
</div>

<script>
  const hoy = '<?= $hoy ?>';
  const ini = document.getElementById('fecha_ini');
  const fin = document.getElementById('fecha_fin');

  function syncMinFin() {
    fin.min = ini.value || hoy;
    if (fin.value && fin.value < fin.min) {
      fin.value = fin.min;
    }
  }

  ini.min = hoy;

  ini.addEventListener('change', syncMinFin);
  document.addEventListener('DOMContentLoaded', syncMinFin);
</script>

</body>
</html>
