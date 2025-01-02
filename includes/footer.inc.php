<?php
/**
 * footer de las paginas
 *
 * @author Eloy
 *
 * @version 1.0
 *
 */
?>
<footer>
    <a href="/index.php">Inicio</a>
    <?php
    if(!empty($_SESSION['userName'])){
        echo '<a href="/account.php">Ajustes del usuario</a>';
    }
    ?>    
    <a href="/author.php">Autor</a>
    <?php
        if(isset($_SESSION['userName'])){
    ?>
    <span id="logout"><a href="/close.php">Desconectar</a></span>
    <?php
        }
    ?>
    
</footer>