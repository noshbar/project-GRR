<?php
require_once 'utils.php';

/* TODO: make engine options:
   curl : use curl and parse the DOM for SRC elements to retreive too
   wkhtmlpdf : use the wkhtmltopdf binary to save the site to a PDF
   wget : use wget to get the page and all its resources, rewriting links to point locally
*/

$tryCurl  = function_exists('curl_init');
$tryWk    = FALSE;

$wkhtmltopdf = shell_exec('which wkhtmltopdf');
if (!empty($wkhtmltopdf))
{
	require_once 'WkHtmlToPdf.php';
	$tryWk = TRUE;
}

function savePageWkhtml($url, $filename)
{
    $result['result'] = FALSE;

	$pdf = new WkHtmlToPdf;	
	$pdf->addPage($url);
	if (!$pdf->saveAs($filename))
    {
        $result['warning'] = '['.$filename.'] '.$pdf->getError(); //Wk is special as it sometimes reports it has fails, but hasn't, and other times just crashes
        return $result;
    }
    $url = substr($filename, strpos($filename, '/saved/') + 1);
    $urlText = substr(strrchr($filename, '/'), 1);
    $result['message'] = 'Created PDF: <a href="'.$url.'">'.$urlText.'</a>';
    $result['result'] = TRUE;
    return $result;
}

function localizeFile($filename)
{
    //get all the resources needed to show the page
    //change their urls to point locally
}

//from http://nadeausoftware.com/articles/2007/06/php_tip_how_get_web_page_using_curl
function savePageCurl($url, $filename)
{
    $result['result'] = FALSE;

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
    {
        $result['error'] = "Could not save page using cURL: $err";
        return $result;
    }

    file_put_contents($filename, $content);
    localizeFile($filename);
    $url = substr($filename, strpos($filename, '/saved/') + 1);
    $urlText = substr(strrchr($filename, '/'), 1);
    $result['message'] = 'Saved HTML: <a href="'.$url.'">'.$urlText.'</a>';
    $result['result'] = TRUE;
    return $result;
}

function savePageFetch($url, $filename)
{
    $result['result'] = FALSE;

    $content = file_get_contents($url);
    if ($content === FALSE)
    {
        $message = error_get_last();
        $message = $message['message'];
        $result['error'] = "Could not save page using get_contents ($message)";
        return $result;
    }

    file_put_contents($filename, $content);
    localizeFile($filename);
    $url = substr($filename, strpos($filename, '/saved/') + 1);
    $urlText = substr(strrchr($filename, '/'), 1);
    $result['message'] = 'Saved HTML: <a href="'.$url.'">'.$urlText.'</a>';
    $result['result'] = TRUE;
    return $result;
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

$source = $row['source'];

$result['result'] = FALSE;

if ($result['result'] === FALSE && $tryWk)
    $result = savePageWkhtml($source, $filename.'.pdf');

if ($result['result'] === FALSE && $tryCurl)
    $result = savePageCurl($source, $filename.'.html');

if ($result['result'] === FALSE)
    $result = savePageFetch($source, $filename.'.html');

if ($result['result'] === FALSE)
    quit("Could not save $filename");

echo(json_encode($result));
?>