/**
* @file			compassites.acl.js
*
* @package    	Javascript
* @author     	Compassites Team
* @copyright  	Copyright (c) 2011 Compassites (http://www.compassitesinc.com)
*
* @version		SVN: $Id: $
*/
compassites.acl = function ()
{

	function deleteRecord(thisObj)
	{
		var sendData = jQuery(thisObj).attr('aclId');
		
		jQuery.ajax(
		{
			url			: linkBaseUrl + '/admin/acl/delete/',
			type		: 'POST',
			dataType	: 'JSON',
			data		: "aclId=" + parseInt(sendData),
			success		: function(response)
							{ 		
								deleteRecordCallback(response, sendData);	
							},
			error		: function(errorObject, ajaxOptions, thrownError)
							{
								jQuery(".message").append("Unknown error");
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