/**
 * @file		compassites.navigation.js
 *
 * @package    	Javascript
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */
compassites.navigation = function ()
{

	function deleteRecord(thisObj)
	{		
		var sendData = jQuery(thisObj).attr('navigationId');
		
		jQuery.ajax(
		{ 
			url			: linkBaseUrl + '/admin/navigation/delete/',
			type		: 'POST',
			dataType	: 'JSON',
			data		: "navigationId=" + parseInt(sendData),
			success		: function(response)
							{
								deleteRecordCallback(response, sendData);
							},
			error		: function(errorObject, ajaxOptions, thrownError)
							{
								jQuery(".message").html("Unknown error");
								setTimeout( "jQuery('.message').hide();",3000 );
							}
	    });
	}
	
	function deleteRecordCallback(response, sendData)
	{
		if( response > 0 )
		{
			jQuery('#recordSet_'+parseInt(sendData)).hide();
			jQuery(".message").html("Removed Successfully");
			setTimeout( "jQuery('.message').hide();",3000 );
		}
		else
		{
			jQuery(".message").html("Remove failed");
			setTimeout( "jQuery('.message').hide();",3000 );
		}
	}
	
	return {
		'deleteRecord' 			: deleteRecord
	}

}();