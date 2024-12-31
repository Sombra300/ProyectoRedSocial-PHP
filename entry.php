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

    //comprueba si se le ha pasado un id
    if(isset($_GET['id'])){
        //guarda el id de la ultima publicacion vista
        $_SESSION['idLastEntry']=$_GET['id'];
    }

    if(isset($_SESSION['idLastEntry'])){
    try {
        require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/env.inc.php');
        require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/connection.inc.php');
        if ($connection = getDBConnection(DB_NAME, DB_USERNAME, DB_PASSWORD)) {
            //obtener los datos de la entrada
            $query =$connesction->prepare ('SELECT e.id AS entry_id, e.user_id, e.text, e.date,
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
            $entryData = $connection->query($query)->fetchAll(PDO::FETCH_OBJ);
                
            //obtener los comentarios
            $queryComents=$connection->prepare ('SELECT c.id AS comment_id, c.user_id AS comment_user_id,
                            c.text AS comment_text, c.date AS comment_date
                        FROM 
                            comments c
                        WHERE 
                            c.entry_id = :id
                        ORDER BY 
                            c.date ASC;');
            $queryComents->bindParam(':id',$_SESSION['idLastEntry']);
            $queryComents->execute();
            //guardar los datos
            $entryComents = $connection->query($queryComents)->fetchAll(PDO::FETCH_OBJ);
        } else {
            throw new Exception('Error en la conexión a la BBDD');
        }
        unset($query);
        unset($connection);
    } catch (Exception $exception) {
        $errors['id']='No se esta buscando ninguna publicacion';
        unset($query);
        unset($connection);
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
					    echo '<span>'. $entry->text .'</span>';
                        echo '<span>'. $entry->total_likes .' likes</span><br>';
                        echo '<span>'. $entry->total_dislikes .' dislikes</span><br>';
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
					    echo '<h2>'. $comment->userNameAutor .'</h2>';
					    echo '<span>'. $comment->text .'</span>';
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
