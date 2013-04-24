/**
 * @file		navigation.js
 *
 * @package    	Javascript
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */
<?php
	loadfile('compassites.navigation.js');
?>
jQuery(document).ready(
    function()
    {
		jQuery('.deleteRecord').click(
    		function(){
				
				var confirmResult = confirm("Are you sure, you want to delete?");

				if(confirmResult)
				{
					compassites.navigation.deleteRecord(this);
				}
				
				return false;
    		}
    	);
    }
);