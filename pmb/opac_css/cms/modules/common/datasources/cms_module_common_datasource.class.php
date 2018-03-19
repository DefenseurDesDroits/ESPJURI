<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource.class.php,v 1.27.2.7 2016-09-22 08:07:02 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource extends cms_module_root{
	protected $cadre_parent;
	protected $selectors=array();
	private $used_external_filter = false;
	private $external_filter;
	public static $sections_tree = array();
	public static $sections_path = array();
		
	public function __construct($id=0){
		$this->id = $id+0;
		parent::__construct();
	}
	
	public function get_available_selectors(){
		return array();
	}
	
	public function set_cadre_parent($id){
		$this->cadre_parent = $id+0;
	}
	
	public function set_filter($filter){
		$this->used_external_filter = true;
		$this->external_filter = $filter;
	}
	
	/*
	 * R�cup�ration des informations en base
	 */
	protected function fetch_datas(){
		global $dbh;
		if($this->id){
			//on commence par aller chercher ses infos
			$query = " select id_cadre_content, cadre_content_hash, cadre_content_num_cadre, cadre_content_data from cms_cadre_content where id_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->id = $row->id_cadre_content+0;
				$this->hash = $row->cadre_content_hash;
				$this->cadre_parent = $row->cadre_content_num_cadre+0;
				$this->unserialize($row->cadre_content_data);
			}
			//on va chercher les infos des s�lecteurs...
			$query = "select id_cadre_content, cadre_content_object from cms_cadre_content where cadre_content_type='selector' and cadre_content_num_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				while($row=pmb_mysql_fetch_object($result)){
					$this->selectors[] = array(
						'id' => $row->id_cadre_content+0,
						'name' => $row->cadre_content_object
					);	
				}
			}
		}
	}
	
	/*
	 * M�thode de g�n�ration du formulaire... 
	 */
	public function get_form(){
		$selectors = $this->get_available_selectors();
		
		$form = "
			<div class='row'>";
		$form.= $this->get_selectors_list_form();
		
		if($this->parameters['selector']!= "" || count($selectors)==1){
			$current_selector_id = 0;
			if($this->parameters['selector']!= ""){
				for($i=0 ; $i<count($this->selectors) ; $i++){
					if($this->selectors[$i]['name'] == $this->parameters['selector']){
						$selector_id = $this->selectors[$i]['id'];
						break;
					}
				}
				$selector_name= $this->parameters['selector'];
			}else if(count($selectors)==1){
				$selector_name= $selectors[0];
			}
			$form.="
			<script type='text/javacsript'>
				cms_module_load_elem_form('".$selector_name."','".$selector_id."','selector_form');
			</script>";
		}
		$form.="
				<div id='selector_form' dojoType='dojox.layout.ContentPane'>
				</div>
			</div>";
		return $form;
	}	
	
	/*
	 * Formulaire de s�lection d'un s�lecteur
	 */
	protected function get_selectors_list_form(){
		$selectors = $this->get_available_selectors();
		if(count($selectors)>1){
			$form= "
				<div class='colonne3'>
					<label for='selector_choice'>".$this->format_text($this->msg['cms_module_common_datasource_selector_choice'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='hidden' name='selector_choice_last_value' id='selector_choice_last_value' value='".($this->parameters['selector'] ? $this->parameters['selector'] : "" )."' />
					<select name='selector_choice' id='selector_choice' onchange='load_selector_form(this.value)'>
						<option value=''>".$this->format_text($this->msg['cms_module_common_datasource_selector_choice'])."</option>";
			foreach($selectors as $selector){
				$form.= "
						<option value='".$selector."' ".($selector == $this->parameters['selector'] ? "selected='selected'":"").">".$this->format_text($this->msg[$selector])."</option>";
			}
			$form.="
					</select>
					<script type='text/javascript'>
						function load_selector_form(selector){
							if(selector != ''){
								//on �vite un message d'alerter si le il n'y a encore rien de fait...
								if(document.getElementById('selector_choice_last_value').value != ''){
									var confirmed = confirm('".addslashes($this->msg['cms_module_common_selector_confirm_change_selector'])."');
								}else{
									var confirmed = true;
								} 
								if(confirmed){
									document.getElementById('selector_choice_last_value').value = selector;
									cms_module_load_elem_form(selector,0,'selector_form');
								}else{
									var sel = document.getElementById('selector_choice');
									for(var i=0 ; i<sel.options.length ; i++){
										if(sel.options[i].value == document.getElementById('selector_choice_last_value').value){
											sel.selectedIndex = i;
										}
									}
								}
							}			
						}
					</script>
				</div>";
		}else{
			$form = "
				<input type='hidden' name='selector_choice' value='".$selectors[0]."'/>";
		}
		return $form;
	}
	
	/*
	 * Sauvegarde des infos depuis un formulaire...
	 */
	public function save_form(){
		global $dbh;
		global $selector_choice;
		
		$this->parameters['selector'] = $selector_choice;
				
		$this->get_hash();
		if($this->id){
			$query = "update cms_cadre_content set";
			$clause = " where id_cadre_content='".$this->id."'";
		}else{
			$query = "insert into cms_cadre_content set";
			$clause = "";
		}
		$query.= " 
			cadre_content_hash = '".$this->hash."',
			cadre_content_type = 'datasource',
			cadre_content_object = '".$this->class_name."',".
			($this->cadre_parent ? "cadre_content_num_cadre = '".$this->cadre_parent."'," : "")."		
			cadre_content_data = '".addslashes($this->serialize())."'
			".$clause;
		$result = pmb_mysql_query($query,$dbh);
		
		if($result){
			if(!$this->id){
				$this->id = pmb_mysql_insert_id();
			}
			//on supprime les anciennes sources de donn�es...
			$query = "delete from cms_cadre_content where id_cadre_content != '".$this->id."' and cadre_content_type='datasource' and cadre_content_num_cadre = '".$this->cadre_parent."'";
			pmb_mysql_query($query,$dbh);
			//s�lecteur
			$selector_id = 0;
			for($i=0 ; $i<count($this->selectors) ; $i++){
				if($this->parameters['selector'] == $this->selectors[$i]['name']){
					$selector_id = $this->selectors[$i]['id'];
					break;
				}
			}
			if($this->parameters['selector']){
				$selector = new $this->parameters['selector']($selector_id);
				$selector->get_hash_from_form();
				$selector->set_parent($this->id);
				$selector->set_cadre_parent($this->cadre_parent);
				$result = $selector->save_form();
				if($result){
					if($selector_id==0){
						$this->selectors[] = array(
							'id' => $selector->id,
							'name' => $this->parameters['selector']
						);
					}
					return true;	
				}else{
					//cr�ation de la source de donn�e rat�e, on supprime le hash de la table...
					$this->delete_hash();
					return false;
				}
			}else{
				return true;
			}
		}else{
			//cr�ation de la source de donn�e rat�e, on supprime le hash de la table...
			$this->delete_hash();		
			return false;
		}
	}
	
	/*
	 * M�thode de suppression
	 */
	public function delete(){
		global $dbh;
		if($this->id){
			//on commence par �liminer le s�lecteur associ�...
			$query = "select id_cadre_content,cadre_content_object from cms_cadre_content where cadre_content_num_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				//la logique voudrait qu'il n'y ai qu'un seul s�lecteur (enfin sous-�l�ment, la conception peut �voluer...), mais sauvons les brebis �gar�es...
				while($row = pmb_mysql_fetch_object($result)){
					$sub_elem = new $row->cadre_content_object($row->id_cadre_content);
					$success = $sub_elem->delete();
					if(!$success){
						//TODO verbose mode
						return false;
					}
				}
			}
			//on est tout seul, �liminons-nous !
			$query = "delete from cms_cadre_content where id_cadre_content = '".$this->id."'";
			$result = pmb_mysql_query($query,$dbh);
			if($result){
				$this->delete_hash();
				return true;
			}else{
				return false;
			}
		}
	}
	
	/*
	 * M�thode pour renvoyer les donn�es tel que d�fini par le s�lecteur
	 */
	public function get_datas(){
		
	}
	
	public function get_headers(){
		$headers=array();
		if($this->parameters['selector']){
			$selector = $this->get_selected_selector();
			$headers = array_merge($headers,$selector->get_headers());
			$headers = array_unique($headers);
		}	
		return $headers;
	}
	
	protected function get_selected_selector(){
		//on va chercher
		if($this->parameters['selector']!= ""){
			$current_selector_id = 0;
			for($i=0 ; $i<count($this->selectors) ; $i++){
				if($this->selectors[$i]['name'] == $this->parameters['selector']){
					return new $this->parameters['selector']($this->selectors[$i]['id']);
				}
			}
		}else{
			return false;
		}
	}
	
	public function set_module_class_name($module_class_name){
		$this->module_class_name = $module_class_name;
		$this->fetch_managed_datas();
	}
	
	protected function fetch_managed_datas($type="datasources"){
		parent::fetch_managed_datas($type);
	}
		
	/*
	 * M�thode pour filtrer les r�sultats en fonction de la visibilit�
	 */
	protected function filter_datas($type,$datas){
		//la m�thode g�n�rique permet de filter les entit�s de base...
		switch($type){
			case "notices" :
				$result = $this->filter_notices($datas);
				break;
			case "articles" :
				$result = $this->filter_articles($datas);
				break;
			case "sections" :
				$result = $this->filter_sections($datas);
				break;
			case "explnums" :
				$result = $this->filter_explnums($datas);
				break;
			default :
				//si on est pas avec une entit� connue, on s'en charge quand m�me...
				if(method_exists($this,"filter_".$type)){
					$result = call_user_func(array($this,"filter_".$type),$datas);
				}else{
					$result = $datas;
				}
				break;	
		}
		if ($this->used_external_filter){
			$result = $this->external_filter->filter($result);
		}
		if(!$result){
			$result=array();
		}
		return $result;
	}

	protected function filter_notices($datas){
		if(count($datas)){
			$notices_ids = "";
			for($i=0 ; $i<count($datas) ; $i++){
				if($notices_ids) $notices_ids.= ",";
				$notices_ids.= $datas[$i]*1;
			}
			$filter = new filter_results($notices_ids);
			$notices_ids = $filter->get_results();
			//les donn�es sont d�j� filtr�es...dont on s'assure que ca ne bouge pas !
			$tmpdatas = explode(",",$notices_ids);
			$finaldatas = array_intersect($datas, $tmpdatas);
		}
		return $finaldatas;
	}

	protected function filter_articles($datas){
		global $dbh;
		$valid_datas = $valid_datas = array();
		array_walk($datas, 'static::int_caster');
		//quand on filtre un article, on cherche d�j� si la rubrique parente est visible...
		$valid_sections = $sections = array();
		$query = "select distinct num_section from cms_articles where id_article in ('".implode("','",$datas)."')";
		$result = pmb_mysql_query($query,$dbh);
		if($result && pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$sections[] = $row->num_section;
			}
			$valid_sections = $this->filter_sections($sections);
		}
		
		$clause_date = "
		((article_start_date != 0 and to_days(article_start_date)<=to_days(now()) and to_days(article_end_date)>=to_days(now()))||(article_start_date != 0 and article_end_date =0 and to_days(article_start_date)<=to_days(now()))||(article_start_date=0 and article_end_date=0)||(article_start_date = 0 and to_days(article_end_date)>=to_days(now())))";
		
		
		if(count($valid_sections)){
			$query = "select id_article from cms_articles 
				join cms_editorial_publications_states on id_publication_state = article_publication_state 
				where num_section in ('".implode("','",$valid_sections)."') and id_article in ('".implode("','",$datas)."') and editorial_publication_state_opac_show = 1".(!$_SESSION['id_empr_session'] ? " and editorial_publication_state_auth_opac_show = 0" : "")." and ".$clause_date;
			$result = pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					$valid_datas[]=$row->id_article;
				}
			}
			foreach($datas as $article_id){
				if(in_array($article_id,$valid_datas)){
					$articles[] = $article_id;
				}
			}
		}
		return $articles;
	}
	
	protected function filter_sections($datas){
		$valid_datas = array();
		//on caste les donn�es
		array_walk($datas, 'static::int_caster');
		//on initialise un arbre avec les sections
		if(!count(self::$sections_tree)){
			self::$sections_tree = $this->get_sections_tree(0,"",self::$sections_path);
		}
		foreach($datas as $id_section){
			if(isset(self::$sections_path[$id_section])){
				$section_path_ids = explode("/",self::$sections_path[$id_section]);
				$current_tree = self::$sections_tree[$section_path_ids[0]];
				if($current_tree['valid'] == 1){
					$valid = true;
					for($i=1 ; $i< count($section_path_ids) ; $i++){
						$current_tree = $current_tree['children'][$section_path_ids[$i]];
						if($current_tree['valid'] == 0){
							$valid = false;
							break;
						}
					}
					if($valid){
						$valid_datas[]=$id_section;
					}
				}
			}else{
				continue;
			}
		}
		return $valid_datas;
	}
	
	protected function get_sections_tree($id_parent = 0,$path="",&$paths){
		global $dbh;
		$id_parent+=0;
		$tree = array();
		$nb_days_since_1970 = 719528;
		$nb_days_today = round((time()/(3600*24)))+$nb_days_since_1970;
		
		$clause = "((section_start_date != 0 and section_start_date<now() and section_end_date>now())||(section_start_date != 0 and section_end_date =0 and section_start_date <now())||(section_start_date = 0 and section_end_date>now()))";
		
		$query="select id_section,to_days(section_start_date) as start_day, to_days(section_end_date) as end_day , editorial_publication_state_opac_show,editorial_publication_state_auth_opac_show from cms_sections join cms_editorial_publications_states on id_publication_state = section_publication_state where section_num_parent = '".($id_parent*1)."'";
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				$paths[$row->id_section] = ($path ? $path."/" : "").$row->id_section ;	
				$valid = 1; 
				//v�rification sur le statut
				if(!($row->editorial_publication_state_opac_show && (!$row->editorial_publication_state_auth_opac_show || ($row->editorial_publication_state_auth_opac_show && $_SESSION['id_empr_session'])))){
					$valid = 0;
				}else{
					//v�rification sur les dates...
					if($row->start_day!= 0 && $row->end_day!=0 && ($row->start_day>$nb_days_today || $row->end_day<$nb_days_today)){
						$valid = 0;
					}else if ($row->start_day!=0 && !$row->end_day && $row->start_day>$nb_days_today){
						$valid = 0;
					}else if ($row->end_day!=0 && !$row->start_day && $row->end_day<$nb_days_today){
						$valid = 0;
					}
				}
				$tree[$row->id_section] = array(
					'start_day' => $row->start_day,
					'end_day' => $row->end_day,
					'opac_show'=> $row->editorial_publication_state_opac_show,
					'auth_opac_show'=> $row->editorial_publication_state_auth_opac_show,
					'valid' => $valid
				);
				if($valid){
					$tree[$row->id_section]['children'] = $this->get_sections_tree($row->id_section,($path ? $path."/" : "").$row->id_section,$paths);
				}	
			}
		}
		return $tree;
	}
	
	protected function filter_explnums($datas=array()){
	    global $dbh;
	    global $class_path;
	    global $gestion_acces_active;
	    global $gestion_acces_empr_docnum;
	    
	    $filtered_datas = array();
	    if(count($datas)){
	    	array_walk($datas, 'static::int_caster');
		    $acces='';
	        $restrict = '';
	        if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
	            require_once("$class_path/acces.class.php");
	        	$ac = new acces();
	        	$dom_3 = $ac->setDomain(3);
	        	$acces = $dom_3->getJoin($_SESSION['id_empr_session'],16,'explnum_id');
	        } else {
	        	$restrict= "and explnum_docnum_statut in (select id_explnum_statut from explnum_statut where (explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
	        }
	        $explnum = 'select explnum_id from explnum '.$acces.' where explnum_id in("'.implode('","', $datas).'") '.$restrict.'  ';
	        $result = pmb_mysql_query($explnum,$dbh);
	        while($row = pmb_mysql_fetch_object($result)){
	          $filtered_datas[] = $row->explnum_id;  
	        }
	    }
	    return $filtered_datas;
	}
	
	public function get_format_data_structure(){
		return array();
	}
	
	public function get_exported_datas(){
		$infos = parent::get_exported_datas();
		$infos['cadre_parent'] = $this->cadre_parent;
		$infos['selector'] = $this->get_selected_selector();
		return $infos;
	}
	
	public function get_human_description($context_name){
		$description = "<span class = 'cms_module_common_datasource_name_human_description'>".$context_name."</span>";
		return $description;
	}
}