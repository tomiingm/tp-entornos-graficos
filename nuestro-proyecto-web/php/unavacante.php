<?php
session_start();
require("conection.php");

// Redirigir si no está logueado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}



$usuario_id = (int) $_SESSION["usuario_id"];

// Obtener ID de vacante desde URL
if (!isset($_GET['id'])) {
    echo "ID de vacante no proporcionado.";
    exit();
}

$idVacante = intval($_GET['id']);

// Obtener datos del usuario
$sql_usuario = "SELECT * FROM persona WHERE id = $usuario_id";
$res_usuario = mysqli_query($conn, $sql_usuario);
$usuario = mysqli_fetch_assoc($res_usuario);

// Verificar si tiene CV
$tiene_cv = !empty($usuario['cv']) && file_exists($usuario['cv']);

// Verificar si ya está postulado
$sql_check_postulacion = "SELECT * FROM postulacion WHERE ID_Persona = $usuario_id AND ID_Vacante = $idVacante";
$res_postulacion = mysqli_query($conn, $sql_check_postulacion);
$ya_postulado = mysqli_num_rows($res_postulacion) > 0;

// Procesar postulación si se envió
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirmar_postulacion"])) {
    $fecha = date("Y-m-d");

    // Escapar los valores para evitar inyecciones
    $usuario_id_escapado = mysqli_real_escape_string($conn, $usuario_id);
    $idVacante_escapado = mysqli_real_escape_string($conn, $idVacante);
    $fecha_escapada = mysqli_real_escape_string($conn, $fecha);

    // Armar y ejecutar la consulta directamente
    $sql_insert = "INSERT INTO postulacion (ID_Persona, ID_Vacante, fecha_hora_post) 
                   VALUES ('$usuario_id_escapado', '$idVacante_escapado', '$fecha_escapada')";

    $resultado_insert = mysqli_query($conn, $sql_insert);

    if ($resultado_insert) {
        $ya_postulado = true;
    } else {
        echo "Error al insertar la postulación: " . mysqli_error($conn);
    }
}

// Obtener datos de la vacante
$sql = "SELECT * FROM vacante WHERE id = $idVacante";
$resultado = mysqli_query($conn, $sql);
if (!$resultado || mysqli_num_rows($resultado) == 0) {
    echo "Vacante no encontrada.";
    exit();
}
if (isset($_GET['finalizada']) && $_GET['finalizada'] == 1) {
    echo '<div class="alert alert-success text-center">La vacante se finalizó correctamente.</div>';
}

$vacante = mysqli_fetch_assoc($resultado);
if ($vacante['estado'] == "cerrada" and $_SESSION['rol'] == 0){
  header("Location: vacantes.php");
} else if ($_SESSION['rol']==2 and $vacante['ID_Jefe'] != $_SESSION['usuario_id'] ) {
  header("Location: vacantes.php");
}

// finalizar vacante
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["finalizar_vacante"])) {
    $sql_finalizar = "UPDATE vacante SET estado = 'cerrada' WHERE ID = $idVacante";
    if (mysqli_query($conn, $sql_finalizar)) {
        header("Location: unavacante.php?id=$idVacante&finalizada=1");
        exit();
    } else {
        echo "Error al finalizar la vacante: " . mysqli_error($conn);
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
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <div class="collapse navbar-collapse justify-content-center">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link px-4" href="../index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link active rounded-pill px-4 bg-secondary text-white" href="vacantes.php">Vacantes</a></li>
        <li class="nav-item"><a class="nav-link px-4" href="../php/perfil.php">Perfil</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="contenedor">
  <div class="cabecera">
    <h2><?= htmlspecialchars($vacante['titulo']) ?></h2>
    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT_FEqjavcxlrifqvl75bLKmY4my0fdwLqDmQ&s" alt="Logo Universidad" class="imagen">
  </div>
</div>
<hr>

<div class="contenedor">
  <div class="columna">
    <p><?= nl2br(htmlspecialchars($vacante['descripcion'])) ?></p>
    <p><strong>Inicio:</strong> <?= htmlspecialchars($vacante['fecha_ini']) ?></p>
    <p><strong>Fin:</strong> <?= htmlspecialchars($vacante['fecha_fin']) ?></p>
  </div>

  <div class="columna-botones">
    <div class="contenedor-botones">
      <?php if ($_SESSION['rol'] == 0): ?>
        <?php if ($ya_postulado): ?>
          <div class="alert alert-info">Ya estás postulado a esta vacante.</div>
        <?php elseif (!$tiene_cv): ?>
          <div class="alert alert-warning">No tienes cargado un Curriculum.</div>
        <?php else: ?>
          <!-- Botón que abre el modal -->
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPostulacion">
            Postulate
          </button>
        <?php endif; ?>
      <?php elseif ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2): ?>
        <a href="editar_vacante.php?id=<?= $vacante['ID'] ?>" class="btn btn-warning">Editar</a>
        <a href="orden_de_merito.php?id=<?= $vacante['ID'] ?>" class="btn btn-info">Orden de Mérito</a>
        <a href="resultados.php?id=<?= $vacante['ID'] ?>" class="btn btn-success">Resultados</a>
        <?php 
          if($vacante['estado'] != "cerrada"){
         echo "<button type='button' class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#finalizarModal'>Finalizar</button>";
          }
        ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal Postulación -->
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

<!-- Modal Finalizar (solo para admin) -->
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
