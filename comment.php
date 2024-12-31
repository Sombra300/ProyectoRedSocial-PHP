<?php
/**
 * pagina para añadir comentarios a la BBDD
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
    if(isset($_SESSION['comentNew'])){
        try {
            //query para insertar el comentario
            require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/env.inc.php');
            require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/connection.inc.php');
            if ($connection = getDBConnection(DB_NAME, DB_USERNAME, DB_PASSWORD)) {
                $query =$connesction->prepare ('INSERT INTO comments (text, entry_id, user_id, date) 
                VALUES (:text, :entry_id, :user_id, :date)');
            $query->bindParam(':text',$_SESSION['comentNew']);
            $query->bindParam(':entry_id',$_SESSION['idLastEntry']);
            $query->bindParam(':user_id',$_SESSION['idLastEntry']);
            $query->bindParam(':date', date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $query->execute();

            if ($query->rowCount() > 0) {
                header('location:/entry.php');
                exit;
            } else {
                $errors['insert']='Error al introducir los datos, intentelo de nuevo mas tarde';
            }
               
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
if(isset($errors)){
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
        <?php
			require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/footer.inc.php');
		?>
        
    </body>
    </html>

    <?php
}else{
    header('location:/entry.php');
}
?>
