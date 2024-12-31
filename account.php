<?php
/**
 * Pagina para editar el usuario
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
    //conecta cnon la base de datos
    require_once($_SERVER['DOCUMENT_ROOT'].'/includes/env.inc.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/includes/connection.inc.php');
    try{
        //consigue los datos actuales del usuario
        if ($connesction=getDBConnection(DB_NAME, DB_USERNAME, DB_PASSWORD)){
            $query= $connesction->prepare('SELECT user, email FROM users WHERE user=:user;');
            $query->bindParam(':user',$_SESSION['userName']);
            $query->execute();
            $account = $connection->query($query)->fetchAll(PDO::FETCH_OBJ);
        }
        unset($query);

        //comprueba si $_POST tiene datos para actualizar
        if(!empty($_POST)){
            foreach($_POST as $key=>$value){
                $_POST[$key]=trim($value);
            }
            //comprueba si tiene algun campo vacio
            if (empty($_POST['user']))
                $errors['user'] = 'El usuario no puede estar en blanco.';   
            if (empty($_POST['email']))
                $errors['email'] = 'El email no puede estar en blanco.';
            if (empty($_POST['password']))
                $errors['password'] = 'La contraseña no puede estar en blanco.';

            if (!isset($errors)) {
            try{
                //actualiza los datos de la base de datos
                if ($connesction=getDBConnection(DB_NAME, DB_USERNAME, DB_PASSWORD)){
                    $query= $connesction->prepare('UPDATE users SET user = :user, email = :email, password = :password  WHERE id=:id;');
                        $query->bindParam(':id',$_SESSION['id']);
                        $query->bindParam(':user',$_POST['user']);
                        $query->bindParam(':email', $_POST['email']);
                        $query->bindParam(':password', password_hash($_POST['password'],PASSWORD_DEFAULT));
                        $query->execute();
                        $account = $connection->query($query)->fetchAll(PDO::FETCH_OBJ);
                }
            }catch (Exception $e) {
                $errors['login'] = 'Error en el acceso';
            }        
            unset($query);
        }     
    }    
}catch (Exception $e) {
    $errors['login'] = 'Error en el acceso';
}  
        
    
unset($connection);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar cuenta</title>
</head>
<body>
<div>
        <?php
        //mostrar errores
        if(isset($errors)){            
            echo ' <h2>Existen errores en el formulario:</h2> ';
            foreach ($errors as $value) {
                echo $value .'<br>';
            }
        }
        ?>
</div>
<br>
<form action="#" method="post">
        <label for="user">Nombre de usuario</label>
        <input type="text" name="user" id="user" value="<?= (isset($account))?$account->user:''?>">
        <br>
        <label for="email">Email</label>
        <input type="text" name="email" id="email" value="<?= (isset($account))?$account->email:''?>">
        <br>
        <label for="password">Introduce la contraseña</label>
        <input type="text" name="password" id="password" value="<?= (isset($_POST['password']))?$_POST['password']:''?>">
        <br>
        <input type="submit" value="Actualizar">
    </form>
    <br>
    <?php
			require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/footer.inc.php');
		?>
</body>
</html>
