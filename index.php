<?php
/**
 * pagina principal para ver red social
 *
 * @author Eloy
 *
 * @version 2.0
 *
 */

ini_set('session.name','sesionEloy');
ini_set('session.cookie_httponly',1);
ini_set('session.cache_expire', 5);
session_start();



require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/env.inc.php');
require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/connection.inc.php');
try {
	if ($connection = getDBConnection(DB_NAME, DB_USERNAME, DB_PASSWORD)) {
		$query =$connection->prepare ('SELECT e.id AS entry_id, e.user_id, u.user AS userNameAutor, e.text, e.date,
         COALESCE(likes_count.total_likes, 0) AS total_likes,
        COALESCE(dislikes_count.total_dislikes, 0) AS total_dislikes
        FROM 
            entries e
        JOIN 
            follows f ON e.user_id = f.user_followed
        JOIN 
            users u ON e.user_id = u.id
        LEFT JOIN 
            (SELECT entry_id, COUNT(*) AS total_likes FROM likes GROUP BY entry_id) likes_count 
            ON e.id = likes_count.entry_id
        LEFT JOIN 
            (SELECT entry_id, COUNT(*) AS total_dislikes FROM dislikes GROUP BY entry_id) dislikes_count 
            ON e.id = dislikes_count.entry_id
        WHERE 
            f.user_id = :id
        ORDER BY 
            e.date DESC
    ');
    $query->bindParam(':id',$_SESSION['id']);
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
?>
<!doctype html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Como se desee</title>
		<link rel="stylesheet" href="/css/style.css">
	</head>

	<body>
		<?php
			require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/header.inc.php');
		
			if(isset($_SESSION['userName'])){
		?>

<!-- Si el usuario está logueado (existe su variable de sesión): -->

		<section class="entradas">
			<?php
			if (count($entries)>0) {
				
				foreach($entries as $entry) {
					echo '<article class="entrada">';
					    echo '<h2>'. $entry->userNameAutor .'</h2>';
					    echo '<span>'. $entry->text .'</span><br>';
                        echo '<span>'. $entry->total_likes .' likes </span>';
                        echo '<span>'   . $entry->total_dislikes .' dislikes</span><br>';
					    echo '<span>';
			    		    echo '<a href="/entry.php/?'.$entryID=$entry->entry_id.'" class="entrada">Ver publicacion</a>';		
					    echo '</span>';
					echo '</article>';
				}
			} else {
				echo '<h2>No sigues a nadie que haya publicado algo.</h2>';
			}
			?>
		</section>
<?php
			}
            require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/footer.inc.php');
?>


	</body>
</html>