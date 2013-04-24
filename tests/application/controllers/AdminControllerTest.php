<?php
/**
 * @file		AdminControllerTest.php
 *
 * @category   	Homeconnect
 * @package    	UnitTest
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: AdminControllerTest.php 94 2011-08-09 11:06:02Z ce148 $
 */

class AdminControllerTest extends ControllerTestCase
{
	public function Login()
	{
		$this->LoginRealUser();
	}

    public function testIndexAction()
    {
        $this->dispatch('/');

        $this->assertController('index');
        $this->assertAction('index');
    }

    public function testLoginAction()
    {
        $this->dispatch('/user/login');

        $this->assertController('user');
        $this->assertAction('login');
        $this->assertXpath('//form');

	}

    public function testDashboardAction()
    {
        $this->dispatch('/dashboard/index');

        $this->assertController('dashboard');
        $this->assertAction('index');
    }

	public function testReportAction()
    {
   // $_COOKIE['PHPSESSID'] = 'm5ugl2odavpqe3dvn83367bge7';
        $this->dispatch('/report/productivity/');

        $this->assertController('report');
        $this->assertAction('productivity');
		$this->assertQueryContentContains('dt#center_id-label label', 'Center:');
    }

	public function LoggingInShouldBeOk()
	{
		// $_COOKIE['PHPSESSID'] = 'm5ugl2odavpqe3dvn83367bge7';
		$this->dispatch('/user/login/');
        $this->assertController('user');
        $this->assertAction('login');
        $this->assertQueryContentContains('dt#username-label label', 'Username');

			//$csrf = $this->_getLoginFormCSRF();
			//$this->resetResponse();
			$request = $this->getRequest();

			$request->setMethod('POST');
			$request->setPost(array(
						'username' => 'salil',
						'password' => 'lilas321',
						//'csrf' => $csrf,
						'submit' => 'Login',
						'return' => '',
			));

			$this->dispatch('/user/login/');

			// print_r($this->getResponse());exit;
			//$this->assertTrue( Zend_Auth::getInstance()->hasIdentity() );

			//$this->assertRedirectTo('/dashboard/');
	}


	private function LoginRealUser()
	{
		$request = $this->getRequest();
		$request->setMethod('POST')->setPost(
			array(
				'username' => 'salil',
				'password' => 'salil123',
			)
		);
		$this->dispatch('/user/login');
		$this->assertRedirect;
		$this->assertTrue(Zend_Auth::getInstance()->hasIdentity());
	}

	private function ZendAuthPlugin()
	{
		$_COOKIE['PHPSESSID'] = 'm5ugl2odavpqe3dvn83367bge7';
		$auth 		= Zend_Auth::getInstance();

	}
};