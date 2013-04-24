<?php
/**
 * @file		Changepassword.php
 *
 * @category    user
 * @package    	user_Form
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2012 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Changepassword Form class definition
 *
 * @package    	user_Form
 * @class		user_Form_Changepassword
 * @see
 */

class user_Form_Changepassword extends Zend_Form
{
    public function init()
    {
       # Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'changepasswordform');

		# Add current password element
		$this->addElement('password', 'user_password',
			array(
				'label'		=> 'Current Password:',
				'required'	=> true,
				'filters'	=> array('StringTrim'),
				'validators'=> array(
									 array('NotEmpty', true, array('messages' => "Required field cannot be left blank",)),
									 array('stringLength', true, array(6, 15, 'messages' => 'Password must be at least 6 characters long')),
									 array('Regex', false, array('/^[a-zA-Z0-9_$#@&\-]*$/', 'messages' => 'Please enter a valid Password')),
								),
				'size'		=> 25,
				'maxlength'	=> 15,
			)
		);

		# Add a new password element
		$this->addElement('password', 'password',
			array(
				'label'		=> 'New Password:',
				'required'	=> true,
				'filters'	=> array('StringTrim'),
				'validators'=> array(
									 array('NotEmpty', true, array('messages' => "Required field cannot be left blank",)),
									 array('stringLength', true, array(6, 15, 'messages' => 'Password must be at least 6 characters long')),
									 array('Regex', false, array('/^[a-zA-Z0-9_$#@&\-]*$/', 'messages' => 'Please enter a valid Password')),
								),
				'size'		=> 25,
				'maxlength'	=> 15,
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
									array('identical', true, array('token' => 'password','messages' => "Passwords do not match",)),
								),
				'size'		=> 25,
				'maxlength'	=> 15,
			)
		);

		# Add the submit button
		$this->addElement('submit', 'submit',
		    array(
		    	'ignore'	=> true,
				'label'		=> 'Submit',
		    )
		);
    }
};