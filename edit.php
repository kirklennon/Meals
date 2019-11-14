<?php
require_once "pdo.php";
require_once "util.php";
session_start();

if ( !isset($_GET['meal_id']) ) {
	$_SESSION['error'] = 'No meal selected';
	header( 'Location: meals.php' );
	return;
}

if ( isset($_POST['name']) OR isset($_POST['instructions']) OR
	isset($_POST['url']) ) {
	
	$msg = validateMeal();
	if ( is_string($msg) ) {
		$_SESSION['error'] = $msg;
		header("Location: edit.php?meal_id=".$_REQUEST['meal_id']);
		return;
	}
	// if valid, continue updating
	
	$sql = "UPDATE Meals SET name = :name,
	 instructions = :instructions, url = :url
	 WHERE meal_id = :mid"; 
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(
		':name' => $_POST['name'],
		':instructions' => $_POST['instructions'],
		':url' => $_POST['url'],
		':mid' => $_POST['meal_id']));
	
	// clear out any existing ingredients for meal
	$sql = "DELETE FROM Linking WHERE meal = :mid";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(':mid' => $_POST['meal_id']));
	
	insertIngredients($pdo, $_POST['meal_id']);
	
	$_SESSION['success'] = 'Record updated';
	header( 'Location: meals.php' );   //     redirect to specific meal instead?
	return;
}

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

$ingredients = loadIng($pdo, $_REQUEST['meal_id']);
?>

<!DOCTYPE html>
<html>
<head>
<title>The Lennon Family Recipe Meal Planner</title>
<?php require_once "header.php"; ?>
</head>
<body>
<div class="container">

<h1>Edit Meal</h1>

<form method="post">
<p>Name: <input type="text" name="name" size="40" value="<?= $name ?>"></p>
<p>Ingredients: <input type="text" id="ing" list="ingDataList" /><input type="button" value="+" onclick="addIng()"></p>
<datalist id="ingDataList"></datalist>
<ul id="ingList">


<?php
$countIng = 1;

if ( count($ingredients) > 0 ) {
	foreach( $ingredients as $ing ) {
		echo('<li><a href="#" onclick="this.parentElement.remove(0);return false;">'.htmlentities($ing)."</a>\n");
		echo('<input type="hidden" name="ingNum'.$countIng.'" value="'.htmlentities($ing).'">'."</li>\n");		
		$countIng++;
	}
}
?>

</ul>
<p>Instructions:<br /><textarea name="instructions" rows="8" cols="80">
<?= $steps ?></textarea></p>
<p>URL: <input type="text" name="url" size="40" value="<?= $url ?>"></p>
<input type="hidden" name="meal_id" value="<?= $mid ?>">
<p><input type="submit" value="Update" />
<a href="index.php">Cancel</a></p>
</form>


<script>
countIng = <?= $countIng ?>;
function addIng() {
	var ingredient = document.getElementById("ing").value
	var markup = `
	<li><a href="#" onclick="this.parentElement.remove(0);return false;">${ingredient}</a><input type="hidden" name="ingNum${countIng}" value="${ingredient}"></li>
	`;
	document.getElementById("ingList").insertAdjacentHTML('beforeend', markup);
	document.getElementById("ing").value = '';
	countIng++;
}
 

// plain Javascript autocomplete code below adapted from
// https://dev.to/stephenafamo/how-to-create-an-autocomplete-input-with-plain-javascript 
 
 
window.addEventListener("load", function(){
    var name_input = document.getElementById('ing');
    name_input.addEventListener("keyup", function(event){hinter(event)});
    window.hinterXHR = new XMLHttpRequest();
});

function hinter(event) {
    var input = event.target;
    var huge_list = document.getElementById('ingDataList');
    var min_characters = 1;
    if (input.value.length < min_characters ) { 
        return;
    } else { 
        window.hinterXHR.abort();
        window.hinterXHR.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse( this.responseText ); 
                huge_list.innerHTML = "";
                response.forEach(function(item) {
                    var option = document.createElement('option');
                    option.value = item;
                    huge_list.appendChild(option);
                });
            }
        };

        window.hinterXHR.open("GET", "ingredients.php?query=" + input.value, true);
        window.hinterXHR.send()
    }
} 
 
</script>

</div>
</body>
</html>