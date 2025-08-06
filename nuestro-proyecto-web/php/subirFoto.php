<?php

// Incluimos la conexión. Solo se necesita una vez.
require_once 'conection.php'; // $conn se crea aquí

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profilePhoto"])) {

    $userId = $_POST['userId'];
    $file = $_FILES['profilePhoto'];

    // --- Validaciones (esto estaba bien) ---
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        die("El archivo es demasiado grande. El máximo permitido es 5MB.");
    }
    $allowedMimeTypes = ['image/jpeg', 'image/png'];
    $fileMimeType = mime_content_type($file['tmp_name']);
    if (!in_array($fileMimeType, $allowedMimeTypes)) {
        die("Tipo de archivo no permitido. Sube solo imágenes JPG o PNG.");
    }

    // --- Lógica para guardar el archivo (esto estaba bien) ---
    $uploadDir = '../uploads/images/';
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = uniqid('user_' . $userId . '_', true) . '.' . $extension;
    $targetPath = $uploadDir . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        
        // --- ACTUALIZACIÓN A LA BASE DE DATOS CON MYSQLI ---
        // La tabla debe llamarse 'persona' y la columna 'ID', igual que en perfil.php
        $sql = "UPDATE persona SET fotoperfil = ? WHERE ID = ?";
        
        // Preparamos la consulta
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            // Asociamos los parámetros ('s' para string, 'i' para integer)
            mysqli_stmt_bind_param($stmt, 'si', $targetPath, $userId);
            
            // Ejecutamos la consulta
            if (mysqli_stmt_execute($stmt)) {
                header("Location: perfil.php");
                exit();
            } else {
                die("Error al actualizar la base de datos.");
            }
            
            mysqli_stmt_close($stmt);
        } else {
            die("Error al preparar la consulta.");
        }

    } else {
        die("Hubo un error al mover el archivo subido.");
    }
} else {
    header("Location: perfil.php");
    exit();
}