$(document).ready(function() {
	
	// Forces all textareas/inputs with a class /int to allow allow numbers in the field
	$(".int").numeric();
	
	// Site navigation dropdown menus
	$('#nav li.cdd').hover(function(){
		$('#nav li.cdd ul.dd').slideUp(150); // hide all other dd
		$(this).children('#nav li.cdd ul.dd').slideDown('fast');
	}, function(){
		$(this).oneTime(250, function() {
			$(this).children('#nav li.cdd ul.dd').slideUp('fast');
		});
	});
	
	// Clear Inpt of Text
	$(".clr-txt").focus(function(){
	   if (this.value == this.defaultValue){
		  this.value = '';
	   }
	});
	
	// hide Site Error Warning 
	$(".err-close").click(function() {		 
		$(this).parent().slideUp("slow");
	});
	
	// Confirm action
	$(".confirm").click(function(){
		var answer = confirm("Are you sure you want to do this?");

		if(answer==true) {
			return true;
		} else {
			return false;
		}
	});
	
	// Confirm Hard Delete
	$(".harddel").click(function(){
		var answer = confirm("Are you sure you want to delete this forever?");

		if(answer==true) {
			return true;
		} else {
			return false;
		}
	});
	
	// Confirm Logout
	$(".logout").click(function(){
		var answer = confirm("Are you sure you want to logout of Echelon?");

		if(answer==true) {
			return true;
		} else {
			return false;
		}
	});
	
	// Game Settings //
	$('#change-pw-box').hide();
	
	$('#cng-pw').click(function(){
		if ($('#cng-pw:checked').val() == 'on') {
			$("#change-pw-box").slideDown();
		} else {
			$("#change-pw-box").slideUp();
		}
	});
	
	// Site Admin Page JS //
	
	$(".edit-key-comment").show();
	// Edit key reg comment
	$(".edit-key-comment").click(function(){
			
		thisItem = $(this);
		
		var td = thisItem.parent();			 
		var comment = td.find("span.comment");
		var commentText = comment.text(); // get the comment text
		
		var key = td.parent().find("td.key").text();
		
		thisItem.fadeOut("fast"); // fade out the edit button
		comment.fadeOut("fast"); 
		
		td.append('<form action="" method="post" style="display: none;" onSubmit="return false;" class="edit-comment-form"><input type="text" name="comment" id="comment-text-box" value="' + commentText + '" /><input type="hidden" name="key" value="' + key + '" id="key-input" /></form>');
		
		$(".edit-comment-form").slideDown("slow"); // slide in the form since the form is usually large than the table row so the animation makes the form addition less jerky when added
			
		$(".edit-coment-form").submit(function(){
			return false;			  
		});
		
		$('#comment-text-box').blur(function(){
										   
			var newText = $(this).val();
			var key = $("#key-input").val();
			
			var dataString = 'key=' + key + '&text=' + newText;
			
			// Troubleshooting
			//alert(dataString);
			//return false;
			
			$.post("actions/key-edit.php", { comment: newText, key: key}, function(data){
																				 
				if(data.length >0) {
					
					$(".edit-comment-form").remove(); // remove the form
					comment.slideDown(); // unhide the comment
					$('.edit-key-comment').show(); // reshow the edit button
					
					// Add success/error message to the body of the page
					if(data=='yes') {
						comment.text(newText); // update the comment on the page with the submitted text
						$("#content").prepend('<div class="success" id="msg"><strong>Success:</strong> Your comment has been updated</div>');
					} else if(data=='no') {						
						$("#content").prepend('<div class="error" id="msg"><strong>Error:</strong> Your comment has not been updated</div>');
					}
				}
				
			}); // end post
			
		}); // end onBlur

		return false;
			
	}); // end .edit-key-comment clikc
	
	// Check Username Function (registration setup user page)
	$("#uname-check").blur(function(){
		
		var loading = $(".loader").fadeIn("normal");
		var key = $("#key").val();
		
		
		$.post("actions/check-username.php",{ username:$(this).val() } ,function(data){
			loading.fadeOut('fast');
			$('div.result').removeClass('r-bad').removeClass('r-good');
			
			if(data=='no') {
				$('div.result').html('Username unavailable').addClass('r-bad').fadeTo(900,1);
			} else if(data=='yes') {
				$('div.result').html('Username available').addClass('r-good').fadeTo(900,1);
			} else {
				$('div.result').html('Name is required').addClass('r-bad').fadeTo(900,1);
			}
		});
		
	});
	
});

// Functions for search auto suggest
function suggest(iString){
	if(iString.length == 0) {
		$('#suggestions').fadeOut();
	} else {
		loading = $('#c-s-load');
		
		loading.css("visibility", "visible");
		$.get("actions/autosuggest.php", {s: ""+iString+""}, function(data){
			if(data.length >0) {
				$('#suggestions').fadeIn();
				$('#suggestionsList').html(data);
				loading.css("visibility", "hidden");
			}
		});
	}
}

function fill(thisValue) {
	$('#search').val(thisValue);
	setTimeout("$('#suggestions').fadeOut('normal');", 500);
}