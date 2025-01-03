<?php
/**
 * pagina para eliminar publicaciones 
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
    foreach($_GET as $key=>$value){
        $_GET[$key]=trim($value);
    }
    if(isset($_GET['entry_id'])){
        require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/env.inc.php');
        require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/connection.inc.php');
        try {
            if ($connection = getDBConnection(DB_NAME, DB_USERNAME, DB_PASSWORD)) {

                $queryDeleteComments = $connection->prepare('DELETE comments 
                                FROM comments
                                WHERE entry_id =:entry_id;'
                            );
                $queryDeleteComments->bindParam(':entry_id', $_GET['entry_id']);
                $queryDeleteComments->execute();
                echo 'elimina comentarios de sus publicaciones<br>';

                // Eliminar likes dados
                $queryDeleteLike = $connection->prepare('DELETE FROM likes WHERE entry_id = :entry_id');
                $queryDeleteLike->bindParam(':entry_id', $_GET['entry_id']);
                $queryDeleteLike->execute();
                echo 'ya no tiene like<br>';

                // Eliminar dislikes
                $queryDeleteDislike = $connection->prepare('DELETE FROM dislikes WHERE entry_id = :entry_id');
                $queryDeleteDislike->bindParam(':entry_id', $_GET['entry_id']);
                $queryDeleteDislike->execute();
                echo 'ya no tiene dislike<br>';
                
                // Eliminar las publicacion del usuario
                $queryDeleteEntry = $connection->prepare('DELETE FROM entries 
                    WHERE id =:entry_id;');
                $queryDeleteEntry->bindParam(':entry_id', $_GET['entry_id']);
                $queryDeleteEntry->execute();
                echo 'elimina la publicacion<br>';
            } else {
                throw new Exception('Error en la conexión a la BBDD');
            }
            unset($query);
            unset($connection);
        } catch (Exception $exception) {
            $errors['delete']='No sa borrao';
            unset($query);
            unset($connection);
        }
    }
}

if(!isset($errors)){
    echo 'funciona';
    //header('location:/list.php');
}else{
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
        if(isset($errors)){
            foreach($errors as $error){
                echo '<div>'.$error.'</div>';
            }
        }
    ?>
    <a href="/list.php">Volver a la lista</a>
</body>
</html>
<?php
}
?>
    
