<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_datasource_articles.class.php,v 1.5.2.1 2017-05-16 07:32:10 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/cms/cms_article.class.php");
/**
 * class docwatch_datasource_articles
 * 
 */
class docwatch_datasource_articles extends docwatch_datasource{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	
	/**
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		parent::__construct($id);
	} // end of member function __construct
	
	/**
	 * G�n�ration de la structure de donn�es representant les items de type article
	 *
	 */
	
	protected function get_items_datas($selector_values){
		global $dbh;
		$articles_retour = array();
		if(count($selector_values)){
			foreach($selector_values as $id){
				$article_instance = new cms_article($id);
				$article_data = $article_instance->format_datas();
				$article = array();
				$article['num_article'] = $article_data['id'];
				$article['title'] = $article_data['title'];
				$article['summary'] = $article_data['resume'];
				$article['content'] = $article_data['content'];
				if($article_data['start_date'] == ""){
					$article['publication_date'] = extraitdate($article_data['create_date']);
				}
				else{
					$article['publication_date'] = extraitdate($article_data['start_date']);
				}
				$article['logo_url'] = $article_data['logo']['large'];
				$article['url'] = $this->get_constructed_link("article", $article_data['id']);
				$articles_retour[] = $article;
			}
		}
		return $articles_retour;
	}
	
	public function filter_datas($datas, $user=0){
		return $this->filter_articles($datas, $user);
	}
	
	public function get_available_selectors(){
		global $msg;
		return array(
			'docwatch_selector_articles_by_sections' => $msg['docwatch_selector_articles_by_sections']
		);
	}

	public function get_form_content(){
		global $msg,$charset;
		$form = parent::get_form_content();
		$form .= "
		<div class='row'>&nbsp;</div>
 		<div class='row'>
 			<label>".htmlentities($msg['dsi_docwatch_datasource_articles_link_select'],ENT_QUOTES,$charset)."</label>
 		</div>
 		<div class='row'>
 			".$this->get_constructor_link_form("article",get_class($this))."
 		</div>";
		return $form;
	}
		
	public function set_from_form() {
		$this->save_constructor_link_form("article",get_class($this));
		parent::set_from_form();
	}
	
} // end of docwatch_datasource_articles

