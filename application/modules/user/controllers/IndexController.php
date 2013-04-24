<?php
/**
 * @file		IndexController.php
 *
 * @category    Compassites
 * @package    	Zend_Controller_Action
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Index Controller class defination
 *
 * @package    	Zend_Controller_Action
 * @class		UserController
 * @see
 */
class User_IndexController extends Zend_Controller_Action
{
    protected $_auth	= null;
	protected $_session	= null;

    protected function getAuthAdapter()
    {
        $config		= Zend_Registry::get('config');
		$dbAdapters	= Zend_Registry::get('dbAdapters');

        $authAdapter= new Zend_Auth_Adapter_DbTable($dbAdapters['default']);
        $authAdapter->setTableName($config['auth']['tableName']);
        $authAdapter->setIdentityColumn($config['auth']['identityColumn']);
		$authAdapter->setCredentialColumn($config['auth']['credentialColumn']);
        $authAdapter->setCredentialTreatment($config['auth']['credentialTreatment']);

        return $authAdapter;
    }

    public function init()
    {
        # Initialize action controller
		$this->_auth		= Zend_Auth::getInstance();
		#user model
		$this->_userModelObj= new User_Model_User();
    }

    public function indexAction()
    {
		$openid = $this->getRequest()->getParam('openid');

        if(isset($openid))
        {
            $this->view->openIdServer =  $this->view->serverUrl().$this->view->url(array( 'module' => 'user', 'controller' => 'provider', 'action' => 'index'), 'default', true);
        }
		elseif( $this->_auth->getIdentity() )
		{
			return $this->_helper->redirector->gotoUrl('/');
		}
		else
		{
			return $this->_helper->redirector->gotoUrl('user/index/login/');
		}
    }

    /**
     * @brief	User home Action
     *
     * @param	Void
     * @return
    */
    public function dashboardAction()
    {
		$this->view->message 	= $this->_getParam('message', '');

        $userId					= $this->_auth->getIdentity()->userId;

    }

	/**
     * @brief	User Login Action
     *
     * @param	Void
     * @return
    */
    public function loginAction()
    {
		# Redirect to dashboard, if user has login
		if( $this->_auth->getIdentity() )
		{
			return $this->_helper->redirector('index', 'index');
		}

		# Form
		$this->view->form	= $form = new user_Form_Login();
		# Store return url
		$form->addElement('hidden', 'return', array('value' =>  $this->getRequest()->getParam('return') ) );

		$this->view->message= base64_decode($this->_getParam('message', ''));
		# Get Openid From query String
        $requestOpenId		= $this->getRequest()->getParam('openid_identity');


		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
		{
			$values 	= $form->getValues();
			# Get salt from config
			$config		= Zend_Registry::get('config');
			$salt		= $config['auth']['salt'];
			# authenticate username and password
			$adapter	= $this->getAuthAdapter();
			$adapter->setIdentity($values['username'])
					->setCredential($salt.$values['password']);
			try
			{
				$result		= $this->_auth->authenticate($adapter);

				if( $result->isValid() )
				{
					$data['lastLoginDate']	= date('Y-m-d H:i:s');
					$data['username'] 		= $values['username'];
					$this->_userModelObj->update($data);

					$return 				= base64_decode($values['return']);

/*
					# For Openid
					$openIdPlugin			= new user_Api_OpenId();
					$userDetails			= $this->_userModelObj->getCurrentUser();
					$openIdPlugin->getServer()->login($userDetails->openid, $password);

					# Redirection
					if(isset($requestOpenId))
					{
						$returnUrl = $this->view->url(array('controller'=>'provider','action'=>'index'), 'default', FALSE);
						Zend_OpenId::redirect($returnUrl, $_GET);
					}
					else
					*/
					if( ('/user/login' !=  $return)  && ('/' != $return) && ('' != $return)  )
					{
						return $this->_redirect( $return );
					}
					else
					{
						return $this->_helper->redirector('index', 'index');
					}
				}
				else
				{
					$this->view->message 	= 'Invalid Username / Password';
				}
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}
        }
        elseif(isset($requestOpenId))
        {
			$userDetails = $this->_userModelObj->getByOpenId($requestOpenId);
			$form->username->setValue($userDetails['username'])->setAttrib('readonly', 'true');
        }

    }

    /**
     * @brief	User Registration Action
     *
     * @param	Void
     * @return
    */
    public function logoutAction()
    {
		# Delete Open id Session
        $userOpenId	= new user_Model_UserOpenIdSession();
        $userOpenId->delLoggedInUser();
        # End

		$this->_auth->clearIdentity();
        $message = $this->_getParam('message', '');
		$redirector = $this->_helper->getHelper('redirector');

		return $redirector->gotoSimple('login', 'index', null, array('message'=>$message));
    }

    /**
     * @brief	User Registration Action
     *
     * @param	Void
     * @return
    */
    public function registerAction()
    {
		# Redirect to dashboard, if user has login
		if( $this->_auth->getIdentity() )
		{
			return $this->_helper->redirector('index', 'index');
		}
        # Form helper to Register a new user
        $this->view->form   = $userform = new user_Form_User();

        # Get values from form and save to database
        if( $this->getRequest()->isPost() && $userform->isValid($this->getRequest()->getPost()) )
        {
            $formvalues = $userform->getValues();

			# TODO need change unset code
			unset($formvalues['terms']);

			$loginUtility				= new Homeconnect_Plugin_LoginUtility();
			$formvalues['creationIP']	= $loginUtility->getIp();
			$formvalues['activationKey']= $loginUtility->randomKey();

			# Insert user details
			if( $userId = $this->_userModelObj->insert($formvalues) )
			{
				# Send the activation mail
				$activationKey 	= $userId.'_'.$formvalues['activationKey'];
				$email 			= new user_Api_Email();
				$email->setSubject('Please activate your Compassites account');
				$email->sendRegisterMail($formvalues['username'], $activationKey);
				# Redirection
				$message		= 'Activation mail has been sent to your registered email. Please activate.';
				$this->_helper->redirector('login', 'index', null, array('message'=>$message));
			}
			else
			{
                $userform->username->addError('User Name already exists. Please Register with different User Name.');
            }
        }
    }

	/**
     * @brief	User registration Activation Action
     *
     * @param	Void
     * @return
    */
	public function activateAction()
	{
		$this->view->formDisplay= true;
		$this->view->message 	= '';
		$form 					= new user_Form_Activate();
		$loginUtility			= new Homeconnect_Plugin_LoginUtility();

		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
		{
			$formValues = $form->getValues();

			/* TODO , need change role id assign
			*/
			$formValues['roleId']			= 4;

			$formValues['active'] 			= 1;
			$formValues['activatedDate']	= date('Y-m-d H:i:s');
			$formValues['lastUpdatedDate']	= date('Y-m-d H:i:s');
			$formValues['openid'] 			=  $loginUtility->buildOpenId($formValues['username']);

			$recordNo	= $this->_userModelObj->update($formValues);

			if($recordNo > 0)
			{
				$this->view->formDisplay 	= false;
				$this->view->message 		= "You have successfully activated your account. To Login and explore Homeconnect <a href='". $this->view->url(array('controller'=> 'index', 'action' =>'login' )) . "'> click here</a>";
			}
			else
			{
				$this->view->formDisplay 	= true;
				$this->view->message 		= 'System could not update user details. Please contact administrator';
			}
		}
		else
		{
			try
			{
				$userId 	  	= (int) $loginUtility->extractActivationLink($this->getRequest()->getParams());

				$userDetails 		= $this->_userModelObj->get($userId);

				if( null == $userDetails )
				{
					$this->view->formDisplay 	= false;
					$this->view->message 		= 'The url you entered to activate your account was incorrect.';
				}
				elseif( $userDetails['active'] == 1 )
				{
					$this->view->formDisplay 	= false;
					$this->view->message 		= 'You have already activated.Please login.';
				}
				else
				{
					$form->setUsernameValue($userDetails->username, $userId);
					$this->view->form = $form;
				}
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}
		}

	}

	/** TODO need to change this action
     * @brief	User account Activation Action
     *
     * @param	Void
     * @return
    */
	public function myaccountAction()
	{
		$form 					= new user_Form_Activate();

		$this->view->message	= true;
		$this->view->formDisplay= true;

		$values						= $this->getRequest()->getParams();
		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
		{
			$formValues 				= $form->getValues();
			$this->view->formDisplay= false;
			# Update
			$this->_userModelObj->update($formValues);

			$this->view->message= "You have successfully updated your account. Login and explore Compassites <a href='". $this->view->url(array('controller'=> 'index', 'action' =>'dashboard' )) . "'> click here</a>";
		}
		else
		{
			$userData			= $this->_userModelObj->get($this->_auth->getIdentity()->userId,true);
			$data				= $userData->toArray();
			$form->setMyAccountDetails($data);
			$this->view->form 	= $form;

		}
	}

	/**
     * @brief	Change password Action
     *
     * @param	Void
     * @return
    */
	public function changepasswordAction()
	{
		$this->view->form 		= $form = new user_Form_Changepassword();
		$this->view->message	= '';

		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
		{
			$formvalues			= $form->getValues();

			$username			= $this->_auth->getIdentity()->username;
			$userDetails		= $this->_userModelObj->getByUsername($username,true);

			$loginUtility		= new Homeconnect_Plugin_LoginUtility();
			$userPasswordEncrypt= $loginUtility->getEncryptPassword($formvalues['user_password']);
			if( $userDetails->password == $userPasswordEncrypt )
			{
				try
				{
					$currentTime		= date('Y-m-d H:i:s');
					$userData 			= array('username'=>$username, 'password'=> $formvalues['password'], 'lastUpdatedDate'=>$currentTime);
					$this->_userModelObj->update($userData);
					$this->view->message= "Your password has changed successfully";
				}
				catch(Exception $e)
				{
					echo $e->getMessage();
				}
			}
			else
			{
				$form->user_password->addError('The password you gave is incorrect');
			}
		}

	}

	public function forgotpasswordAction()
	{
		# Form
		$this->view->form 		= $form = new user_Form_Forgotpassword();
		$this->view->message 	= '';

		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
		{
			$formValues 	= $form->getValues();
			$email 	= new user_Api_Email();
			$userDetails	= $this->_userModelObj->getByUsername($formValues['username'], true);

			if($userDetails != null)
			{
				# send an activation mail, if user account not activate
				if( $userDetails->active == 0  )
				{
					$activationKey = $userDetails->userId.'_'.$userDetails->activationKey;
					$email->sendRegisterMail($formValues['username'], $activationKey);
				}
				else
				{
					# Get new random password
					$loginUtility				= new Homeconnect_Plugin_LoginUtility();
					$newPassword 				= $loginUtility->getRandomPassword();
					# Update the new password in database
					$userData 					= array( 'username'=>$formValues['username'], 'password'=> $newPassword );
					$userData['lastUpdatedDate']= date('Y-m-d H:i:s');

					if($this->_userModelObj->update($userData))
					{
						# Send the new password to user's mail
						$email->setSubject('Your Request to Reset your password - Home Connect');
						$email->sendforgotPasswordMail($userDetails->username, $newPassword, $userDetails->firstName);
					}
				}
				$this->view->message = "Details has been send to your email address with the steps to access your account.";
			}
			else
			{
				$form->username->addError('Username entered is incorrect/invalid ');
			}
		}
	}
};
