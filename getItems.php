<?php
require_once 'utils.php';

$maxItems = 10;
if (isset($_POST['maxItems']))
    $maxItems = $_POST['maxItems'];

$db             = openDatabase('test.db');
$parameters     = array();
$query          = 'SELECT item.id, site.name, item.source, item.timestamp, contents.title, contents.body FROM site, item, contents WHERE site.id=item.siteId AND contents.docid=item.contentId';
if (isset($_POST['site']) && ($_POST['site'] != -1))
{
	$query .= ' AND item.siteId=?';
	array_push($parameters, $_POST['site']);
}
if (isset($_POST['itemId']) && ($_POST['itemId'] != -1))
{
	$query .= ' AND item.id=?';
	array_push($parameters, $_POST['itemId']);
}
else
{   //we're not looking for a specific item, so block those that are read or deleted
	$query .= ' AND read=0 AND item.deleted=0';
}
if (isset($_POST['lastId']) && ($_POST['lastId'] != -1))
{
	$query .= ' AND item.id<=?';
	array_push($parameters, $_POST['lastId']);
}

$query .= ' ORDER BY timestamp ASC LIMIT '.$maxItems;

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

	{
		$parameters   = array($row['id']);
		$query        = 'SELECT tags.name, tags.id, taggedItems.tagId FROM tags, taggedItems WHERE tags.id=taggedItems.tagId AND taggedItems.itemId=? AND tags.deleted=0';
		$prepared     = $db->prepare($query);
		$prepared->execute($parameters);
		$tags         = $prepared->fetchAll(); 
		$item['tags'] = array();
		foreach ($tags as $tag)
		{
			array_push($item['tags'], $tag);
		}
	}

	$result['items'][$index++] = $item;
}
$result['count'] = $index;

echo(json_encode($result));

?>