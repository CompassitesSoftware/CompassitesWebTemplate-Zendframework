<?php

class User_indexControllerTest extends ControllerTestCase
{
	public function testLoginAction()
	{
		$this->dispatch('/user/index/login/');
		$this->assertModule('user');
        $this->assertController('index');
        $this->assertAction('login');
        $this->assertQueryContentContains('dt#username-label label', 'Email:');
        $this->assertQueryContentContains('dt#password-label label', 'Password:');

		$request = $this->getRequest();

		$request->setMethod('POST');
		$request->setPost(array(
					'username' => 'senthilkumar@compassitesinc.com',
					'password' => '123654',
					'submit' => 'Login',
					'return' => '',
		));

		$this->dispatch('/user/index/login/');

		$this->assertTrue( !Zend_Auth::getInstance()->hasIdentity() );

	}


	public function  testLoginRealUser()
	{
		$request = $this->getRequest();
		$request->setMethod('POST')->setPost(
			array(
				'username' => 'l.senthilkumar@compassitesinc.com',
				'password' => '123654',
			)
		);
		$this->dispatch('/user/index/login');
		$this->assertTrue(Zend_Auth::getInstance()->hasIdentity());
	}

}

