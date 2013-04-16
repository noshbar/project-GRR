<?php

function quit($message)
{
    $result['error'] = $message;
    die(json_encode($result));
}

function openDatabase()
{
	$filename = 'grr.db';
	try
	{
		$existed = file_exists($filename);
		$db = new PDO("sqlite:$filename");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		if (!$existed)
		{
			$tables['tags']        = 'CREATE TABLE tags(id INTEGER PRIMARY KEY, name TEXT UNIQUE, deleted BOOLEAN DEFAULT 0);';
			$tables['taggedItems'] = 'CREATE TABLE taggedItems(id INTEGER PRIMARY KEY, tagId INTEGER, itemId INTEGER);';
			$tables['site']        = 'CREATE TABLE site(id INTEGER PRIMARY KEY, name TEXT UNIQUE, source TEXT, deleted BOOLEAN DEFAULT 0);';
			$tables['contents']    = 'CREATE VIRTUAL TABLE contents USING fts4(title, body);';
			$tables['item']        = 'CREATE TABLE item(id INTEGER PRIMARY KEY, guid TEXT UNIQUE, source TEXT, deleted BOOLEAN DEFAULT 0, siteId INTEGER, timestamp DATETIME, contentId INTEGER NOT NULL, read BOOLEAN DEFAULT 0);';
			foreach ($tables as $table)
			{	
				$result = $db->exec($table);
				if ($result)
				{
					quit('Could not create table: '.$table."\n");
				}
			}
			$db->exec("INSERT INTO tags(name) VALUES('Favourites')");
		}
		return $db;	
	}	
	catch(PDOException $e)
	{
		quit('openDatabase() Exception : '.$e->getMessage());
	}
	return null;
}

//from http://stackoverflow.com/questions/2668854/sanitizing-strings-to-make-them-url-and-filename-safe
function sanitize($string, $force_lowercase = true, $anal = false) 
{
    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
                   "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
                   "â€”", "â€“", ",", "<", ".", ">", "/", "?");
    $clean = trim(str_replace($strip, "", strip_tags($string)));
    $clean = preg_replace('/\s+/', "-", $clean);
    $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "_", $clean) : $clean ;
    return ($force_lowercase) ?
        (function_exists('mb_strtolower')) ?
            mb_strtolower($clean, 'UTF-8') :
            strtolower($clean) :
        $clean;
}

function getSaveName($siteName, $itemId, $itemTitle)
{
	$siteName  = sanitize($siteName, true, true);
	$itemTitle = sanitize($itemTitle, true, true);
	$details   = explode(',', __FILE__);
	$dir       = dirname($details[0]);
	$filename = $dir.'/saved/'.$siteName.'/'.$itemId.'-'.$itemTitle;
	return $filename;
}
?>
