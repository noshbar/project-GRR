<?php
require_once 'database.php';

$tagId      = $_POST['id'];
$query      = "UPDATE tags SET deleted=1 WHERE id=?;";
$parameters = array($tagId);

$db = openDatabase('test.db');
$prepared = $db->prepare($query);
$prepared->execute($parameters);

$result['result'] = 'Tag removed.';
echo(json_encode($result));

?>
