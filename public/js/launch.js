/**
 * @file		launch.js
 *
 * @package    	Javascript
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */

<?php
	loadFile('compassites.launch');
?>
jQuery(document).ready(
    function()
    {
    	// init
    	compassites.launch.init();

		// feeds filter management
		jQuery('.feeds-filter li').click(
			function()
			{
				compassites.launch.filterFeeds(this);
			}
		);
    }
);