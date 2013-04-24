<?php
/**
 * @file		Forgotpassword.php
 *
 * @category    user
 * @package    	user_Form
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Forgotpassword Form class definition
 *
 * @package    	user_Form
 * @class		user_Form_Forgotpassword
 * @see
 */
class user_Form_Forgotpassword extends Zend_Form
{
    public function init()
    {
		# Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'forgotpasswordform');

		# Add a Username element
		$this->addElement('text', 'username',
			array(
				'label'			=> 'Username:',
				'required'		=> true,
				'filters'		=> array('StringTrim'),
				'validators'	=> array('EmailAddress'),
				'size'			=> 25,
				'maxlength'		=> 150,
				'ErrorMessages' => array('Please enter a valid e-mail address.'),
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