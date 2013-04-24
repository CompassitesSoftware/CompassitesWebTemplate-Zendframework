<?php
/**
 * @file		Login.php
 *
 * @category    user
 * @package    	user_Form
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Login Form class defination
 *
 * @package    	user_Form
 * @class		user_Form_Login
 * @see
 */
class user_Form_Login extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions  */
        
		# Add terms and conditions
		$helper		= new Zend_view();
		
		# Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAction($helper->url( array('module' =>'user','controller'=>'index', 'action'=>'login')));

		# Add an Username element
		$this->addElement('text', 'username',
			array(
				'label'		=> 'Username:',
				'required'	=> true,
				'filters'	=> array('StringTrim', 'StringToLower'),
				//'validators'=> array('EmailAddress'),
				'size'		=> 25,
				'maxlength'	=> 150,
			)
		);

        # Add the password element
        $this->addElement('password', 'password',
			array(
				'label'		=> 'Password:',
				'required'	=> true,
				'validators'=> array(
									array('stringLength', true, array(6, 50)),
								),
				'size'		=> 25,
				'maxlength'	=> 50,
			)
		);

		# Add the submit button
		$this->addElement('submit', 'submit',
			array(
				'ignore'	=> true,
				'label'		=> 'Login',
			)
		);
    }
};