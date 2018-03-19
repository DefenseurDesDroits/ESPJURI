// JavaScript Document// JavaScript Document
$(document).ready(function(){
	if($( window ).width() <768) {
		$("#connexion").addClass("uk-hidden");	
		$("#cms_module_search_22").addClass("uk-hidden");
		}
		else {
		$("#connexion").removeClass("uk-hidden");	
		$("#cms_module_search_22").removeClass("uk-hidden");
	}		
	//grid
		var grid = new Array();
		//grid.push("#main_hors_footer");
		grid.push("#intro");
		grid.push("#footer");	
		for (key in grid){
			$(grid[key]).addClass("uk-grid");
			$(grid[key]).attr("data-uk-grid-margin","");
		}	
	//full width
		var fullW = new Array();
		fullW.push("#intro");
		fullW.push("#footer");	
		for (key in fullW){
			$(fullW[key]).addClass("uk-width-1-1");
		}
	// toggle facette responsice 
		if(document.getElementById('facette_wrapper')){$("#tgle-facette").removeClass("uk-hidden")}
	
					
	$("#intro").addClass("uk-grid-collapse");
	$("#footer").addClass("uk-grid-collapse");
	$("#search").addClass("uk-width-1-1");	
	$("#container").addClass("wyk-grid uk-grid uk-grid-collapse");
	$("#bandeau").addClass("uk-width-large-1-4 uk-width-medium-1-3 uk-width-small-1-1");	
	$("#main").addClass("uk-width-large-3-4 uk-width-medium-2-3 uk-width-small-1-1");
	$("#main_hors_footer").addClass("wyk-sub-grid");
	$("div[id^='record_container_'].parentNotCourte").addClass("uk-clearfix");
	$("#blocNotice_descr>div").addClass("uk-clearfix");
	$(".socialNetworkN").addClass("uk-clearfix");
	$("div.avisN").addClass("uk-flex");
	
	
	// calendar 
	$(".cms_module_agenda>div.row").first().addClass("uk-clearfix");
	
	// responsive 
	// table
	$(".localisation").attr("data", function(){ return $(".collstate_header_location_libelle").text() });
	$(".emplacement_libelle").attr("data", function(){ return $(".collstate_header_emplacement_libelle").text() });
	$(".cote").attr("data", function(){ return $(".collstate_header_cote").text() });	
	$(".type_libelle").attr("data", function(){ return $(".collstate_header_type_libelle").text() });
	$(".statut_opac_libelle").attr("data", function(){ return $(".collstate_header_statut_opac_libelle").text() });	
	$(".state_collections").attr("data", function(){ return $(".collstate_header_state_collections").text() });
	$(".origine").attr("data", function(){ return $(".collstate_header_origine").text() });
	$(".archive").attr("data", function(){ return $(".collstate_header_archive").text() });
	$(".lacune").attr("data", function(){ return $(".collstate_header_lacune").text() });	
	$(".location_libelle").attr("data", function(){ return $(".expl_header_location_libelle").text() });	
	$(".section_libelle").attr("data", function(){ return $(".expl_header_section_libelle").text() });
	$(".tdoc_libelle").attr("data", function(){ return $(".expl_header_tdoc_libelle").text() });	
	$(".expl_cote").attr("data", function(){ return $(".expl_header_expl_cote").text() });
	$(".Code-barres").attr("data", function(){ return $(".expl_header_expl_cb").text() });

	$(".location_libelle").attr("data", function(){ return $(".expl_header_location_libelle").text() });	
	$(".section_libelle").attr("data", function(){ return $(".expl_header_section_libelle").text() });
	$(".tdoc_libelle").attr("data", function(){ return $(".expl_header_tdoc_libelle").text() });	
	$(".expl_cote").attr("data", function(){ return $(".expl_header_expl_cote").text() });	
	
	$(".Localisation").attr("data", function(){ return $(".expl_header_location_libelle").text() });	
	$(".Section").attr("data", function(){ return $(".expl_header_section_libelle").text() });
	$(".Support").attr("data", function(){ return $(".expl_header_tdoc_libelle").text() });	
	$(".Cote").attr("data", function(){ return $(".expl_header_expl_cote").text() });	
	$(".Disponibilité").attr("data", function(){ return $(".expl_header_statut").text() });	

		
	// user section
	$(".tab_empr_info_cb td+td").attr("data", function(){ return $(".tab_empr_info_cb .etiq_champ").text() });
	$(".tab_empr_info_year td+td").attr("data", function(){ return $(".tab_empr_info_year .etiq_champ").text() });
	$(".tab_empr_info_adh td+td").attr("data", function(){ return $(".tab_empr_info_adh .etiq_champ").text() });
	


	//-------------------


});