<?php
require_once "pdo.php";
require_once "util.php";
session_start();


if ( isset($_POST['name']) OR isset($_POST['instructions']) OR
	isset($_POST['url']) ) {
	
	$msg = validateMeal();
	if ( is_string($msg) ) {
		$_SESSION['error'] = $msg;
		header("Location: new.php");
		return;
	}
	// if valid, continue adding
	
	$sql = "INSERT INTO Meals (name, instructions, url)
		VALUES (:name, :instructions, :url)";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(
		':name' => $_POST['name'],
		':instructions' => $_POST['instructions'],
		':url' => $_POST['url']));
	$mid = $pdo->lastInsertId();
	
	insertIngredients($pdo, $mid);
	
	
	$_SESSION['success'] = 'Record Added';
	header( 'Location: meals.php' );
	return;
}

?>

<!DOCTYPE html>
<html>
<head>
<title>The Lennon Family Recipe Meal Planner</title>
<?php require_once 'header.php'; ?>
</head>
<body>
<div class="container">
<h1>Add A New Meal</h1>
<?php flashMessages(); ?>

<form method="post">
<p>Name: <input type="text" name="name" size="40"></p>
<p>Ingredients: <input type="text" id="ing" list="ingDataList" /><input type="button" value="+" onclick="addIng()"></p>
<datalist id="ingDataList"></datalist>
<ul id="ingList">
</ul>
<p>Instructions:<br /><textarea name="instructions" rows="8" cols="80"></textarea></p>
<p>URL: <input type="text" name="last_name" size="40"></p>
<p><input type="submit" value="Submit" />
<a href="index.php">Cancel</a></p>
</form>

<script>
countIng = 1;
function addIng() {
	var ingredient = document.getElementById("ing").value
	var markup = `
	<li>${ingredient}</li>
	<input type="hidden" name="ingNum${countIng}" value="${ingredient}">
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