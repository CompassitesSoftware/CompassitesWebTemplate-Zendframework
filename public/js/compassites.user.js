<?php

/**
 * @file		compassites.user.js
 *
 * @package    	Javascript
 * @author     	Compassites Team
 * @copyright  	Copyright (c) 2013 Compassites (http://www.compassitesinc.com)
 *
 * @version		SVN: $Id: $
 */
?>
compassites.user = function ()
{
	function init()
	{
		jQuery.getJSON(linkBaseUrl + '/admin/user/getjson', null, function(data) {
            if (data != null) 
			{
				var roleOptions = '';
                jQuery.each(data,
					function(index)
					{
						roleOptions +=  index + ":" + data[index] + ';';
					});
				roleOptions += '~';
				roleOptions = roleOptions.replace(';~', '');
				buildGrid(roleOptions);
            }
        });
	}
	
	function buildGrid(roleOptions)
	{
		jQuery('#list').jqGrid(
			{
				url		:linkBaseUrl + '/admin/user/index',
				datatype: 'json',
				width	:jQuery(window).width() - 352,
				rowNum	:10,
				rowList	: [10,20,30],
				colNames:['User Id', 'Email', 'First Name',  'Last Name', 'Contact No', 'Mobile No', 'Address Line 1', 'Address Line 2', 'Area', 'City', 'Pincode', 'Role', 'Actions'],
				colModel:[
							{name:'userId', index:'userId', width:30, sortable:true, editable:false, sorttype:'int', search : false},
							{name:'username', index:'username', sortable:true, search:true, editable:true, editrules: { required: true}},
							{name:'firstName', index:'firstName', width:80, editable:true, editrules: { required: true}},
							{name:'lastName', index:'lastName', width:80, editable:true},
							{name:'landLine', index:'landLine', width:80, editable:true, editrules: { required: true}},
							{name:'mobile', index:'mobile', width:80, editable:true},
							{name:'addressLine1', index:'addressLine1',  editable:true},
							{name:'addressLine2', index:'addressLine2', editable:true},
							{name:'area', index:'area', width:80, editable:true},
							{name:'city', index:'city', width:80, editable:true},
							{name:'pincode', index:'pincode', width:80, editable:true},
							{name:'title', index:'roleId', width:80, search:false, editable:true, edittype:'select', editoptions:{ value:roleOptions}},
							{name:'act', index:'action', sortable:false,search:false, },
						],
				jsonReader: { repeatitems:false, id:'userId' },
				scrollOffset:1,
				pager: '#pager',
				viewrecords: true,
				editurl: linkBaseUrl + '/admin/user/edituser',
				sortorder: 'asc',
				sortname: 'userId',
				caption: 'User Details',
				gridComplete: function()
							{
								var ids = jQuery('#list').jqGrid('getDataIDs');
								userActions(ids);
							}
			}
		);
		//footer options like search,export and pagination
		jQuery('#list').jqGrid('navGrid', '#pager', {edit:false, add:false, del:false }, {}, {}, {}, { multipleSearch:false, caption: 'User Details Search...', Find: 'Search', Reset: 'Reset', sopt:['cn']} );
		//export the user details to CSV files
		jQuery('#list').jqGrid('navButtonAdd','#pager',
		{ 	  caption:'Export',
			  onClickButton : function ()
			  {
				  jQuery('#list').jqGrid('excelExport',
					{
						url: linkBaseUrl+'/admin/user/export'
					});
			  }
		});
	}
	// edit,disable buttons generation
	function userActions(ids)
	{
		for(var i=0;i < ids.length;i++)
		{
			var userId 	= ids[i];
			// edit button
			edit	 = "<input style='height:22px;width:50px;' type='button' value='Edit' onclick=\"jQuery('#list').editGridRow('"+userId+"');\" />";
			jQuery("#list").jqGrid('setRowData', ids[i], {act:edit});
		}
	}

	return {
		'init' : init
	}
}();