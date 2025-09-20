<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<?php 
$titulo="Vacantes";
include("head.php");

?>
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

$vacantesPorPagina = 3;
$paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($paginaActual < 1) $paginaActual = 1;

$offset = ($paginaActual - 1) * $vacantesPorPagina;

if (isset($_SESSION["usuario_id"]) && isset($_SESSION["rol"])) {
    $idUsuario = $_SESSION['usuario_id'];
    $rolUsuario = $_SESSION['rol'];

    if ($rolUsuario == 1) {
        $sqlTotal = "SELECT COUNT(*) AS total FROM vacante";
    } elseif ($rolUsuario == 2) {
        $sqlTotal = "SELECT COUNT(*) AS total FROM vacante WHERE ID_Jefe = $idUsuario";
    } else {
        $sqlTotal = "SELECT COUNT(*) AS total FROM vacante WHERE estado IN ('abierta','cerrada')";
    }
    $resultTotal = mysqli_query($conn, $sqlTotal);
    $filaTotal = mysqli_fetch_assoc($resultTotal);
    $totalVacantes = $filaTotal['total'];
    $totalPaginas = ceil($totalVacantes / $vacantesPorPagina);

    if ($rolUsuario == 1) {
        $sql = "SELECT * 
                FROM vacante 
                ORDER BY fecha_fin DESC
                LIMIT $vacantesPorPagina OFFSET $offset";
    } elseif ($rolUsuario == 2) {
        $sql = "SELECT v.* 
                FROM vacante v
                WHERE v.ID_Jefe = $idUsuario
                ORDER BY v.fecha_fin DESC
                LIMIT $vacantesPorPagina OFFSET $offset";
    } else {
        $sql = "SELECT * 
                FROM vacante 
                WHERE estado IN ('abierta','cerrada')
                ORDER BY fecha_fin DESC
                LIMIT $vacantesPorPagina OFFSET $offset";
    }
    $resultado = mysqli_query($conn, $sql);

} else {
    $sqlTotal = "SELECT COUNT(*) AS total FROM vacante WHERE estado IN ('abierta','cerrada')";
    $resultTotal = mysqli_query($conn, $sqlTotal);
    $filaTotal = mysqli_fetch_assoc($resultTotal);
    $totalVacantes = $filaTotal['total'];
    $totalPaginas = ceil($totalVacantes / $vacantesPorPagina);

    $sql = "SELECT * 
            FROM vacante 
            WHERE estado IN ('abierta','cerrada') 
            ORDER BY fecha_fin DESC
            LIMIT $vacantesPorPagina OFFSET $offset";
    $resultado = mysqli_query($conn, $sql);
}

$paginaActiva="vacantes";
include("navbar.php");
?>

<div class="container2 ">

  <a href="../index.php">
  <img id="image-utn2" src="../assets/images/UTN-Logo-M2.png" alt="Logo Universidad" >
  </a>
  
<h1>Vacantes</h1>  

<div class="linea mb-3">
  <div class="d-flex align-items-center gap-2">
  <label for="busqueda" class="visually-hidden">Buscar personas</label>  
    <input type="text" class="form-control" name="busqueda" id="busqueda" placeholder="Buscar">
  
<?php if (isset($_SESSION["rol"])): ?>
  <?php if ($_SESSION["rol"] == 1 || $_SESSION["rol"] == 2): ?>
    <label for="filtro-estado" class="visually-hidden">Filtrar por estado</label>
    <select id="filtro-estado" class="form-select w-auto">
      <option value="">Todas</option>
      <option value="abierta">Abiertas</option>
      <option value="cerrada">Cerradas</option>
      <option value="en revision">En revisión</option>
      <option value="sin abrir">Sin abrir</option>
    </select>
  <?php else: ?>

    <select id="filtro-estado" class="form-select w-auto">
      <option value="">Todas</option>
      <option value="abierta">Abiertas</option>
      <option value="cerrada">Cerradas</option>
    </select>
  <?php endif; ?>
<?php else: ?>

  <label for="filtro-estado" class="visually-hidden">Filtrar por estado</label>
  <select id="filtro-estado" class="form-select w-auto">
    <option value="">Todas</option>
    <option value="abierta">Abiertas</option>
    <option value="cerrada">Cerradas</option>
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

<div class="paginacion d-flex justify-content-center mt-3">
  <nav>
    <ul class="pagination">
  
      <?php if ($paginaActual > 1): ?>
        <li class="page-item">
          <a class="page-link" href="?page=<?php echo $paginaActual - 1; ?>">Anterior</a>
        </li>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <li class="page-item <?php echo ($i == $paginaActual) ? 'active' : ''; ?>">
          <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        </li>
      <?php endfor; ?>

      <?php if ($paginaActual < $totalPaginas): ?>
        <li class="page-item">
          <a class="page-link" href="?page=<?php echo $paginaActual + 1; ?>">Siguiente</a>
        </li>
      <?php endif; ?>
    </ul>
  </nav>
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
      const titulo = vacante.querySelector('h5').textContent;
      const descripcion = vacante.querySelector('.descripcion-recortada').textContent;
      const estado = vacante.querySelector('.estado').textContent.toLowerCase();

      const coincideTexto = 
        normalizar(titulo).includes(filtroTexto) || 
        normalizar(descripcion).includes(filtroTexto);

      const coincideEstado = !filtroEstado || estado.includes(filtroEstado);

      vacante.style.display = (coincideTexto && coincideEstado) ? 'block' : 'none';
    });
  }

  document.getElementById('busqueda').addEventListener('input', filtrarVacantes);
  document.getElementById('filtro-estado')?.addEventListener('change', filtrarVacantes);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
