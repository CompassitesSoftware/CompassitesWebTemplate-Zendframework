/**
 * @file		acl.js
 *
 * @package    	Javascript
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */
<?php
	loadfile('compassites.acl.js');
?>
jQuery(document).ready(	
    function()
    {
		jQuery('.deleteRecord').click(
    		function(){
				
				var confirmResult=confirm("Are you sure, you want to delete?");

				if(confirmResult)
				{
					compassites.acl.deleteRecord(this);
				}
				
				return false;
    		}
    	);

    }
);