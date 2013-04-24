<?php
/**
 * @file		Abstract.php
 *
 * @category    Compassites
 * @package    	Compassites_Model_DbTable
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Db table abstract class definition for multiple database access
 *
 * @package    	Compassites_Model_DbTable
 * @class		Compassites_Model_DbTable_Abstract
 * @see
 */
abstract class Compassites_Model_DbTable_Abstract extends Zend_Db_Table
{
	public function __construct($config = null)
	{
        if( isset($this->_use_database) )
		{
            $dbAdapters	= Zend_Registry::get('dbAdapters');
            $config 	= $dbAdapters[$this->_use_database];
        }

        return parent::__construct($config);
	}
};