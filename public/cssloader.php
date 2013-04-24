<?php
/**
 * @file		index.php
 *
 * @version		SVN: $Id: $
 *
 * @package		Controller
 * @author     	Salil Kothadia <salil.kothadia@ymail.com>
 * @license    	GPL, LGPL
 */

/**
 * CSS file loader
 *
 * Helps reduce HTTP Requests per page by combining the css files and also minifys them.
 * RewriteRule	^/cssloader/(.*)\.css$	/cssloader/index.php?files=$1 [L]
 *
 * @see
 * @todo
 */
# file type header
header('Content-Type: text/css');

include realpath('../globals.php');
ini_set('zlib.output_compression', 4096);

$files 		= isset($_GET['files']) ? $_GET['files'] : '';
if(empty($files))
{
	exit;
}
$fileArray 	= explode('-', $files);
if( !in_array('Debug', $fileArray) )
{
	if(!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']))
	{
		$ModSince = preg_replace( '/;.*$/', '',$_SERVER['HTTP_IF_MODIFIED_SINCE']);

		if($ModSince !== '')
		{
			header('Connection: Keep-Alive');
			header('Keep-Alive: timeout=15, max=100');
			header('HTTP/1.0 304 Not Modified');
			exit;
		}
	}
	# Client side caching
	header('Last-Modified: '. gmdate('D, d M Y H:i:s') . ' GMT');
	header('Expires: Tue, 19 Jan 2038 01:00:00 GMT');
	header('Cache-Control: public, cache');
	header('Pragma: public, cache');
}

# set the css include path
$path		= '/public/css/';
if(in_array('custom', $fileArray))
{
	$path	= '/public/';
}

# Server Cache
$frontendOptions = array(
    'lifetime' => 86400, // cache lifetime of one day
    'automatic_serialization' => false
);
$backendOptions = array();

$cacheKey =  'cssLoader.'.$files;
if( extension_loaded('apc') && $content = apc_fetch($cacheKey) )
{
	echo $content;
	exit;
}

ob_start();
foreach($fileArray as $file)
{
	if(!in_array($file, array('Debug', 'custom', 'cssloader')) && !is_numeric($file[0]))
	{
		loadFile(basename($file), $path);
	}
}
$content = ob_get_contents();
ob_end_clean();

if(!in_array('Debug', $fileArray))
{
	$content = preg_replace(array('@/\*(.*?)\*/@s','@ +@'), array('',' '), $content);
	$content = str_replace( array("\r","\n","\t",'}',': ',', ','; ','{ ',' {'), array('','','',"}\n",':',',',';','{','{'), $content );

	# store data into cache
	if( extension_loaded('apc') )
	{
		apc_store($cacheKey, $content, $frontendOptions['lifetime']);
	}
}
echo $content;

/**
*	Loads CSS files
*
*	@param file name
*/
function loadFile($name, $path='/public/css/')
{
	# To avoid duplicate files
	static $names = array();
    $name = basename($name, '.css');
    $name = str_replace('_', '/', $name);
	if(!isset($names[$name]))
	{
		$names[$name] = true;
		include APPLICATION_ROOT . "{$path}{$name}.css";
	}
}

final class CSS3
{
	static function borderRadius($radius='2px')
	{
		echo "-moz-border-radius: {$radius};
		-webkit-border-radius: {$radius};
		border-radius: {$radius};
		";
	}

	static function gradient($start='#fff', $end='#f1f1f1')
	{
		echo "background: {$end};
		background: -webkit-linear-gradient(top, {$start}, {$end});
		background: -moz-linear-gradient(top, {$start}, {$end});
		background: linear-gradient(top, {$start}, {$end});
		";
	}

	static function boxShadow($radius='4px', $colour='#eee', $insetoutset=false)
	{
		$inset = '';
		if($insetoutset)
		{
			$inset = 'inset';
		}

		echo "-moz-box-shadow: {$inset} 0 0 {$radius} {$radius} {$colour};
		-webkit-box-shadow: {$inset} 0 0 {$radius} {$radius} {$colour};
		box-shadow: {$inset} 0 0 {$radius} {$radius} {$colour};

		border: 1px solid {$colour};
		";
	}
};