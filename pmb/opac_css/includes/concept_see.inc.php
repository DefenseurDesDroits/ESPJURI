<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: concept_see.inc.php,v 1.3.2.3 2016-07-06 10:02:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/autoloader.class.php");
if(!is_object($autoloader)){
	$autoloader = new autoloader();
}

$controler = new skos_page_concept($id);
$controler->proceed();

rec_last_authorities();

//FACETTES
//gestion des facette si active
require_once($base_path.'/classes/facette_search.class.php');
$facettes_tpl = '';
$records = "";
//comparateur de facettes : on ré-initialise
$_SESSION['facette']="";
if(count($controler->get_indexed_notices())){
	$records= implode(",",$controler->get_indexed_notices());

	if(!$opac_facettes_ajax){
		$facettes_tpl .= facettes::make_facette($records);
	}else{
		$_SESSION['tab_result']=$records;
		$facettes_tpl .=facettes::call_ajax_facettes();
	}
	//Formulaire "FACTICE" pour l'application du comparateur et du filtre multiple...
	if($facettes_tpl) {
		$facettes_tpl.= '
		<form name="form_values" style="display:none;" method="post" action="'.$base_path.'/index.php?lvl=more_results&mode=extended">
			<input type="hidden" name="from_see" value="1" />
			'.facette_search_compare::form_write_facette_compare().'
		</form>';
	}
}