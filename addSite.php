<?php
require_once 'utils.php';
require_once 'update.php';

function addSite($Database, $Site)
{
	try
	{
		$Database->beginTransaction();
		$prepared      = $Database->prepare('INSERT INTO site(name, source) VALUES (?, ?)');
		$parameters    = array();
		$parameters[0] = $Site['name'];
		$parameters[1] = $Site['source'];
		$prepared->execute($parameters);
		$result = $Database->lastInsertId();
		$Database->commit();
	}
	catch(PDOException $e)
	{
		quit('addSite() Exception : '.$e->getMessage());
	}
	return $result;
}

$db = openDatabase();
$site['name']    = $_POST['siteName'];
$site['source']  = $_POST['siteSource'];
$result['site']['unread'] = 0;

$id = addSite($db, $site);
if ($id)
{
	$count = updateSites($db, $id);
	$result['site']['unread'] = $count[$site['name']];
}
$result['site']['id']     = $id;
$result['site']['name']   = $site['name'];
$result['site']['source'] = $site['source'];

echo(json_encode($result));

?>