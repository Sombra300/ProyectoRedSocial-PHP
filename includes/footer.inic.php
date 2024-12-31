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
    <a href="/author.php">Autor</a>
    <?php
    if(!empty($_SESSION['userName'])){
        echo '<a href="/user.php">'.$_SESSION['userName'].'</a>';
    }
    ?>    
</footer>