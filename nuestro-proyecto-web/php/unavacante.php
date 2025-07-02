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
        </ul>
      </div>
    </div>
  </nav>
 <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT_FEqjavcxlrifqvl75bLKmY4my0fdwLqDmQ&s" 
     alt="Logo Universidad" class="imagen">

<hr>

<div class="contenedor">
  

  <div class="columna vacante">
    <?php
      echo "<h2>". $vacante['titulo'] ."</h2>";        
      echo "<p> ". $vacante['descripcion'] . "</p>";
      echo "<p><strong>Inicio: </strong> " . $vacante['fecha_ini'] ."</p>";
      echo "<p><strong>Fin: </strong>" . $vacante['fecha_fin'] ."</p>";
    ?>
  </div>


  <div class="columna">
    <div class="contenedor-botones">
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"> Postulate </button>
      <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 1): ?>
        <a href="editar_vacante.php?id=<?= $vacante['ID'] ?>" class="btn btn-warning">Editar</a>
        <a href="orden_merito.php?id=<?= $vacante['ID'] ?>" class="btn btn-info">Orden de Mérito</a>
        <a href="finalizar_vacante.php?id=<?= $vacante['ID'] ?>" class="btn btn-danger">Finalizar</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmación</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php
          echo "¿Segur@ que quieres postularte a la vacante" . $vacante['titulo'] . " ?";
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
