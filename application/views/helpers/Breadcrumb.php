<?php
/**
 * @file		Breadcrumb.php
 *
 * @category   	Homeconnect
 * @package    	Zend_View_Helper
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: Breadcrumb.php 629 2012-02-03 06:49:41Z ce163 $
 */

/**
 * @brief		Breadcrumb view helper
 *
 * This helper is automated and uses the
 * current module, controller & action to
 * generated the breadcrumb. A custom breadcrumb
 * can be specified, using the set method.
 *
 * @todo Refactor code to allow building of a "path"
 * to current location. Use this in conjuction with
 * "path building" idea to subcategorise controllers
 * and still have a nice breadcrumb trail.
 *
 * @author 		Steven Bakhtiari <steven@ctisn.com>
 * @package    	Zend_View_Helper
 * @class		Compassites_View_Helper_Breadcrumb
 * @see
 */
class Compassites_View_Helper_Breadcrumb extends Zend_View_Helper_Abstract
{
    /**
     * @brief	Request Object
     *
     * @var 	Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @brief	Breadcrumb separator
     *
     * @var 	string
     */
    protected $_separator = '&rsaquo;';

    /**
     * @brief	Breadcrumb
     *
     * @var 	array
     */
    protected $_breadcrumb = array();

    /**
     * @brief	Constructor
     *
     * @return 	void
     */
    public function __construct()
    {
        $fc 			= Zend_Controller_Front::getInstance();
        $this->_request	= $fc->getRequest();
    }

    /**
     * @brief	Set the breadcrumb separator
     *
     * @param 	string 	$separator
     */
    public function setSeparator($separator)
    {
        $this->_separator = $separator;
    }

    /**
     * @brief	Set custom breadcrumb
     *
     * @param 	array 	$breadcrumb
     * @return 	My_View_Helper_Breadcrumb
     */
    public function set(array $breadcrumb)
    {
        $this->_breadcrumb = $breadcrumb;

        return $this;
    }

    /**
     * @brief	breadcrumb
     *
     * @param 	array 	$breadcrumb Set a custom breadcrumb
     * @return 	My_View_Helper_Breadcrumb
     */
    public function breadcrumb(array $breadcrumb = array())
    {
        if (empty($this->_breadcrumb))
        {
            if (!empty($breadcrumb))
            {
                $this->set($breadcrumb);
            }
            else
            {
                $module     = $this->_request->getModuleName();
                $controller = $this->_request->getControllerName();
                $action     = $this->_request->getActionName();

				if ($module != 'default')
				{
					$this->_breadcrumb[] = array(
						'title' => $module,
						'url' => $this->view->url(array('module' => $module), 'default', true)
					);
				}

				if ($controller != 'index')
				{
					$this->_breadcrumb[] = array(
						'title' => $controller,
						'url' => $this->view->url(array('module' => $module, 'controller' => $controller), 'default', true)
					);
				}

				if ($action != 'index')
				{
					$this->_breadcrumb[] = array(
						'title' => $action,
						'url' => $this->view->url(array('module' => $module, 'controller' => $controller, 'action' => $action), 'default', true)
					);
				}

				$count = count($this->_breadcrumb);
				if( $count > 0 )
				{
					$this->_breadcrumb[$count - 1]['url'] = null;
				}
            }
        }

        return $this;
    }

    /**
     * @brief	Compile and output the breadcrumb
     *
     * @return 	string
     */
    public function __toString()
    {
        if ( count($this->_breadcrumb) == 0 )
        {
            $breadcrumb = '';
        }
        else
        {
            $breadcrumb = '<ul id="breadcrumb" class="breadcrumb">';

            foreach ($this->_breadcrumb as $i => $bc)
            {
                $breadcrumb .= '<li>' . ($i != 0 ? '<span>' . $this->_separator . '</span>' : null);

                if ($bc['url'] === null)
                {
                    $breadcrumb .= ucfirst($this->view->escape($bc['title']));
                }
                else
                {
                    $breadcrumb .= '<a href="' . $bc['url'] . '">' . ucfirst($this->view->escape($bc['title'])) . '</a>';
                }

                $breadcrumb .= '</li>';
            }

            $breadcrumb .= '</ul>';
        }

        return $breadcrumb;
    }
};