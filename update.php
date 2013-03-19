<?php

require_once 'feed.class.php';
require_once 'database.php';

function updateFeed($Database, $Site)
{
	//This is horrible really. There is a UNIQUE flag set on the GUID column of the database... and, well...
	//I kinda just rely on the insert failing if there's a duplicate because the feed hasn't updated.
	//What I SHOULD do is either check to see if the item exists before inserting it... or better yet,
	//only get the newer items through a smarter HTTP get
	$rss      = Feed::loadRss($Site['source']);
	$prepared = $Database->prepare('INSERT INTO item(siteId, guid, source, timestamp, contents, title) VALUES (?, ?, ?, ?, ?, ?)');
	$count    = 0;
	foreach ($rss->item as $item)
	{
		try
		{
			$parameters    = array();
			$parameters[0] = $Site['id'];
			$parameters[1] = $item->guid;
			$parameters[2] = $item->link;
			$parameters[3] = $item->timestamp;
			if (isset($item->{'content:encoded'}))
				$parameters[4] = $item->{'content:encoded'};
			else
				$parameters[4] = $item->description;
			$parameters[5] = $item->title;

			$prepared->execute($parameters);
		}
		catch(PDOException $e)
		{
			die('updateFeed() Exception : '.$e->getMessage());
		}
		$count++;
	}
	return $count;
}

function updateSites($Database, $SiteId = -1)
{
	$query      = "SELECT name, id, source, deleted FROM site WHERE deleted=0";
	$parameters = array();
	if ($SiteId !== -1)
	{
		$query         .= ' AND id=?';
		$parameters[0]  = $SiteId;
	}

	$prepared = $Database->prepare($query);
	$prepared->execute($parameters);
	$rows = $prepared->fetchAll(); 

	$index = 0;
	foreach ($rows as $row)
	{
		$site['source']       = $row['source'];
		$site['id']           = $row['id'];
		$result[$row['name']] = updateFeed($Database, $site);
	}
	return $result;
}

$db = openDatabase('test.db');

if (isset($_SERVER['TERM']) || (isset($_POST['id']) && $_POST['id'] == 'all'))
	updateSites($db);

?>