// Clientdetails Page JS Document

$(document).ready(function() {
	
	$('#cd-h-pen').click(function(){
		var thisItem = this;
		getPenInfo(thisItem, 'client');
	}); // #cd-h-pen click
	  
	$('#cd-h-admin').click(function(){
		var thisItem = this;
		getPenInfo(thisItem, 'admin');		
	}); // #cd-h-pen click  
	  
});

function formatError(msg) {
	
	return '<tr class="table-error"><td colspan="7"><span><strong>Error:</strong> ' + msg + '</span></td></tr>';
	
}

function getPenInfo(thisItem, type) {
	
	var table = $(thisItem).attr('rel');
	$("#cd-table-"+table).slideDown('slow');
	
	$(thisItem).children('.cd-open').hide();
	
	$(thisItem).parent().prepend('<h3 class="cd-h">Penalties</h3>');
	
	// after showing the empty table load in the data
	
	var loader = $('#cd-tr-load-'+table);
	
	var cid = $('#cd-table-'+table).attr('rel');
			
	var container = $('#contain-'+table);
	
	$(thisItem).remove();
	
	$.ajax({
		url: "inc/cd/penalties.php?id="+cid+"&type="+type,
		timeout: 7000,
		success: function(data){
			container.html(data);
		},
		error: function(req,error){
			if(error === 'error'){error = req.statusText;}
			
			// if the error was a timeouut then suppy a more informative error message
			if(error == 'timeout'){errormsg = 'There was a communication error, your request timed out.';}
			
			var errormsg = formatError(errormsg);
			container.html(errormsg);
		},
	}); // end ajax
	
}