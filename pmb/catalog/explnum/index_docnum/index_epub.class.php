<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: index_epub.class.php,v 1.1.10.1 2016-06-10 09:44:54 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/epubData.class.php");

/**
 * Classe qui permet la gestion de l'indexation des fichiers epub
 */
class index_epub{
	
	var $fichier='';
	
	function index_epub($filename, $mimetype='', $extension=''){
		$this->fichier = $filename;
	}
	
	/**
	 * Méthode qui retourne le texte à indexer des epub
	 */
	function get_text($filename){
		global $charset;
		
		$epub=new epub_Data($this->fichier);
		return $epub->getFullTextContent($charset);
	}
}
?>