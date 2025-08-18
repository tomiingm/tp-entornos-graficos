<?php
session_start();
require("conection.php");

// SESSION seguros
$usuario_id = isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
$rol = isset($_SESSION['rol']) ? (int) $_SESSION['rol'] : null;

// Obtener ID de vacante desde URL
if (!isset($_GET['id'])) {
    echo "ID de vacante no proporcionado.";
    exit();
}
$idVacante = intval($_GET['id']);

$error = null;
$success = null;

// Obtener datos de la vacante (prepared statement)
$stmtVac = mysqli_prepare($conn, "SELECT * FROM vacante WHERE ID = ?");
mysqli_stmt_bind_param($stmtVac, "i", $idVacante);
mysqli_stmt_execute($stmtVac);
$resVac = mysqli_stmt_get_result($stmtVac);
if (!$resVac || mysqli_num_rows($resVac) == 0) {
    echo "Vacante no encontrada.";
    exit();
}
$vacante = mysqli_fetch_assoc($resVac);

// Mostrar mensaje si vino por finalización
if (isset($_GET['finalizada']) && $_GET['finalizada'] == 1) {
    $success = "La vacante se finalizó correctamente.";
}

// Si hay usuario logueado, obtener datos y estado de postulación
$usuario = null;
$tiene_cv = false;
$ya_postulado = false;
if ($usuario_id !== null) {
    $stmtU = mysqli_prepare($conn, "SELECT * FROM persona WHERE ID = ?");
    mysqli_stmt_bind_param($stmtU, "i", $usuario_id);
    mysqli_stmt_execute($stmtU);
    $resU = mysqli_stmt_get_result($stmtU);
    $usuario = mysqli_fetch_assoc($resU);

    if ($usuario) {
        $tiene_cv = !empty($usuario['cv']) && file_exists($usuario['cv']);
    }

    $stmtCheck = mysqli_prepare($conn, "SELECT 1 FROM postulacion WHERE ID_Persona = ? AND ID_Vacante = ? LIMIT 1");
    mysqli_stmt_bind_param($stmtCheck, "ii", $usuario_id, $idVacante);
    mysqli_stmt_execute($stmtCheck);
    $resCheck = mysqli_stmt_get_result($stmtCheck);
    $ya_postulado = ($resCheck && mysqli_num_rows($resCheck) > 0);
}

// Restricciones de acceso que se aplican solo si hay sesión y rol:
if ($rol !== null) {
    // Si es jefe (rol 2) y no es el jefe de esta vacante, redirigir
    if ($rol === 2 && $vacante['ID_Jefe'] != $usuario_id) {
        header("Location: vacantes.php");
        exit();
    }

    if ($rol === 0 && $vacante['estado'] === "sin abrir") {
        header("Location: vacantes.php");
        exit();
    }
}

// Procesar postulación (solo postulantes logueados - rol 0)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirmar_postulacion"])) {
    if ($usuario_id === null || $rol !== 0) {
        $error = "Debes iniciar sesión como postulante para postularte.";
    } elseif ($ya_postulado) {
        $error = "Ya estás postulado a esta vacante.";
    } elseif (!$tiene_cv) {
        $error = "No tienes cargado un Curriculum.";
    } else {
        $fecha = date("Y-m-d");
        $stmtIns = mysqli_prepare($conn, "INSERT INTO postulacion (ID_Persona, ID_Vacante, fecha_hora_post) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmtIns, "iis", $usuario_id, $idVacante, $fecha);
        if (mysqli_stmt_execute($stmtIns)) {
            $ya_postulado = true;
            $success = "Postulación realizada correctamente.";
        } else {
            $error = "Error al insertar la postulación: " . mysqli_error($conn);
        }
    }
}

// Finalizar vacante (solo rol 1 o 2)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["finalizar_vacante"])) {
    if ($rol === 1 || $rol === 2) {
        $stmtFin = mysqli_prepare($conn, "UPDATE vacante SET estado = 'cerrada' WHERE ID = ?");
        mysqli_stmt_bind_param($stmtFin, "i", $idVacante);
        if (mysqli_stmt_execute($stmtFin)) {
            header("Location: unavacante.php?id=$idVacante&finalizada=1");
            exit();
        } else {
            $error = "Error al finalizar la vacante: " . mysqli_error($conn);
        }
    } else {
        $error = "No tienes permisos para finalizar la vacante.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detalle de Vacante</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/estilosunavacante.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="icon" href="/nuestro-proyecto-web/assets/images/utn.ico" type="image/x-icon">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <div class="collapse navbar-collapse justify-content-center">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link px-4" href="../index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link active rounded-pill px-4 bg-secondary text-white" href="vacantes.php">Vacantes</a></li>
        <?php if ($usuario_id !== null): ?>
        <li class="nav-item"><a class="nav-link px-4" href="../php/perfil.php">Perfil</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-3">
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
</div>

<div class="contenedor">
  <div class="cabecera">
    <h2><?= htmlspecialchars($vacante['titulo']) ?></h2>
    <a href="../index.php" >
    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT_FEqjavcxlrifqvl75bLKmY4my0fdwLqDmQ&s" alt="Logo Universidad" class="imagen">
    </a>
  </div>
</div>
<hr>

<div class="contenedor">
  <div class="columna">
    <p><?= nl2br(htmlspecialchars($vacante['descripcion'])) ?></p>
    <p><strong>Inicio:</strong> <?= htmlspecialchars($vacante['fecha_ini']) ?></p>
    <p><strong>Fin:</strong> <?= htmlspecialchars($vacante['fecha_fin']) ?></p>
    <?php if ($rol === 2 && $vacante['estado'] === 'en revision'): ?>
        <div class="alert alert-warning text-center mt-2">
          La vacante se encuentra actualmente en revisión. Si desea extender la fecha de cierre debe editar la vacante.
        </div>
    <?php endif; ?>
  </div>

  <div class="columna-botones">
    <div class="contenedor-botones">
      <?php if ($rol === 0): // postulante logueado ?>
        <?php if ($ya_postulado): ?>
          <div class="alert alert-info">Ya estás postulado a esta vacante.</div>
        <?php elseif (!$tiene_cv): ?>
          <div class="alert alert-warning">No tienes cargado un Curriculum.</div>
        <?php elseif ($vacante['estado'] === 'abierta'): ?>
          <!-- Botón que abre el modal -->
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPostulacion">
            Postulate
          </button>
        <?php else: ?>
          <div class="alert alert-secondary">La vacante no está disponible para postularse.</div>
        <?php endif; ?>

      <?php elseif ($rol === 1 || $rol === 2): // admin / jefe ?>
        <a href="editar_vacante.php?id=<?= $vacante['ID'] ?>" class="btn btn-warning">Editar</a>
        <a href="orden_de_merito.php?id=<?= $vacante['ID'] ?>" class="btn btn-info">Orden de Mérito</a>
        <?php if ($vacante['estado'] != "cerrada"): ?>
          <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#finalizarModal">Finalizar</button>
        <?php endif; ?>

      <?php else: // visitante no logueado: NO mostrar otros botones ?>
        <!-- Sólo visitante: no mostramos Postulate/Editar/Finalizar -->
      <?php endif; ?>

      <a href="resultados.php?id=<?= $vacante['ID'] ?>" class="btn btn-success">Resultados</a>
    </div>
  </div>
</div>

<?php if ($rol === 0): ?>
<!-- Modal Postulación (solo postulante logueado) -->
<div class="modal fade" id="modalPostulacion" tabindex="-1" aria-labelledby="modalPostulacionLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modalPostulacionLabel">Confirmar postulación</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro de que deseas postularte a <strong><?= htmlspecialchars($vacante['titulo']) ?></strong>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" name="confirmar_postulacion" class="btn btn-primary">Confirmar</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php if ($rol === 1 || $rol === 2): ?>
<!-- Modal Finalizar (solo para admin/jefe) -->
<div class="modal fade" id="finalizarModal" tabindex="-1" aria-labelledby="finalizarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="finalizarModalLabel">Finalizar Vacante</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro de que deseas finalizar la vacante <strong><?= htmlspecialchars($vacante['titulo']) ?></strong>?
      </div>
      <form method="POST">
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="finalizar_vacante" class="btn btn-danger">Finalizar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
