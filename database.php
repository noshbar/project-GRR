<?php

function quit($message)
{
    $result['error'] = $message;
    die(json_encode($result));
}

function openDatabase($Filename)
{
	try
	{
		$existed = file_exists($Filename);
		$db = new PDO("sqlite:$Filename");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		if (!$existed)
		{
			$tables['site']     = 'CREATE TABLE site(id INTEGER PRIMARY KEY, name TEXT UNIQUE, source TEXT, deleted BOOLEAN DEFAULT 0);';
			$tables['contents'] = 'CREATE VIRTUAL TABLE contents USING fts4(title, body);';
			$tables['item']     = 'CREATE TABLE item(id INTEGER PRIMARY KEY, guid TEXT UNIQUE, source TEXT, deleted BOOLEAN DEFAULT 0, siteId INTEGER, timestamp DATETIME, contentId INTEGER NOT NULL, read BOOLEAN DEFAULT 0);';
			foreach ($tables as $table)
			{	
				$result = $db->exec($table);
				if ($result)
				{
					quit('Could not create table: '.$table."\n");
				}
			}
		}
		return $db;	
	}	
	catch(PDOException $e)
	{
		quit('openDatabase() Exception : '.$e->getMessage());
	}
	return null;
}

?>
