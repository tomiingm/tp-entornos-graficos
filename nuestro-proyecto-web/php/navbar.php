<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
            data-bs-target="#navbarContenido" aria-controls="navbarContenido" 
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarContenido">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link px-4 <?php echo ($paginaActiva == 'inicio') ? 'active rounded-pill bg-secondary text-white' : ''; ?>" 
             href="../index.php">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link px-4 <?php echo ($paginaActiva == 'vacantes') ? 'active rounded-pill bg-secondary text-white' : ''; ?>" 
             href="vacantes.php">Vacantes</a>
        </li>
        <?php if (isset($_SESSION["usuario_id"])): ?>
        <li class="nav-item">
            <a class="nav-link px-4 <?php echo ($paginaActiva == 'perfil') ? 'active rounded-pill bg-secondary text-white' : ''; ?>"  href="../php/perfil.php">Perfil</a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>