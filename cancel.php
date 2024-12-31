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

        if (!isset($errors)){
            try{
                require_once($_SERVER['DOCUMENT_ROOT'].'/includes/env.inc.php');
                require_once($_SERVER['DOCUMENT_ROOT'].'/includes/connection.inc.php');
                if ($connesction=getDBConnection(DB_NAME, DB_USERNAME, DB_PASSWORD)){
                    $query= $connesction->prepare('SELECT user, password FROM users WHERE (user=:user OR email=mail);');
                    $query->bindParam(':user',$_POST['user']);
                    $query->bindParam(':mail',$_POST['email']);
                    $query->execute();
                    if ($query->rowCount()!=1){
                        $errors['login']='Error en el acceso';
                    }else{
                        if($_POST['deleteConfirm']==true){
                            $datainDB=$query->fetch();
                            if(password_verify($_POST['password'],$datainDB['password'])){
                                unset($query);
                                $queryDelete = $connection->prepare(
                                    'DELETE users, entries, comments 
                                     FROM users
                                     LEFT JOIN entries ON users.id = entries.user_id
                                     LEFT JOIN comments ON entries.id = comments.entry_id
                                     WHERE (users.user = :user OR users.email = :email);'
                                );
                                $queryDelete->bindParam(':user', $_POST['user']);
                                $queryDelete->bindParam(':email', $_POST['email']);
                                $queryDelete->execute();
                                unset($passInDB);
                                unset($connection);
                                header ('location: /close.php');
                                exit;
                            }else{
                                $errors['password']='La contraseña no es correcta';
                            }
                        }else{
                            $errors['confirm']='No has confirmado que se elimine la cuenta';
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
        <?php
            require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/header.inc.php');
        ?>
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
                    <input type="submit" value="Accede">
                </form>
                <?php
            }
            require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/footer.inc.php');
    ?>
</body>
</html>