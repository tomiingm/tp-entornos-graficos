<?php
session_start();
require('conection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idVacante = intval($_POST["id_vacante"]);
    $idPersona = intval($_POST["id_persona"]);
    $puntajes = $_POST["puntajes"]; // array asociativo: [nro_item => valor]

    foreach ($puntajes as $nro_item => $resultado) {
        $nro_item = intval($nro_item);
        $resultado = intval($resultado);

        // Insertar en resultado_item
        $sql = "INSERT INTO resultado_item (ID, ID_Vacante, nro_item, resultado)
                VALUES ($idPersona, $idVacante, $nro_item, $resultado)";
        mysqli_query($conn, $sql);
    }

    // Redirigir a la página anterior (opcional)
    header("Location: vacantes.php");
    exit();
}
?>