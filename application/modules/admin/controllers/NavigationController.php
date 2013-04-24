<?php
/**
 * @file		NavigationController.php
 *
 * @category   	Admin
 * @package    	Admin_Controller
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Admin Navigation Controller class defination
 *
 * @package    	Zend_Controller_Action
 * @class		Admin_NavigationController
 * @see
 */
class Admin_NavigationController extends Zend_Controller_Action
{
	protected $_navigationTableObj	= null;

	public function init()
	{
		$this->_navigationTableObj 	= new Admin_Model_Navigation();
	}

	# Navigation listing
	public function indexAction()
	{
		# Get agent navigation from navigation table and display using paginator
		$this->view->paginator	 	= $paginator = Zend_Paginator::factory($this->_navigationTableObj->getAll());
		$this->view->pagecount 		= $this->_getParam('page');
		$this->view->recordscount 	= $recordscount = 30;

		# Paginator settings
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setItemCountPerPage($recordscount);
		$paginator->setCache(Zend_Registry::get('cache'));

		# For display the Status message
		$this->view->message = $this->_getParam('message', false);
	}

	#  Add Navigation Action
	public function addAction()
	{
		# form helper to add a new navigation
		$this->view->form 	= $navigationform = new Admin_Form_Navigation();

		# Get values from form and save to database
		if( $this->getRequest()->isPost() && $navigationform->isValid($this->getRequest()->getPost()) )
		{
			$formValues 	= $navigationform->getValues();
			$result			= $this->_navigationTableObj->insert($formValues);

			if( !empty($result) )
			{
				$this->_redirect('/admin/navigation/index/message/'.urlencode('Navigation created successfully'));
			}
		}
	}

	#  Edit Navigation Action
	public function editAction()
	{
		# form helper to change navigation's details
		$this->view->form 	= $form = new Admin_Form_Navigation();
		$this->view->error 	= '';

		$navigationId		= (int) $this->_getParam('navigationId');
		$navigationRow 		= $this->_navigationTableObj->get($navigationId, true);

		# validate navigation id
		if ( empty($navigationRow) )
		{
			$this->view->error 	= 'No such record';

			return;
		}

		# Set saved field values for the navigation
		$form->populate($navigationRow);

		# Get values from Form and update the database record for that particular navigation.
		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
		{
			$formValues 	= $form->getValues();
			$result 		= $this->_navigationTableObj->update($formValues, true);

			if( !empty($result) )
			{
				$this->_redirect('/admin/navigation/index/message/'.urlencode('Navigation updated successfully'));
			}
		}
	}

	# Delete Navigation action
	public function deleteAction()
	{
		if( $this->getRequest()->isXmlHttpRequest() )
		{
		    $this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender();

			$data 	= $this->getRequest()->getPost('navigationId');
			$result	= $this->_navigationTableObj->delete($data);

		    echo json_encode($result);
		}
	}
};