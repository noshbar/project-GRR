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
		$result = 'addSite() Exception : '.$e->getMessage();
	}
}

$db = openDatabase();
$itemId = $_POST['itemId'];
$action = $_POST['action'];

markItem($db, $itemId, $action);

?>