<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
    if ($_SESSION["rol"] != 2 ) {
        header("Location: vacantes.php");
        exit();
    }
}

require("conection.php");

// Obtener jefes de cátedra (rol = 2)
$sqlJefes = "SELECT id, nombre, apellido FROM persona WHERE rol = 2";
$resJefes = mysqli_query($conn, $sqlJefes);
$jefesdecatedra = mysqli_fetch_all($resJefes, MYSQLI_ASSOC);

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = mysqli_real_escape_string($conn, $_POST["titulo"]);
    $descripcion = mysqli_real_escape_string($conn, $_POST["descripcion"]);
    $fecha_ini = $_POST["fecha_ini"];
    $fecha_fin = $_POST["fecha_fin"];
    $jefe_catedra_id = intval($_POST["jefe_catedra"]);

    // Validación en servidor
    if ($fecha_ini < date('Y-m-d')) {
        die("La fecha de inicio no puede ser anterior a hoy.");
    }
    if ($fecha_fin < $fecha_ini) {
        die("La fecha de fin no puede ser anterior a la fecha de inicio.");
    }

    if ($fecha_ini > date('Y-m-d')) {
        $estado = "sin abrir";
    } else {
        $estado = "abierta";
    }

    $sqlInsert = "INSERT INTO vacante (titulo, descripcion, fecha_ini, fecha_fin, ID_Jefe, estado)
                  VALUES ('$titulo', '$descripcion', '$fecha_ini', '$fecha_fin', $jefe_catedra_id, '$estado')";
    
    if (mysqli_query($conn, $sqlInsert)) {
        header("Location: vacantes.php");
        exit();
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
<div class="container">
<div class="caja_nueva_vacante shadow-lg">

  <h2>Crear Nueva Vacante</h2>
  <form method="POST" action="crear_vacante.php" class="mt-4">

    <div class="mb-3">
      <label for="titulo" class="form-label">Título</label>
      <input type="text" class="form-control" id="titulo" name="titulo" required>
    </div>

    <div class="mb-3">
      <label for="descripcion" class="form-label">Descripción</label>
      <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
    </div>

    <div class="mb-3">
      <label for="fecha_ini" class="form-label">Fecha de Inicio</label>
      <input type="date" class="form-control" id="fecha_ini" name="fecha_ini" 
             min="<?= date('Y-m-d') ?>" required>
    </div>

    <div class="mb-3">
      <label for="fecha_fin" class="form-label">Fecha de Fin</label>
      <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
    </div>

    <div class="mb-3">
      <label for="jefe_catedra" class="form-label">Jefe de Cátedra</label>
      <select class="form-select" id="jefe_catedra" name="jefe_catedra" required>
        <option value="" disabled selected>Seleccione un jefe de cátedra</option>
        <?php foreach ($jefesdecatedra as $jefe): ?>
          <option value="<?= $jefe['id'] ?>">
            <?= htmlspecialchars($jefe['nombre'] . ' ' . $jefe['apellido']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Crear Vacante</button>
    <a href="vacantes.php" class="btn btn-secondary">Cancelar</a>
  </form>

  <script>
    document.getElementById('fecha_ini').addEventListener('change', function() {
      document.getElementById('fecha_fin').min = this.value;
    });
  </script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</div>
</div>
</body>
</html>
