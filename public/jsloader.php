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
 * JavaScript file loader
 *
 * Helps reduce HTTP Requests per page by combining the js files and also minifys them.
 * RewriteRule	^/jsloader/(.*)\.js$	/.../jsloader/index.php?files=$1 [L]
 * @see
 * @todo
 */
# file type header
header('Content-Type: text/javascript');

include realpath('../globals.php');
//include 'Zend/Cache/Backend/Apc.php';
include 'Minify/JSMin.php';
ini_set('zlib.output_compression', 4096);

$files 	= isset($_GET['files']) ? $_GET['files'] : '';
if( empty($files) )
{
	# for urls without ?
	$files = explode('files=', $_SERVER['PATH_INFO'] );
	unset($files[0]);
	if( empty($files[1]) )
	{
		exit;
	}
	$files = $files[1];
}
$fileArray = explode('-', $files);

if(!in_array('Debug', $fileArray))
{
	if(!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']))
	{
		$modifiedSince = preg_replace( '/;.*$/', '',$_SERVER['HTTP_IF_MODIFIED_SINCE']);

		if($modifiedSince !== '')
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

# set the js include path
$path	= '/public/js/';
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

# getting a Zend_Cache_Core object
//$cache = new Zend_Cache_Backend_Apc($frontendOptions);

$cacheKey =  'jsLoader.'.$files;
//if( $content = $cache->load($cacheKey) )
if( $content = apc_fetch($cacheKey) )
{
	echo $content;
	exit;
}

ob_start();
foreach($fileArray as $file)
{
	if($file !== 'Debug' && $file !== 'lib' && !is_numeric($file[0]))
	{
		loadFile(basename($file), $path);
	}
}
$content = ob_get_contents();
ob_end_clean();

if(!in_array('Debug', $fileArray))
{
	//$content = preg_replace(array('@/\*(.*?)\*/@s','@//.*?\n@','@ +@'),array('','',' '),$content);
	//$content = str_replace(array("\r","\n","\t"),'',$content);
	$content = JSMin::minify($content);

	# store data into cache
	//$cache->save($content, $cacheKey);
	apc_store($cacheKey, $content, $frontendOptions['lifetime']);
}
echo $content;

/**
*	Loads js file
*
*	@param file name
*/
function loadFile($name, $path='/public/js/')
{
	# To avoid duplicate files
	static $names = array();
	$name = basename($name, '.js');
    $name = str_replace('_', '/', $name);
	if(!isset($names[$name]))
	{
		$names[$name] = true;
		include APPLICATION_ROOT . "{$path}{$name}.js";
	}
}