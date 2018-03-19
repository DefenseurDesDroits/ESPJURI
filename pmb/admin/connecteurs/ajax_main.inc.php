<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.1.4.2 2017-03-22 17:38:50 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub){
	case 'in':
		include('./admin/connecteurs/in/ajax_main.inc.php');
		break;
	default:
		break;
}