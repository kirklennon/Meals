<?php
require_once 'pdo.php';
require_once 'util.php';
session_start();

?>

<!DOCTYPE html>
<html>
<head>
<title>The Lennon Family Recipe Meal Planner</title>
<?php require_once "header.php"; ?>
</head>
<body>
<div class="container">
<h1>View Meals</h1>
<?php
	flashMessages();
	
if ( !isset($_GET['meal_id']) ) {
	echo('<table>');
	$stmt = $pdo->query('SELECT meal_id, name from Meals');
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		echo('<tr><td>');
		echo(htmlentities($row['name']).'</td><td>');
		echo('<a href="meals.php?meal_id='.$row['meal_id'].'">view</a> / ');
		echo('<a href="edit.php?meal_id='.$row['meal_id'].'">edit');
		echo("</a></td></tr>\n");
	}
	echo('</table>');
}


if ( isset($_GET['meal_id']) ) {
	$stmt = $pdo->prepare("SELECT * FROM Meals where meal_id = :meal_id");
	$stmt->execute(array(":meal_id" => $_GET['meal_id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if ( $row === false ) {
		$_SESSION['error'] = 'Bad value for meal_id';
		header( 'Location: meals.php' );
		return;
	}
	
	$name = htmlentities($row['name']);
	$steps = htmlentities($row['instructions']);
	$url = htmlentities($row['url']);
	$mid = $row['meal_id'];

	echo('<h2>'.$name.'<h2>');
	echo('<h3>Ingredients</h3><ul>');
	$ingredients = loadIng($pdo, $mid);
	if ( count($ingredients) > 0 ) {
		foreach( $ingredients as $ing ) {
			echo('<li>'.htmlentities($ing)."</li>\n");
		}
	}
	echo('</ul>');
	echo('<h3>Instructions</h3>');
	echo('<p>'.$steps.'</p>');
	echo('<p>URL: ');
	if ($url) {
		echo('<a href="'.$url.'">'.$url.'</a>');
	}
	echo("</p>\n");
	echo('<p><a href="edit.php?meal_id='.$mid.'">Edit</a></p>');
}


?>



<p><a href="new.php">Add a new meal</a></p>

<p><a href="index.php">Main page</a></p>


</body>
</html>