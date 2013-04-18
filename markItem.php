<?php
require_once 'utils.php';

function markItem($Database, $ItemId)
{
	try
	{
		$prepared      = $Database->prepare('UPDATE item SET read=1 WHERE id=?');
		$parameters    = array($ItemId);
		$prepared->execute($parameters);
	}
	catch(PDOException $e)
	{
		quit('markItem() Exception : '.$e->getMessage());
	}
}

$db = openDatabase();
$itemId = $_POST['itemId'];
$siteId = $_POST['siteId'];
$action = $_POST['action'];

markItem($db, $itemId, $action);

$result['itemId'] = $itemId;
$result['siteId'] = $siteId;
echo(json_encode($result));

?>