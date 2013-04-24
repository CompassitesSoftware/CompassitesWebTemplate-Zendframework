<?php

require_once 'PHPUnit/Framework/TestCase.php';

class UserControllerTest extends ControllerTestCase
{

	public function testIndexAction()
		{
			$this->dispatch('/admin');
			$this->assertController('user');
			$this->assertAction('index');
		}
}

