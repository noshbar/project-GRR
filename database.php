<?php

function openDatabase($Filename)
{
	try
	{
		$existed = file_exists($Filename);
		$db = new PDO("sqlite:$Filename");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		if (!$existed)
		{
			$tables['site'] = 'CREATE TABLE site(id INTEGER PRIMARY KEY, name TEXT UNIQUE, source TEXT, deleted BOOLEAN DEFAULT 0);';
			$tables['item'] = 'CREATE TABLE item(id INTEGER PRIMARY KEY, guid TEXT UNIQUE, source TEXT, deleted BOOLEAN DEFAULT 0, siteId INTEGER, title TEXT, timestamp DATETIME, contents TEXT, read BOOLEAN DEFAULT 0);';
			foreach ($tables as $table)
			{	
				$result = $db->exec($table);
				if ($result)
				{
					die('Could not create table: '.$table."\n");
				}
			}
		}
		return $db;	
	}	
	catch(PDOException $e)
	{
		die('openDatabase() Exception : '.$e->getMessage());
	}
	return null;
}

?>
