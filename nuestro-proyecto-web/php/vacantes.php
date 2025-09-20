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

$filtroTexto = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$filtroEstado = isset($_GET['filtro']) ? $_GET['filtro'] : '';
$condiciones = [];

if ($filtroTexto !== '') {
    $texto = mysqli_real_escape_string($conn, $filtroTexto);
    $condiciones[] = "(titulo LIKE '%$texto%' OR descripcion LIKE '%$texto%')";
}

if ($filtroEstado !== '') {
    $estado = mysqli_real_escape_string($conn, $filtroEstado);
    $condiciones[] = "estado = '$estado'";
}

$whereExtra = '';
if (!empty($condiciones)) {
    $whereExtra = " AND " . implode(" AND ", $condiciones);
}

if (isset($_SESSION["usuario_id"]) && isset($_SESSION["rol"])) {
    $idUsuario = $_SESSION['usuario_id'];
    $rolUsuario = $_SESSION['rol'];

    if ($rolUsuario == 1) {
        $sqlTotal = "SELECT COUNT(*) AS total FROM vacante WHERE 1=1 $whereExtra";
        $sql = "SELECT * FROM vacante 
                WHERE 1=1 $whereExtra
                ORDER BY fecha_fin DESC
                LIMIT $vacantesPorPagina OFFSET $offset";
    } elseif ($rolUsuario == 2) {
        $sqlTotal = "SELECT COUNT(*) AS total FROM vacante 
                     WHERE ID_Jefe = $idUsuario $whereExtra";
        $sql = "SELECT * FROM vacante 
                WHERE ID_Jefe = $idUsuario $whereExtra
                ORDER BY fecha_fin DESC
                LIMIT $vacantesPorPagina OFFSET $offset";
    } else {
        $sqlTotal = "SELECT COUNT(*) AS total FROM vacante 
                     WHERE estado IN ('abierta','cerrada') $whereExtra";
        $sql = "SELECT * FROM vacante 
                WHERE estado IN ('abierta','cerrada') $whereExtra
                ORDER BY fecha_fin DESC
                LIMIT $vacantesPorPagina OFFSET $offset";
    }
} else {
    $sqlTotal = "SELECT COUNT(*) AS total FROM vacante 
                 WHERE estado IN ('abierta','cerrada') $whereExtra";
    $sql = "SELECT * FROM vacante 
            WHERE estado IN ('abierta','cerrada') $whereExtra
            ORDER BY fecha_fin DESC
            LIMIT $vacantesPorPagina OFFSET $offset";
}

$resultTotal = mysqli_query($conn, $sqlTotal);
$filaTotal = mysqli_fetch_assoc($resultTotal);
$totalVacantes = $filaTotal['total'];
$totalPaginas = ceil($totalVacantes / $vacantesPorPagina);

$resultado = mysqli_query($conn, $sql);

$paginaActiva="vacantes";
include("navbar.php");
?>

<div class="container2 ">

  <a href="../index.php">
    <img id="image-utn2" src="../assets/images/UTN-Logo-M2.png" alt="Logo Universidad" >
  </a>
  
  <h1>Vacantes</h1>  

  <div class="linea mb-3">

    <form method="get" class="d-flex align-items-center gap-2">
      <input type="text" class="form-control" name="busqueda" id="busqueda" 
             placeholder="Buscar" value="<?php echo htmlspecialchars($filtroTexto); ?>">

      <select id="filtro-estado" name="filtro" class="form-select w-auto">
        <option value="">Todas</option>
        <option value="abierta" <?php if ($filtroEstado=='abierta') echo 'selected'; ?>>Abiertas</option>
        <option value="cerrada" <?php if ($filtroEstado=='cerrada') echo 'selected'; ?>>Cerradas</option>
        <?php if (isset($_SESSION["rol"]) && ($_SESSION["rol"] == 1 || $_SESSION["rol"] == 2)): ?>
          <option value="en revision" <?php if ($filtroEstado=='en revision') echo 'selected'; ?>>En revisión</option>
          <option value="sin abrir" <?php if ($filtroEstado=='sin abrir') echo 'selected'; ?>>Sin abrir</option>
        <?php endif; ?>
      </select>

      <button type="submit" class="btn btn-primary">Filtrar</button>

      <?php if (isset($_SESSION["rol"]) && $_SESSION["rol"] == 1): ?>
        <a href="crear_vacante.php" class="btn btn-success px-4 text-nowrap">Crear Vacante</a>
      <?php endif; ?>
    </form>
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
            <a class="page-link" 
               href="?page=<?php echo $paginaActual - 1; ?>&busqueda=<?php echo urlencode($filtroTexto); ?>&filtro=<?php echo urlencode($filtroEstado); ?>">
               Anterior
            </a>
          </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
          <li class="page-item <?php echo ($i == $paginaActual) ? 'active' : ''; ?>">
            <a class="page-link" 
               href="?page=<?php echo $i; ?>&busqueda=<?php echo urlencode($filtroTexto); ?>&filtro=<?php echo urlencode($filtroEstado); ?>">
               <?php echo $i; ?>
            </a>
          </li>
        <?php endfor; ?>

        <?php if ($paginaActual < $totalPaginas): ?>
          <li class="page-item">
            <a class="page-link" 
               href="?page=<?php echo $paginaActual + 1; ?>&busqueda=<?php echo urlencode($filtroTexto); ?>&filtro=<?php echo urlencode($filtroEstado); ?>">
               Siguiente
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
