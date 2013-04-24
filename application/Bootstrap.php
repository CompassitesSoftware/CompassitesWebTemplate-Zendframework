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
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function run()
    {
        parent::run();
    }

	protected function _initAutoload()
    {
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath'      => realpath(dirname(__FILE__)),
            'namespace'     => 'Compassites',
            'resourceTypes' => array(
                'api' => array(
                    'path'      => 'api/',
                    'namespace' => 'Api'
                )
            )
        ));
    }

    protected function _initFrontModules()
    {
		$this->bootstrap('frontController');
		$frontController = $this->getResource('frontController');
		$frontController->addModuleDirectory(APPLICATION_PATH . '/modules');
	}

    protected function _initConfig()
    {
        return new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        //return new Zend_Config(require 'configs/application.php');
    }

    protected function _initDoctype()
    {
        $this->bootstrap('view');

        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
	}

    protected function _initCache()
    {
        # config / the ini file options
        $config     = $this->getApplication()->getOptions();
        Zend_Registry::set('config', $config);

        # cache
        $cacheConfig = $config['resources']['cachemanager']['database'];
        $cache = null;
        try
        {
            $cache = Zend_Cache::factory('Core',
                                    $cacheConfig['backend']['name'],
                                    $cacheConfig['frontend']['options'],
                                    $cacheConfig['backend']['options']
                                );
        }
        catch( Exception $e )
        {
            //
        }
        Zend_Registry::set('cache', $cache);

        # APC cache
        $cacheApc = Zend_Cache::factory('Core', 'Apc', $cacheConfig['frontend']['options']);
        Zend_Registry::set('cacheFast', $cacheApc);

        # APC cache small time
        $cacheApc15 = Zend_Cache::factory('Core', 'Apc', $cacheConfig['frontend']['options15mins']);
        Zend_Registry::set('cacheFast15', $cacheApc15);

        # APC cache small time
        $cacheApc5 = Zend_Cache::factory('Core', 'Apc', $cacheConfig['frontend']['options5mins']);
        Zend_Registry::set('cacheFast5', $cacheApc5);
    }

    protected function _initDB()
    {
        $this->bootstrap('config');
        $config = $this->getResource('config');

		# db adapter
        $db = Zend_Db::factory($config->get(APPLICATION_ENV)->resources->db);
        $db->query('set names utf8');

		# multiple databases
		$databases	= $this->getPluginResource('db')->getOptions();

        $dbAdapters = array();
        foreach($databases['dbs'] as $adapterName=>$dbName)
        {
            $dbConfig   					= $databases;
            $dbConfig['params']['dbname']	= $dbName;
            $dbAdapters[$adapterName] 		= Zend_Db::factory($dbConfig['adapter'],$dbConfig['params']);

            if('default' == $adapterName)
            {
                Zend_Db_Table::setDefaultAdapter($dbAdapters[$adapterName]);
            }
        }

        Zend_Registry::set('dbAdapters', $dbAdapters);


        if ($this->hasResource('cache'))
        {
            $this->bootstrap('cache');
            $cache = $this->getResource('cache');
            Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        }

        return $db;
    }

    protected function _initRegistry()
    {
        # config / the ini file options
        $config     = $this->getApplication()->getOptions();
        Zend_Registry::set('config', $config);
    }

	protected function _initPlugins()
	{
		$layout 	= new Compassites_Plugin_SetLayout();
		$authCheck 	= new Compassites_Plugin_AuthCheck();

		$this->bootstrap('frontController');
		$frontController = $this->getResource('frontController');

		$frontController->registerPlugin($layout);
		$frontController->registerPlugin($authCheck);
	}

	protected function _initLog()
	{
		$options=  $this->getPluginResource('log')->getOptions();
		$file 	= str_replace('%d', date('Y-m-d'), $options['file']);

		# create the logger
		$logger = new Zend_Log();

		# create the writer
		$writer = new Zend_Log_Writer_Stream($file);

		# create the priority filter
		//$filter = new Zend_Log_Filter_Priority(Zend_Log::CRIT);
		//$writer->addFilter($filter);

		# add the writer to the logger
		$logger->addWriter($writer);

		Zend_Registry::set('Zend_Log', $logger);
		/*
		# logging examples
		$logger->info('info message');
		$logger->warn('warning message');
		$logger->err('error message');
		$logger->crit('critical message');
		*/
	}

	protected function _initZFDebug()
	{
		//if( (APPLICATION_ENV == 'development') )
		if(0)
		{
			$autoloader = Zend_Loader_Autoloader::getInstance();
			$autoloader->registerNamespace('ZFDebug');

			$options = array( 'plugins' =>
								array(
									'Auth',
									'Exception',
									'Registry',
									'Variables',
									'Html',
									'File' => array('base_path' => array(APPLICATION_ROOT, '/var/www/toolkit')),
									'Memory',
									'Time',
								)
							);

			# Instantiate the database adapter and setup the plugin.
			# Alternatively just add the plugin like above and rely on the autodiscovery feature.
			if ($this->hasPluginResource('db'))
			{
				//$this->bootstrap('db');
				$options['plugins']['Database']['adapter'] = Zend_Registry::get('dbAdapters');
			}

			# Setup the cache plugin
			if ($this->hasPluginResource('cachemanager'))
			{
				//$this->bootstrap('cachemanager');
				$cache = Zend_Registry::get('cache');

				$options['plugins']['Cache']['backend'] = $cache->getBackend();

				//echo '<pre>'; print_r(get_class_methods($cache)); echo '</pre>';
				//echo '<pre>'; print_r($cache->getOption('lifetime')); echo '</pre>';
			}

			$debug = new ZFDebug_Controller_Plugin_Debug($options);

			$this->bootstrap('frontController');
			$frontController = $this->getResource('frontController');
			$frontController->registerPlugin($debug);
		} // end if
	}

};