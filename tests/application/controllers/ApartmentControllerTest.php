<?php
/**
 * @file		ApartmentControllerTest.php
 *
 * @category   	Homeconnect
 * @package    	UnitTest
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $
 */
class ApartmentControllerTest extends ControllerTestCase
{
    public function testRegisterAction()
    {
        $this->dispatch('/apartment/register/');
        $this->assertController('apartment');
        $this->assertAction('register');
        #Check breadcum
        $this->assertQueryContentContains('#breadcrumb li a', 'Apartment');
		$this->assertQueryContentContains('#breadcrumb li', 'Register');
    }
    public function testRegisterProcess ()
    {
		$request = $this->getRequest();
		$request->setMethod('POST')->setPost(
			array(
				'apartmentName' => 'compassitesinc',
				'addressLine1' => '123654',
				'pincode'		=> '560012',
				'cityId'   		=> '2',
				'stateId'   		=> '18',
				'countryId'   		=> '2',
				'contactFirstName' => 'compass',
				'contactDesignation' => 'owner',
				'emailAddress'	=> 'l.senthilkumar@compassitesinc.com',
				'contactNumber' => '78963214563',
				'remarks'		=> 'test'
			)
		);
		 $this->dispatch('/apartment/register/');

// 		print_r($this->getResponse());
		$this->assertQueryContentContains('#apartmentregister','Thank you for your Interest with HomeConnect. Our Marketing team will be contacting you shorty');
	}
};