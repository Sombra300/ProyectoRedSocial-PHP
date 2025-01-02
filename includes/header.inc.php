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
        echo '<span id="usuario"><a href="/user.php">'.$_SESSION['userName'].'</a></span>';
        echo '<span><a href="/new.php">¿Quieres publicar algo?</a></span>';
        
        
        ?>
        <form action="/results.php" method="post">
        <input type="buscar" name="buscar" id="buscar" default="Buscar...">
        <input type="submit" value="Buscar">
        </form>
        <?php
        }
        ?>
    
    <!-- Fin usuario logueado -->
    </div>
</header>
