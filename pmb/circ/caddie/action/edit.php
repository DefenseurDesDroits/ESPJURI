<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edit.php,v 1.4.4.1 2017-02-22 11:14:09 jpermanne Exp $

// d�finition du minimum n�c�ssaire 
$base_path="../../..";                            
$base_auth = "CATALOGAGE_AUTH";  
$base_title = "";
$base_noheader=1;
require_once ("$base_path/includes/init.inc.php");  
require_once ("./edition_func.inc.php");  
require_once ("$class_path/empr_caddie.class.php");
$fichier_temp_nom=str_replace(" ","",microtime());
$fichier_temp_nom=str_replace("0.","",$fichier_temp_nom);

$myCart = new empr_caddie($idemprcaddie);
if (!$myCart->idemprcaddie) die();
// cr�ation de la page
switch($dest) {
	case "TABLEAU":
		require_once ($class_path."/spreadsheet.class.php");
		$worksheet = new spreadsheet();

		$worksheet->write_string(0,0,"Panier N ".$idemprcaddie);
		$worksheet->write_string(0,1,$myCart->name);
		$worksheet->write_string(0,2,$myCart->comment);
		break;
	case "TABLEAUHTML":
		header("Content-Type: application/download\n");
		header("Content-Disposition: atachement; filename=\"tableau.xls\"");
		print "<html><head>" .
	 	'<meta http-equiv=Content-Type content="text/html; charset='.$charset.'" />'.
		"</head><body>";
		break;
	default:
        header ("Content-Type: text/html; charset=$charset");
		print $std_header;
		break;
	}

afftab_empr_cart_objects ($idemprcaddie, $elt_flag , $elt_no_flag ) ;

switch($dest) {
	case "TABLEAU":
		$worksheet->download('Caddie_EMPR_'.$idemprcaddie.'.xls');
		break;
	case "TABLEAUHTML":
	default:
		if ($etat_table) echo "\n</table>";
		break;
	}
	
pmb_mysql_close($dbh);
