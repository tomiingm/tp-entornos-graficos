<?php session_start();
?>
<!DOCTYPE html>
<html lang="es">

<?php 
$titulo="Log In";
include("head.php"); 
$paginaActiva="inicio";

require('conection.php');
if (!$conn) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni = $_POST["dni"];
    $clave = $_POST["clave"];

    $sql = "SELECT * FROM persona WHERE DNI = '$dni'";
    $resultado = mysqli_query($conn, $sql);

    if (mysqli_num_rows($resultado) == 1) {
        $usuario = mysqli_fetch_assoc($resultado);

        if (password_verify($clave, $usuario["clave"])) {
            $_SESSION["mail"]= $usuario["mail"];
            $_SESSION["dni"]= $usuario["dni"];
            $_SESSION["usuario_id"] = $usuario["ID"];
            $_SESSION["nombre"] = $usuario["nombre"];
            $_SESSION["apellido"] = $usuario["apellido"];
            $_SESSION["rol"] = $usuario["rol"] ;

            header("Location: vacantes.php"); // REDIRIGIR
            exit();
        } else {
            $mensaje = "Clave incorrecta.";
        }
    } else {
        $mensaje = "Usuario no encontrado.";
    }
}

?>
<body>
<?php 
include("navbar.php"); 
?>
  <div class=" d-flex align-items-center vh-100">
    <div class="container col-xl-10 col-xxl-8 px-4"> 
    <div class="row align-items-center g-lg-5"> 
        
    <a href="../index.php" class="link-logo-facu" >
        <div class="col-lg-7 text-center text-lg-start"> 
            <img src="/../assets/images/UTN-Logo-M.png" alt="Logo Universidad" class="imagen-logo-facu" id="image-utn" >
        </div> 
    </a>

        <div class="col-md-10 mx-auto col-lg-5">
            
            <form action="login.php" method="post" class="p-4 p-md-5 border rounded-3 bg-body-tertiary shadow-lg"> 
            <h1>Iniciar Sesión</h1>
                <div class="form-floating mb-3" > 
                    <input type="number" class="form-control" name="dni" id="dni" required min="1000000" max="99999999"  placeholder="Documento (DNI)"> 
                    <label for="dni">Número de Documento (DNI)</label> 
                </div>
                <?php if ($mensaje) { ?>
                    <div class="text-danger">
                    <?= htmlspecialchars($mensaje) ?>
                    </div>
                    <?php } ?>
                    <div class="form-floating mb-3"> 
                        <input id="clave" type="password" class="form-control" name="clave" placeholder="Contraseña" required> 
                        <label for="clave">Contraseña</label>
                        <span class="ver-password" onclick="verPassword('clave', this)">
                        <i class="bi bi-eye"></i>
                    </span> 
                    </div> 
                    <button class="w-100 btn btn-lg btn-primary mb-3" type="submit">Iniciar Sesión</button> 

                    <div class="text-center">
                        ¿No tienes cuenta? 
                        <a href="registro.php" class="text-decoration-none">Regístrate aquí</a>
                    </div>
</form>
            </div> 
        </div> 
    </div>
                </div>




<script>
  function verPassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector('i');

    const showing = input.type === 'text';
    input.type = showing ? 'password' : 'text';

    
    icon.classList.toggle('bi-eye', showing);        // si estaba visible, vuelve al ojo normal
    icon.classList.toggle('bi-eye-slash', !showing); // si estaba oculto, muestra el ojo tachado
  }
</script>

</body>



</html>