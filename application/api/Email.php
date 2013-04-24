<?php
/**
 * @file		Email.php
 *
 * @category   	Compassites
 * @package    	Compassites_Api
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2012 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Email API class defination
 *
 * @package    	Compassites_Api
 * @class		Compassites_Api_Email
 * @see
 */
class Compassites_Api_Email
{
	protected $_subject	= 'Compassites ';
	protected $_mailInfo;
	protected $_from 	= 'support@compassitesinc.com';

	/**
     * @brief	Set the Email Subject
     *
     * @param 	String $subject
     * @return 	Void
     */
	public function setSubject($subject)
	{
		$this->_subject = $subject;
	}

	public function getMailInfo()
	{
		return $this->_mailInfo;
	}
	 
	/**
     * @brief	Set the Email From address
     *
     * @param 	String $from
     * @return 	Void
     */
	 public function setFrom($from)
	 {
	 	$this->_from = $from;
	 }

	/**
	  * @brief 	Send Mail
	  *
	  * @param	string toAddress, receiverName, fromAddress, senderName, mailSubject, mailContent
	  *
	  * @return bool
	  * @todo	log exceptions
	  */
	  public function sendMail($toAddress, $receiverName, $fromAddress, $mailSubject, $mailContent, $senderName='Compassites Support')
	  {
		try
		{
			# redirect all the testing mails
			if( APPLICATION_ENV != 'production' ) 
			{
				$toAddress = 'compassites.support@compassitesinc.com';
			}
			
			$mail = new Zend_Mail('utf-8');
			$mail->setBodyHtml($mailContent);
			$mail->setFrom($this->_from, $senderName);
			$mail->setReplyTo($fromAddress);
			$mail->addTo($toAddress, $receiverName);
			$mail->setSubject($mailSubject);
 			$mail->send();
		}
		catch (Zend_Mail_Exception $MailException)
		{
 			echo 'Email sending error <br />';
 			echo $MailException->getMessage();exit();
 			$this->error = $MailException->getMessage(); // if error in view custom error message is showed to user.
		}
		//echo $mailContent; exit;

		return ;
	}

	/**
     * @brief	Get the activation link
     *
     * @param
     * @return 	string 	content
     *
     */
	public function getActivationLink($controller, $action = 'activate', $activationKey= '' )
	{
	 	$helper 	= new Zend_view();
	 	$request 	= Zend_Controller_Front::getInstance()->getRequest();
	 	$code 		= md5('code');
	 	$codevalue 	= base64_encode($activationKey);
	 	$url 		= $helper->url(
								array('module'=>'user', 'controller'=>$controller, 'action'=>$action, $code=>$codevalue),
								'default',
								false
								);
	 	$hyperLink 	= $request->getScheme().'://'.$request->getHttpHost(). $url;
	 	$link 		= "<a href = '{$hyperLink}'>{$hyperLink}</a>";

	 	return $link;
	}
};