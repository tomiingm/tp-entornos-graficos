<!DOCTYPE html>
<html lang="es">
<?php 
$titulo="Registrarse";
include("head.php");
?>

<?php
require('conection.php');
if (!$conn) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre    = trim($_POST["nombre"]);
    $apellido  = trim($_POST["apellido"]);
    $dni       = trim($_POST["dni"]);
    $mail      = trim($_POST["mail"]);
    $domicilio = trim($_POST["domicilio"]);
    $telefono  = trim($_POST["telefono"]);
    $clave     = trim($_POST["clave"]);
    $rol       = $_POST["rol"];

    // Validaciones del lado servidor
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre) || strlen($nombre) > 255) {
        $mensaje = "❌ El nombre solo puede contener letras y debe tener menos de 256 caracteres.";
    } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $apellido) || strlen($apellido) > 255) {
        $mensaje = "❌ El apellido solo puede contener letras y debe tener menos de 256 caracteres.";
    } elseif (strlen($mail) > 255) {
        $mensaje = "❌ El correo debe tener menos de 256 caracteres.";
    } elseif (strlen($clave) > 255) {
        $mensaje = "❌ La contraseña debe tener menos de 256 caracteres.";
    } elseif (!empty($domicilio) && strlen($domicilio) > 50) {
        $mensaje = "❌ El domicilio no puede superar los 50 caracteres.";
    } elseif (!empty($telefono) && (!preg_match("/^[0-9]+$/", $telefono) || strlen($telefono) > 13)) {
        $mensaje = "❌ El teléfono debe contener solo números y un máximo de 13 dígitos.";
    } else {
        // Hashear clave
        $password = password_hash($clave, PASSWORD_DEFAULT);

        // Verificar si ya existe ese DNI
        $check = mysqli_prepare($conn, "SELECT ID FROM persona WHERE DNI = ?");
        mysqli_stmt_bind_param($check, "s", $dni);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $mensaje = "⚠️ El DNI ya está registrado. Intenta con otro.";
        } else {
            // Insertar usuario nuevo
            $sql = "INSERT INTO persona (nombre, apellido, mail, clave, DNI, rol, domicilio, telefono) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssssisss", $nombre, $apellido, $mail, $password, $dni, $rol, $domicilio, $telefono);

            if (mysqli_stmt_execute($stmt)) {
                $mensaje = "✅ ¡Registro exitoso!";
            } else {
                $mensaje = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<body>
    <?php 
        $paginaActiva="inicio";
        include("navbar.php");

    ?>
    <div class="container contenedor">
        <div class="row justify-content-md-center">
            <div class="col text-end">
                <a href="../index.php" class="btn btn-light rounded-circle shadow-sm " aria-label="Volver al inicio">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                </a>
            </div>
            <div class="container col-8">
                <div class="formulario-box shadow">
                    <h1 class="text-center mb-4">Registrate</h1>
                    <?php if ($mensaje) {
                        echo "<div class='alert alert-info'>$mensaje</div>";
                    } ?>
                    <form action="registro.php" method="post">
                        <div class="mb-3">
                            <label for="dni" class="form-label">Número de Documento (DNI):</label>
                            <input type="number" 
                                class="form-control" 
                                name="dni" id="dni" 
                                required min="1000000" max="99999999"
                                oninvalid="this.setCustomValidity('⚠️ El DNI debe ser un número válido entre 1.000.000 y 99.999.999.')"
                                oninput="this.setCustomValidity('')">
                        </div>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" 
                                class="form-control" 
                                name="nombre" id="nombre" 
                                required maxlength="255" 
                                pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                                oninvalid="this.setCustomValidity('⚠️ El nombre solo puede contener letras y espacios, máximo 255 caracteres.')"
                                oninput="this.setCustomValidity('')">
                        </div>
                        <div class="mb-3">
                            <label for="apellido" class="form-label">Apellido:</label>
                            <input type="text" 
                                class="form-control" 
                                name="apellido" id="apellido" 
                                required maxlength="255" 
                                pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                                oninvalid="this.setCustomValidity('⚠️ El apellido solo puede contener letras y espacios, máximo 255 caracteres.')"
                                oninput="this.setCustomValidity('')">
                        </div>
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo electrónico:</label>
                            <input type="email" 
                                class="form-control" 
                                name="correo" id="correo" 
                                required maxlength="255"
                                oninvalid="this.setCustomValidity('⚠️ Ingresa un correo válido, máximo 255 caracteres.')"
                                oninput="this.setCustomValidity('')">
                        </div>
                        <div class="mb-3">
                            <label for="domicilio" class="form-label">Domicilio:</label>
                            <input type="text" 
                                class="form-control" 
                                name="domicilio" id="domicilio" 
                                maxlength="50"
                                oninvalid="this.setCustomValidity('⚠️ El domicilio no puede superar los 50 caracteres.')"
                                oninput="this.setCustomValidity('')">
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono:</label>
                            <input type="text" 
                                class="form-control" 
                                name="telefono" id="telefono" 
                                pattern="[0-9]{1,13}" maxlength="13"
                                title="Solo números, máximo 13 dígitos"
                                oninvalid="this.setCustomValidity('⚠️ El teléfono solo puede tener números, máximo 13 dígitos.')"
                                oninput="this.setCustomValidity('')">

                        </div>
                        <div class="mb-3">
                            <label for="clave" class="form-label">Clave:</label>
                            <input type="password" 
                                class="form-control" 
                                name="clave" id="clave" 
                                required maxlength="255"
                                oninvalid="this.setCustomValidity('⚠️ La contraseña es obligatoria y debe tener menos de 255 caracteres.')"
                                oninput="this.setCustomValidity('')">
                        </div>
                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol:</label>
                            <select class="form-select" name="rol" id="rol" required>
                                <option value="" disabled selected>Selecciona un rol</option>
                                <option value="0">Postulante</option>
                                <option value="2">Jefe de cátedra</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Registrarse</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>
