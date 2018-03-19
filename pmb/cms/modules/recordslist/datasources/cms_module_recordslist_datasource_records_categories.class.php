<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_recordslist_datasource_records_categories.class.php,v 1.1.2.2 2016-09-21 07:58:51 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_recordslist_datasource_records_categories extends cms_module_common_datasource_list{

	public function __construct($id=0){
		parent::__construct($id);
		$this->limitable = true;
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_recordslist_selector_categories"
		);
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		global $dbh;
		$return = array();
		$selector = $this->get_selected_selector();
		if ($selector) {
			$value = $selector->get_value();
			if($value['categories'] != ""){
				$query = "select notcateg_notice, count(num_noeud) as pert from notices_categories where num_noeud in (".$value['categories'].") and notcateg_notice != '".($value['record']*1)."' group by notcateg_notice order by pert";
				$result = pmb_mysql_query($query,$dbh);
				if(pmb_mysql_num_rows($result) > 0){
					$return["title"] = $this->msg['cms_module_recordslist_datasource_records_categories_title'];
					$records = array();
					while($row = pmb_mysql_fetch_object($result)){
						$records[] = $row->notcateg_notice*1;
					}
				}
				$return['records'] = $this->filter_datas("notices",$records);
				if($this->parameters['nb_max_elements'] > 0){
					$return['records'] = array_slice($return['records'], 0, $this->parameters['nb_max_elements']);
				}
				
				$query = "select notcateg_notice, count(num_noeud) as pert from notices_categories join notices on notice_id = notcateg_notice where num_noeud in (".$value['categories'].") and notcateg_notice != '".($value['record']*1)."' AND notice_id in ('".implode("','", $return['records'])."') group by notcateg_notice order by count(*) desc, create_date desc";
				
				$result = pmb_mysql_query($query,$dbh);
				$return = array();
				if($result && (pmb_mysql_num_rows($result) > 0)){
					$return["title"] = $this->msg['cms_module_recordslist_datasource_records_categories_title'];
					while($row = pmb_mysql_fetch_object($result)){
						$return["records"][] = $row->notcateg_notice;
					}
				}
			}
			return $return;
		}
		return false;
	}
}