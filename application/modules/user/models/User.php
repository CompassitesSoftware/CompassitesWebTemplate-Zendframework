<?php
/**
 * @file		User.php
 *
 * @category   	User
 * @package    	User_Model
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2010 Compassites (http://www.compassitesinc.com/)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		User Model class definition
 *
 * @package    	User_Model
 * @class		User_Model_User
 * @see
 */
class User_Model_User extends Compassites_Model_Abstract
{
	/**
	* @brief	Set Zend_Db_Table class name
	*
	*/
	public function __construct()
	{
		$this->setDbTable('User_Model_DbTable_User');
	}

    /**
     * @brief	Insert new user
     *
     * @param  	array $data insert data array
     * @return 	int   insert id
     */
	public function insert(array $data)
	{
		# Get salt From config
		$config		= Zend_Registry::get('config');
		$salt		= $config['auth']['salt'];
		$insertId	= 0;

		unset($data['user_id']);

		$data['password'] 	= md5( $salt . $data['password'] );
		$data['createdDate']= date('Y-m-d h:m:s');

		try
		{
			$insertId = $this->getDbTable()->insert($data);
		}
		catch(Exception $e )
		{
			//
		}

		return $insertId;
	}

    /**
     * @brief	Update User
     *
     * @param  	array  $data update data (userid or username is must to update data)
     * @return 	int	no of affected/updated rows
     */
	public function update(array $data, $cleanUserCache=false)
	{
		$updatedRows 	= 0;
		$whereArray		= array();

		if( !empty($data['userId']) )
		{
			# update by userid
			$userId = (int) $data['userId'];
			unset($data['userId']);

			$whereArray = array('userId = ?' => $userId);
		}
		else if( !empty($data['username']) )
		{
			# update by username
			$username = (string) $data['username'];
			unset($data['username']);

			$whereArray = array('username = ?' => $username);
		}

		# check for password update
		if( empty($data['password']) )
		{
			unset($data['password']);
		}
		else
		{
			$config				= Zend_Registry::get('config');
			$salt				= $config['auth']['salt'];
			$data['password'] 	= md5( $salt . $data['password'] );
		}

		# only update if userid or username is set
		if( !empty($whereArray) )
		{
			$updatedRows = $this->getDbTable()->update($data, $whereArray);
			/*
			# clean up the user getcurrentuser cacheFast
			if( $cleanUserCache )
			{
				$userData 	= $this->get($userId);
				$cache		= Zend_Registry::get('cacheFast');
				$cacheKey	= APPLICATION_NAME . '_db_user_getCurrentUser_'.$userData['username'];
				$cache->remove($cacheKey);
			}
			*/
		}

		return $updatedRows;
	}

	/**
     * @brief	Get current user
     *
     * @return 	object 	current user object
     */
	public static function getCurrentUser( $noCache=false )
    {
    	$authInstance= Zend_Auth::getInstance();
		$userObj 	= $authInstance->getIdentity();
		$data		= null;
		$objectSelf = new self();

		if( !empty($userObj ) )
		{
			$username	= $userObj;
			$dbTable 	= $objectSelf->getDbTable();
			$cacheKey	= str_replace('.', '_', $username);
			$cacheKey	= str_replace('@', '_', $cacheKey);
			$cacheKey	= APPLICATION_NAME . '_db_user_getCurrentUser_'.$cacheKey;
			$cache		= Zend_Registry::get('cacheFast15');
			$data		= null;

			if( !$data 	= $cache->load($cacheKey) )
			{
				$select	= $dbTable->select()->where('username = ?', $username);
				$data 	= $dbTable->fetchRow($select);
				$cache->save($data, $cacheKey);
			}
		}

		return $data;
    }

    /**
     * @brief	Get an User using username
     *
     * @param	string	$username
     * @return 	Array 	userData
     */
	public function getByUsername($username, $noCache=false)
    {
    	$dbTable 	= $this->getDbTable();
    	$username	= (string) $username;
		$cacheKey	= APPLICATION_NAME . '_db_user_getByUsername_'.md5($username);
		$cache		= Zend_Registry::get('cache');
		$userData	= null;

		if( (!$data = $cache->load($cacheKey)) || ($noCache == true) )
		{
			$select = $dbTable->select()->where('username = ?', $username);
			$data = $dbTable->fetchRow($select);
			$cache->save($data, $cacheKey);
			# NOTE at the time of register action no userId, so by default value will be null passed for userId
			if (!empty($data))
			{
				$userData	= $this->get($data->userId,true);
			}
		}

		return $userData;
    }

    /**
     * @brief	Get an User using openid
     *
     * @param	string	$openid
     * @return 	Array 	userData
     */
	public function getByOpenId($openid, $noCache=false)
    {
    	$dbTable 	= $this->getDbTable();
		$cacheKey	= APPLICATION_NAME . '_db_user_getByopenId_'.md5($openid);
		$cache		= Zend_Registry::get('cache');
		$userData	= null;

		if( (!$data = $cache->load($cacheKey)) || ($noCache == true) )
		{
			$select = $dbTable->select()->where('openid = ?', $openid);
			$data 	= $dbTable->fetchRow($select);
			$cache->save($data, $cacheKey);
			if (!empty($data))
			{
				$userData	= $this->get($data->userId,true);
				$userData 	= $userData->toArray();
			}
		}

		return $userData;
    }
};