<?php
/**
 * @file		Navigation.php
 *
 * @category    Admin
 * @package    	Admin_Form
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: Navigation.php 148 2011-11-14 05:02:46Z jisha $
 */

/**
 * @brief 		Admin Navigation Form class definition
 *
 * @package    	Zend_Form
 * @class		Admin_Form_Navigation
 * @see
 */
class Admin_Form_Navigation extends Zend_Form
{
    public function init()
    {
    	$fc 		= Zend_Controller_Front::getInstance();
        $request	= $fc->getRequest();
        $controller	= $request->getControllerName();
        $action		= $request->getActionName();

		$navigationModel	= new Admin_Model_Navigation();
		$sectionArray		= $navigationModel->getSections();

		# Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'navigationform');

		$this->addElement('select', 'section',
			array(
				'label'			=> 'Section:',
				'value'=>'admin',
				'required'		=> true,
				'filters'		=> array('StringTrim'),
				'multiOptions' 	=> $sectionArray,
				'validators'	=> array(
										array('NotEmpty', true, array('messages' => "Section is required and can't be empty",))
								   ),
				'class'			=> 'validate[required] text-input'
			)
		);

		# Add a module element
		$this->addElement('text', 'module',
			array(
				'label'		=> 'Module:',
				'required'	=> true,
				'filters'	=> array('StringTrim'),
				'validators'=> array(
									 array('NotEmpty', true, array('messages' => "Module is required and can't be empty",)),
									 array('stringLength', true, array(1, 100)),
									 array('alpha', true, array('allowWhiteSpace' => false, 'messages' => 'Please enter a valid Module')),
								),
				'size'		=> 30,
				'maxlength'	=> 100,
				'class'		=> 'validate[required,custom[onlyLetterSp]] text-input'
			)
		);

		# Add a controller element
		$this->addElement('text', 'controller',
			array(
				'label'		=> 'Controller:',
				'required'	=> true,
				'filters'	=> array('StringTrim'),
				'validators'=> array(
									 array('NotEmpty', true, array('messages' => "Controller is required and can't be empty",)),
									 array('stringLength', true, array(1, 100)),
									 array('alpha', true, array('allowWhiteSpace' => false, 'messages' => 'Please enter a valid Controller name')),
									 ),
				'size'		=> 30,
				'maxlength'	=> 100,
				'class'		=> 'validate[required,custom[onlyLetterSp]] text-input'
			)
		);

		# Add a action element
		$this->addElement('text', 'action',
			array(
				'label'		=> 'Action:',
				'required'	=> true,
				'filters'	=> array('StringTrim'),
				'validators'=> array(
									 array('NotEmpty', true, array('messages' => "Action is required and can't be empty",)),
									 array('stringLength', true, array(1, 100)),
									 array('alpha', true, array('allowWhiteSpace' => false, 'messages' => 'Please enter a valid Action name')),
									 ),
				'size'		=> 30,
				'maxlength'	=> 100,
				'class'		=> 'validate[required,custom[onlyLetterSp]] text-input'
			)
		);

		# Add a name element
		$this->addElement('text', 'name',
			array(
				'label'		=> 'Name:',
				'required'	=> true,
				'filters'	=> array('StringTrim'),
				'validators'=> array(
									 array('NotEmpty', true, array('messages' => "Name is required and can't be empty",)),
									 array('stringLength', true, array(1, 100)),
									 array('alpha', true, array('allowWhiteSpace' => true, 'messages' => 'Please enter a valid  name')),
									 ),
				'size'		=> 30,
				'maxlength'	=> 100,
				'class'		=> 'validate[required,custom[onlyLetterSp]] text-input'
			)
		);

		# Add the submit button
		$this->addElement('submit', 'navigationSubmit',
			array(
				'ignore'	=> true,
				'label'		=> 'Submit',
			)
		);

		# Add the reset button
		$this->addElement('reset', 'navigationReset',
			array(
				'ignore'	=> true,
				'label'		=> 'Reset',
			)
		);

		# Add a navigationId element
		$this->addElement('hidden', 'navigationId');

	}
};