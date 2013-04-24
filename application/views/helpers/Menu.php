<?php
/**
 * @file		Menu.php
 *
 * @category   	Compassites
 * @package    	Zend_View_Helper
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2010 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/**
 * @brief	menu view helper
 *
 * @see
 */
class Compassites_View_Helper_Menu extends Zend_View_Helper_Abstract
{
    /**
     * @brief	Request Object
     *
     * @var 	Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * menu separator
     *
     * @var string
     */
    protected $_separator = '&rsaquo;';

    /**
     * menu
     *
     * @var array
     */
    protected $_menu = array();


    /**
     * module/section
     *
     * @var array
     */
    protected $section = 'default';

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
		$fc 			= Zend_Controller_Front::getInstance();
        $this->_request	= $fc->getRequest();
    }

    /**
     * Set the menu separator
     *
     * @param string $separator
     */
    public function setSeparator($separator)
    {
        $this->_separator = $separator;
    }

    /**
     * Set custom menu
     *
     * @param array $menu
     * @return My_View_Helper_menu
     */
    public function set(array $menu)
    {
        $this->_menu = $menu;

        return $this;
    }

    /**
     * menu
     *
     * @param	string	$section	module / section name
     * @param	array	$menu	Set a custom menu
     * @return 	My_View_Helper_menu
     */
    public function menu($section='default', array $menu = array())
    {
		$this->section = $section;

        if (empty($this->_menu))
        {
            if (!empty($menu))
            {
                $this->set($menu);
            }
            else
            {
       			$menuModel	= new Admin_Model_Navigation();
				$this->_menu=  $menuModel->getAllBySection($section, 100);
            }
        }

        return $this;
    }

    /**
     * Compile and output the menu
     *
     * @return string
     */
    public function __toString()
    {
		$menuString = "<ul id='menu' class='menu {$this->section}'>";
		foreach ($this->_menu as $menu)
        {
			$class = '';
			if( $this->_request->getModuleName() == $menu['module'] && $this->_request->getControllerName() == $menu['controller'] && $this->_request->getActionName() == $menu['action'] )
			{
				$class = 'active';
			}

			$url = $this->view->url( array('module' => $menu['module'], 'controller' => $menu['controller'], 'action' => $menu['action'], 'default', true) );

			$menuString .= "\n<li><a href='{$url}' class='{$class}'>" . ucfirst($menu['name']) . '</a></li>';
		}
		$menuString .= '</ul>';

		return $menuString;
    }
};