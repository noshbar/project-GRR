<?php
require_once 'database.php';

$db = openDatabase('test.db');
$prepared = $db->prepare("SELECT id, name, source, deleted FROM site WHERE deleted=0");
$prepared->execute();
$rows = $prepared->fetchAll(); 

$index = 0;
foreach ($rows as $row)
{
	$prepared = $db->prepare("SELECT COUNT(siteId) FROM item WHERE siteId=? AND read=0");
	$prepared->execute(array($row['id']));
	$count    = $prepared->fetch();

	$site['siteId']     = $row['id'];
	$site['siteName']   = $row['name'];
	$site['siteSource'] = $row['source'];
	$site['unread']     = $count[0];
	$result['sites'][$index++] = $site;
}

echo(json_encode($result));

?>