<?php
/**
 * header de las paginas
 *
 * @author Eloy
 *
 * @version 1.0
 *
 */
?>
<header>
    <?php
        if(empty($_SESSION['userName'])){
    ?>

    <div id="zonausuario">
    <!-- Si el usuario no está logueado (no existe su variable de sesión): -->
        <span>¿Ya tienes cuenta? <a href="/login.php">Loguéate aquí</a>.</span>
    <!-- Fin usuario no logueado -->

    <?php
        }else{
    //<!-- Si el usuario está logueado (existe su variable de sesión): -->
        echo '<span id="usuario">'.$_SESSION['userName'].'</span>';

        if(isset($_POST['buscar'])){
            $_SESSION['buscar']=$_POST['buscar'];
            header('location:/results.php');
            exit;
        }
        ?>
        <form action="#" method="post">
        <input type="buscar" name="buscar" id="buscar" value="Buscar...">
        <input type="submit" value="Accede">
        </form>
    <!-- Fin usuario logueado -->
    <?php
        }
    ?>
    </div>
</header>
