<?php
require_once "pdo.php";
require_once "util.php";
session_start();

for($i=1; $i<=13; $i++) {
	if ( !isset($_POST['slot'.$i]) ) continue;
	$stmt = $pdo->prepare('UPDATE Slots
	SET meal = :mid
	WHERE slot_id = :sid');
	$stmt->execute(array(
	':mid' => substr($_POST['slot'.$i], 4),
	':sid' => $i)
	);		
	$_SESSION['success'] = 'Record Added';
	header( 'Location: index.php' );
	return;
}


$optionString = '<option disabled selected value>select a meal</option>';
$stmt = $pdo->query('SELECT meal_id, name from Meals');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	$optionString .= '<option value="meal'.$row['meal_id'].'">';
	$optionString .= htmlentities($row['name'])."</option>\n";
}


?>

<!DOCTYPE html>
<html>
<head>
<title>The Lennon Family Recipe Meal Planner</title>
<?php require_once "header.php"; ?>
</head>
<body>
<div class="container">
<h1>This Week&rsquo;s Meals</h1>
<?php  

flashMessages();

echo('<table border="1">'."\n");
$stmt = $pdo->query('SELECT Slots.name as day, Slots.meal, Slots.slot_id as sid, Meals.name as dish, Meals.meal_id as mid FROM Slots LEFT JOIN Meals on Slots.meal = Meals.meal_id ORDER BY sid');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
	echo('<tr><td>');
	echo(htmlentities($row['day']));
	echo('</td><td><a href="meals.php?meal_id='.$row['mid'].'">');
	echo(htmlentities($row['dish']));
	echo("</a></td><td>\n");
	echo('<form method="post"><select name="slot'.$row['sid'].'">');
	echo($optionString);
	echo('<input type="submit" value="Submit" />');
	echo("</form></td>\n");
}
?>
</table>
<p><a href="new.php">Add New Meal</a></p>
<p><a href="meals.php">View All Meals</a></p>


</div>
</body>
</html>