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
      echo "<div class='vacante'>" ;
      echo "<p> Nombre: " . htmlspecialchars($unaPersona["nombre"]) ." ". htmlspecialchars($unaPersona["apellido"]) . "</p>" ;
      echo "<p class='correo'> Correo: " . htmlspecialchars($unaPersona["mail"]) . "</p>";
      echo "<p class='documento'> DNI: " . htmlspecialchars($unaPersona["DNI"]) . "</p>";
      echo "<div class='mt-3'>";
      echo "<a href=". $unaPersona['cv'] . " class='btn btn-outline-primary' download>";
      echo "Descargar CV </a> </div>";
      echo "<a href='resultado_item.php'> Asignar puntaje </a>";
      echo "</div>";
    } ?>
  </div>
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
      const descripcion = persona.querySelector('.descripcion-recortada').textContent;
      if (normalizar(descripcion).includes(filtro)) {
        persona.style.display = 'block';
      } else {
        persona.style.display = 'none';
      }
    });
  });
</script>

</body>
</html>
