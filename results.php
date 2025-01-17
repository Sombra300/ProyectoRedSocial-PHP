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
    if(!isset($_POST['buscar'])){
        $errors['busqueda']='No estas buscando nada';
    }else{
        require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/env.inc.php');
        require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/connection.inc.php');
        try {
            if ($connection = getDBConnection(DB_NAME, DB_USERNAME, DB_PASSWORD)) {
                if(isset($_POST['user_to_follow'])){
                    try{
                        $queryFollow = $connection->prepare('INSERT INTO follows (user_id, user_followed) VALUES (:user_id, :user_followed)');
                        $queryFollow->bindParam(':user_id', $_SESSION['id']); 
                        $queryFollow->bindParam(':user_followed', $_POST['user_to_follow']); // Usuario a seguir
                        $queryFollow->execute();
                        unset($queryFollow);
                    }catch(Exception $exception) {
                        $errors['follow']='No se ha podido seguir al usuario';
                    }
                }

                if(isset($_POST['user_to_unfollow'])){
                    try{
                        $queryUnfollow = $connection->prepare('DELETE FROM follows WHERE user_id = :user_id AND user_followed = :user_followed');
                        $queryUnfollow->bindParam(':user_id', $_SESSION['id']); 
                        $queryUnfollow->bindParam(':user_followed', $_POST['user_to_unfollow']); // Usuario a dejar de seguir
                        $queryUnfollow->execute();
                        unset($queryUnfollow);
                    }catch(Exception $exception) {
                        $errors['unfollow']='No se ha podido dejar de seguir al usuario';
                    }
                }
                //obtener los datos de la entrada
                $query =$connection->prepare ('SELECT id AS user_id, user, 
                                    EXISTS (
                                        SELECT 1 
                                        FROM follows 
                                        WHERE user_id = :current_user_id AND user_followed = users.id
                                    ) AS followed
                                    FROM 
                                    users 
                                    WHERE 
                                    user LIKE :buscar');
                // Preparar el valor de búsqueda con comodines para la consulta LIKE
                $buscar = '%' . $_POST['buscar'] . '%';
                // Vincular el parámetro con el valor de búsqueda
                $query->bindParam(':buscar', $buscar);
                $query->bindParam(':current_user_id', $_SESSION['id']);
                $query->execute();
                //guardar los datos
                $results = $query->fetchAll(PDO::FETCH_OBJ);
            } else {
                throw new Exception('Error en la conexión a la BBDD');
            }
            unset($query);
            unset($connection);
        } catch (Exception $exception) {
            $errors['id']='No se esta encontrando ningun usuario';
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
<section class="entradas">
			<?php
            //si no hay errores mostrar la publicacion
			if (!isset($errors)) {
                if(isset($results)){
                    foreach($results as $result) {
                        echo'<form action="#" method="post">';
                            echo '<article class="entrada">';
                            echo '<a href="/user.php?user_id='.$result->user_id.'">'.$result->user.'</a>';
                            //dato oculto para identificar el usuario
                            if($result->followed==1){
                                    echo '<input type="hidden" name="user_to_unfollow" value="'.$result->user_id.'">';
                                    echo '<input type="hidden" name="buscar" value="'.$_POST['buscar'].'">';
                                    echo '<input type="submit" value="Dejar de seguir">';
                                }else{
                                    echo '<input type="hidden" name="user_to_follow" value="'.$result->user_id.'">';
                                    echo '<input type="hidden" name="buscar" value="'.$_POST['buscar'].'">';
                                    echo '<input type="submit" value="Seguir">';
                                }
                            echo '</article>';
                    echo'</form>';
                    }
                }else{
                    echo '<div>No se ha encontrado ningun usario con estas caracteristicas</div>';
                }
				
			} else {
				foreach($errors as $error){
                    echo '<div>'.$error.'</div>';
                }
			}
			?>
	</section>
    <?php
        require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/footer.inc.php');
    ?>
</body>
</html>
