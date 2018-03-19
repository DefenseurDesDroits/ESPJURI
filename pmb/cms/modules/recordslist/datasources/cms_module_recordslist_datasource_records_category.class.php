<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_recordslist_datasource_records_category.class.php,v 1.1.2.1 2017-03-16 10:07:10 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_recordslist_datasource_records_category extends cms_module_common_datasource_records_list{
	
	/*
	 * On d�fini les s�lecteurs utilisable pour cette source de donn�e
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_category_permalink",
			"cms_module_common_selector_category",
			"cms_module_common_selector_env_var"
		);
	}

	/*
	 * R�cup�ration des donn�es de la source...
	 */
	public function get_datas(){
		global $dbh;
		$return = array();
		
		$selector = $this->get_selected_selector();
		if ($selector) {					
			$value = $selector->get_value();
			if($value){
				$query = "select notcateg_notice as notice_id from notices_categories where num_noeud = '".($value*1)."' ";
				$result = pmb_mysql_query($query,$dbh);
				if(pmb_mysql_num_rows($result) > 0){
					$records = array();
					while($row = pmb_mysql_fetch_object($result)){
						$records[] = $row->notice_id;
					}
				}
				$return['records'] = $this->filter_datas("notices",$records);
			}
			if(!count($return['records'])) return false;
		
			$return = $this->sort_records($return['records']);
			$return["title"] = $this->msg['cms_module_recordslist_datasource_records_category_title'];
			return $return;
		}
		return false;
	}
}