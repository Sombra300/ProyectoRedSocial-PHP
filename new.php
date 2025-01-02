<?php
/**
 * pagina para añadire publicaciones
 *
 * @author Eloy
 *
 * @version 1.0
 *
 */
ini_set('session.name','sesionEloy');
ini_set('session.cookie_httponly',1);
ini_set('session.cache_expire',10);
session_start();
//si no hay sesion se manda al index
if (!isset($_SESSION['userName'])){
    header('location:/index.php');
    exit;
}else{
    if(!empty($_POST)){
    require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/env.inc.php');
    require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/connection.inc.php');
    if(empty($_POST['publicacion'])){
        $errors['vacio']='la publicacion no puede estar vacia';
    }
    if(!isset($errors)){
        try {
            if($connection = getDBConnection(DB_NAME, DB_USERNAME, DB_PASSWORD)) {
                $query =$connection->prepare ('INSERT INTO entries (user_id, text, date) 
                                        VALUES (:user_id, :text, NOW())');
                $query->bindParam(':user_id',$_SESSION['id']);
                $query->bindParam(':text',$_POST['publicacion']);
                $query->execute();
                $_SESSION['idLastEntry'] = $connection->lastInsertId();
            } else {
                throw new Exception('Error en la conexión a la BBDD');
            }
            unset($query);
            unset($connection);
        } catch (Exception $exception) {
            $errors['select']=$exception;
            unset($query);
            unset($connection);
        }
    }

    if(!isset($errors)){
        header('location:/entry.php');
        exit;
    }
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
<?php
			require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/header.inc.php');
?>
<?php
        if(isset($errors)){
            foreach($errors as $error){
                echo '<div>'.$error.'</div>';
            }
        }
?>
    
    <form action="#" method="post">
        <label for="publicacion">Introduce lo que quieras publicar</label>
        <input type="text" name="publicacion" id="publicacion">
        <br>
        <input type="submit" value="Publicar">
    </form>
<?php
			require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/footer.inc.php');
?>
</body>
</html>

<?php

}
?>

