<?php
/**
 * @file		Subscriber.php
 *
 * @category    Compassites Subscriber
 * @package    	Compassites_Form
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */
/**
 * @brief 		Refer friend class definition
 *
 * @package    	Compassites_Form
 * @class		Compassites_Form_Subscriber
 * @see
 */
class Compassites_Form_Subscriber extends Zend_Form
{
	protected $elementDecorators = array(
			'ViewHelper',
			'Errors',
			'Label',
		);

    protected $elementDecoratorsForm = array(
			'FormElements',
			array(),
			'Form',
		);

	public function init()
    {
		$this->setMethod('post');
		$this->setAttrib('id', 'enrollForm');

		// Add an element EmailAddress
		$this->addElement('Text', "email",
			array(
				'required'		=> true,
				'placeholder'	=> 'your email',
				'filters'		=> array('StringTrim'),
				'validators'	=> array('EmailAddress'),
				'size'			=> 30,
				'maxlength'		=> 150,
				'ErrorMessages' => array('Please enter a valid Email Address.'),
				'decorators' 	=> $this->elementDecorators,
				'class'			=> 'validate[required,custom[email]] text-input'
			)
		);

		// Add the submit button
		$this->addElement('submit', 'submit',
		    array(
		    	'ignore'	=> true,
		    	'label'		=> 'Subscribe',
		    	'decorators'=> $this->elementDecorators
		    )
		);
    }
};