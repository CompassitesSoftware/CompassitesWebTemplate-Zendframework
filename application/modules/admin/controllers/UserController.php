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
		$this->_userTableObj		= new User_Model_User();
		$this->_navigationTableObj 	= new Admin_Model_Navigation();
		$this->_logger				= Zend_Registry::get('Zend_Log');
	}

	#  List Users Action
	public function indexAction()
	{
		if( $this->getRequest()->isXmlHttpRequest())
		{
			$roleModel  = new User_Model_Role();
			$userData	= array();
			# Disable the layout
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender();

			$getParams		= $this->getRequest()->getParams();
			$page 			= $getParams['page']; # Get the requested page
			$limit 			= $getParams['rows']; # Get how many rows we want to have into the grid
			$orderby 		= $getParams['sidx']; # Get index row - i.e. user click to sort
			$order 			= $getParams['sord']; # Get the direction
			$searchField 	= $this->getRequest()->getQuery('searchField', '') ;
			$searchString	= $this->getRequest()->getQuery('searchString', '');
			
			# Getting the data from table
			$offset = 0;
			if( $page > 0 )
			{
				$offset		= $limit * ($page -1);
			}

			$where = array();
			if(!empty($searchField))
			{
				$where = array($searchField => $searchString);
			}
			$data = $this->_userTableObj->getAllByField($where, $limit, $offset, $order, $orderby);

			if(!empty($data))
			{
				foreach($data as &$user)
				{
					$roleData = $roleModel->get($user['roleId']);
					$user = array_merge($user, $roleData);
				}

				#getting the data from table
				$userData['rows']		= array_values($data);
				$userData['page'] 		= $page;
				$userData['records']	= $this->_userTableObj->getCount();
				$userData['total']		= ceil($userData['records'] / $limit);
			}

			echo json_encode($userData, JSON_NUMERIC_CHECK);
		}
	}


	public function getjsonAction()
	{
		# Get role and user tables
		$roleModel = new User_Model_Role();
		#disable the layout
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		if( $this->getRequest()->isXmlHttpRequest())
		{
			# Get role of each user.
			$ops		= $roleModel->getAll();
			$options	= array();
			foreach ($ops as $op)
			{
				$options[$op['roleId']] = $op['title'];
			}

			echo json_encode($options);
		}
	}
	public function exportAction()
	{
		$roleModel  = new User_Model_Role();
		# Disable the layout/view
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNeverRender();

		$offset  		= 0;
		$page 			= 0;
		$limit 			= 0;
		$orderby 		= '';
		$order 			= '';

		# Header added for exporting the data to CSV file
		header("Content-Type: text/csv; charset=utf-8");
		header("Cache-Control: no-store, no-cache");
		header("Content-Disposition: attachment; filename=UserList.csv");

		$output = fopen('php://output', 'w');
		fputcsv($output, array( 'User Id', 'Email', 'First Name',  'Last Name', 'Contact No', 'Mobile No', 'Address Line 1', 'Address Line 2', 'Area', 'City', 'Pincode', 'Role'));

		
		$data = $this->_userTableObj->getAllByField(array(), $limit, $offset, $order, $orderby);

		if(!empty($data))
		{
			foreach($data as $user)
			{
				$roleData = $roleModel->get($user['roleId']);
				$user = array($user['userId'], $user['username'], $user['firstName'], $user['lastName'], $user['landLine'], $user['mobile'], $user['addressLine1'], $user['addressLine2'], $user['area'], $user['city'], $user['pincode'], $roleData['title']);
				fputcsv($output, $user);
			}
		}

	}

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
		if( $this->getRequest()->isPost())
		{
			$formValues	= $this->getRequest()->getPost();
			$formValues['userId'] = $formValues['id'];
			$formValues['roleId'] = $formValues['title'];
			unset($formValues['id']);
			unset($formValues['title']);
			unset($formValues['oper']);
			$result		= $this->_userTableObj->update($formValues, true);
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