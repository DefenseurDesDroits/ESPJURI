<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie_ajax.inc.php,v 1.1.2.2 2016-04-15 12:50:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/empr_caddie.class.php");
require_once($include_path."/empr_cart.inc.php");

switch($sub) {
	default:
		$idcaddie=substr($caddie,5);
		$object_type=substr($object,0,4);
		$object_id=substr($object,10);
		$idcaddie = verif_droit_empr_caddie($idcaddie) ;
		if ($idcaddie) {
			$myCart = new empr_caddie($idcaddie);
			$myCart->add_item($object_id);
			$myCart->compte_items();
		} else die("Failed: "."obj=".$object." caddie=".$caddie);
		print $myCart->nb_item;
		break;
}


?>