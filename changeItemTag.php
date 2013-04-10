<?php
require_once 'utils.php';

$result['action'] = $_POST['action'];
$result['item']   = $_POST['item'];
$result['tag']    = $_POST['tag'];
$result['text']   = $_POST['text'];
$result['select'] = $_POST['select'];
$result['div']    = $_POST['div'];

if ($result['action'] == 'add')
    $query = "INSERT INTO taggedItems(tagId, itemId) VALUES(?, ?);";
elseif ($result['action'] == 'remove')
    $query = "DELETE FROM taggedItems WHERE tagId=? AND itemId=?;";

$parameters = array($result['tag'], $result['item']);

$db = openDatabase('test.db');
$prepared = $db->prepare($query);
$prepared->execute($parameters);

echo(json_encode($result));
?>
