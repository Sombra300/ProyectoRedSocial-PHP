<?php
/**
 * pagina para ver la lista de publicaciones y poder eliminarlos
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
    try {
        if ($connection = getDBConnection(DB_NAME, DB_USERNAME, DB_PASSWORD)) {//SELECT id, text FROM entries WHERE user_id = :user_id
            $query =$connection->prepare ('SELECT e.id AS entry_id, e.user_id, e.text,
         COALESCE(likes_count.total_likes, 0) AS total_likes,
        COALESCE(dislikes_count.total_dislikes, 0) AS total_dislikes
        FROM 
            entries e
        JOIN 
            users u ON e.user_id = u.id
        LEFT JOIN 
            (SELECT entry_id, COUNT(*) AS total_likes FROM likes GROUP BY entry_id) likes_count 
            ON e.id = likes_count.entry_id
        LEFT JOIN 
            (SELECT entry_id, COUNT(*) AS total_dislikes FROM dislikes GROUP BY entry_id) dislikes_count 
            ON e.id = dislikes_count.entry_id
        WHERE 
            user_id = :user_id
        ORDER BY 
            e.date DESC');
        $query->bindParam(':user_id',$_SESSION['id']);
        $query->execute();
            $entries = $query->fetchAll(PDO::FETCH_OBJ);
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
			if (count($entries)>0) {
                //listar las publicaciones
				foreach($entries as $entry) {
					echo '<article class="entrada">';
					    echo '<span>'. $entry->text .'</span><br>';
                        echo '<span>'. $entry->total_likes .' likes</span>';
                        echo '<span>'. $entry->total_dislikes .' dislikes</span><br>';
					
					    echo '<span class="botonesCarrito">';
			    		    echo '<a href="/delete.php?entry_id='.$entry->entry_id.'" class="entrada">Eliminar publicacion</a>';		
					    echo '</span>';
					echo '</article>';
				}
			} else {
				echo '<h2>No tienes ninguna publicacion.</h2>';
			}
			?>
		</section>
        <?php
			require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/footer.inc.php');
		?>
</body>
</html>
