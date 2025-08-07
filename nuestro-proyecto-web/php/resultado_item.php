<?php
session_start();
require('conection.php');

if (!isset($_GET['id_persona']) || !isset($_GET['id_vacante'])) {
    die("Faltan parámetros");
}

$idPersona = intval($_GET['id_persona']);
$idVacante = intval($_GET['id_vacante']);

// Obtener ítems de la vacante
$sqlItems = "SELECT * FROM item WHERE ID_Vacante = $idVacante";
$resultado = mysqli_query($conn, $sqlItems);

// Obtener puntajes ya asignados
$sqlPuntajes = "SELECT nro_item, resultado FROM resultado_item 
                WHERE ID_Vacante = $idVacante AND ID = $idPersona";
$resultadoPuntajes = mysqli_query($conn, $sqlPuntajes);

// Guardar puntajes en array asociativo para fácil acceso
$puntajesExistentes = [];
while ($fila = mysqli_fetch_assoc($resultadoPuntajes)) {
    $puntajesExistentes[$fila['nro_item']] = $fila['resultado'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Asignar Puntaje</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h3>Asignar puntaje por ítem</h3>

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
</body>
</html>
