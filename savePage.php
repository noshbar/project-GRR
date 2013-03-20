<?php
require_once 'database.php';

/* TODO: make engine options:
   curl : use curl and parse the DOM for SRC elements to retreive too
   wkhtmlpdf : use the wkhtmltopdf binary to save the site to a PDF
   wget : use wget to get the page and all its resources, rewriting links to point locally
*/

$engine      = '';
$wkhtmltopdf = shell_exec('which wkhtmltopdf');
if (!empty($wkhtmltopdf))
{
	require_once 'WkHtmlToPdf.php';
	$engine = 'wkhtml';
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

    if ($err == 0)
    {
        file_put_contents($filename, $content);
    }
    //TODO: parse DOM to get all SRC elements, then get each of the files.
}

$itemId = -1;

if (isset($_POST['itemId']))
	$itemId = $_POST['itemId'];
if (isset($argv[1]))
	$itemId = $argv[1];

if ($itemId == -1)
	die();

$db         = openDatabase('test.db');
$query      = 'SELECT site.name, item.source, item.title FROM site, item WHERE site.id=item.siteId AND item.id = ?';
$parameters = array($itemId);

$prepared = $db->prepare($query);
$prepared->execute($parameters);
$row      = $prepared->fetch(); 

$site  = sanitize($row['name'], true, true);
$title = sanitize($row['title'], true, true);

$filename = dirname(__FILE__).'/saved/'.$site.'/';
mkdir($filename, true);
$filename .= $itemId.'-'.$title;

if ($engine == 'wkhtml')
    $result = savePageWkhtml($row['source'], $filename.'.pdf');
elseif ($engine == 'curl')
    $result = savePageCurl($row['source'], $filename.'.html');
else
    $result = "No download mechanism found (enable wkhtmltopdf, cURL or wget)";

echo $result;
?>