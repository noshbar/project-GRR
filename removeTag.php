<?php
require_once 'utils.php';

$tagId      = $_POST['id'];
$query      = "UPDATE tags SET deleted=1 WHERE id=?;";
$parameters = array($tagId);

$db = openDatabase();
$prepared = $db->prepare($query);
$prepared->execute($parameters);

$result['result'] = 'Tag removed.';
echo(json_encode($result));

?>
