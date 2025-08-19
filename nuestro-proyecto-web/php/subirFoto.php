<?php
require_once 'conection.php'; // $conn se crea aquí

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profilePhoto"])) {
    $userId = (int) $_POST['userId'];
    $file = $_FILES['profilePhoto'];

    // --- Validaciones ---
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        die("El archivo es demasiado grande. El máximo permitido es 5MB.");
    }
    $allowedMimeTypes = ['image/jpeg', 'image/png'];
    $fileMimeType = mime_content_type($file['tmp_name']);
    if (!in_array($fileMimeType, $allowedMimeTypes)) {
        die("Tipo de archivo no permitido. Sube solo imágenes JPG o PNG.");
    }

    // --- Buscar foto anterior ---
    $sqlSelect = "SELECT fotoperfil FROM persona WHERE ID = ?";
    $stmtSelect = mysqli_prepare($conn, $sqlSelect);
    mysqli_stmt_bind_param($stmtSelect, 'i', $userId);
    mysqli_stmt_execute($stmtSelect);
    $res = mysqli_stmt_get_result($stmtSelect);
    $row = mysqli_fetch_assoc($res);

    if (!empty($row["fotoperfil"]) && file_exists($row["fotoperfil"])) {
        unlink($row["fotoperfil"]); // ✅ eliminar foto anterior
    }
    mysqli_stmt_close($stmtSelect);

    // --- Guardar nueva foto con nombre fijo ---
    $uploadDir = '../uploads/images/';
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $newFileName = "foto_" . $userId . "." . $extension;  // siempre el mismo nombre por usuario
    $targetPath = $uploadDir . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $sqlUpdate = "UPDATE persona SET fotoperfil = ? WHERE ID = ?";
        $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
        mysqli_stmt_bind_param($stmtUpdate, 'si', $targetPath, $userId);

        if (mysqli_stmt_execute($stmtUpdate)) {
            header("Location: perfil.php");
            exit();
        } else {
            die("Error al actualizar la base de datos.");
        }
        mysqli_stmt_close($stmtUpdate);
    } else {
        die("Hubo un error al mover el archivo subido.");
    }
} else {
    header("Location: perfil.php");
    exit();
}