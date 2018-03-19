<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie_ajax.inc.php,v 1.5.2.1 2016-04-15 12:50:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// functions particuli�res � ce module
require_once("./catalog/caddie/caddie_func.inc.php");
require_once("$include_path/templates/cart.tpl.php");
require_once("$include_path/expl_info.inc.php");
require_once("$class_path/caddie.class.php");
require_once("$class_path/serials.class.php");
require_once("$class_path/parameters.class.php") ;
require_once("$class_path/emprunteur.class.php") ;
require_once("$include_path/cart.inc.php");
require_once("$include_path/empr_cart.inc.php");
require_once("$include_path/bull_info.inc.php");

switch($sub) {
	case "pointage" :
		if ($typecaddie=='empr') {
			$idcaddie = verif_droit_empr_caddie($idcaddie) ;
		} else {
			$idcaddie = verif_droit_caddie($idcaddie) ;
		}
		include('./catalog/caddie/pointage/main_ajax.inc.php');
		break;
	case "collecte" :
		if ($typecaddie=='empr') {
			$idcaddie = verif_droit_empr_caddie($idcaddie) ;
		} else {
			$idcaddie = verif_droit_caddie($idcaddie) ;
		}
		include('./catalog/caddie/collecte/main_ajax.inc.php');
		break;
	default:
		$idcaddie=substr($caddie,5);
		$object_type=substr($object,0,4);
		$object_id=substr($object,10);
		$idcaddie = verif_droit_caddie($idcaddie) ;
		
		if ($idcaddie) {
			$myCart = new caddie($idcaddie);
			$myCart->add_item($object_id,$object_type);
			$myCart->compte_items();
		} else die("Failed: "."obj=".$object." caddie=".$caddie);
		print $myCart->nb_item;
		break;
}