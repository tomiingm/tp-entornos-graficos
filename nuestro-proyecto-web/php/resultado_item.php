<?php
session_start();
require('conection.php');

if (!isset($_GET['id_persona']) || !isset($_GET['id_vacante'])) {
    die("Faltan parámetros");
}

$idPersona = intval($_GET['id_persona']);
$idVacante = intval($_GET['id_vacante']);

$sqlItems = "SELECT * FROM item WHERE ID_Vacante = $idVacante";
$resultado = mysqli_query($conn, $sqlItems);

$sqlPuntajes = "SELECT nro_item, resultado FROM resultado_item 
                WHERE ID_Vacante = $idVacante AND ID = $idPersona";
$resultadoPuntajes = mysqli_query($conn, $sqlPuntajes);

$puntajesExistentes = [];
while ($fila = mysqli_fetch_assoc($resultadoPuntajes)) {
    $puntajesExistentes[$fila['nro_item']] = $fila['resultado'];
}
?>
<!DOCTYPE html>
<html lang="es">
<?php 
$titulo="Asignar resultado";
include("head.php");
?>
<body >

<?php 
        $paginaActiva="vacantes";
        include("navbar.php");

  ?>
  <div class="container mt-5">

<h1>Asignar puntaje por ítem</h1>

<?php if (mysqli_num_rows($resultado) === 0) { ?>
  <div class="alert alert-warning">
    ⚠ No hay ningún ítem cargado en <strong>Orden de mérito</strong>.  
    Por favor, cargue al menos uno antes de asignar puntajes.
  </div>
  <a href="orden_de_merito.php?id=<?= $idVacante ?>" class="btn btn-primary">Ir a Orden de mérito</a>
  <a href="resultados.php?id=<?= $idVacante ?>" class="btn btn-secondary">Volver</a>
<?php } else { ?>
  <form action="guardar_puntajes.php" method="post">
    <input type="hidden" name="id_vacante" value="<?= $idVacante ?>">
    <input type="hidden" name="id_persona" value="<?= $idPersona ?>">

    <?php while($item = mysqli_fetch_assoc($resultado)) { 
        $valorActual = $puntajesExistentes[$item['nro_item']] ?? '';
    ?>
      <div class="mb-3">
        <label for="item_<?= $item['nro_item'] ?>" class="form-label">
          <?= htmlspecialchars($item['descripcion']) ?> (máx <?= $item['valor_max'] ?>):
        </label>
        <input 
          type="number" 
          class="form-control" 
          name="puntajes[<?= $item['nro_item'] ?>]" 
          id="item_<?= $item['nro_item'] ?>" 
          min="0" 
          max="<?= $item['valor_max'] ?>" 
          value="<?= htmlspecialchars($valorActual) ?>"
          required
        >
      </div>
    <?php } ?>

    <button type="submit" class="btn btn-primary">Guardar puntajes</button>
    <a href="resultados.php?id=<?= $idVacante ?>" class="btn btn-secondary">Cancelar</a>
  </form>
<?php } ?>
    </div>
</body>
</html>
