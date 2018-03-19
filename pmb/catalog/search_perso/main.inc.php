<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.1.18.1 2016-08-23 09:39:58 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de switch recherche notice

// inclusions principales
require_once("$class_path/search_perso.class.php");

$search_p= new search_perso($id);

switch($sub) {
	case "form":
		// affichage du formulaire de recherche perso, en cr�ation ou en modification => $id)
		print $search_p->do_form();	
	break;
	case "save":
		// sauvegarde issu du formulaire
		$search_p->update_from_form();
		print $search_p->do_list();
	break;	
	case "delete":
		// effacement d'une recherche personalis�e, issu du formulaire
		$search_p->delete();
		print $search_p->do_list();
	break;
	case "launch":
		// acc�s direct � une recherche personalis�e
		print $search_p->launch();
		break;
	default :
		// affiche liste des recherches personalis�e
		print $search_p->do_list();
	break;
}


