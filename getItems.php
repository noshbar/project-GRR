<?php
require_once 'database.php';

$maxItems = 10;
if (isset($_POST['maxItems']))
    $maxItems = $_POST['maxItems'];
$itemOffset = 0;
if (isset($_POST['itemOffset']))
    $itemOffset = $_POST['itemOffset'];

$db             = openDatabase('test.db');
$parameters     = array();
$query          = 'SELECT item.id, site.name, item.source, item.timestamp, contents.title, contents.body FROM site, item, contents WHERE read=0 AND item.deleted=0 AND site.id=item.siteId AND contents.docid=item.contentId';
if (isset($_POST['id']) && ($_POST['id'] != -1))
{
	$query .= ' AND item.siteId=?';
	$parameters[0] = $_POST['id'];
}
if (isset($_POST['lastId']) && ($_POST['lastId'] != -1))
{
	$query .= ' AND item.id<=?';
	$parameters[1] = $_POST['lastId'];
}

$query .= ' ORDER BY timestamp ASC LIMIT '.$maxItems;//.' OFFSET '.$itemOffset; <- this is taken care of by things marking themselves as read
error_log("$query");
$prepared = $db->prepare($query);
$prepared->execute($parameters);
$rows = $prepared->fetchAll(); 

$index = 0;
foreach ($rows as $row)
{
	$item['itemId']    = $row['id'];
	$item['siteId']    = $row['name'];
	$item['source']    = $row['source'];
	$item['title']     = $row['title'];
	$item['contents']  = $row['body'];
	$item['timestamp'] = date("F j, Y, g:i a", (int)$row['timestamp']);
	$result['items'][$index++] = $item;
}
$result['count'] = $index;

echo(json_encode($result));

?>