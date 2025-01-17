<?php
/**
 * pagina para ver las entradas
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
//Comprueba que se ha iniciado la sesion
if (!isset($_SESSION['userName'])){
    header('location:/index.php');
    exit;
}else{
    if(isset($_POST['comentNew'])){
        $_SESSION['comentNew']=$_POST['comentNew'];
        header('location:/comment.php');
        exit;
    }

    foreach($_GET as $key=>$value){
        $_GET[$key]=trim($value);
    }
    
    //comprueba si se le ha pasado un id
    if(isset($_GET['entry_id'])){
        //guarda el id de la ultima publicacion vista
        $_SESSION['idLastEntry']=$_GET['entry_id'];
    }
    if(isset($_SESSION['idLastEntry'])){
        
        try {
            require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/env.inc.php');
            require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/connection.inc.php');
            if ($connection = getDBConnection(DB_NAME, DB_USERNAME, DB_PASSWORD)) {
                
                    if(isset($_GET['like'])){
                        try {
                        // Verificar si ya existe el like
                            $queryCheckLike = $connection->prepare('SELECT * FROM likes WHERE entry_id = :entry_id AND user_id = :user_id');
                            $queryCheckLike->bindParam(':entry_id', $_SESSION['idLastEntry']);
                            $queryCheckLike->bindParam(':user_id', $_SESSION['id']);
                            $queryCheckLike->execute();

                            if ($queryCheckLike->rowCount() > 0) {
                                // Si existe, eliminar el like
                                $queryDeleteLike = $connection->prepare('DELETE FROM likes WHERE entry_id = :entry_id AND user_id = :user_id');
                                $queryDeleteLike->bindParam(':entry_id', $_SESSION['idLastEntry']);
                                $queryDeleteLike->bindParam(':user_id', $_SESSION['id']);
                                $queryDeleteLike->execute();
                            } else {
                                // Si no existe, agregar el like
                                $queryInsertLike = $connection->prepare('INSERT INTO likes (entry_id, user_id) VALUES (:entry_id, :user_id)');
                                $queryInsertLike->bindParam(':entry_id', $_SESSION['idLastEntry']);
                                $queryInsertLike->bindParam(':user_id', $_SESSION['id']);
                                $queryInsertLike->execute();

                                // Eliminar cualquier dislike del usuario para la misma entrada
                                $queryDeleteDislike = $connection->prepare('DELETE FROM dislikes WHERE entry_id = :entry_id AND user_id = :user_id');
                                $queryDeleteDislike->bindParam(':entry_id', $_SESSION['idLastEntry']);
                                $queryDeleteDislike->bindParam(':user_id', $_SESSION['id']);
                                $queryDeleteDislike->execute();
                            }
                        } catch (Exception $e) {
                            $errors['like']='Error con el like, intentelo de nuevo mas tarde';
                        }

                    }
                    if(isset($_GET['dislike'])){
                        try {                    
                            // Verificar si ya existe el dislike
                            $queryCheckDislike = $connection->prepare('SELECT * FROM dislikes WHERE entry_id = :entry_id AND user_id = :user_id');
                            $queryCheckDislike->bindParam(':entry_id', $_SESSION['idLastEntry']);
                            $queryCheckDislike->bindParam(':user_id', $_SESSION['id']);
                            $queryCheckDislike->execute();
                    
                            if ($queryCheckDislike->rowCount() > 0) {
                                // Si existe, eliminar el dislike
                                $queryDeleteDislike = $connection->prepare('DELETE FROM dislikes WHERE entry_id = :entry_id AND user_id = :user_id');
                                $queryDeleteDislike->bindParam(':entry_id', $_SESSION['idLastEntry']);
                                $queryDeleteDislike->bindParam(':user_id', $_SESSION['id']);
                                $queryDeleteDislike->execute();
                            } else {
                                // Si no existe, agregar el dislike
                                $queryInsertDislike = $connection->prepare('INSERT INTO dislikes (entry_id, user_id) VALUES (:entry_id, :user_id)');
                                $queryInsertDislike->bindParam(':entry_id', $_SESSION['idLastEntry']);
                                $queryInsertDislike->bindParam(':user_id', $_SESSION['id']);
                                $queryInsertDislike->execute();
                    
                                // Eliminar cualquier like del usuario para la misma entrada
                                $queryDeleteLike = $connection->prepare('DELETE FROM likes WHERE entry_id = :entry_id AND user_id = :user_id');
                                $queryDeleteLike->bindParam(':entry_id', $_SESSION['idLastEntry']);
                                $queryDeleteLike->bindParam(':user_id', $_SESSION['id']);
                                $queryDeleteLike->execute();
                            }
                        } catch (Exception $e) {
                            $errors['dislike']='Error con el dislike, intentelo de nuevo mas tarde';
                            
                        }
                    }

            //obtener los datos de la entrada
            $query =$connection->prepare ('SELECT e.id AS entry_id, e.user_id, e.text, e.date,
                    COALESCE(likes_count.total_likes, 0) AS total_likes,
                    COALESCE(dislikes_count.total_dislikes, 0) AS total_dislikes
                FROM 
                    entries e
                LEFT JOIN 
                    (SELECT entry_id, COUNT(*) AS total_likes FROM likes GROUP BY entry_id) likes_count 
                    ON e.id = likes_count.entry_id
                LEFT JOIN 
                    (SELECT entry_id, COUNT(*) AS total_dislikes FROM dislikes GROUP BY entry_id) dislikes_count 
                    ON e.id = dislikes_count.entry_id
                WHERE 
                    e.id = :id;');
            $query->bindParam(':id',$_SESSION['idLastEntry']);
            $query->execute();
            //guardar los datos
            $entryData = $query->fetchAll(PDO::FETCH_OBJ);
                
            //obtener los comentarios
            $queryComents=$connection->prepare ('SELECT c.id AS comment_id, 
                                                c.user_id AS comment_user_id,
                                                c.text AS comment_text, 
                                                c.date AS comment_date,
                                                u.user AS comment_user_name
                                            FROM 
                                                comments c
                                            INNER JOIN 
                                                users u
                                            ON 
                                                c.user_id = u.id
                                            WHERE 
                                                c.entry_id = :id
                                            ORDER BY 
                                                c.date ASC;');
            $queryComents->bindParam(':id',$_SESSION['idLastEntry']);
            $queryComents->execute();
            //guardar los datos
            $entryComents = $queryComents->fetchAll(PDO::FETCH_OBJ);
        } else {
            throw new Exception('Error en la conexión a la BBDD');
        }
        unset($query);
        unset($connection);
        unset($queryCheckLike);
        unset($queryDeleteLike);
        unset($queryInsertLike);
        unset($queryDeleteDislike);
        unset($queryInsertDislike);
        unset($queryCheckDislike);
    } catch (Exception $exception) {
        $errors['id']='No se ha encontrado ninguna publicacion';
        unset($query);
        unset($connection);
        unset($queryCheckLike);
        unset($queryDeleteLike);
        unset($queryInsertLike);
        unset($queryDeleteDislike);
        unset($queryInsertDislike);
        unset($queryCheckDislike);
    }
    }else{
        $errors['id']='No se esta buscando ninguna publicacion';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Como se desee</title>
		<link rel="stylesheet" href="/css/style.css">
	</head>

	<body>
    <?php
			require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/header.inc.php');
		?>
		<section class="entradas">
			<?php
            //si no hay errores mostrar la publicacion
			if (!isset($errors)) {
				foreach($entryData as $entry) {
					echo '<article class="entrada">';
					    echo '<span>'. $entry->text .'</span><br>';
                        echo '<a href="/entry.php?like=true"><span>'. $entry->total_likes .' likes</span></a>';
                        echo '<a href="/entry.php?dislike=true"><span>'. $entry->total_dislikes .' dislikes</span><br></a>';
					echo '</article>';
				}

                //formulario para comentar
                ?>
                <form action="#" method="post">
                    <label for="comentNew">Quieres comentar?</label>
                    <input type="text" name="comentNew" id="comentNew">
                    <input type="submit" value="Publicar comentario">
                </form>


                <?php
                //mostrar comentarios
                foreach($entryComents as $comment) {
					echo '<article class="comentario">';
					    echo '<h2>'. $comment->comment_user_name .'</h2>';
					    echo '<span>'. $comment->comment_text .'</span>';
					echo '</article>';
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
