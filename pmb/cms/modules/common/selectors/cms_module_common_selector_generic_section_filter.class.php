<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_generic_section_filter.class.php,v 1.1.2.2 2017-04-11 07:27:36 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_generic_section_filter extends cms_module_common_selector{	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->cms_module_common_selector_type_editorial_type = "section";
	}

	public function get_form(){
		$form=parent::get_form();
		$form.= "
			<div id='type_editorial_fields'>
				<div class='row'>
					<div class='colonne3'>
						<label for=''>".$this->format_text($this->msg['cms_module_common_selector_field_choise'])."</label>
					</div>
					<div class='colonne-suite'>
						<select name='".$this->get_form_value_name("select_field")."' >";
		$fields = new cms_editorial_parametres_perso(0);
		$form.= $fields->get_selector_options($this->parameters["type_editorial_field"], 1);
		$form.= "
						</select>
					</div>
				</div>
			</div>";
		return $form;
	
	}

	public function save_form(){
		$this->parameters["type_editorial_field"] = $this->get_value_from_form("select_field");
		$this->parameters["type_editorial"] = 0;
		return parent::save_form();
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){		
			$this->value = array(
					'type' => 0,
					'field' =>$this->parameters['type_editorial_field']
			);
		}
		return $this->value;
	}		
}