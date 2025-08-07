<?php
require('conection.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idVacante = intval($_POST['id_vacante']);
    $idPersona = intval($_POST['id_persona']);
    $puntajes = $_POST['puntajes'] ?? [];

    foreach ($puntajes as $nro_item => $puntaje) {
        $nro_item = intval($nro_item);
        $puntaje = intval($puntaje);

        $sql = "INSERT INTO resultado_item (ID_Vacante, ID, nro_item, resultado)
                VALUES ($idVacante, $idPersona, $nro_item, $puntaje)
                ON DUPLICATE KEY UPDATE resultado = $puntaje";

        mysqli_query($conn, $sql);
    }

    header("Location: resultados.php?id=$idVacante");
    exit();
}
?>