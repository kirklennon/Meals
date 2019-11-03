<?php
header('Content-Type: application/json; charset=utf-8');
require_once "pdo.php";
$stmt = $pdo->prepare('SELECT name FROM Ingredients WHERE name LIKE :prefix');
$stmt->execute(array( ':prefix' => $_REQUEST['query']."%"));
$retval = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
  $retval[] = $row['name'];
}
echo(json_encode($retval, JSON_PRETTY_PRINT));
?>