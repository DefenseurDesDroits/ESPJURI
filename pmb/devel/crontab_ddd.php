<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: crontab_pmb.php,v 1.3 2011-11-04 13:44:01 dgoron Exp $
//
// Défenseur des droits
// Version 1 (DB-05/10/2016)
// +-------------------------------------------------+


// Identifiant de la source du connecteur sortant
$source_id=8;

// adresse WS
$wsdl_url="https://admindocumentation.defenseurdesdroits.fr/pmbws/PMBWs_".$source_id."?&wsdl";

//credentials
$options = array('login'=>'external_user', 'password'=>'5xtPw6');

try {
	$ws=new SoapClient($wsdl_url,$options);

	//ces 3 fonctions doivent être autorisées dans le groupe anonyme
	//Tâches dont le timeout serait dépassé...
	$ws->pmbesTasks_timeoutTasks();
	//Tâches interrompues involontairement..
	$ws->pmbesTasks_checkTasks();
	//Tâches à exécuter
	$ws->pmbesTasks_runTasks($source_id);

} catch (Exception $e) {
	error_log($e->getMessage());
}
