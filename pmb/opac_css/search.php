<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.php,v 1.5.4.1 2015-12-15 16:23:19 dbellamy Exp $

$base_path = ".";
$base_noheader = 1;
$base_nobody = 1;
$include_path=$base_path."/includes";
$class_path=$base_path."/classes";

require_once($base_path."/includes/init.inc.php");
require_once($base_path."/includes/error_report.inc.php") ;
require_once($base_path."/includes/global_vars.inc.php");

require_once($base_path.'/includes/opac_config.inc.php');

// récupération paramètres MySQL et connection á la base
if (file_exists($base_path.'/includes/opac_db_param.inc.php')) require_once($base_path.'/includes/opac_db_param.inc.php');
	else die("Fichier opac_db_param.inc.php absent / Missing file Fichier opac_db_param.inc.php");

require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();

//Sessions !! Attention, ce doit être impérativement le premier include (à cause des cookies)
require_once($base_path."/includes/session.inc.php");

require_once($base_path.'/includes/start.inc.php');
require_once($base_path."/includes/check_session_time.inc.php");

// récupération localisation
require_once($base_path.'/includes/localisation.inc.php');

require_once($base_path.'/includes/divers.inc.php');
require_once($include_path."/marc_tables/".$pmb_indexation_lang."/empty_words");
require_once($base_path."/includes/misc.inc.php");

require_once($base_path."/includes/rec_history.inc.php");

// inclusion des fonctions utiles pour renvoyer la réponse à la requette recu
require_once ($base_path . "/includes/ajax.inc.php");
require_once ($base_path . "/includes/divers.inc.php");

/*
 * Parse la commande Ajax du client vers
 * $module est passé dans l'url,envoyé par http_send_request, in http_request.js script file
 * les valeurs envoyées dans les requêtes en ajax du client vers le serveur sont encodées
 * exclusivement en utf-8 donc décodage de toutes les variables envoyées si nécessaire
*/

require_once ($base_path."/classes/search.class.php");

// Pour les tests:
// http://localhost/~ngantier/pmb3_2/opac_css/search.php?territoire=vista&theme=linux
// http://localhost/~ngantier/pmb3_2/opac_css/search.php?territoire=vista&theme=linux&type=1

$label_du_fonds = "Observatoire de l'Economie et des Territoires de Loir et Cher";
$type_document_cartographique = "e";
$territoire=str_replace('-',' ',$territoire);
$theme=str_replace('-',' ',$theme);

if($type) {

	// Lancement de la recherche dans l'opac
	$form="<body onLoad=\"document.forms[0].submit();return false;\">";
	switch($type) {
		case 1:
			$search[0]="f_1";
			$inter_0_f_1="";
			$op_0_f_1="BOOLEAN";
			$field_0_f_1[0]=$territoire;

			$search[1]="f_15";
			$inter_1_f_15="and";
			$op_1_f_15="EQ";
			$field_1_f_15[0]=$type_document_cartographique;
		break;
		case 2:
			$search[0]="f_1";
			$inter_0_f_1="";
			$op_0_f_1="BOOLEAN";
			$field_0_f_1[0]=$theme;

			$search[1]="f_15";
			$inter_1_f_15="and";
			$op_1_f_15="EQ";
			$field_1_f_15[0]=$type_document_cartographique;

		break;
		case 3:
			// fond: documents liés au territoire
			$search[0]="f_1";
			$inter_0_f_1="";
			$op_0_f_1="BOOLEAN";
			$field_0_f_1[0]=$territoire;

			$search[1]="f_3";
			$inter_1_f_3="and";
			$op_1_f_3="BOOLEAN";
			$field_1_f_3[0]=$label_du_fonds;
		break;
		case 4:
			// documents liés au thème
			$search[0]="f_1";
			$inter_0_f_1="";
			$op_0_f_1="BOOLEAN";
			$field_0_f_1[0]=$theme;

			$search[1]="f_3";
			$inter_1_f_3="and";
			$op_1_f_3="BOOLEAN";
			$field_1_f_3[0]=$label_du_fonds;
		break;
		case 5:
			$search[0]="f_1";
			$inter_0_f_1="";
			$op_0_f_1="BOOLEAN";
			$field_0_f_1[0]=$territoire;

			$search[1]="f_1";
			$inter_1_f_1="and";
			$op_1_f_1="BOOLEAN";
			$field_1_f_1[0]=$theme;

			$search[2]="f_3";
			$inter_2_f_3="and";
			$op_2_f_3="BOOLEAN";
			$field_2_f_3[0]=$label_du_fonds;
		break;
		case 6:
			// Tout le fond: documents liés au territoire
			$search[0]="f_1";
			$inter_0_f_1="";
			$op_0_f_1="BOOLEAN";
			$field_0_f_1[0]=$territoire;
		break;
		case 7:
			// Tout le fond: documents liés au thème
			$search[0]="f_1";
			$inter_0_f_1="";
			$op_0_f_1="BOOLEAN";
			$field_0_f_1[0]=$theme;
		break;
		case 8:
			// Tout le fond: documents liés au thème et au territoire
			$search[0]="f_1";
			$inter_0_f_1="";
			$op_0_f_1="BOOLEAN";
			$field_0_f_1[0]=$territoire;

			$search[1]="f_1";
			$inter_1_f_1="and";
			$op_1_f_1="BOOLEAN";
			$field_1_f_1[0]=$theme;
		break;

		default:
		break;
	}
	$sc=new search();
	$form.=$sc->make_hidden_search_form("./index.php?lvl=more_results&mode=extended","search_form","",true);
//	$form.=$sc->make_hidden_search_form("./index.php?lvl=search_result&search_type_asked=extended_search","search_form","",true);
	$form.="</body>";
	print $form;

} else {
// retourne le nombre de résultat trouvés, a affiché sur la page du client

	if (strtoupper($charset)!="UTF-8") {
		$t=array_keys($_POST);
		foreach($t as $v) {
			global $$v;
			$$v=utf8_decode($$v);
		}
		$t=array_keys($_GET);
		foreach($t as $v) {
			global $$v;
			$$v=utf8_decode($$v);
		}
	}

// Cartothèque:
	// cartes liées au territoire
	$search=array();
	$search[0]="f_1";
	$inter_0_f_1="";
	$op_0_f_1="BOOLEAN";
	$field_0_f_1[0]=$territoire;

	$search[1]="f_15";
	$inter_1_f_15="and";
	$op_1_f_15="EQ";
	$field_1_f_15[0]=$type_document_cartographique;

	$result.=do_search().";";

	$inter_1_f_15='';
	$op_1_f_15='';
	$field_1_f_15=array();

	// cartes liées au thème
	$search=array();
	$search[0]="f_1";
	$inter_0_f_1="";
	$op_0_f_1="BOOLEAN";
	$field_0_f_1[0]=$theme;

	$search[1]="f_15";
	$inter_1_f_15="and";
	$op_1_f_15="EQ";
	$field_1_f_15[0]=$type_document_cartographique;

	$result.=do_search().";";
	$inter_1_f_15='';
	$op_1_f_15='';
	$field_1_f_15=array();

// Fonds Observatoire:
	// documents liés au territoire
	$search=array();
	$search[0]="f_1";
	$inter_0_f_1="";
	$op_0_f_1="BOOLEAN";
	$field_0_f_1[0]=$territoire;

	$search[1]="f_3";
	$inter_1_f_3="and";
	$op_1_f_3="BOOLEAN"; //"BOOLEAN";
	$field_1_f_3[0]=$label_du_fonds;

	$result.=do_search().";";

	$inter_1_f_3='';
	$op_1_f_3='';
	$field_1_f_3=array();

	// documents liés au thème
	$search=array();
	$search[0]="f_1";
	$inter_0_f_1="";
	$op_0_f_1="BOOLEAN";
	$field_0_f_1[0]=$theme;

	$search[1]="f_3";
	$inter_1_f_3="and";
	$op_1_f_3="BOOLEAN";
	$field_1_f_3[0]=$label_du_fonds;

	$result.=do_search().";";

	$inter_1_f_3='';
	$op_1_f_3='';
	$field_1_f_3=array();

	// documents liés au thème et au territoire
	$search= array();
	$search[0]="f_1";
	$inter_0_f_1="";
	$op_0_f_1="BOOLEAN";
	$field_0_f_1[0]=$territoire;

	$search[1]="f_1";
	$inter_1_f_1="and";
	$op_1_f_1="BOOLEAN";
	$field_1_f_1[0]=$theme;

	$search[2]="f_3";
	$inter_2_f_3="and";
	$op_2_f_3="BOOLEAN";
	$field_2_f_3[0]=$label_du_fonds;

	$result.=do_search().";";


	$inter_1_f_1='';
	$op_1_f_1='';
	$field_1_f_1=array();

	$inter_2_f_3='';
	$op_2_f_3='';
	$field_2_f_3=array();


// Tout le fond:
	// documents liés au territoire
	$search= array();
	$search[0]="f_1";
	$inter_0_f_1="";
	$op_0_f_1="BOOLEAN";
	$field_0_f_1[0]=$territoire;
	$result.=do_search().";";

	// documents liés au thème
	$search= array();
	$search[0]="f_1";
	$inter_0_f_1="";
	$op_0_f_1="BOOLEAN";
	$field_0_f_1[0]=$theme;

	$result.=do_search().";";

	// documents liés au thème et au territoire
	$search= array();
	$search[0]="f_1";
	$inter_0_f_1="";
	$op_0_f_1="BOOLEAN";
	$field_0_f_1[0]=$territoire;

	$search[1]="f_1";
	$inter_1_f_1="and";
	$op_1_f_1="BOOLEAN";
	$field_1_f_1[0]=$theme;

	$result.=do_search().";";

	// retour des nombre de notices trouvéees de la forme 0;2;0;3;6;0;0;0;
	ajax_http_send_response($result.' '.$debug_notice);

}

function do_search() {
global $debug_notice;
	$sc=new search();
	$table=$sc->make_search();
	$requete = "select count(1) from $table,notices, notice_statut where notices.notice_id=$table.notice_id and statut=id_notice_statut and  notice_visible_opac=1 and notice_visible_opac_abon=0 ";
	$nb_results=pmb_mysql_result(pmb_mysql_query($requete),0,0);

	// pour le debug
	$requete = "select $table.notice_id from $table,notices, notice_statut where notices.notice_id=$table.notice_id and statut=id_notice_statut and  notice_visible_opac=1 and notice_visible_opac_abon=0 ";
	$found=pmb_mysql_query($requete);
	while(($mesNotices = pmb_mysql_fetch_object($found))) {
		$debug_notice.=$mesNotices->notice_id.' ';
	}
	$debug_notice.=' | ';
	// fin debug

	pmb_mysql_query("drop TEMPORARY table IF EXISTS $table");
	return $nb_results;
}

?>