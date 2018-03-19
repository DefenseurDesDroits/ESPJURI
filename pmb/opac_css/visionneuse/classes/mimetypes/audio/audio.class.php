<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: audio.class.php,v 1.3.8.1 2017-04-10 09:52:48 jpermanne Exp $

require_once($visionneuse_path."/classes/mimetypes/affichage.class.php");

class audio extends affichage{
	var $doc;					//le document numérique à afficher
	var $driver;				//class driver de la visionneuse
	var $params;				//paramètres éventuels
	var $toDisplay= array();	//tableau des infos à afficher	
	var $tabParam = array();	//tableau décrivant les paramètres de la classe
	var $parameters = array();	//tableau des paramètres de la classe
 
    function audio($doc=0) {
    	if($doc){
    		$this->doc = $doc; 
    		$this->driver = $doc->driver;
    		$this->params = $doc->params;
    		$this->getParamsPerso();
    	}
    }
    
    function fetchDisplay(){
    	global $base_path;
    	global $visionneuse_path;
     	//le titre
    	$this->toDisplay["titre"] = $this->doc->titre;
    	
    	//10/04/2017 : le flash n'est plus supporté par les navigateurs, on passe sur la balise html5 audio
    	
    	// lecture audio  	
    	$this->toDisplay["doc"]="
    	<audio controls ".($this->parameters["size_x"]?"style='width : ".$this->parameters["size_x"]."px;'":"").">
    		<source  src='".$this->driver->getDocumentUrl($this->doc->id)."' width='".$this->parameters["size_x"]."' height='".$this->parameters["size_y"]."' type='audio/mpeg' />
    	</audio>";

		//la description
		$this->toDisplay["desc"] = $this->doc->desc;
		return $this->toDisplay;  	
    }
    
    function render(){
    }
    
    function getTabParam(){

    	$this->tabParam = array(
			"size_x"=>array("type"=>"text","name"=>"size_x","value"=>$this->parameters['size_x'],"desc"=>"Largeur du lecteur")
		);
       	return $this->tabParam;
    }
    
	function getParamsPerso(){
		$params = $this->driver->getClassParam('audio');
		$this->unserializeParams($params);
		
		if($this->parameters['size_x'] == 0) $this->parameters['size_x'] = $this->driver->getParam("maxX");
		if($this->parameters['size_y'] == 0) $this->parameters['size_y'] = $this->driver->getParam("maxY");
	}
	
	function unserializeParams($paramsToUnserialized){
		$this->parameters = unserialize($paramsToUnserialized);
		if(!$this->parameters['showstop']) $this->parameters['showstop'] = 0;
		if(!$this->parameters['showinfo']) $this->parameters['showinfo'] = 0;
		if(!$this->parameters['showvolume']) $this->parameters['showvolume'] = 0;
		return $this->parameters;
	}
	
	function serializeParams($paramsToSerialized){
		if(!$paramsToSerialized['showstop']) $paramsToSerialized['showstop'] = 0;
		if(!$paramsToSerialized['showinfo']) $paramsToSerialized['showinfo'] = 0;
		if(!$paramsToSerialized['showvolume']) $paramsToSerialized['showvolume'] = 0;
		$this->parameters =$paramsToSerialized;
		return serialize($paramsToSerialized);
	}
}
?>
