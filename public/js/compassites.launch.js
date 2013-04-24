/**
* @file			compassites.launch.js
*
* @package    	Javascript
* @author     	Compassites Team
* @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
*
* @version		SVN: $Id: $
*/
compassites.launch = function ()
{
	var counter = 1;

	function init()
	{
		jQuery('#enrollForm').validationEngine(
			{
				onValidationComplete: subscribe
			}
		);
	}

	function subscribe(form, status)
	{
		if(status == true)
		{
			jQuery.ajax(
				{
					url		: baseUrl + '/subscriber/subscribe/',
					type	: 'POST',
					dataType: 'JSON',
					data	: "enrollMail=" + jQuery('#email').val(),
					success	: handleEnrollResult
				}
			);
		}
	}

	function handleEnrollResult(response)
	{
		if(response.enrollMail > 0)
		{
			jQuery("#message").text("You Have been Subscribed successfully");
			jQuery("#message").show(500).delay(5000).hide(500);
			jQuery("#email").val('');
		}
		else
		{
			jQuery("#message").text(response.enrollMail.toString());
			jQuery("#message").show(500).delay(5000).hide(500);
		}
	}

	function filterFeeds(thisObj)
	{
		// implimentation
	}	

	return {
		'init' 			: init,
		'filterFeeds' 	: filterFeeds
	}
}();