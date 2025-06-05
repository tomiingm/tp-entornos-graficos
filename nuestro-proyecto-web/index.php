<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal de Vacantes Docentes UTN</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<?php
session_start();
?>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
      <div class="collapse navbar-collapse justify-content-center">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link active rounded-pill px-4 bg-secondary text-white" href="#">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-4" href="php/vacantes.php">Vacantes</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container d-flex flex-column align-items-center text-center">

    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT_FEqjavcxlrifqvl75bLKmY4my0fdwLqDmQ&s" alt="Logo Universidad" style="max-width: 800px;" class="mb-4">

    <h1 class="fw-bold mb-3">Bienvenidos</h1>

    <p class="text-muted" style="max-width: 600px;">
      En nuestra facultad, reconocemos la importancia de gestionar de manera eficiente la selección y contratación de personal docente. Por ello, ponemos a disposición este portal de vacantes, diseñado para facilitar la publicación de oportunidades laborales, la recepción de postulaciones y la evaluación de candidatos. A través de una plataforma moderna, intuitiva y responsiva, buscamos optimizar los procesos de recursos humanos, garantizando transparencia, agilidad y accesibilidad para todos los interesados.
    </p>

    <!-- Botones -->
   <div class="d-flex justify-content-center gap-3 mt-4"> 
      <?php if (!isset($_SESSION["usuario_id"])): ?>
        <a href="php/login.php" class="btn btn-primary px-4">Iniciar Sesión</a>
        <a href="php/registro.php" class="btn btn-outline-dark px-4">Registrarse</a>
      <?php else: ?>
        <span class="align-self-center fw-bold">
          Hola, <?= htmlspecialchars($_SESSION["nombre"] . " " . $_SESSION["apellido"]) ?>
        </span>
        <a href="php/cerrarsesion.php" class="btn btn-danger px-4">Cerrar Sesión</a>
      <?php endif; ?>
    </div>

  </div>

</body>
</html>
