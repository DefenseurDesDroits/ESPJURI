/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: classementGen.js,v 1.1.4.1 2016-06-15 12:42:56 jpermanne Exp $ */

function classementGen_save(object_type, object_id, url_callback){
	var id = 'classementGen_' + object_type + '_' + object_id;
	var classement = document.getElementById(id).value;
	//sauvegarde valeur en ajax
	var ajax = new http_request();
	ajax.request('./ajax.php?module=ajax&categ=classementGen&action=update',true,'&object_type='+object_type+'&object_id='+object_id+'&classement_libelle='+classement,true,classementGen_callback);
}

function classementGen_callback(url_callback){
	window.location=url_callback;
}