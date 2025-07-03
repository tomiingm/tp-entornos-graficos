<?php session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vacantes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/estilosvacantes.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<?php
require('conection.php');

$sql = "SELECT * FROM vacante WHERE fecha_fin > CURDATE() ORDER BY fecha_fin DESC";
$resultado = mysqli_query($conn, $sql);
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
      <div class="collapse navbar-collapse justify-content-center">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link px-4 " href="../index.php">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link rounded-pill active px-4 bg-secondary text-white" href="../php/vacantes.php">Vacantes</a>
          </li>
        </ul>
      </div>
    </div>
</nav>

<div class="container">

    <img class="imagen" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT_FEqjavcxlrifqvl75bLKmY4my0fdwLqDmQ&s" alt="Logo Universidad" class="logo-facu">

    <div class="linea">
        <div class="titulo">
            <p>Vacantes</p>
        </div>
        <div>
            <input type="text" class="buscar" name="busqueda" id="busqueda" placeholder="Buscar">
        </div>
    </div>
    
     <div class="scroll-vacantes">
      <?php while($unaVacante = mysqli_fetch_assoc($resultado)) { 
      echo "<div class='vacante'>" ;
      echo "<h5> Vacante: " .$unaVacante["titulo"] . "</h5>" ;
      echo "<p class='descripcion-recortada'>" . $unaVacante["descripcion"] . " </p>" ;
      $fechaOriginal = $unaVacante["fecha_fin"];
      $fechaConFormato = date("d-m-Y", strtotime($fechaOriginal));
      echo "<p class='fecha'> Fecha finalizacion: " . $fechaConFormato . "</p> <p class='estado'> Estado: ". $unaVacante['estado'] . "</p>";
      echo "<a class='ver-mas' href='unavacante.php?id=". $unaVacante['ID'] ."?>' class='ver-mas'>Ver más</a>";
      echo "</div>";
      
      } ?>
    </div>
</div>

