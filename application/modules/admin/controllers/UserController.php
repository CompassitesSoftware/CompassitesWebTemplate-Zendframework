<?php
/**
 * @file		IndexController.php
 *
 * @category   	Compassites
 * @package    	Zend_Controller_Action
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Admin Controller class defination
 *
 * @package    	Zend_Controller_Action
 * @class		Admin_IndexController
 * @see
 */
class Admin_UserController extends Zend_Controller_Action
{
	protected $_userTableObj= null;
	protected $_navigationTableObj=null;
	protected $_logger		= null;

	public function init()
	{
		$this->_userTableObj= new User_Model_User();
		$this->_navigationTableObj = new Admin_Model_Navigation();
		$this->_logger		= Zend_Registry::get('Zend_Log');
	}

	#  List Users Action
	public function indexAction()
	{
		# Get role and user tables
		$roleModel = new User_Model_Role();

		# Get agent users from users table and display using paginator
		$this->view->paginator	 	= $paginator = Zend_Paginator::factory( $this->_userTableObj->getDbTable()->select()->where('roleId > 1') );
		$this->view->pagecount 		= $this->_getParam('page');
		$this->view->recordscount 	= $recordscount = 25;

		$paginator->setCurrentPageNumber($this->_getParam('page'));
//  		$paginator->setItemCountPerPage($recordscount);
		$paginator->setCache(Zend_Registry::get('cache'));

		# Get role of each user.
		$ops = $roleModel->getAll();
		$options = array();
		foreach ($ops as $op)
		{
			$options[$op['roleId']] = $op['title'];
		}
		$this->view->options = $options;
	}


	#  Display User details
	public function viewuserAction()
	{
		$userId 	= $this->_getParam('user_id');

		$userRow 	= $this->_userTableObj->get($userId);
		$roleModel	= new Homeconnect_Model_Role();
		$centerTable= new Homeconnect_Model_Center();
		$educationTable	= new Homeconnect_Model_Education();


		if (!empty($userRow) )
		{
			# Get details of the user
			$this->view->userRow = $userRow;

			# Get role of the user.
			$roleData	= $roleModel->get($userRow->role_id);
			$roleTitle = $roleData->title;

			$this->view->role = $roleTitle;

			# Get center name for the user.
			$user=$userRow->toArray();
			$rawCenterData = $centerTable->get($user);
			$centerName= $rawCenterData->center_name;

			$this->view->centerName = $centerName;

			# Get education level of user.
			$educationData	= $educationTable->get($userRow->education_id);
			$education_level = $educationData->education_level;

			$this->view->educationlevel = $education_level;
		}
 		else
		{
 			$this->view->error = 'No record present for this user.';
		}
	}

	#  User Registration Action
	public function registeruserAction()
	{
		# form helper to Register a new user
		$this->view->form 	= $userform = new user_Form_Activate();

		# Get values from form and save to database
		if( $this->getRequest()->isPost() && $userform->isValid($this->getRequest()->getPost()) )
		{
			$formValues 					= $userform->getValues();
			//$loginUtility					= new Homeconnect_Plugin_LoginUtility();
			//$formValues['creationIP']		= $loginUtility->getIp();
			$formValues['active'] 			= 1;
			$formValues['lastUpdatedDate']	= date('Y-m-d H:i:s');
			//$formValues['openid'] 			=  $loginUtility->buildOpenId($formValues['username']);

			try
			{
				$this->_userTableObj->insert($formValues);
				$this->_helper->redirector('index');
			}
			catch( Exception $e )
			{
				$errorInfo = 'Register User db error: '. $e->getMessage();
				$this->_logger->info($errorInfo);
				$error = 'User Name already exists. Please Register with different User Name.' ;
				$this->view->message = $error;
			}
		}
	}

	/**
     * @brief	Edit user
     *
     */
	public function edituserAction()
	{
		# form helper to change user's details
		$this->view->form 		= $form = new user_Form_Activate();
		$this->view->username 	= '';
		$this->view->error 		= '';
		$userId					= (int) $this->_getParam('userId');
		$userRow 				= $this->_userTableObj->get($userId, true);

		if (!empty($userRow) )
		{
			# Set saved field values for the user
			$userArray = $userRow;
			unset($userArray['password']);
			unset($userArray['language']);
			$form->populate($userArray);

			# Get values from Form and update the database record for that particular user.
			if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))
			{
				$formValues = $form->getValues();

				$result = $this->_userTableObj->update($formValues, true);

				# if it has changed redirect to list of users, else stay in form itself.
				if( !empty($result) )
				{
					return $this->_helper->redirector('user', 'index');
				}
			}
		}
		else
		{
			$this->view->error = 'No record present for this user.';
		}
	}

	#  Edit Role Action
	public function editroleAction()
	{
		# form helper to change user's role
		$this->view->form 		= $form = new admin_Form_Role();
		$this->view->username 	= '';
		$this->view->error 		= '';
		$userId					= (int) $this->_getParam('userId');
		$userRow 				= $this->_userTableObj->get($userId);

		# Displayusername and role
		if (!empty($userRow) )
		{
			$this->view->username = $userRow['username'];
 			$form->getElement('roleId')->setValue($userRow['roleId']);
		}

		# Get values from form and save to database for a partitcular user.
		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
		{
			$formValues = $form->getValues();
			$userData	= array();

			if (!empty($userRow) )
			{
				$userData['roleId']		= $formValues['roleId'];
				$userData['username']	= $userRow['username'];

				$result 				= $this->_userTableObj->update($userData, true);

				# if role had changed redirect to list of users, else stay in form itself.
				if( !empty($result) )
				{
					return $this->_helper->redirector('user', 'index');
				}
			}
			else
			{
				$this->view->error = 'No record present for this user.';
			}
		}
	}
};