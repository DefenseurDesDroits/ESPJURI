<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_list.class.php,v 1.1.4.2 2017-03-16 10:07:10 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records_list extends cms_module_common_datasource_list{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->limitable = true;
		$this->sortable = true;
	}
	
	/*
	 * On d�fini les crit�res de tri utilisable pour cette source de donn�e
	 */
	protected function get_sort_criterias() {
		return array (
			"date_parution",
			"notice_id",
			"index_sew"
		);
	}
	
	protected function sort_records($records) {
		global $dbh;
		if(!count($records)) return false;
		$query = 'select notice_id from notices
				where notice_id in ('.implode(',', $records).')
				order by '.$this->parameters["sort_by"].' '.$this->parameters["sort_order"].' limit '.$this->parameters['nb_max_elements']*1;
		$result = pmb_mysql_query($query,$dbh);
		$return = array();
		if (pmb_mysql_num_rows($result) > 0) {
			$return["title"] = "Liste de notices";
			while($row = pmb_mysql_fetch_object($result)){
				$return["records"][] = $row->notice_id;
			}
		}
		return $return;
	}
}