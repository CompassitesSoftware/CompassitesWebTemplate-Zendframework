<?php
/**
 * @file		Bootstrap.php
 *
 * @category   	user
 * @package    	Bootstrap
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2012 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id:  $
 */

/**
 * @brief 		Acl DbTable class
 *
 * @package    	user
 * @class		Bootstrap
 * @see
 */
class User_Bootstrap extends Zend_Application_Module_Bootstrap
{
	protected function _initAutoload()
    {
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath'      => realpath(dirname(__FILE__)),
            'namespace'     => 'user',
            'resourceTypes' => array(
                'api' 		=> array(
                    'path'  	=> 'api/',
                    'namespace' => 'Api'
                )
            )
        ));

        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'user',
            'basePath'  => dirname(__FILE__),
            'resourceloader' => $resourceLoader
        ));

        return $autoloader;
    }

    protected function _initRegistry()
    {
        # current user
        $user = User_Model_User::getCurrentUser();
        Zend_Registry::set('user', $user);
    }
};