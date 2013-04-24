<?php
/**
 * @file		SetLayout.php
 *
 * @category   	Compassites
 * @package    	Compassites_Plugin
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2012 Compassites (http://www.compassitesinc.com/)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		SetLayout Plugin class defination
 *
 * @package    	Compassites_Plugin
 * @class		Compassites_Plugin_SetLayout
 * @see
 */
class Compassites_Plugin_SetLayout extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        $layout = Zend_Layout::getMvcInstance();

        # check module and automatically set layout
        $layoutsDir = $layout->getLayoutPath();
        # check if module layout exists else use default
        if(file_exists($layoutsDir . DIRECTORY_SEPARATOR . $module . '.phtml'))
        {
            $layout->setLayout($module);
        }
    }
};