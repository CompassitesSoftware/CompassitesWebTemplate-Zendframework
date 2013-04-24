<?php
/**
 * @file		User.php
 *
 * @category    user
 * @package    	user_Form
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2012 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $
 */

/**
 * @brief 		User Form class definition
 *
 * @package    	user_Form
 * @class		user_Form_User
 * @see
 */
class user_Form_User extends Zend_Form
{
    public function init()
    {
		# Set the method for the display form to POST
		$this->setMethod('post');


		# Add a Username element
		$this->addElement('text', 'username',
			array(
				'label'			=> 'Email:',
				'required'		=> true,
				'filters'		=> array('StringTrim'),
				'validators'	=> array('EmailAddress'),
				'size'			=> 25,
				'maxlength'		=> 150,
				'ErrorMessages' => array('Please enter a valid e-mail address.'),
			)
		);

		# Add a password element
		$this->addElement('password', 'password',
			array(
				'label'		=> 'Password:',
				'required'	=> true,
				'filters'	=> array('StringTrim'),
				'validators'=> array(
									 array('NotEmpty', true, array('messages' => "Required field cannot be left blank",)),
									 array('stringLength', true, array(6, 50, 'messages' => 'Password must be at least 6 characters long')),
									 array('Regex', false, array('/^[a-zA-Z0-9_$#@&\-]*$/', 'messages' => 'Please enter a valid Password')),
								),
				'size'		=> 25,
				'maxlength'	=> 50,
			)
		);

		# Add a password element
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

		# Add terms and conditions
		$helper		= new Zend_view();
		$termsUrl   = $helper->url( array('controller'=>'index', 'action'=>'terms'),'default', true);
		$statement	= "I agreed to the <a href='{$termsUrl}' onclick=\"return !window.open('{$termsUrl}','Terms of Use', 'width=500, height=400, left=20, top=20, scrollbars=yes')\" target='_blank'>terms of use</a>";
		$this->addElement('checkbox', 'terms',
							array(
									'description'	=>$statement,
									'decorators'	=> array( 'ViewHelper',
															array('Description', array('escape' => false, 'tag' => false)),
															array('HtmlTag', array('tag' => 'dd','id'=>'terms-element')),
															array('Label', array('tag' => 'dt')),
															),
									'required'		=>true,
									'uncheckedValue'=> '',
									'checkedValue' => 'I Agree',
									'validators' 	=> array(
															array('NotEmpty', true),),
									'ErrorMessages' => array('You must agree to the terms.'),
								)
							);

		# Add the submit button
		$this->addElement('submit', 'submit',
		    array(
		    	'ignore'	=> true,
				'label'		=> 'Submit',
		    )
		);

		# Add the cancel button
		$this->addElement('reset', 'register_close',
		    array(
				'ignore'	=> true,
				'label'		=> 'Cancel',
		    )
		);
    }
};