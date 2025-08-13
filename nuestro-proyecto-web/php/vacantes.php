<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vacantes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/estilosvacantes.css" rel="stylesheet">
  <link rel="icon" href="/nuestro-proyecto-web/assets/images/utn.ico" type="image/x-icon">
</head>
<body class="d-flex flex-column min-vh-100">

<?php
require('conection.php');

$hoy = date('Y-m-d');
$sqlActualizar = "UPDATE vacante 
                  SET estado = 'en revision' 
                  WHERE estado = 'abierta' AND fecha_fin < ?";
$stmtActualizar = mysqli_prepare($conn, $sqlActualizar);
mysqli_stmt_bind_param($stmtActualizar, "s", $hoy);
mysqli_stmt_execute($stmtActualizar);

$sqlActualizar2 = "UPDATE vacante 
                  SET estado = 'abierta' 
                  WHERE estado = 'sin abrir' AND fecha_ini <= ?";
$stmtActualizar2 = mysqli_prepare($conn, $sqlActualizar2);
mysqli_stmt_bind_param($stmtActualizar2, "s", $hoy);
mysqli_stmt_execute($stmtActualizar2);

if (isset($_SESSION["usuario_id"]) and isset($_SESSION["rol"]) and $_SESSION["rol"]==2 ) {

$idUsuario = $_SESSION['usuario_id'];
$rolUsuario = $_SESSION['rol'];
$sql = "SELECT v.* 
        FROM vacante v
        WHERE v.ID_Jefe = $idUsuario
        ORDER BY v.fecha_fin DESC";
        $resultado = mysqli_query($conn, $sql);
} else {
    $sql = "SELECT * 
         FROM vacante 
         WHERE fecha_fin > CURDATE() OR estado = 'abierta' 
         ORDER BY fecha_fin DESC";
         $resultado = mysqli_query($conn, $sql);
}

?>

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

  <img class="imagen" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT_FEqjavcxlrifqvl75bLKmY4my0fdwLqDmQ&s" alt="Logo Universidad" class="logo-facu">

<div class="linea mb-3">
  <div class="titulo">
    <p>Vacantes</p>
  </div>
  <div class="d-flex align-items-center gap-2">
  <input type="text" class="form-control" name="busqueda" id="busqueda" placeholder="Buscar">
  
  <?php if (isset($_SESSION["rol"]) && ( $_SESSION["rol"] == 1 or  $_SESSION["rol"] == 2)): ?>
  <select id="filtro-estado" class="form-select w-auto">
    <option value="">Todas</option>
    <option value="abierta">Abiertas</option>
    <option value="cerrada">Cerradas</option>
    <option value="en revision">En revisión</option>
    <option value="sin abrir">Sin abrir</option>
  </select>
  <?php endif; ?>
<?php if (isset($_SESSION["rol"]) && $_SESSION["rol"] == 1): ?>
  <a href="crear_vacante.php" class="btn btn-success px-4 text-nowrap">Crear Vacante</a>
<?php endif; ?>
</div>

</div>



  <div class="scroll-vacantes">
    <?php while($unaVacante = mysqli_fetch_assoc($resultado)) { 
      echo "<div class='vacante'>" ;
      echo "<h5> Vacante: " . htmlspecialchars($unaVacante["titulo"]) . "</h5>" ;
      echo "<p class='descripcion-recortada'>" . htmlspecialchars($unaVacante["descripcion"]) . "</p>" ;
      $fechaOriginal = $unaVacante["fecha_fin"];
      $fechaConFormato = date("d-m-Y", strtotime($fechaOriginal));
      echo "<p class='fecha'> Fecha finalización: " . $fechaConFormato . "</p>";
      echo "<br>";
      $estado = $unaVacante["estado"];
      $claseEstado = '';

     switch ($estado) {
         case 'abierta':
             $claseEstado = 'text-success';
             break;
         case 'cerrada':
             $claseEstado = 'text-danger';
             break;
         case 'en revision':
             $claseEstado = 'text-warning';
             break;
         default:
             $claseEstado = 'text-secondary';
     }

      echo "<p class='estado fw-bold'>Estado: <span class='$claseEstado'>" . ucfirst($estado) . "</span></p>";
      echo "<br>";
      echo "<a class='ver-mas' href='unavacante.php?id=" . $unaVacante['ID'] . "'>Ver más</a>";
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

  function filtrarVacantes() {
    const filtroTexto = normalizar(document.getElementById('busqueda').value);
    const filtroEstado = document.getElementById('filtro-estado')?.value || '';
    const vacantes = document.querySelectorAll('.vacante');

    vacantes.forEach(function(vacante) {
      const descripcion = vacante.querySelector('.descripcion-recortada').textContent;
      const estado = vacante.querySelector('.estado').textContent.toLowerCase();

      const coincideTexto = normalizar(descripcion).includes(filtroTexto);
      const coincideEstado = !filtroEstado || estado.includes(filtroEstado);

      vacante.style.display = (coincideTexto && coincideEstado) ? 'block' : 'none';
    });
  }

  document.getElementById('busqueda').addEventListener('input', filtrarVacantes);
  document.getElementById('filtro-estado')?.addEventListener('change', filtrarVacantes);
</script>

</body>
</html>
