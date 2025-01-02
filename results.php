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
    if(!isset($_SESSION['buscar'])){
        $errors['busqueda']='No estas buscando nada';
    }else{
        require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/env.inc.php');
        require_once($_SERVER['DOCUMENT_ROOT'] .'/includes/connection.inc.php');
        try {
            if ($connection = getDBConnection(DB_NAME, DB_USERNAME, DB_PASSWORD)) {
                //obtener los datos de la entrada
                $query =$connection->prepare ('SELECT id AS user_id, user FROM users WHERE user LIKE :buscar');
                // Preparar el valor de búsqueda con comodines para la consulta LIKE
                $buscar = '%' . $_SESSION['buscar'] . '%';
                // Vincular el parámetro con el valor de búsqueda
                $query->bindParam(':buscar', $buscar);
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
                        echo '<article class="entrada">';
                            echo '<a href="/user.php/?'.$result->user_id.'">'. $result->user .'</a>';
                        echo '</article>';
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
