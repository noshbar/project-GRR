<?php
require_once 'utils.php';

$tagName = $_POST['name'];

//$query      = "INSERT OR REPLACE INTO tags(name, deleted) VALUES(?, 0);";
$query      = "INSERT INTO tags(name, deleted) VALUES(?, 0);";
$parameters = array($tagName);

$db = openDatabase('test.db');
$prepared = $db->prepare($query);
if (!$prepared->execute($parameters))
	quit("Could not add tag '$tagName' (".$prepared->errorInfo()[2].")");

$result['result'] = "Tag '$tagName' added.";
echo(json_encode($result));

?>
