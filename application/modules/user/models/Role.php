<?php
/**
 * @file		Role.php
 *
 * @category   	user
 * @package    	User_Model
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2010 Compassites (http://www.compassitesinc.com/)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Role Model class definition
 *
 * @package    	User_Model
 * @class		User_Model_Role
 * @see
 */
class User_Model_Role extends Compassites_Model_Abstract
{
	/**
	* @brief	Set Zend_Db_Table class name
	*
	*/
	public function __construct()
	{
		$this->setDbTable('User_Model_DbTable_Role');
	}

	public function getAssociativeArrayData($limit=100, $noCache=false)
    {
    	$dbTable 	= $this->getDbTable();
		$cacheKey	= APPLICATION_NAME . '_db_role_getAssociativeArrayData_sa'.$limit;
		$cache		= Zend_Registry::get('cache');
		$data		= array();

		if( (!$data = $cache->load($cacheKey)) || ($noCache == true) )
		{
			$select = $dbTable->select();
			$select->limit($limit);

			$rawData = $dbTable->fetchAll($select)->toArray();
			foreach ($rawData as $value)
			{
				$data[$value['role_id']] = $value;
			}

			$cache->save($data, $cacheKey);
		}

		return $data;
    }

    /**
	* @brief	Get user role except super admin
	*
	* @param 	boolean	disable cache [false]
	* @return 	array	data array
	*/
    public function getNonAdminRole( $noCache=false )
    {
		$dbTable	= $this->getDbTable();
		$data		= array();
		$cacheKey	= APPLICATION_NAME . '_db_role_getNonAdminRole';
		$cache		= Zend_Registry::get('cache');

		if( (!$data = $cache->load($cacheKey)) || ($noCache == true) )
		{
			$select		= $dbTable->select()->where('roleId > 2');
			$data		= $dbTable->fetchAll($select)->toArray();

			$cache->save($data, $cacheKey);
		}

		return $data;
    }
};