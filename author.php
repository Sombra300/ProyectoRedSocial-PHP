<?php
/**
 * 
 *
 * @author Eloy
 *
 * @version 
 *
 */
ini_set('session.name','sesionEloy');
ini_set('session.cookie_httponly',1);
ini_set('session.cache_expire',10);
session_start();

if (!isset($_SESSION['userName'])){
    header('location:/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Eloy Estevens Romero</h1>
    <img src="img/yo.jpg" alt="Imagen del autor de esta web">
    <?php
			require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/footer.inc.php');
		?>
</body>
</html>