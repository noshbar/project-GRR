<?php
require_once 'database.php';

$db             = openDatabase('test.db');
$parameters     = array($_POST['searchTerm']);
$query          = 'SELECT item.id, site.name, contents.title FROM site, item, contents WHERE item.contentId=contents.docid AND site.id=item.siteId AND contents.body MATCH ?';

$prepared = $db->prepare($query);
$prepared->execute($parameters);
$rows = $prepared->fetchAll(); 

$index = 0;
foreach ($rows as $row)
{
	$item['itemId']    = $row['id'];
	$item['site']      = $row['name'];
	$item['title']     = $row['title'];
	$result['items'][$index++] = $item;
}
$result['count'] = $index;

echo(json_encode($result));

?>