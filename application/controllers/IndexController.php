<?php
/**
 * @file		IndexController.php
 *
 * @category   	Compassites
 * @package    	Zend_Controller_Action
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2012 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Index Controller class defination
 *
 * @package    	Zend_Controller_Action
 * @class		IndexController
 * @see
 */
class IndexController extends Zend_Controller_Action
{
    protected $_auth 		= null;

    public function init()
    {
        $this->_auth = Zend_Auth::getInstance();
    }

    public function indexAction()
    {
		//$this->_forward('launch');
    }

    public function dummyAction()
    {

		$this->view->enrollForm	= new Compassites_Form_Subscriber();

        //$this->_helper->layout->setLayout('launch');
    }

    public function aboutusAction()
    {
		//
    }

    public function termsAction()
    {

    }

    public function faqAction()
    {
		//
    }

    public function feedbackAction()
    {
		//
    }

    public function contactusAction()
    {
		$this->view->enrollForm	= new Compassites_Form_Subscriber();
    }

    public function errorAction()
    {
		//
    }
};