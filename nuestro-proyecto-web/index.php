<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal de Vacantes Docentes UTN</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/estilos.css" rel="stylesheet">
  <link rel="icon" href="/nuestro-proyecto-web/assets/images/utn.ico" type="image/x-icon">
</head>
<body class="d-flex flex-column min-vh-100">  


  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContenido" aria-controls="navbarContenido" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
      <div class="collapse navbar-collapse justify-content-center" id="navbarContenido">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link active rounded-pill px-4 bg-secondary text-white" href="#">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-4" href="php/vacantes.php">Vacantes</a>
          </li>
          <?php if (isset($_SESSION["usuario_id"])): ?>
          <li class="nav-item">
            <a class="nav-link px-4" href="php/perfil.php">Perfil</a>
          </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container d-flex flex-column align-items-center text-center bg-white border rounded-4 shadow my-5 p-4">
    
    <a href="index.php">
    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT_FEqjavcxlrifqvl75bLKmY4my0fdwLqDmQ&s" alt="Logo Universidad" class="logo-facu" >
    </a>

    <h1 class="fw-bold mb-3">Portal de Vacantes</h1>

    <p class="text-muted fst-italic" style="max-width: 600px;">
      En nuestra facultad, reconocemos la importancia de gestionar de manera eficiente la selección y contratación de personal docente. Por ello, ponemos a disposición este portal de vacantes, diseñado para facilitar la publicación de oportunidades laborales, la recepción de postulaciones y la evaluación de candidatos. A través de una plataforma moderna, intuitiva y responsiva, buscamos optimizar los procesos de recursos humanos, garantizando transparencia, agilidad y accesibilidad para todos los interesados.
    </p>

    <!-- Botones -->
   <div class="d-flex justify-content-center gap-3 mt-4"> 
      <?php if (!isset($_SESSION["usuario_id"])): ?>
        <a href="php/login.php" class="btn btn-success px-2 "><strong>Iniciar Sesión</strong></a>
        <a href="php/registro.php" class="btn btn-outline-info px-3 text- "><strong>Registrarse</strong></a>
      <?php else: ?>
        <span class="align-self-center fw-bold">
          Hola, <?= htmlspecialchars($_SESSION["nombre"] . " " . $_SESSION["apellido"]) ?>
        </span>
        <a href="php/cerrarsesion.php" class="btn btn-danger px-4">Cerrar Sesión</a>
      <?php endif; ?>
    </div>

  </div>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</html>
