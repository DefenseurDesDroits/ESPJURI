<?php
// +-------------------------------------------------+
// $Id: ajax_catalog_verif_ddd dbellamy Exp $
//
// Defenseur Des Droits
// Gestion : pmb/catalog
// ATTENTION : fichier en UTF-8
// V1 (DB-14/08/2017)
// +-------------------------------------------------+

$base_path = '.';
$base_noheader = 1;
$base_nobody = 1;  
$base_nodojo = 1;  
$clean_pret_tmp=1;
$base_is_http_request=1;

$base_auth = 'AUTORITES_AUTH';

require_once ($base_path . '/includes/init.inc.php');
if(!SESSrights) exit;

// inclusion des fonctions utiles pour renvoyer la réponse 
require_once ($base_path . '/includes/ajax.inc.php');

$author_id *= 1;
$q = '';
$response = '';
		
if ($action=='get_url_eli') {
	$response = $opac_url_base;
}

if ($action && $author_id){
	
	switch($action) {
	
		case 'get_cp_identifiant_autorite' :
			$q = 'select author_custom_small_text from author_custom_values where author_custom_champ=(select idchamp from author_custom where name=\'cp_identifiant_autorite\' limit 1) and author_custom_origine='.$author_id.' limit 1';
			break;
		case 'get_cp_identifiant_service' :
			$q = 'select author_custom_small_text from author_custom_values where author_custom_champ=(select idchamp from author_custom where name=\'cp_identifiant_service\' limit 1) and author_custom_origine='.$author_id.' limit 1';
			break;
		default:
			break;
	}
		
}

if($q) {
	$r = pmb_mysql_query($q,$dbh);
	if(pmb_mysql_num_rows($r)) {
		$response = pmb_mysql_result($r,0,0);
	}
}


ajax_http_send_response($response);
