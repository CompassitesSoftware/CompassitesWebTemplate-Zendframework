<?php
/**
 * @file		Subscriber.php
 *
 * @category   	Default
 * @package    	Compassites_Model
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2010 Compassites (http://www.compassitesinc.com/)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief 		Subscriber Model class definition
 *
 * @package    	Compassites_Model
 * @class		Compassites_Model_Subscriber
 * @see
 */
class Compassites_Model_Subscriber extends Compassites_Model_Abstract
{
	public function __construct()
	{
		$this->setDbTable('Compassites_Model_DbTable_Subscriber');
	}
};