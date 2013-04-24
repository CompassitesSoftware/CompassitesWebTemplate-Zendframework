/**
 * @file		user.validation.js
 *
 * @package    	Javascript
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */
<?php	
	loadfile('jquery.validationEngine.en.js');
	loadfile('jquery.validationEngine.js');
?>
jQuery(document).ready(
	function()
	{
		// binds form submission and fields to the validation engine
		jQuery("#activateform").validationEngine();
	}
);