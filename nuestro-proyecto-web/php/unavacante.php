<?php
session_start();

// Redirigir si no está logueado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

require("conection.php");

// Obtener el ID de la vacante desde la URL
if (!isset($_GET['id'])) {
    echo "ID de vacante no proporcionado.";
    exit();
}

$idVacante = intval($_GET['id']);

// Buscar la vacante en la base de datos
$sql = "SELECT * FROM vacante WHERE id = $idVacante";
$resultado = mysqli_query($conn, $sql);

if (!$resultado || mysqli_num_rows($resultado) == 0) {
    echo "Vacante no encontrada.";
    exit();
}

$vacante = mysqli_fetch_assoc($resultado);
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
<body >
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
      <div class="collapse navbar-collapse justify-content-center">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link px-4 " href="../index.php">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active rounded-pill px-4 bg-secondary text-white" href="vacantes.php">Vacantes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-4" href="../php/perfil.php">Perfil</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

<div class="contenedor">
<div class="cabecera">
  <?php
  echo "<h2>". $vacante['titulo'] ."</h2>";
  ?>
 <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT_FEqjavcxlrifqvl75bLKmY4my0fdwLqDmQ&s" alt="Logo Universidad" class="imagen">
</div>
</div>
<hr>

<div class="contenedor">
  
  <div class="columna">
    <?php
              
      echo "<p> ". $vacante['descripcion'] . "</p>";
      echo "<p><strong>Inicio: </strong> " . $vacante['fecha_ini'] ."</p>";
      echo "<p><strong>Fin: </strong>" . $vacante['fecha_fin'] ."</p>";
    ?>
  </div>


  <div class="columna-botones">
  <div class="contenedor-botones">
    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 0): ?>
      <form action="crear_postulacion.php" method="post" style="display:inline;">
        <input type="hidden" name="id_persona" value="<?= $_SESSION['usuario_id'] ?>">
        <input type="hidden" name="id_vacante" value="<?= $vacante['ID'] ?>">
        <button type="submit" class="btn btn-primary">Postulate</button>
      </form>
    <?php endif; ?>

    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 1): ?>
      <a href="editar_vacante.php?id=<?= $vacante['ID'] ?>" class="btn btn-warning">Editar</a>
      <a href="orden_de_merito.php?id=<?= $vacante['ID'] ?>" class="btn btn-info">Orden de Mérito</a>
      <a href="resultados.php?id=<?= $vacante['ID'] ?>" class="btn btn-success">Resultados</a>
      <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#finalizarModal">Finalizar</button>
    <?php endif; ?>
  </div>
</div>


<!-- Modals -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmación</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php
          echo "¿Segur@ que quieres postularte a la vacante " . $vacante['titulo'] . " ?";
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary">Confirmar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="finalizarModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmación</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php
          echo "¿Segur@ que quieres finalizar a la vacante " . $vacante['titulo'] . " ? ID: " .$vacante['ID'];
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary">Confirmar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>




</html>
