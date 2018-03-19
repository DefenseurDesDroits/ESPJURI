/* +--------------------------------------------------------------------------+
// 2017 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: set-grid-main-uncolored.js,v 1.1.2.10 2017-03-22 09:01:41 wlair Exp $ */
// grid test
$(document).ready(function(){
	$("#main_hors_footer").addClass(function(){
		return ($('#grid-init').length) ? "uk-grid uk-grid-medium" : ''; 
	});
	$("#main_hors_footer").attr("data-uk-grid-margin","");
	$("#intro").addClass("uk-width-1-1");
	$("#footer").addClass("uk-width-1-1");	
	$("#container").addClass("uk-grid uk-grid-collapse uncolored");
	$("#container").attr("data-uk-grid-margin","");
	var bandeauHasChilds = function(){
		var bandeau = document.getElementById('bandeau');
		if(bandeau){
			var bandeauChilds = bandeau.children;
			for(var i=0 ; i<bandeauChilds.length ; i++){
				if(
				bandeauChilds[i].getAttribute('id') != 'accueil' && 
				bandeauChilds[i].getAttribute('id') != 'adresse' &&
				bandeauChilds[i].getAttribute('class') != 'cmsNoStyles' &&  
				bandeauChilds[i].getAttribute('type') != 'text/javascript'){
					return true;
				}
			}
			return false;
		}
		return false;
	}
	if(bandeauHasChilds() == false){
		//Soit bandeau n'est pas présent dans la page
		//Soit bandeau est présent mais n'as pas d'autres enfants que #accueil et #adresse
		$("#main").addClass("uk-width-1-1");
		$("#bandeau").addClass("uk-width-1-1 ready");
		$("#bandeau").css({"margin": "0", "padding": "0px", "border": "none"});
		
	}
	else if(bandeauHasChilds() == true){
		$("#bandeau").addClass("uk-width-large-1-4 uk-width-medium-1-3 uk-width-small-1-1 ready");	
		$("#main").addClass("uk-width-large-3-4 uk-width-medium-2-3 uk-width-small-1-1");
		$("#container>#bandeau:nth-child(3)").addClass("is-on-right-side");
		$("#container>#bandeau:nth-child(2)").addClass("is-on-left-side");
	}
	//full width
		var fullW = new Array();
		fullW.push("#main_hors_footer>div");
		for (key in fullW){
			$(fullW[key]).addClass("uk-width-1-1 wl-width-custom");
		}
	$("#main").addClass(function(){
		return ($('#home-tracker').length) ? "on-home" : ''; 
	});		
	$(".notice_corps").addClass(function(){
		return ($('div#cart_action').length) ? "no-right-content" : ''; 
	});
	$("body").addClass("ready");
});