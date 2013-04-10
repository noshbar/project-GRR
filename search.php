<?php
require_once 'utils.php';

$db         = openDatabase('test.db');
$parameters = array();

if (isset($_POST['tag']) && ($_POST['tag'] != -1))
{
    $query  = 'SELECT item.id, site.name, contents.title FROM site, item, contents WHERE item.contentId=contents.docid AND site.id=item.siteId AND item.id IN (SELECT taggedItems.itemId FROM taggedItems WHERE tagId = ?)';
    array_push($parameters, $_POST['tag']);
    error.log($query);
}
else
{
    $query  = 'SELECT item.id, site.name, contents.title FROM site, item, contents WHERE item.contentId=contents.docid AND site.id=item.siteId';
}

if (isset($_POST['site']) && ($_POST['site'] != -1))
{
    $query .= ' AND item.siteId=?';
    array_push($parameters, $_POST['site']);
}

if (isset($_POST['searchTerm']) && $_POST['searchTerm'] != '')
{
    $query .= ' AND contents MATCH ?';
    array_push($parameters, $_POST['searchTerm']);
}

$query .= ' ORDER BY timestamp ASC';
$prepared = $db->prepare($query);
$prepared->execute($parameters);
$rows = $prepared->fetchAll(); 

$index = 0;
$extensions[1] = '.pdf';
$extensions[0] = '.html';
foreach ($rows as $row)
{
	$item['itemId']    = $row['id'];
	$item['site']      = $row['name'];
	$item['title']     = $row['title'];
    
    unset($item['localCopy']);
    $filename = getSaveName($row['name'], $row['id'], $row['title']);
    foreach ($extensions as $extension)
    {
        $filepath = $filename.$extension;
        if (file_exists($filepath))
        {
            $url = substr($filepath, strpos($filepath, '/saved/') + 1);
            $item['localCopy'] = $url;
            break;
        }
    }

	$result['items'][$index++] = $item;
}
$result['count'] = $index;

echo(json_encode($result));

?>