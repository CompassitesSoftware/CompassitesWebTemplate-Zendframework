<?php
/**
 * @file		Acl.php
 *
 * @category    Admin
 * @package    	Admin_Form
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $
 */
/**
 * @brief 		Admin ACL form class definition
 *
 * @package    	Zend_Form
 * @class		Admin_Form_Acl
 * @see
 */
class Admin_Form_Acl extends Zend_Form
{
	protected $elementDecorators = array(
			'ViewHelper',
			'Errors',
			'Label',
		);

	public function init()
    {
		# Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'aclForm');

		$roleModel		= new User_Model_Role();
		$roleDetails 	= $roleModel->getNonAdminRole();
		$roleArray = array();

		foreach($roleDetails as $roleDetail)
		{
			$roleArray[ $roleDetail['roleId'] ] = $roleDetail['title'];
		}

		# Add a aclId element
		$this->addElement('hidden', 'aclId');

		# Add a module element
		$this->addElement('text', 'module',
			array(
				'label'			=> 'Module:',
				'required'		=> true,
				'maxlength'		=> 100,
				'ErrorMessages' => array('Please enter module.'),
				'class'			=> 'validate[required,custom[onlyLetterSp]] text-input'
			)
		);

		# Add a controller element
		$this->addElement('text', 'controller',
			array(
				'label'			=> 'Controller:',
				'required'		=> true,
				'maxlength'		=> 100,
				'ErrorMessages' => array('Please enter controller.'),
				'class'			=> 'validate[required,custom[onlyLetterSp]] text-input'
			)
		);

		# Add a action element
		$this->addElement('text', 'action',
			array(
				'label'			=> 'Action:',
				'required'		=> true,
				'maxlength'		=> 100,
				'ErrorMessages' => array('Please enter action.'),
				'class'			=> 'validate[required,custom[onlyLetterSp]] text-input'
			)
		);

		# Add a role element
		$this->addElement('select', 'roleId',
			array(
				'label'			=> 'Role:',
				'required'		=> true,
				'maxlength'		=> 100,
				'multiOptions' 	=> $roleArray,
				'ErrorMessages' => array('Please select Role.'),
				'class'			=> 'validate[required] text-input'

			)
		);
		$this->setDefault('roleId','Select Role');

		# Add a allow element
		$this->addElement('select', 'allow',
			array(
				'label'			=> 'Allow:',
				'required'		=> true,
				'maxlength'		=> 100,
				'multiOptions' 	=> array(0 => 'No',1 => 'Yes'),
				'ErrorMessages' => array('Please select Allow.'),
				'class'			=> 'validate[required] text-input'
			)
		);

		# Add the submit button
		$this->addElement('submit', 'roleSubmit',
		    array(
		    	'ignore'	=> true,
		    	'label'		=> 'Submit'
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
};
?>