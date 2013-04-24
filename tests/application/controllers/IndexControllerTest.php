<?php
/**
 * @file		IndexControllerTest.php
 *
 * @category   	Homeconnect
 * @package    	UnitTest
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: IndexControllerTest.php 790 2012-03-01 04:41:24Z ce163 $
 */
class IndexControllerTest extends ControllerTestCase
{
    public function testIndexAction()
    {
        $this->dispatch('/');
        $this->assertController('index');
        $this->assertAction('index');
    }

    public function testErrorURL()
    {
        $this->dispatch('foo');
        $this->assertController('error');
        $this->assertAction('error');
    }
};