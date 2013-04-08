<?php
require_once 'database.php';

/* TODO: make engine options:
   curl : use curl and parse the DOM for SRC elements to retreive too
   wkhtmlpdf : use the wkhtmltopdf binary to save the site to a PDF
   wget : use wget to get the page and all its resources, rewriting links to point locally
*/

$wkhtmltopdf = shell_exec('which wkhtmltopdf');
if (!empty($wkhtmltopdf))
{
	require_once 'WkHtmlToPdf.php';
	$engine = 'wkhtml';
}
elseif (function_exists('curl_init'))
{
    $engine = 'curl';
}
else
{
    $engine = 'fetch';
}

function savePageWkhtml($url, $filename)
{
	$pdf = new WkHtmlToPdf;	
	$pdf->addPage($url);
	if (!$pdf->saveAs($filename))
        return '['.$filename.'] '.$pdf->getError();
    $url = substr($filename, strpos($filename, '/saved/') + 1);
    $urlText = substr(strrchr($filename, '/'), 1);
    return 'Created PDF: <a href="'.$url.'">'.$urlText.'</a>';
}

function localizeFile($filename)
{
    //get all the resources needed to show the page
    //change their urls to point locally
}

//from http://nadeausoftware.com/articles/2007/06/php_tip_how_get_web_page_using_curl
function savePageCurl($url, $filename)
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "spider", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    if ($err != 0)
        quit("Could not save page using cURL: $err");

    file_put_contents($filename, $content);
    localizeFile($filename);
    $url = substr($filename, strpos($filename, '/saved/') + 1);
    $urlText = substr(strrchr($filename, '/'), 1);
    return 'Saved HTML: <a href="'.$url.'">'.$urlText.'</a>';
}

function savePageFetch($url, $filename)
{
    $content = file_get_contents($url);
    if ($content === FALSE)
    {
        $message = error_get_last();
        $message = $message['message'];
        quit("Could not save page using get_contents ($message)");
    }

    file_put_contents($filename, $content);
    localizeFile($filename);
    $url = substr($filename, strpos($filename, '/saved/') + 1);
    $urlText = substr(strrchr($filename, '/'), 1);
    return 'Saved HTML: <a href="'.$url.'">'.$urlText.'</a>';
}

$itemId = -1;

if (isset($_POST['itemId']))
	$itemId = $_POST['itemId'];
if (isset($argv[1]))
	$itemId = $argv[1];

if ($itemId == -1)
	die();

$db         = openDatabase('test.db');
$query      = 'SELECT site.name, item.source, contents.title FROM site, item, contents WHERE site.id=item.siteId AND contents.docid=item.id AND item.id = ?';
$parameters = array($itemId);

$prepared = $db->prepare($query);
$prepared->execute($parameters);
$row      = $prepared->fetch(); 

$filename = getSaveName($row['name'], $itemId, $row['title']);
$folder   = dirname($filename.'.pdf');
if (!file_exists($folder) && !mkdir($folder, 0777, true))
{
    $message = error_get_last();
    $message = $message['message'];
    quit("Could not create folder $folder ($message)");
}

$source = explode('#', $row['source']);
$source = $source[0];

if ($engine == 'wkhtml')
    $result['message'] = savePageWkhtml($source, $filename.'.pdf');
elseif ($engine == 'curl')
    $result['message'] = savePageCurl($source, $filename.'.html');
else
    $result['message'] = savePageFetch($source, $filename.'.html');

echo(json_encode($result));
?>