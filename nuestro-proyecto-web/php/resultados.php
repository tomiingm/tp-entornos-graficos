<?php
session_start();


$idVacante = intval($_GET['id']);
require('conection.php');

$sql = "SELECT * FROM postulacion po 
INNER JOIN persona p ON p.ID = po.ID_Persona
WHERE ID_Vacante = $idVacante
ORDER BY po.puntaje DESC;";
$resultado = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultados</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="../css/estilosvacantes.css" rel="stylesheet">
  <link rel="icon" href="/nuestro-proyecto-web/assets/images/utn.ico" type="image/x-icon">
</head>
<body class="d-flex flex-column min-vh-100">

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
  <a href="../index.php">
  <img class="imagen" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT_FEqjavcxlrifqvl75bLKmY4my0fdwLqDmQ&s" alt="Logo Universidad" class="logo-facu">
        </a>

<div class="linea d-flex justify-content-between align-items-center">
  <div class="titulo mb-0">
    <p class="mb-0">Personas</p>
  </div>
  <div class="d-flex align-items-center gap-3">
    <input type="text" class="buscar" name="busqueda" id="busqueda" placeholder="Buscar">
    <a href="unavacante.php?id=<?= urlencode($idVacante) ?>" class="btn btn-outline-secondary btn-volver">
      <i class="bi bi-arrow-left"></i>
    </a>
  </div>
</div>

<div class="scroll-vacantes">
  <?php if (mysqli_num_rows($resultado) === 0): ?>
    <div class="alert alert-info text-center">
      No hay personas postuladas a esta vacante.
    </div>
  <?php else: ?>
    <?php while($unaPersona = mysqli_fetch_assoc($resultado)) { ?>
      <div class='persona vacante p-3 mb-4 border rounded'>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($unaPersona["nombre"]) ." ". htmlspecialchars($unaPersona["apellido"]) ?></p>
        <p class='correo'><strong>Correo:</strong> <?= htmlspecialchars($unaPersona["mail"]) ?></p>
        <p class='documento'><strong>DNI:</strong> <?= htmlspecialchars($unaPersona["DNI"]) ?></p>

        <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2)): ?>
          <div class='mt-2'>
              <a href='<?= htmlspecialchars($unaPersona['cv']) ?>' class='btn btn-outline-primary' download>Descargar CV</a>
          </div>
          <div class='mt-2'>
              <a href='resultado_item.php?id_persona=<?= urlencode($unaPersona['ID']) ?>&id_vacante=<?= urlencode($idVacante) ?>' class='btn btn-outline-success'>Asignar puntaje</a>
          </div>
        <?php endif; ?>

        <?php
          $idPersona = intval($unaPersona['ID']);
          $queryPuntaje = "SELECT SUM(resultado) AS total FROM resultado_item WHERE ID = $idPersona AND ID_Vacante = $idVacante";
          $resPuntaje = mysqli_query($conn, $queryPuntaje);
          $puntaje = mysqli_fetch_assoc($resPuntaje)['total'];
        ?>
        <div class='mt-2 d-flex justify-content-between align-items-center'>
          <?php if ($puntaje !== null): ?>
            <span class='badge bg-info text-dark mb-0'>Puntaje total: <?= $puntaje ?></span>
            <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2)): ?>
              <a href='notificar.php?id_persona=<?= urlencode($unaPersona['ID']) ?>&id_vacante=<?= urlencode($idVacante) ?>' class='btn btn-warning btn-notificar'>Notificar</a>
            <?php endif; ?>
          <?php else: ?>
            <span class='badge bg-secondary'>Aún no se asignó puntaje</span>
          <?php endif; ?>
        </div>
      </div>
    <?php } ?>
  <?php endif; ?>
</div>

</div>

<script>
  function normalizar(texto) {
    return texto.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
  }

  document.getElementById('busqueda').addEventListener('input', function() {
    const filtro = normalizar(this.value);
    const personas = document.querySelectorAll('.persona');
    personas.forEach(function(persona) {
      const textoPersona = normalizar(persona.textContent);
      persona.style.display = textoPersona.includes(filtro) ? 'block' : 'none';
    });
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>