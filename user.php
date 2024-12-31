<?php
/**
 * pagina para ver el usuario 
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
    require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/env.inc.php');
    require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/connection.inc.php');
    if(isset($_POST['id'])){
        try {
            if ($connection = getDBConnection(DB_NAME, DB_USERNAME, DB_PASSWORD)) {
                $query =$connesction->prepare ('SELECT id, text FROM entries WHERE user_id = :user_id');
            $query->bindParam(':user_id',$_POST['id']);
            $query->execute();
                $entries = $connection->query($query)->fetchAll(PDO::FETCH_OBJ);
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
    }else{
        try {
            if ($connection = getDBConnection(DB_NAME, DB_USERNAME, DB_PASSWORD)) {
                $query =$connesction->prepare ('SELECT id, text FROM entries WHERE user_id = :user_id');
            $query->bindParam(':user_id',$_SESSION['id']);
            $query->execute();
                $entries = $connection->query($query)->fetchAll(PDO::FETCH_OBJ);
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

        //si no hay errores mostrar la publicacion
        if (!isset($errors)) {
            if (count($entries)>0) {
                foreach($entries as $entry) {
                    echo '<article class="entrada">';
                        echo '<span>'. $entry->text .'</span>';
                        echo '<span>'. $entry->total_likes .' likes</span><br>';
                        echo '<span>'. $entry->total_dislikes .' dislikes</span><br>';
                        echo '<a href="/entry.php/'.$entry->id.'" class="entrada">Ver publicacion</a>';
                    echo '</article>';
                }
            } else {
                echo '<h2>No hay ninguna publicacion.</h2>';
            }
        } else {
            foreach($errors as $error){
                echo '<div>'.$error.'</div>';
            }
        }
    require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/footer.inc.php');
?>
</body>
</html>