<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: 

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/facette_search_opac.class.php");
require_once($class_path."/facette.class.php");

$facette_search = new facette_search();

switch($action) {
	case "edit":
		$facette = new facette($id);
		print $facette->get_form();
		break;
	case "save":
		$facette = new facette($id);
		$facette->set_properties_from_form();
		$facette->save();
		print $facette_search->get_display_list();
		break;
	case "delete":
		$facette = new facette($id);
		$facette->delete();
		print $facette_search->get_display_list();
		break;
	case "up":
		facette_search::facette_up($id);
		print $facette_search->get_display_list();
		break;
	case "down":
		facette_search::facette_down($id);
		print $facette_search->get_display_list();
		break;
	case "order":
		facette_search::facette_order_by_name();
		print $facette_search->get_display_list();
		break;
	default:
		print $facette_search->get_display_list();
		break;
}

