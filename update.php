<?php

require_once 'feed.class.php';
require_once 'utils.php';

function updateFeed($Database, $Site)
{
	//This is horrible really. There is a UNIQUE flag set on the GUID column of the database... and, well...
	//I kinda just rely on the insert failing if there's a duplicate because the feed hasn't updated.
	//What I SHOULD do is either check to see if the item exists before inserting it... or better yet,
	//only get the newer items through a smarter HTTP get
	try
	{
		$rss             = Feed::loadRss($Site['source']);
		$itemPrepared    = $Database->prepare('INSERT INTO item(siteId, guid, source, timestamp, contentId) VALUES (?, ?, ?, ?, ?)');
		$contentPrepared = $Database->prepare('INSERT INTO contents(title, body) VALUES (?, ?)');
		$count           = 0;
		foreach ($rss->item as $item)
		{
			try
			{
				//populate the full text search table with the title and body
				$parameters = array();
				$parameters[0] = $item->title;
				if (isset($item->{'content:encoded'}))
					$parameters[1] = $item->{'content:encoded'};
				else
					$parameters[1] = $item->description;
				$Database->beginTransaction();
				$contentPrepared->execute($parameters);
				$contentId = $Database->lastInsertId();
				$Database->commit();

				//add the item details pointing to the new content id
				$parameters    = array();
				$parameters[0] = $Site['id'];
				$parameters[1] = $item->guid;
				$parameters[2] = $item->link;
				$parameters[3] = $item->timestamp;
				$parameters[4] = $contentId;

				$itemPrepared->execute($parameters);
			}
			catch(PDOException $e)
			{
				quit('updateFeed() Database Exception : '.$e->getMessage());
			}
			$count++;
		}
		return $count;
	}
	catch(Exception $e)
	{
		echo('updateFeed() RSS fetch Exception : '.$e->getMessage());
	}
	return 0;
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
		error_log('Updating feed for ['.$row['name'].']');
		$result[$row['name']] = updateFeed($Database, $site);
	}
	return $result;
}

if ((isset($argv[1]) && $argv[1] == 'now') || (isset($_POST['id']) && $_POST['id'] == 'all'))
{
	$db = openDatabase();
	updateSites($db);
}

?>