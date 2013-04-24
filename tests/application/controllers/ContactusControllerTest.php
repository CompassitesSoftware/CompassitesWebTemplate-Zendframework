<?php
/**
 * @file		ContactusControllerTest.php
 *
 * @category   	Homeconnect
 * @package    	UnitTest
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $
 */
class ContactusControllerTest extends ControllerTestCase
{
    public function testIndexAction()
    {
        $this->dispatch('/contactus/');
        $this->assertController('contactus');
        $this->assertAction('index');
        $this->assertQueryContentContains('h2','Bangalore Center');
        $this->assertQueryContentContains('h2','Pune Center');
    }
};