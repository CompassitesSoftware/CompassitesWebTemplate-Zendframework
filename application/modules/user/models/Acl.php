<?php
/**
 * @file		Acl.php
 *
 * @category   	user
 * @package    	User_Model
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2010 Compassites (http://www.compassitesinc.com/)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Acl Model class definition
 *
 * @package    	Compassites_Model_Abstract
 * @class		User_Model_Acl
 * @see
 */
class User_Model_Acl extends Compassites_Model_Abstract
{
	/**
	* @brief	Set Zend_Db_Table class name
	*
	*/
	public function __construct()
	{
		$this->setDbTable('User_Model_DbTable_Acl');
	}

	/**
	* @brief	Creates an ACL for a specific page
	*
	* @param 	string resource (controller-action)
	* @return 	Zend_Acl
	*/
    public static function getResource($module, $controller, $action)
    {
		# only controller as resource
		//return "{$controller}";

		# module + controller + action as resource
		return "{$module}_{$controller}_{$action}";
    }

	/**
     * @brief	Split resource as module, controller, action
     *
     * @param	string	seperate (module,controller,action)
     * @return 	Array 	result set array
     */
	public static function splitResource( $resources )
    {
		$resourceData				= list($module, $controller, $action) = explode('_', $resources);
		$resourceData['module']		= $module;
		$resourceData['controller'] = $controller;
		$resourceData['action']		= $action;

		return $resourceData;
    }

	/**
	* @brief	Creates an ACL for a specific page
	*
	* @param 	string resource (controller-action)
	* @return 	Zend_Acl
	*/
	public function getAcl($resource, $cache=true)
	{
		$dbTable 	= $this->getDbTable();
		$cacheKey	= APPLICATION_NAME . 'db_acl_getAcl_'.$resource;
		$cache		= Zend_Registry::get('cacheFast');
		$acl 		= new Zend_Acl();
		$privileges	= array();

		if( (!$acl = $cache->load($cacheKey)) || ($cache == false) )
		{
			$acl 	= new Zend_Acl();
			$select = $dbTable->select();
			$select->where('resource = ?', $resource);

			$privileges = $dbTable->fetchAll($select);

			foreach($privileges as $privilege)
			{
				// role
				$acl->addRole( new Zend_Acl_Role( $privilege['roleId']) );

				// resource
				//echo '<pre>'; print_r(get_class_methods($acl)); echo '</pre>';
				$resourceObj = new Zend_Acl_Resource($resource);

				if( !$acl->has($resourceObj) )
				{
					$acl->addResource( $resourceObj );
				}

				// privileges
				if( $privilege['allow'] )
				{
					$acl->allow( $privilege['roleId'], $resource);
				}
				else
				{
					$acl->deny( $privilege['roleId'], $resource);
				}
			}

			$cache->save($acl, $cacheKey);
		}

		return $acl;
	}

};