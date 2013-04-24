<?php
/**
 * @file		Navigation.php
 *
 * @category   	Admin
 * @package    	Admin_Model
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2010 Compassites (http://www.compassitesinc.com/)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Admin Navigation Model class definition
 *
 * @package    	Compassites_Model_Abstract
 * @class		Admin_Model_Navigation
 * @see
 */
class Admin_Model_Navigation extends Compassites_Model_Abstract
{
	public function __construct()
	{
		$this->setDbTable('Admin_Model_DbTable_Navigation');
	}

	/**
     * @brief	Get Section Values
     *
     * @return 	array	section array
     */
	public function getSections()
	{
		$sections = array('admin'=>'Admin','main'=>'Main');

		return $sections;
	}

	/**
     * @brief	Get All Menu Items by Section
     *
     * @param 	string	section name
     * @param 	int		no of records to fetch [100]
     * @param 	boolean	disable cache [false]
     * @return 	Object
     */
	public function getAllBySection($section, $limit=100, $noCache=false)
    {
    	$dbTable 	= $this->getDbTable();
		$cacheKey	= APPLICATION_NAME . get_class($dbTable) . "_getAllBySection_{$section}_{$limit}";
		$cache		= Zend_Registry::get('cache');
		$data		= array();

		if( (!$data = $cache->load($cacheKey)) || ($noCache == true) )
		{
			$select = $dbTable->select()->limit($limit);
			$select = $select->where('`section` = ? AND `parentId` = 0', $section);
			$sectionData 	= $dbTable->fetchAll($select)->toArray();

			foreach($sectionData as $temp)
			{
				$data[$temp['navigationId']] = $temp;
				$data[$temp['navigationId']]['children'] = $this->getAllByParent($temp['navigationId']);
			}

			$cache->save($data, $cacheKey);
		}

		return $data;
    }

	/**
     * @brief	Get All Menu Items by Parent Id
     *
     * @param 	int 	parent id [0]
     * @param 	int 	no of records to fetch [100]
     * @param 	boolean	disable cache [false]
     * @return 	Object
     */
	public function getAllByParent($parentId=0, $limit=100, $noCache=false)
    {
    	$dbTable	= $this->getDbTable();
		$data		= array();
		$cacheKey	= APPLICATION_NAME . get_class($dbTable) . "_getAllByParent_{$parentId}_{$limit}";
		$cache		= Zend_Registry::get('cache');

		if( (!$data = $cache->load($cacheKey)) || ($noCache == true) )
		{
			$select = $dbTable->select()->limit($limit);
			$select = $select->where('`parentId` = ?', $parentId);
			$data 	= $dbTable->fetchAll($select)->toArray();

			$cache->save($data, $cacheKey);
		}

		return $data;
    }
};