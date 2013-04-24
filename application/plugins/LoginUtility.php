<?php
/**
 * @file		LoginUtility.php
 *
 * @category   	Compassites
 * @package    	Compassites_Plugin
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2012 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		LoginUtility Plugin class defination
 *
 * @package    	Compassites_Plugin
 * @class		Compassites_Plugin_LoginUtility
 * @see
 */
class Compassites_Plugin_LoginUtility extends Zend_Controller_Plugin_Abstract
{
	protected $_auth	= null;

    public function init()
    {
        # Initialize action controller
		$this->_auth		= Zend_Auth::getInstance();

		#user model
		$this->_userTableObj= new User_Model_User();
    }
    
	/**
     * @brief	get salt value
     * 			This treatment has to do do while registration and login
     *
     * @param 	string 	password
     * @return 	string salt
     */
	public function getEncryptPassword($password)
	{
		return md5($password);
	}

	/**
     * @brief	prepare random number
     *
     * @param
     * @return 	string 	random number
     *
     * NOTE: srand() is  related to rand() which gives the value with seed.
     */
	 public function randomKey()
	 {
	 	list($usec, $sec) = explode(' ', microtime());
  		$seed = (int) $sec + ((int) $usec * 100000);
  		srand($seed);

	 	return rand();
	 }

	 /**
     * @brief	retrieve id from acivation link
     *
     * @param	array parameterValues
     * @return 	0 in failure , int Id in success
     */
	 public function extractActivationLink($paramValues)
	 {
	 	$returnvalue= 0;
	 	$param 		= md5('code');
	 	if(array_key_exists($param, $paramValues))
		{
			$encryptedId 	= $paramValues[$param];
			$activationKey	= base64_decode($encryptedId);
			$returnvalue	= explode('_', $activationKey);
		}

		return $returnvalue;
	 }

	/**
     * @brief	Get the ip of user
     *
     * @param
     * @return 	string 	ip
     */
	 public function getIp()
	 {
		$ip = $_SERVER['REMOTE_ADDR'];
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		return $ip;
	 }
	 
	 /**
     * @brief	build the open id for users
     *
     * @param   string  username
     * @return 	string 	openid
     */
	 public function buildOpenId($username)
	 {
		$baseUrl =  Zend_Controller_Front::getInstance()->getBaseUrl();

		return Zend_OpenId::absoluteURL( $baseUrl. '/user/index/index/openid/'.str_replace('@', '-', $username));
	 }
	 
	/**
	* @brief	forgot password validation - get the username[ mail id] and check whether tis user is
	*                                         available or not..if available, send a mail regarding reset the password
	* @param	array username
	* @return   array response
	*
	**/
	public function forgotPasswordValidation( $values )
	{
		$email 		= new User_Api_Email();
		$userDetails= $userDetails	= $this->_userTableObj->getByUsername($values['username'], true);
		try
		{
			if($userDetails != null)
			{
				# send an activation mail, if user account not activate
				if( $userDetails['active'] == 0  )
				{
					$activationKey = $userDetails['userId'].'_'.$userDetails['activationKey'];
					$email->sendRegisterMail($values['username'], $activationKey);
					$message = "Details has been sent to your email address with the steps to access your account.";
				}
				else
				{
					# Send the new password to user's mail
					$passwordActivationKey		= $userDetails['userId'].'_'.$userDetails['activationKey'].'_'.$userDetails['lastLoginDate'];

					$email->setSubject('Your Request to Reset your password.');
					$email->sendforgotPasswordMail($userDetails['username'], $passwordActivationKey, $userDetails['firstName']);
					$message = "Reset password Details has been sent to your email address to access your account.";
				}
				$response['status'] 	= 'true';
				$response['message'] 	= $message ;
				return $response;
			}
			else
			{
				$response['status'] 	= 'false';
				$response['message'] 	= 'Username entered is incorrect/invalid' ;
				return $response;
			}
		}
		catch( Exception $e )
		{
			$message =  $e->getMessage(); //'Email id already exists.';
			# Ajax Response
			if( $this->getRequest()->isXmlHttpRequest())
			{
				$response['result'] 	= 'failure';
				$response['message'] 	= $message;
				
				return $response;
			}
		}
	}

};