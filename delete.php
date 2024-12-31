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
    if(!isset($_GET['id'])){
        require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/env.inc.php');
        require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/connection.inc.php');
        try {
            if ($connection = getDBConnection(DB_NAME, DB_USERNAME, DB_PASSWORD)) {
                $query =$connesction->prepare ('DELETE entries, comments
                                    FROM entries
                                    LEFT JOIN comments ON comments.entry_id = entries.id
                                    WHERE entries.id = :entry_id AND entries.user_id = :user_id');
                $query->bindParam(':user_id',$_SESSION['id']);
                $query->bindParam(':entry_id', $_GET['id']);
                $query->execute();
            } else {
                throw new Exception('Error en la conexión a la BBDD');
            }
            unset($query);
            unset($connection);
        } catch (Exception $exception) {
            $errors['delete']=$exception;
            unset($query);
            unset($connection);
        }
    }
}

if(!isset($errors)){
    unset($_SESSION['idLastEntry']);
    header('location:/list.php');
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
    
