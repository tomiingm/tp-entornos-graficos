<?php
session_start();

// Verificacion si el usuario está logueado y es admin
if (!isset($_SESSION["usuario_id"]) || $_SESSION["rol"] != 1) {
    header("Location: login.php");
    exit();
}

require("conection.php");

// Verificar ID de vacante
if (!isset($_GET['id'])) {
    echo "ID de vacante no proporcionado.";
    exit();
}

$idVacante = intval($_GET['id']);

// Insertar nuevo ítem si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['descripcion'], $_POST['puntaje_max'])) {
    $desc = mysqli_real_escape_string($conn, $_POST['descripcion']);
    $puntaje = intval($_POST['puntaje_max']);

    $sqlInsert = "INSERT INTO item (ID_Vacante, descripcion, valor_max) 
                  VALUES ($idVacante, '$desc', $puntaje)";
    mysqli_query($conn, $sqlInsert);
}

// Obtener ítems existentes
$sqlItems = "SELECT * FROM item WHERE ID_Vacante = $idVacante";
$resultado = mysqli_query($conn, $sqlItems);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Orden de Mérito</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

  <h2 class="mb-4">Orden de Mérito - Vacante #<?= $idVacante ?></h2>
  <hr>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Descripción</th>
        <th>Puntaje Máximo</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($item = mysqli_fetch_assoc($resultado)): ?>
        <tr>
          <td><?= htmlspecialchars($item['descripcion']) ?></td>
          <td><?= $item['valor_max'] ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <h4 class="mt-5">Agregar nuevo ítem</h4>
  <form action="orden_de_merito.php?id=<?= $idVacante ?>" method="post" class="row g-3 mt-2">
    <div class="col-md-6">
      <input type="text" name="descripcion" class="form-control" placeholder="Descripción" required>
    </div>
    <div class="col-md-3">
      <input type="number" name="puntaje_max" class="form-control" placeholder="Puntaje máximo" min="0" required>
    </div>
    <div class="col-md-3">
      <button type="submit" class="btn btn-primary w-100">Agregar</button>
    </div>
  </form>

</body>
</html>
