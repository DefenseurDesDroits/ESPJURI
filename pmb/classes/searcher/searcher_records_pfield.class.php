<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_records_pfield.class.php,v 1.1.4.1 2017-02-07 09:04:13 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class searcher_records_pfield extends searcher_records {
	
	protected $id;
	
	public function __construct($user_query,$id=0){
		parent::__construct($user_query);
		$this->field_restrict=array();
		$this->id = $id;
		$sub=array();
		if($this->id>0){
			$sub[]=array(
					'sub_field' => "code_ss_champ",
					'values' => $this->id,
					'op' => "and",
					'not' => false
			);
		}
		$this->field_restrict[]= array(
				'field' => "code_champ",
				'values' => 100,
				'op' => "and",
				'not' => false,
				'sub'=> $sub
		);
	
	}
	
	protected function _get_search_type(){
		return parent::_get_search_type()."_pfield";
	}
	
	protected function _get_sign($sorted=false){
		$sign = parent::_get_sign($sorted);
		$sign.= md5('&id='.$this->id);
		return $sign;
	}
}