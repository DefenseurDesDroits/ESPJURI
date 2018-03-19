<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_test.php,v 1.2 2009-05-16 10:52:44 dbellamy Exp $

// Interface de demo pour exécuter une recherche en dehors de PMB
// Nécessite le fichier /includes/javascript/http_request.js pour exécuter les requêtes Ajax.

print "
<script type='text/javascript' src='./includes/javascript/http_request.js'></script>
<script language=\"JavaScript\">

var req_pmb_search;
var url_pmb_search;

function go_search() {
	// Récupération de la valeur de l'objet 'territoire' et 'theme'
	var territoire = document.search_test.territoire.value;
	var theme = document.search_test.theme.value;
	
	// Construction de la requete 
	url_pmb_search= \"./search.php?territoire=\" + territoire + \"&theme=\" + theme;
	
	// On initialise la classe de la requete ajax:
	req_pmb_search = new http_request();
	
	// Exécution de la requete en asynchrone. (url, post_flag ,post_param, async_flag, func_return, func_error,ident_req)
	req_pmb_search.request(url_pmb_search,false,'',true,callback_search);
}

function callback_search(){
	
	// Mise à jour des lien de l'opac
	var url_go_opac= url_pmb_search + \"&type=\";
	
	chaine=req_pmb_search.get_text();		
	var tab_nb_notices=chaine.split(';');

	var type_1=document.getElementById('type_1');
	type_1.innerHTML=tab_nb_notices[0]+ ' cartes liées au territoire';
	type_1.href= url_go_opac + '1';
	
	var type_2=document.getElementById('type_2');
	type_2.innerHTML=tab_nb_notices[1]+ ' cartes liées au thème';
	type_2.href= url_go_opac + '2';
			
	var type_3=document.getElementById('type_3');
	type_3.innerHTML=tab_nb_notices[2]+ ' documents liés au territoire';
	type_3.href= url_go_opac + '3';
			
	var type_4=document.getElementById('type_4');
	type_4.innerHTML=tab_nb_notices[3]+ ' documents liés au thème';
	type_4.href= url_go_opac + '4';
			
	var type_5=document.getElementById('type_5');
	type_5.innerHTML=tab_nb_notices[4]+ ' documents liés au thème et au territoire';
	type_5.href= url_go_opac + '5';
			
	var type_6=document.getElementById('type_6');
	type_6.innerHTML=tab_nb_notices[5]+ ' documents liés au territoire';
	type_6.href= url_go_opac + '6';
			
	var type_7=document.getElementById('type_7');
	type_7.innerHTML=tab_nb_notices[6]+ ' documents liés au thème';
	type_7.href= url_go_opac + '7';
			
	var type_8=document.getElementById('type_8');
	type_8.innerHTML=tab_nb_notices[7]+ ' documents liés au thème et au territoire';
	type_8.href= url_go_opac + '8';
}
</script>

<form name='search_test'  >
	Territoire: <input name='territoire' id='territoire' value='' type='text'>
	Thème: <input name='theme' id='theme' value='' type='text'>
	<input type='button' value='Recherche' onclick=\"go_search();\">
	<br /><hr />
	Cartothèque:<br />
	<a href='#' id='type_1' onclick=\"\"></a><br />
	<a href='#' id='type_2' onclick=\"\"></a><br />
	Fonds Observatoire:<br />
	<a href='#' id='type_3' onclick=\"\"></a><br />
	<a href='#' id='type_4' onclick=\"\"></a><br />
	<a href='#' id='type_5' onclick=\"\"></a><br />
	Tout le fond:<br />
	<a href='#' id='type_6' onclick=\"\"></a><br />
	<a href='#' id='type_7' onclick=\"\"></a><br />
	<a href='#' id='type_8' onclick=\"\"></a><br />
</form>
";

?>