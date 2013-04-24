<?php
/**
 * @file		Bootstrap.php
 *
 * @category   	Compassites
 * @package    	Bootstrap
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Acl DbTable class
 *
 * @package    	Bootstrap
 * @class		Bootstrap
 * @see
 */
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{

	protected function _initAutoload()
    {
 /*       $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath'      => realpath(dirname(__FILE__)),
            'namespace'     => 'admin',
            'resourceTypes' => array(
                'api' 		=> array(
                    'path'  	=> 'api/',
                    'namespace' => 'Api'
                )
            )
        ));
*/
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'admin',
            'basePath'  => dirname(__FILE__),
            //'resourceloader' => $resourceLoader
        ));
    }

};