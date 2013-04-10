<?php
require_once 'utils.php';

$tagName = $_POST['name'];

//$query      = "INSERT OR REPLACE INTO tags(name, deleted) VALUES(?, 0);";
$query      = "INSERT INTO tags(name, deleted) VALUES(?, 0);";
$parameters = array($tagName);

$db = openDatabase('test.db');
$prepared = $db->prepare($query);
$prepared->execute($parameters);

$result['result'] = 'Tag added.';
echo(json_encode($result));

?>
