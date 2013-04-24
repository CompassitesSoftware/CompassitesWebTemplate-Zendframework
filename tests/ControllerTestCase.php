<?php
/**
 * @file		ControllerTestCase.php
 *
 * @category   	Homeconnect
 * @package    	UnitTest
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: ControllerTestCase.php 787 2012-02-28 08:57:12Z ce148 $
 */
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

abstract class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
    public $application;

    public function setUp()
    {
		//require_once 'Zend/Loader/Autoloader.php';
		//Zend_Loader_Autoloader::getInstance();

		/** Session into apc */
		//require_once 'SessionHandler.php';

		/** Zend_Application */
		require_once 'Zend/Application.php'; // not required as auto loader takes care of it

		# Create application, bootstrap, and run
		$this->application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH.'/configs/application.ini');
        $this->bootstrap = array($this, 'appBootstrap');

        parent::setUp();
    }

    public function appBootstrap()
    {
		$this->application->bootstrap();
    }

	public function tearDown()
	{
		$this->resetRequest();
		$this->resetResponse();

		parent::tearDown();
	}
};