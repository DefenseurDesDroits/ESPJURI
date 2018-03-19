// responsive toggle search 
$(document).ready(function(){
	var touch = function(){  
		try{  
			document.createEvent("TouchEvent");  
			return true;  
		} catch(e){  
			return false;  
		}  
	}	
	if( touch() == true){
		$("#cms_module_search_22").addClass("hide");
	}
});