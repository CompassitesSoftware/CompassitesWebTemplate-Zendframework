<?php
/**
 * @file		TestHelper.php
 *
 * @category   	Compassites
 * @package		Constants
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: TestHelper.php 790 2012-03-01 04:41:24Z ce163 $
 */

//ini_set('memory_limit', -1);
/** Output buffering */
ob_start();
session_save_path('/tmp/');

/** xhtml validation */
//header('Content-type: text/xml');

/** Define the app name, used mostly for cachekey */
defined('APPLICATION_NAME')
    || define('APPLICATION_NAME', 'COMPASSITES');

/** Define path to application base directory */

defined('APPLICATION_ROOT')
    || define('APPLICATION_ROOT', realpath( dirname(__FILE__) . '/../' ));

/** Define path to application directory */

defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', APPLICATION_ROOT .'/application');

/** Define application environment */
define('APPLICATION_ENV', 'testing');

/** Ensure library is in include_path */
set_include_path(
	implode( PATH_SEPARATOR, array(
			'/var/www/toolkit',
			get_include_path(),
		)
	)
);

require_once 'ControllerTestCase.php';