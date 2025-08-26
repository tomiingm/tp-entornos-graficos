<?php
require('conection.php');
session_start();

if (!isset($_GET['id'])) {
  echo "ID de vacante no especificado.";
  exit;
}

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
    if ($_SESSION["rol"] != 2 ) {
        header("Location: vacantes.php");
        exit();
    }
}

$sqlJefes = "SELECT id, nombre, apellido FROM persona WHERE rol = 2";
$resJefes = mysqli_query($conn, $sqlJefes);
$jefesdecatedra = mysqli_fetch_all($resJefes, MYSQLI_ASSOC);

$id = (int) $_GET['id'];
$hoy = date('Y-m-d');

// Traer la vacante
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

// Determinar si puede editar la fecha de inicio
$puedeEditarInicio = ($vacante['fecha_ini'] > $hoy);

$errores = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $titulo = trim($_POST['titulo']);
  $descripcion = trim($_POST['descripcion']);
  $fecha_fin = $_POST['fecha_fin'];
  $jefe_catedra = $_POST['jefe_catedra'];

  // Si puede editar fecha inicio, la tomamos del form, sino mantenemos la original
  $fecha_ini = $puedeEditarInicio ? $_POST['fecha_ini'] : $vacante['fecha_ini'];

  // === Validaciones de fecha (SERVIDOR) ===
  if ($puedeEditarInicio && $fecha_ini < $hoy) {
    $errores[] = "La fecha de inicio no puede ser anterior a hoy.";
  }
  if ($fecha_fin < $fecha_ini) {
    $errores[] = "La fecha de fin no puede ser anterior a la fecha de inicio.";
  }

  // Determinar estado según fechas
  if (empty($errores)) {
    if ($fecha_ini > $hoy) {
      $estado = "sin abrir";
    } elseif ($fecha_ini <= $hoy && $fecha_fin >= $hoy) {
      $estado = "abierta";
    } else {
      $estado = "finalizada";
    }

    $sql = "UPDATE vacante SET titulo=?, descripcion=?, fecha_ini=?, fecha_fin=?, ID_Jefe=?, estado=? WHERE ID=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssisi", $titulo, $descripcion, $fecha_ini, $fecha_fin, $jefe_catedra, $estado, $id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) >= 0) {
      header("Location: vacantes.php");
      exit;
    } else {
      $errores[] = "Error al actualizar: " . mysqli_error($conn);
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Vacante</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/estilosvacantes.css" rel="stylesheet">
  <link href="../css/estilos.css" rel="stylesheet">
  <link rel="icon" href="/nuestro-proyecto-web/assets/images/utn.ico" type="image/x-icon">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContenido" aria-controls="navbarContenido" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarContenido">
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
        <label class="form-label">Fecha de inicio</label>
        <input
          type="date"
          name="fecha_ini"
          id="fecha_ini"
          class="form-control"
          value="<?= htmlspecialchars($vacante['fecha_ini']) ?>"
          min="<?= $hoy ?>"
          <?= $puedeEditarInicio ? '' : 'readonly' ?>>
        <?php if (!$puedeEditarInicio): ?>
          <small class="text-muted">La fecha de inicio no puede modificarse porque la vacante ya ha comenzado.</small>
        <?php endif; ?>
      </div>

      <div class="mb-3">
        <label class="form-label">Fecha de fin</label>
        <input
          type="date"
          name="fecha_fin"
          id="fecha_fin"
          class="form-control"
          value="<?= htmlspecialchars($vacante['fecha_fin']) ?>"
          min="<?= htmlspecialchars($vacante['fecha_ini']) ?>"
          required>
      </div>

      <div class="mb-3">
        <label for="jefe_catedra" class="form-label">Jefe de Cátedra</label>
        <select class="form-select" id="jefe_catedra" name="jefe_catedra" required>
          <option value="" disabled>Seleccione un jefe de cátedra</option>
          <?php foreach ($jefesdecatedra as $jefe): ?>
            <option value="<?= $jefe['id'] ?>" <?= $jefe['id'] == $vacante['ID_Jefe'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($jefe['nombre'] . ' ' . $jefe['apellido']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <button type="submit" class="btn btn-primary">Guardar cambios</button>
      <a href="unavacante.php?id=<?= $id ?>" class="btn btn-secondary">Cancelar</a>
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

  ini.addEventListener('change', syncMinFin);
  document.addEventListener('DOMContentLoaded', syncMinFin);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
