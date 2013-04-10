<?php
require_once 'utils.php';

$db = openDatabase('test.db');
$prepared = $db->prepare("SELECT id, name, source, deleted FROM site WHERE deleted=0");
$prepared->execute();
$rows = $prepared->fetchAll(); 

$index = 0;
$largestId = -1;
foreach ($rows as $row)
{
	$prepared = $db->prepare("SELECT id, COUNT(siteId) FROM item WHERE siteId=? AND read=0");
	$prepared->execute(array($row['id']));
	$items    = $prepared->fetch();

	$site['siteId']     = $row['id'];
	$site['siteName']   = $row['name'];
	$site['siteSource'] = $row['source'];
	$site['unread']     = $items[1];
	$result['sites'][$index++] = $site;

	if ($items[0] > $largestId)
		$largestId = $items[0];
}

$result['lastItemId'] = $largestId;
echo(json_encode($result));

?>