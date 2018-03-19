<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_sparql_view_carousel.class.php,v 1.1.6.1 2016-09-21 07:58:51 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($include_path."/h2o/h2o.php");

class cms_module_sparql_view_carousel extends cms_module_carousel_view_carousel{
	
	public function render($datas){
		$datas['records'] = $datas['result'];
		return parent::render($datas);
	}
	
	
}