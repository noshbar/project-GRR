<?php

require_once 'utils.php';
require_once 'addSite.php';
require_once 'update.php';

function importSection($Database, $Section)
{
	foreach($Section->children() as $item)
	{
		if ($item->getName() == 'outline')
		{
			$site['name']    = $item['text'];
			$site['source']  = $item['xmlUrl'];
			echo "Trying to add [".$site['name']."]\n";
			addSite($Database, $site);
		}
	}
}

$db  = openDatabase();
$xml = simplexml_load_file($argv[1]);

foreach($xml->children() as $section)
{
	if ($section->getName() == 'body')
	{
		importSection($db, $section);
	}
}

echo "Updating site feeds...\n";
updateSites($db);
echo "Done\n";

?>