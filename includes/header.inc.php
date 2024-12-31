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
        <span>¿Ya tienes cuenta? <a href="/login">Loguéate aquí</a>.</span>
    <!-- Fin usuario no logueado -->

    <?php
        }else{
    //<!-- Si el usuario está logueado (existe su variable de sesión): -->
        echo '<span id="usuario">'.$_SESSION['userName'].'</span>';
        ?>
        <span id="acount"><a href="/account.php">Ajustes de usuario</a></span>
        <span id="logout"><a href="/close.php">Desconectar</a></span>
    <!-- Fin usuario logueado -->
    <?php
        }
    ?>
    </div>
</header>
