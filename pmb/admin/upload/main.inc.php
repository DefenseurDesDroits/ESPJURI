<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3.4.1 2016-04-29 14:13:43 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'rep':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['upload_repertoire'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['upload_repertoire'].$msg['1003'].$msg['1001']);
		include("./admin/upload/folders.inc.php");		
		break;
	case "storages" :
		require_once($class_path."/storages/storages.class.php");
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['storage_menu'], $admin_layout);
		print $admin_layout;	
		echo window_title($database_window_title.$msg['storage_menu'].$msg['1003'].$msg['1001']);
		$storages = new storages();
		$storages->process($action,$id);
		break;
	case "statut" :
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_menu_docnum_statut'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['admin_menu_docnum_statut'].$msg['1003'].$msg['1001']);
		include("./admin/upload/statut.inc.php");
		break;
	default:
		$admin_layout = str_replace('!!menu_sous_rub!!', "", $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['admin_menu_upload_docnum'].$msg['1003'].$msg['1001']);
		break;
}
?>