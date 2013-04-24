<?php
/**
 * @file		AdminControllerTest.php
 *
 * @category   	Homeconnect
 * @package    	UnitTest
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: IndexControllerTest.php 797 2012-03-02 12:59:36Z ce163 $
 */
class Admin_IndexControllerTest extends ControllerTestCase
{
	public function Login()
	{
		# admin is display only login user
		/*$userTest = new User_indexControllerTest();
		$userTest->testLoginRealUser();
		*/# End
	}

    public function testIndexAction()
    {
        $this->dispatch('/admin/order/');

        $this->assertController('order');
        $this->assertAction('index');
    }

	public function testOrderAction()
    {
   // $_COOKIE['PHPSESSID'] = 'm5ugl2odavpqe3dvn83367bge7';
        $this->dispatch('/admin/order/');

        $this->assertController('order');
        $this->assertAction('index');
// 		$this->assertQueryContentContains('dt#center_id-label label', 'Center:');
    }

	private function ZendAuthPlugin()
	{
		$_COOKIE['PHPSESSID'] = 'm5ugl2odavpqe3dvn83367bge7';
		$auth 		= Zend_Auth::getInstance();

	}
};