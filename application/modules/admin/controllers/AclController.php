<?php
/**
 * @file		AclController.php
 *
 * @category   	Admin
 * @package    	Admin_Controller
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Admin ACL Controller class defination
 *
 * @package    	Zend_Controller_Action
 * @class		Admin_AclController
 * @see
 */
class Admin_AclController extends Zend_Controller_Action
{
	protected $aclTableObj	= null;

	public function init()
	{
		$this->aclTableObj  = new User_Model_Acl();
	}

	# Default ACL Action
	public function indexAction()
	{
		# Get role and user tables
		$roleModel = new User_Model_Role();

		# Get ACL record from acl table and display using paginator
		$this->view->paginator	 	= $paginator = Zend_Paginator::factory( $this->aclTableObj->getAll() );
		$this->view->pagecount 		= $this->_getParam('page');
		$this->view->recordscount 	= $recordscount = 30;

		# Paginator settings
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setItemCountPerPage($recordscount);
		$paginator->setCache(Zend_Registry::get('cache'));

		# Get role of each user.
		$ops 		= $roleModel->getAll();
		$options 	= array();

		foreach ( $ops as $op )
		{
			$options[$op['roleId']] = $op['title'];
		}

		$this->view->options = $options;

		# For display the Status message
		$this->view->message = $this->_getParam('message', false);
	}

	# Add ACL Action
	public function addAction()
	{
		$this->view->form 			= $aclform  = new Admin_Form_Acl();

		if( $this->getRequest()->isPost() && $aclform->isValid($this->getRequest()->getPost()) )
		{
			$formValues 			= $this->getRequest()->getPost();
			$concat 				= $formValues['module'].'_'.
										$formValues['controller'].'_'.
										$formValues['action'];
			$formValues['resource'] = $concat;
			$id						= $this->aclTableObj->insert($formValues);
			if( $id > 0 )
			{
				$this->_redirect('/admin/acl/index/message/'.urlencode('ACL created'));
			}
			else
			{
				$this->view->message = 'Duplicate Entry!!';
			}
		}
	}

	# Edit ACL action
	public function editAction()
	{
		# form helper to change acl details
		$this->view->form 		= $form = new Admin_Form_Acl();
		$this->view->error 		= '';
		$aclId					= (int) $this->_getParam('aclId');
		$aclRow 				= $this->aclTableObj->get($aclId, true);
		$resource				= $this->aclTableObj->splitResource($aclRow['resource']);
		$aclRow['module']		= $resource['module'];
		$aclRow['controller']	= $resource['controller'];
		$aclRow['action']		= $resource['action'];

		if ( empty($aclRow) )
		{
			$this->view->error 	= 'No record present for this acl.';
		}

		# Set saved field values for the acl
		$form->populate($aclRow);

		# Get values from Form and update the database record for that particular acl.
		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
		{
			$formValues 			= $form->getValues();
			$formValues['resource']	= $formValues['module'].'_'.
										$formValues['controller'].'_'.
										$formValues['action'];

			$result 				= $this->aclTableObj->update($formValues, true);

			# if it has changed redirect to list of acl, else stay in form itself.
			if( !empty($result) )
			{
				$this->_redirect('/admin/acl/index/message/'.urlencode('ACL updated successfully'));
			}
		}
	}

	# delete ACL action
	public function deleteAction()
	{
		if( $this->getRequest()->isXmlHttpRequest() )
		{
		    $this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender();

			$data 		= $this->getRequest()->getPost('aclId');
			$result		= $this->aclTableObj->delete($data);

		    echo json_encode($result);
		}
	}
};