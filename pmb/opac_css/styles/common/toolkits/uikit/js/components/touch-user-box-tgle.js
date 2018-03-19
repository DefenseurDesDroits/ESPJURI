// responsive toggle user quick acces
$(document).ready(function(){
	$("#connexion").addClass(function(){
		return ($("html").hasClass("uk-touch")) ? "hide" : ''; 
	});	
});