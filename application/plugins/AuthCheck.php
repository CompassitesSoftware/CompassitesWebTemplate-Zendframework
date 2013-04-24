<?php
/**
 * @file		AuthCheck.php
 *
 * @category   	Compassites
 * @package    	Compassites_Plugin
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2012 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		AuthCheck Plugin class defination
 *
 * @package    	Compassites_Plugin
 * @class		Compassites_Plugin_AuthCheck
 * @see
 */
class Compassites_Plugin_AuthCheck extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
		$module		= $request->getmoduleName();
   		$controller	= $request->getControllerName();
		$action		= $request->getActionName();
		$iframeurl	= $request->getParam('url');
		$auth 		= Zend_Auth::getInstance();
		$user		= Zend_Registry::get('user');

      	#Default role is set to 6 for guest user;
		$roleId		= 6;

		$dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
		$redirector	= Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');

		if (!$dispatcher->isDispatchable($request))
		{
			$redirector->gotoUrl('error/error');
		}

		# check for logged in
		$aclModel 	= new User_Model_Acl();
		$resource	= $aclModel->getResource($module, $controller, $action);
		$acl 		= $aclModel->getAcl($resource);
		$allow		= false;

        if(	!empty($user) )
        {
 			$roleId = $user->roleId;
		}
		$acl 		= $aclModel->getAcl($resource);
		$allow		= false;
		$message	= '';
		
		# Validate role with controller/action permissions
		if($acl->has($resource) && $acl->hasRole($roleId))
		{
			$allow = $acl->isAllowed($roleId, $resource);
		}
		elseif( !$acl->has($resource) )
		{
			# No acl rule defination, just allow
			$allow = true;
		}

		if( false ===  $allow )
		{
			$message= 'No Privileges to access this page: '. ucfirst($controller) . ' /  ' . ucfirst($action);

			$redirector->gotoUrl('user/index/login/message/' . base64_encode($message));
		}
    }
};