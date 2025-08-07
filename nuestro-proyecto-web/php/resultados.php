<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}
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
  <link href="../css/estilosvacantes.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">


<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <div class="collapse navbar-collapse justify-content-center">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link px-4" href="../index.php">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link rounded-pill active px-4 bg-secondary text-white" href="../php/vacantes.php">Vacantes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-4" href="../php/perfil.php">Perfil</a>
          </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">

  <img class="imagen" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT_FEqjavcxlrifqvl75bLKmY4my0fdwLqDmQ&s" alt="Logo Universidad" class="logo-facu">

  <div class="linea">
    <div class="titulo">
      <p>Personas</p>
    </div>
    <div>
      <input type="text" class="buscar" name="busqueda" id="busqueda" placeholder="Buscar">
    </div>
  </div>

  <div class="scroll-vacantes">
  <?php while($unaPersona = mysqli_fetch_assoc($resultado)) { 
    echo "<div class='persona vacante p-3 mb-4 border rounded'>";

    echo "<p><strong>Nombre:</strong> " . htmlspecialchars($unaPersona["nombre"]) ." ". htmlspecialchars($unaPersona["apellido"]) . "</p>" ;
    echo "<p class='correo'><strong>Correo:</strong> " . htmlspecialchars($unaPersona["mail"]) . "</p>";
    echo "<p class='documento'><strong>DNI:</strong> " . htmlspecialchars($unaPersona["DNI"]) . "</p>";

    if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
    echo "<div class='mt-2'>";
    echo "<a href='" . htmlspecialchars($unaPersona['cv']) . "' class='btn btn-outline-primary' download>Descargar CV</a>";
    echo "</div>";

    echo "<div class='mt-2'>";
    echo "<a href='resultado_item.php?id_persona=" . urlencode($unaPersona['ID']) . "&id_vacante=" . urlencode($idVacante) . "' class='btn btn-outline-success'>Asignar puntaje</a>";
    echo "</div>";
  }

    $idPersona = intval($unaPersona['ID']);
    $queryPuntaje = "SELECT SUM(resultado) AS total FROM resultado_item WHERE ID = $idPersona AND ID_Vacante = $idVacante";
    $resPuntaje = mysqli_query($conn, $queryPuntaje);
    $puntaje = mysqli_fetch_assoc($resPuntaje)['total'];

    echo "<div class='mt-2'>";
    if ($puntaje !== null) {
        echo "<span class='badge bg-info text-dark'>Puntaje total: $puntaje</span>";
    } else {
        echo "<span class='badge bg-secondary'>Aún no se asignó puntaje</span>";
    }
    echo "</div>";

    echo "</div>";
} ?>
</div>

<script>
  function normalizar(texto) {
    return texto
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .toLowerCase();
  }

  document.getElementById('busqueda').addEventListener('input', function() {
    const filtro = normalizar(this.value);
    const personas = document.querySelectorAll('.persona');

    personas.forEach(function(persona) {
      const textoPersona = normalizar(persona.textContent);
      if (textoPersona.includes(filtro)) {
        persona.style.display = 'block';
      } else {
        persona.style.display = 'none';
      }
    });
  });
</script>

</body>
</html>
