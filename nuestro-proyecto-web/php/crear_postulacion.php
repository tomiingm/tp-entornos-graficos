<?php
session_start();
require_once('conection.php');

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php?error=sesion');
    exit();
}

if (!isset($_POST['id_vacante'])) {
    header('Location: ../index.php?error=no_vacante');
    exit();
}

$idPersona = $_SESSION['usuario_id'];
$idVacante = $_POST['id_vacante'];
$fecha = date("Y-m-d");
$cv = 'No se ha subido CV';

$sql = "INSERT INTO postulacion (ID_Persona, ID_Vacante, fecha_hora_post, cv) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $idPersona, $idVacante, $fecha, $cv);

if ($stmt->execute()) {
    header("Location: ../index.php?postulacion=ok");
} else {
    echo "Error en DB: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
