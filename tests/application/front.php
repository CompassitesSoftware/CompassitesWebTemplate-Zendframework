<?php
/**
 * @file		front.php
 *
 * @category   	Compassites
 * @package    	UnitTest_FrontController
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: front.php 94 2011-08-09 11:06:02Z ce148 $
 */

/** Use auto loader to boost performance */
//require_once 'Zend/Loader/Autoloader.php';
//Zend_Loader_Autoloader::getInstance();

/** Session into apc */
//require_once 'SessionHandler.php';

/** Zend_Application */
require_once 'Zend/Application.php'; // not required as auto loader takes care of it

# Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH.'/configs/application.ini');
$application->bootstrap()->run();
