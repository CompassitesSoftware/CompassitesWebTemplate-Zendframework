<?php
/**
 * @file		Activate.php
 *
 * @category    user
 * @package    	user_Form
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: User.php 148 2011-11-14 05:02:46Z jisha $
 */

/**
 * @brief 		Activate Form class definition
 *
 * @package    	user_Form
 * @class		user_Form_Activate
 * @see
 */
class User_Form_Activate extends Zend_Form
{
    public function init()
    {
    	$fc 		= Zend_Controller_Front::getInstance();
        $request	= $fc->getRequest();
        $controller	= $request->getControllerName();
        $action		= $request->getActionName();


		# Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'activateform');

		# Add a Username element
		$this->addElement('text', 'username',
			array(
				'label'		=> 'Email:',
				'size'		=> 30,
			)
		);

		if( ('referfriend' == $controller && 'activate' == $action) ||
			('index' == $controller && 'registeruser' == $action) ||
			'apartment-user-activate' == $action
		)
		{
					# Add a password element
					$this->addElement('password', 'password',
						array(
							'label'		=> 'Password:',
							'required'	=> true,
							'filters'	=> array('StringTrim'),
							'validators'=> array(
												array('NotEmpty', true, array('messages' => "Required field cannot be left blank",)),
												array('stringLength', true, array(6, 50, 'messages' => 'Password must be at least 6 characters long')),
												array('Regex', false, array('/^[a-zA-Z0-9_!$#@&\-]*$/', 'messages' => 'Please enter a valid Password')),
											),
							'size'		=> 25,
							'maxlength'	=> 50,
						)
					);

					# Add a confirm password element
					$this->addElement('password', 'confirm_password',
						array(
							'label'		=> 'Confirm Password:',
							'required'	=> true,
							'ignore'	=> true,
							'filters'	=> array('StringTrim'),
							'validators'=> array(
												array('NotEmpty', true, array('messages' => "Required field cannot be left blank",)),
												array('identical', true, array('token' => 'password')),
											),
							'size'		=> 25,
							'maxlength'	=> 50,
						)
					);
		}
		if('index' == $controller && 'registeruser' == $action)
		{
			$roleModel 	= new User_Model_Role();
			$roleDetails= $roleModel->getAll();
			$roleArray 	= array(''=>'Select');
			foreach($roleDetails as $roleDetail)
			{
				$roleArray[ $roleDetail['roleId'] ] = $roleDetail['title'];
			}

			$this->addElement('select', 'roleId',
						array(
							'label'		=> 'Role:',
							'required'	=> true,
							'multiOptions' 	=>  $roleArray,
							'ErrorMessages' => array('Please select Role.'),
						)
					);

		}
		# Add a Gender/Suffix element
		$optionsSuffix			= array();
		$optionsSuffix['mr'] 	= 'Mr';
		$optionsSuffix['mrs'] 	= 'Mrs';
		$optionsSuffix['ms'] 	= 'Ms';

        $this->addElement('select', 'suffix',
			array(
				'label'		=> 'Suffix:',
				'required'	=>  true,
				'multiOptions'=> $optionsSuffix,
			)
		);


		# Add a first Name element
		$this->addElement('text', 'firstName',
			array(
				'label'		=> 'First Name:',
				'required'	=> true,
				'filters'	=> array('StringTrim'),
				'validators'=> array(
									 array('NotEmpty', true, array('messages' => "First name is required and can't be empty",)),
									 array('stringLength', true, array(1, 100)),
									 array('alpha', true, array('allowWhiteSpace' => true, 'messages' => 'Please enter a valid First Name')),
									 ),
				'size'		=> 30,
				'maxlength'	=> 100,
			)
		);

		// Add a last Name element
		$this->addElement('text', 'lastName',
			array(
				'label'		=> 'Last Name:',
				'required'	=> false,
				'filters'	=> array('StringTrim'),
				'validators'=> array(
									array('stringLength', true, array(1, 100)),
									array('alpha', true, array('allowWhiteSpace' => true, 'messages' => 'Please enter a valid Last Name')),
								),
				'size'		=> 30,
				'maxlength'	=> 100,
			)
		);

		// Add the Contact No element
        $this->addElement('text', 'landLine',
			array(
				'label'			=> 'Contact No:',
				'required'		=> false,
				'filters'		=> array('StringTrim'),
				'validators'	=> array(
									array('regex', false, array('/^[+]{0,1}[0-9 ]*$/', 'messages' => 'Enter a valid Contact Number')),
								 	array('stringLength',true,array(7,15)),
					           	),
				'size'			=> 30,
				'maxlength'		=> 15,
			)
		);

		// Add the Mobile No element
        $this->addElement('text', 'mobile',
			array(
				'label'			=> 'Mobile No:',
				'required'		=> true,
				'filters'		=> array('StringTrim'),
				'validators'	=> array(
										array('regex', false, array('/^[+]{0,1}[0-9 ]*$/', 'messages' => 'Enter a valid Mobile Number')),
										array('stringLength',true,array(10,13)),
					           	),
				'size'			=> 30,
				'maxlength'		=> 13,
			)
		);

		// Add the Address line 1 element
        $this->addElement('text', 'addressLine1',
			array(
				'label'		=> 'Address Line 1:',
				'required'	=> true,
				'filters'	=> array('StringTrim'),
				'validators'=> array(
									array('NotEmpty', true, array('messages' => "Address line 1 is required and can't be empty",)),
									array('stringLength', true, array(1, 150)),
								),
				'size'		=> 30,
				'maxlength'	=> 150,
			)
		);
		// Add the Address line 2 element
        $this->addElement('text', 'addressLine2',
			array(
				'label'		=> 'Address Line 2:',
				'required'	=> false,
				'filters'	=> array('StringTrim'),
				'validators'=> array(
									array('stringLength', true, array(1, 150)),
								),
				'size'		=> 30,
				'maxlength'	=> 150,
			)
		);

		// Add the Address Area element
        $this->addElement('text', 'area',
			array(
				'label'		=> 'Area:',
				'required'	=> false,
				'filters'	=> array('StringTrim'),
				'validators'=> array(
									array('alnum', true, array('allowWhiteSpace' => true, 'messages' => 'Please enter a valid Area')),
									array('stringLength', true, array(3, 150)),
								),
				'size'		=> 30,
				'maxlength'	=> 150,
			)
		);


		// Add a zip code element
		$this->addElement('text', 'pincode',
			array(
				'label'			=> 'Pincode:',
				'required'		=> true,
				'filters'		=> array('StringTrim'),
				'validators'	=>  array(
											array('stringLength',true,array(6,7)),
											array('regex', false, 'pattern' => array('/^[0-9 ]+$/') ),
										),
				'size'			=> 30,
				'maxlength'		=> 7,
				'ErrorMessages' => array('Please enter a Valid pincode.'),
			)
		);

		if( $action != 'myaccount' &&  $action != 'apartment-user-activate')
		{
			// Add the submit button
			$this->addElement('submit', 'submit',
				array(
					'ignore'	=> true,
					'label'		=> 'Submit',
				)
			);

			// Add the reset button
			$this->addElement('reset', 'reset',
				array(
					'ignore'	=> true,
					'label'		=> 'Reset',
				)
			);
		}
		else
		{
				// Add a userId element
			$this->addElement('hidden', 'userId',
				array(
					'Decorators' => array('ViewHelper'),
				)
			);

		    // Add the update button
			$this->addElement('submit', 'submit',
				array(
					'ignore'	=> true,
					'label'		=> 'Update',
				)
			);

			// Add the cancel button
			$this->addElement('button', 'cancel',
				array(
					'ignore'	=> true,
					'label'		=> 'Cancel',
				)
			);
		}

    }

    public function setUsernameValue($emailValue, $firstName = '', $lastName = '')
    {
    	$this->username->setValue($emailValue);
    	$this->username->setAttrib('readonly','yes');
    	$this->firstName->setValue($firstName);
		$this->lastName->setValue($lastName);
    }

    public function setMyAccountDetails($data)
    {

		$cityModel  	= new Homeconnect_Model_City();
		$stateModel  	= new Homeconnect_Model_State();
		$countryModel  	= new Homeconnect_Model_Country();

		// if no info display as others
		if(empty($data['cityId'])) {$data['cityId'] = 1;}
		if(empty($data['stateId'])) {$data['stateId'] = 1;}
		if(empty($data['countryId'])) {$data['countryId'] = 1;}

		$city			= $cityModel->get($data['cityId'],true);
		$state			= $stateModel->get($data['stateId'],true);
		$country		= $countryModel->get($data['countryId'],true);

		//$this->openid->setValue($data['openid']);
		$this->username->setValue($data['username']);
		$this->suffix->setValue($data['suffix']);
		$this->firstName->setValue($data['firstName']);
		$this->lastName->setValue($data['lastName']);
		$this->landLine->setValue($data['landLine']);
		$this->mobile->setValue($data['mobile']);
		$this->addressLine1->setValue($data['addressLine1']);
		$this->addressLine2->setValue($data['addressLine2']);
		$this->area->setValue($data['area']);
		$this->cityId->setValue($city['cityId']);
		$this->stateId->setValue($state['stateId']);
		$this->countryId->setValue($country['countryId']);
		$this->pincode->setValue($data['pincode']);

		$this->userId->setValue($data['userId']);

    	$this->username->setAttrib('readonly','yes');

    }

};