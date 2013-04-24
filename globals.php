<?php
/**
 * @file		globals.php
 *
 * @category   	Compassites
 * @package		Constants
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

/** Output buffering */
ob_start();

/** xhtml validation */
//header('Content-type: text/xml');

/** Define the app name, used mostly for cachekey */
define('APPLICATION_NAME', 'COMPASSITES');

/** Define path to application base directory */
define('APPLICATION_ROOT', realpath( dirname(__FILE__) ));

/** Define path to application directory */
define('APPLICATION_PATH', APPLICATION_ROOT .'/application');

/** Define application environment */
//define('APPLICATION_ENV', getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production');
define('APPLICATION_ENV', 'development');

/** Ensure library is in include_path */
set_include_path(
	implode( PATH_SEPARATOR, array(
			'/var/www/toolkit',
			//get_include_path()
		)
	)
);

/**
* @brief	Minify HTML
*
* @param  	string	html string
* @return 	the minified html string
*/
function sanitizeOutput($buffer)
{
	# disable minification for development environment
	if( APPLICATION_ENV == 'development' )
	{
		return $buffer;
	}

    $search = array(
        '/\>[^\S ]+/s',	# strip whitespaces after tags, except space
        '/[^\S ]+\</s',	# strip whitespaces before tags, except space
        '/(\s)+/s'  	# shorten multiple whitespace sequences
	);
    $replace = array(
        '>',
        '<',
        '\\1'
    );

	return preg_replace($search, $replace, $buffer);
}
