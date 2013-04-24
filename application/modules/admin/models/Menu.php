<?php
/**
 * @file		Menu.php
 *
 * @category   	Admin
 * @package    	Admin_Model
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2010 Compassites (http://www.compassitesinc.com/)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Menu Model class definition
 *
 * @package    	Admin_Model
 * @class		Admin_Model_Menu
 * @see
 */
class Admin_Model_Menu extends Compassites_Model_Abstract
{
	public function __construct()
	{
		$this->setDbTable('Admin_Model_DbTable_Menu');
	}
};