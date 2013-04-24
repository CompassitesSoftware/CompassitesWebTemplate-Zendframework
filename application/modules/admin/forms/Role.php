<?php
/**
 * @file		Role.php
 *
 * @category    admin
 * @package    	admin_Form
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $
 */
/**
 * @brief 		Refer friend class definition
 *
 * @package    	admin_Form
 * @class		admin_Form_Advertisewithus
 * @see
 */
class admin_Form_Role extends Zend_Form
{

	public function init()
    {
		# Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'roleform');

		$roleModel = new User_Model_Role();
		$roleDetails= $roleModel->getAll();
		$roleArray = array();
		foreach($roleDetails as $roleDetail)
		{
			$roleArray[ $roleDetail['roleId'] ] = $roleDetail['title'];
		}

		# Add a Username element
		$this->addElement('select', 'roleId',
			array(
				'label'			=> 'Role:',
				'required'		=> true,
				'maxlength'		=> 150,
				'multiOptions' 	=> $roleArray,
				'ErrorMessages' => array('Please select Role.'),
			)
		);

		# Add the submit button
		$this->addElement('submit', 'roleSubmit',
		    array(
		    	'ignore'	=> true,
				'label'		=> 'Submit',
		    )
		);
    }
};
?>