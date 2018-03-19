<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_filter_articles_by_all_cp.class.php,v 1.1.2.2 2017-04-11 07:27:36 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_filter_articles_by_all_cp extends cms_module_common_filter{
	protected $generic_type = 0;
	
	public function __construct($id=0){
		parent::__construct($id);
		$query = 'select id_editorial_type from cms_editorial_types where editorial_type_element = "article_generic"';
		$result = pmb_mysql_query($query);
		$this->generic_type = pmb_mysql_result($result,0,0);
	}
	
	public function get_filter_from_selectors(){
		return array(
			"cms_module_common_selector_generic_article_filter"
		);
	}
	
	public function get_filter_by_selectors(){
		return array(
			"cms_module_common_selector_env_var",
			"cms_module_common_selector_empr_infos",
			"cms_module_common_selector_value"
		);
	}
	
	public function filter($datas){

		$filtered_datas = $filter = array();
		
		$selector_from = $this->get_selected_selector("from");
		$field_from = $selector_from->get_value();		
		$selector_by = $this->get_selected_selector("by");
		$field_by = $selector_by->get_value();		
		
		if(!count($field_from) || !$field_by) return array();

		$query = "SELECT datatype FROM cms_editorial_custom where idchamp=".$field_from['field']*1;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			if($row = pmb_mysql_fetch_object($result)){
				$row_name = 'cms_editorial_custom_'.$row->datatype;				
				$query_to_view = "SELECT cms_editorial_custom_origine FROM cms_editorial_custom_values where ".$row_name." = '".addslashes($field_by)."'";			
				$result_to_view = pmb_mysql_query($query_to_view);
				if(pmb_mysql_num_rows($result_to_view)){
					while($row_to_view = pmb_mysql_fetch_object($result_to_view)){
						$filter[] = $row_to_view->cms_editorial_custom_origine;
					}
					foreach($datas as $article){
						if(in_array($article, $filter)){
							$filtered_datas[] = $article;
						}
					}
				}
			}
		}
		return $filtered_datas;	
	}
}