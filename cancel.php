<?php
/**
 * Para eliminar las cuentas 
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

if (!isset($_SESSION['userName'])){
    header('location:/index.php');
    exit;
}else{
    if(!empty($_POST)){
        foreach($_POST as $key=>$value){
            $_POST[$key]=trim($value);
        }

        if (empty($_POST['user'])){
            $errors['user']='El usuario no puede estar en blanco';
        }

        if (empty($_POST['password'])){
            $errors['password']='La contraseña no puede estar en blanco';
        }

        if (empty($_POST['deleteConfirm'])){
            $errors['password']='No has confirmado que quieres eliminar la cuenta';
        }
        if (!isset($errors)){
            try{
                require_once($_SERVER['DOCUMENT_ROOT'].'/includes/env.inc.php');
                require_once($_SERVER['DOCUMENT_ROOT'].'/includes/connection.inc.php');

                if ($connection=getDBConnection(DB_NAME, DB_USERNAME, DB_PASSWORD)){
                    //query pedida completamente a chat gpt
                    $query= $connection->prepare('SELECT password FROM users WHERE (user=:user OR email=:email);');
                    $query->bindParam(':user',$_POST['user']);
                    $query->bindParam(':email',$_POST['user']);
                    $query->execute();
                    $datainDB=$query->fetch();

                    if ($query->rowCount()!=1){
                        $errors['login']='Error en la verificacion';
                    }else{
                        if(password_verify($_POST['password'],$datainDB['password'])){
                            // Eliminar los comentarios creados por el usuario
                            //query pedida completamente a chat gpt
                            $queryDeleteUserComments = $connection->prepare('DELETE FROM comments 
                                WHERE user_id = :user_id;');
                            $queryDeleteUserComments->bindParam(':user_id', $_SESSION['id']);
                            $queryDeleteUserComments->execute();
                            echo 'elimina comentarios del usuario<br>';
                            
                            // Eliminar los comentarios de las publicaciones del usuario
                            //query pedida completamente a chat gpt
                            $queryDeleteCommentsOnEntries = $connection->prepare('DELETE comments 
                                FROM comments
                                WHERE entry_id IN (
                                    SELECT id FROM entries WHERE user_id = :user_id);'
                            );
                            $queryDeleteCommentsOnEntries->bindParam(':user_id', $_SESSION['id']);
                            $queryDeleteCommentsOnEntries->execute();
                            echo 'elimina comentarios de sus publicaciones<br>';
                            
                            // Eliminar las publicaciones del usuario
                            //query pedida completamente a chat gpt
                            $queryDeleteEntries = $connection->prepare('DELETE FROM entries 
                                WHERE user_id = :user_id;');
                            $queryDeleteEntries->bindParam(':user_id', $_SESSION['id']);
                            $queryDeleteEntries->execute();
                            echo 'elimina sus publicaciones<br>';
                            
                            // Eliminar follows del usuario
                            //query pedida completamente a chat gpt
                            $queryDeleteFollows = $connection->prepare('DELETE FROM follows WHERE user_id = :user_id');
                            $queryDeleteFollows->bindParam(':user_id',$_SESSION['id']);
                            $queryDeleteFollows->execute();
                            echo 'ya no sigue a nadie<br>';
                            
                            // Eliminar follows hacia el usuario
                            //query pedida completamente a chat gpt
                            $queryDeleteFollower = $connection->prepare('DELETE FROM likes WHERE user_id = :user_id');
                            $queryDeleteFollower->bindParam(':user_id', $_SESSION['id']);
                            $queryDeleteFollower->execute();
                            echo 'ya no le sigue nadie<br>';

                            // Eliminar likes dados
                            //query pedida completamente a chat gpt
                            $queryDeleteLike = $connection->prepare('DELETE FROM likes WHERE user_id = :user_id');
                            $queryDeleteLike->bindParam(':user_id', $_SESSION['id']);
                            $queryDeleteLike->execute();
                            echo 'ya no ha dado like<br>';

                            // Eliminar dislikes dados
                            //query pedida completamente a chat gpt
                            $queryDeleteDislike = $connection->prepare('DELETE FROM follows WHERE user_followed = :user_id');
                            $queryDeleteDislike->bindParam(':user_id', $_SESSION['id']);
                            $queryDeleteDislike->execute();
                            echo 'ya no ha dado dislike<br>';

                            // Eliminar al usuario
                            //query pedida completamente a chat gpt
                            $queryDeleteUser = $connection->prepare('DELETE FROM users 
                                WHERE id = :user_id;');
                            $queryDeleteUser->bindParam(':user_id', $_SESSION['id']);
                            $queryDeleteUser->execute();
                            echo 'no hay usuario<br>';

                                unset($query);
                                unset($queryDeleteComments);
                                unset($queryDeleteEntry);
                                unset($queryDeleteFollower);
                                unset($queryDeleteFollows);
                                unset($queryDeleteLike);
                                unset($queryDeleteDislike);
                                unset($queryDeleteUser);
                                unset($connection);
                                session_destroy();
                                header ('location:/index.php');
                                exit;
                            }else{
                                $errors['password']='La contraseña no es correcta';
                            }
                    }
                } else {
                    throw new Exception('Error en la conexión a la BBDD');
                }  
            } catch (Exception $e) {
                $errors['login'] = 'Error en el acceso';
            }        
            unset($query);
            unset($connection);
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Como se desee - Login</title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <h2>Necesitamos confirmacion para que se pueda eliminar su cuenta</h2>
                <form action="#" method="post">
                <label for="user">Usuario o mail</label>
                <input type="text" name="user" id="user" placeholder="usuario o mail" value="<?=$_POST['user']??""?>">
                    <?=isset($errors['user']) ? '<span class="error">'. $errors['user'] .'</span>' : ""?>
                    <br>
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password" value="<?=$_POST['password']??""?>">
                    <?=isset($errors['password']) ? '<span class="error">'. $errors['password'] .'</span>' : ""?>
                    <br>
                    <label>Esta seguro de que quiere eliminar la cuenta?</label>
                    <input type="checkbox" name="deleteConfirm" id="deleteConfirm">
                    <?=isset($errors['deleteConfirm']) ? '<span class="error">'. $errors['deleteConfirm'] .'</span>' : ""?>
                    <input type="submit" value="Accede">
                </form>
                <?php
            }
            require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/footer.inc.php');
    ?>
</body>
</html>