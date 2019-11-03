<?php 
function flashMessages() {
	if ( isset($_SESSION['error']) ) {
	echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
	unset($_SESSION['error']);
	}
	if ( isset($_SESSION['success']) ) {
		echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
		unset($_SESSION['success']);
	}
}

function validateMeal() {
	if ( strlen($_POST['name']) == 0 OR
		(strlen($_POST['url']) == 0 AND
		strlen($_POST['instructions']) == 0) ) {
    	return 'Name and either instructions or URL are required';
   	 }
}

function insertIngredients($pdo, $mid) {
	for($i=1; $i<=30; $i++) {
		if ( !isset($_POST['ingNum'.$i]) ) continue;
		$ingredient = ucwords($_POST['ingNum'.$i]);
		$ingredient_id = false;
		$stmt = $pdo->prepare('SELECT ingredient_id FROM
			Ingredients WHERE name = :name');
		$stmt->execute(array(':name' => $ingredient));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ( $row !== false ) $ingredient_id = $row['ingredient_id'];
		if ( $ingredient_id == false ) {
			$stmt = $pdo->prepare('INSERT INTO Ingredients
				(name) VALUES (:name)');
			$stmt->execute(array(':name' => $ingredient));
			$ingredient_id = $pdo->lastInsertId();
		}
				
		$stmt = $pdo->prepare('INSERT INTO Linking
		(meal, ingredient)
		VALUES ( :mid, :iid)');
		$stmt->execute(array(
		':mid' => $mid,
		':iid' => $ingredient_id)
		);
	}

}

function loadIng($pdo, $mid) {
	$stmt = $pdo->prepare('SELECT Ingredients.name AS iname, Ingredients.ingredient_id, Linking.meal, Linking.ingredient FROM Ingredients LEFT JOIN Linking ON Ingredients.ingredient_id = Linking.ingredient WHERE Linking.meal = :mid');
	$stmt->execute(array( ':mid' => $mid));
	$ingredients = $stmt->fetchAll(PDO::FETCH_COLUMN, 'iname');
	return $ingredients;
}