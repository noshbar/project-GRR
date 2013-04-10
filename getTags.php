<?php
require_once 'database.php';

$db = openDatabase('test.db');
$prepared = $db->prepare("SELECT id, name FROM tags WHERE deleted=0");
$prepared->execute();
$rows = $prepared->fetchAll(); 

$index = 0;
$largestId = -1;
foreach ($rows as $row)
{
	$site['id']     = $row['id'];
	$site['name']   = $row['name'];
	$result['tags'][$index++] = $site;
}

echo(json_encode($result));
?>
