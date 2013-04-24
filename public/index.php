<?php
/**
 * @file		index.php
 *
 * @category   	Compassites
 * @package    	FrontController
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

include_once realpath('../globals.php');
if( 'development' == APPLICATION_ENV )
{
	$target = null;
	if( empty($_GET['rewrite']) && 0 !== strpos($_SERVER['REQUEST_URI'], $_SERVER['PHP_SELF']) )
	{
		# Redirect to index if rewrite not enabled
		$target = $_SERVER['PHP_SELF'];
		$params = $_GET;
		unset($params['rewrite']);
		if( !empty($params) )
		{
			$target .= '?' . http_build_query($params);
		}
	}
	else if( isset($_GET['rewrite']) && $_GET['rewrite'] == 2 )
	{
		# Redirect to virtual index if rewrite enabled
		$target = str_replace($_SERVER['PHP_SELF'], dirname($_SERVER['PHP_SELF']), $_SERVER['REQUEST_URI']);
	}

	if( null !== $target )
	{
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: ' . $target);
		exit;
	}
}

/** Use auto loader to boost performance */
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

/** Session into apc */
//require_once 'SessionHandler.php';

/** Zend_Application */
//require_once 'Zend/Application.php'; # not required as auto loader takes care of it

# Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH.'/configs/application.ini');
$application->bootstrap()->run();