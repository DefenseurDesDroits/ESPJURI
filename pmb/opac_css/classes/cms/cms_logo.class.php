<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_logo.class.php,v 1.11.2.1 2016-09-01 12:13:10 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/cms/cms_logo.tpl.php");

class cms_logo {
	public $id;		// identifiant de l'objet
	public $type;	// type d'objet
	public $data;	// donn�e binaire du logo

	public function __construct($id="",$type="section"){
		$this->id= $id*1;
		$this->type = $type;
		if($this->id){
			$this->fetch_data_cache();
		}
	}

	protected function fetch_data_cache(){
		if($tmp=cms_cache::get_at_cms_cache($this)){
			$this->restore($tmp);
		}else{
			$this->fetch_data();
			cms_cache::set_at_cms_cache($this);
		}
	}

	protected function restore($cms_object){
		foreach(get_object_vars($cms_object) as $propertieName=>$propertieValue){
			$this->{$propertieName}=$propertieValue;
		}
	}

	protected function fetch_data(){
		$table=$this->get_sql_table();
		if(!$table) return false;
		$rqt = "select ".$this->type."_logo from ".$table." where id_".$this->type." = '".$this->id."'";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			$this->data = pmb_mysql_result($res,0,0);
		}
	}

	public function get_form(){
		global $msg;
		global $charset;
		global $cms_logo_form_tpl;
		global $cms_logo_form_exist_obj_tpl;
		global $cms_logo_form_new_obj_tpl;

		$form = $cms_logo_form_tpl;
		if($this->id){
			$form = str_replace("!!field!!",$cms_logo_form_exist_obj_tpl,$form);
		}else{
			$form = str_replace("!!field!!",$cms_logo_form_new_obj_tpl,$form);
			$form = str_replace("!!js!!","",$form);
		}
		$form = str_replace("!!id!!",$this->id,$form);
		$form = str_replace("!!type!!",$this->type,$form);
		return $form;
	}

	public function get_field(){
		global $msg;
		global $charset;
		global $cms_logo_field_tpl;

		$field = str_replace("!!type!!",$this->type,$cms_logo_field_tpl);

		//si $_FILES n'est pas vide, on a du matos...
		if(count($_FILES)){
			$result = $this->save();
			if($result === true){
				$js = "
				var div_vign = window.parent.document.getElementById('cms_logo_vign');
				var old_img = window.parent.document.getElementById('cms_logo_vign_img');
				div_vign.removeChild(old_img);
				var img = document.createElement('img');
				img.setAttribute('id','cms_logo_vign_img');
				img.setAttribute('class','cms_logo_vign');
				img.setAttribute('src','./cms_vign.php?type=".$this->type."&id=".$this->id."&mode=vign');
				div_vign.appendChild(img);";
			}else{
				$js = "
					alert(\"".$result."\");";
			}
		}else{
			$js = "";
		}
		$field = str_replace("!!js!!",$js,$field);
		return $field;
	}
	public function clean_cache($id = 0){
		//TODO
	}
	public function save(){
		global $msg;
		//on commence par regarder ce qu'on nous a donn�...
		$mimetype = $_FILES['cms_logo_file']['type'];
		//on ne veut que les images
		if(substr($mimetype,0,5) != "image"){
			return $msg['cms_editorial_form_logo_unsupported_file'];
		}else{
			if(substr($mimetype,6,3) == "png"){
				$data = file_get_contents($_FILES['cms_logo_file']['tmp_name']);
			}else{
				//et que du png...
				$data = $this->convert_to_png($_FILES['cms_logo_file']['tmp_name']);
			}
		}
		$table=$this->get_sql_table();
		if(!$table) return $msg['cms_editorial_form_logo_cant_save'];
		$rqt = "update ".$table." set ".$this->type."_logo=\"".addslashes($data)."\" where id_".$this->type." = '".$this->id."'";
		$res= pmb_mysql_query($rqt);
		if($res){
			$this->clean_cache($this->id);
			return true;
		}else{
			return $msg['cms_editorial_form_logo_cant_save'];
		}
	}

	protected function get_sql_table(){
		switch ($this->type){
			case "section" :
				$table = "cms_sections";
				break;
			case "article" :
				$table = "cms_articles";
				break;
			default :
				$table ="";
				break;
		}
		return $table;
	}

	protected function convert_to_png($picture){
		$data = file_get_contents($picture);
		$src_img = imagecreatefromstring($data);
		$src_x = imagesx($src_img);
		$src_y = imagesy($src_img);
		$dst_img=imagecreatetruecolor($src_x,$src_y);
		ImageSaveAlpha($dst_img, true);
		ImageAlphaBlending($dst_img, false);
		imagefilledrectangle($dst_img,0,0,$src_x,$src_y,imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
		imagecopyresampled($dst_img,$src_img,0,0,0,0,$src_x,$src_y,$src_x,$src_y);
		$tmp_path = realpath("./temp");
		imagepng($dst_img,$tmp_path."/tmp_cms_logo");
		$data = file_get_contents($picture);
		unlink($tmp_path."/tmp_cms_logo");
		return $data;
	}

	public function show_picture($mode=""){
  		global $cms_active_image_cache,$base_path;
  		header("Content-Type: image/png");
  		if($cms_active_image_cache && file_exists($base_path."/temp/cms_vign/".$mode."/".$this->type.$this->id.".png")){
  			print file_get_contents($base_path."/temp/cms_vign/".$mode."/".$this->type.$this->id.".png");
  		}else{
  			if(strpos($mode,"custom_") !== false){
  				$elems = explode("_",$mode);
  				$size = $elems[1]*1;
  				if($size>0){
	  				$dst_img=$this->resize($size,$size);
  				}else{
	  				$dst_img=$this->resize(500,500);
  				}	
  			}else{
				switch($mode){
					case "small_vign" :
						$dst_img=$this->resize(16,16);
					break;
					case "vign" :
						$dst_img=$this->resize(100,100);
						break;
					case "small" :
						$dst_img=$this->resize(140,140);
						break;
					case "medium" :
						$dst_img=$this->resize(300,300);
					break;
					case "big" :
						$dst_img=$this->resize(600,600);
						break;
					case "large" :
					default :
						$dst_img=$this->resize(0,0);
					break;
				}
			}
  			if($cms_active_image_cache && $dst_img){
	  			$this->init_cache_path($mode);
	  			imagepng($dst_img,$base_path."/temp/cms_vign/".$mode."/".$this->type.$this->id.".png");
	  		}
  		}
	}
	
	private function init_cache_path($mode){
		global $base_path;
		if(!file_exists($base_path."/temp/cms_vign")){
			mkdir($base_path."/temp/cms_vign");
		}
		if(!file_exists($base_path."/temp/cms_vign/".$mode)){
			mkdir($base_path."/temp/cms_vign/".$mode);
		}
	}
	
	public function get_vign(){
	   $this->resize(100,100);
	}

	public function get_small_vign(){
	   $this->resize(16,16);
	}

	public function get_large(){
		$this->resize(0,0);
	}

	protected function resize($size_x=0,$size_y=0){
		if($this->data){
			$src_img = imagecreatefromstring($this->data);
			$maxX=$size_x;
			$maxY=$size_y;

			if(!$size_x && !$size_y){
				ImageSaveAlpha($src_img, true);
				ImageAlphaBlending($src_img, false);
				imagepng($src_img);
				return $src_img;
			}else if ($src_img) {
				$rs=$maxX/$maxY;
				$taillex=imagesx($src_img);
				$tailley=imagesy($src_img);
				if (!$taillex || !$tailley) return "" ;
				if (($taillex>$maxX)||($tailley>$maxY)) {
					$r=$taillex/$tailley;
					if (($r<1)&&($rs<1)) {
						//Si x plus petit que y et taille finale portrait
						//Si le format final est plus large en proportion
						if ($rs>$r) {
							$new_h=$maxY;
							$new_w=$new_h*$r;
						} else {
							$new_w=$maxX;
							$new_h=$new_w/$r;
						}
					} else if (($r<1)&&($rs>=1)){
						//Si x plus petit que y et taille finale paysage
						$new_h=$maxY;
						$new_w=$new_h*$r;
					} else if (($r>1)&&($rs<1)) {
						//Si x plus grand que y et taille finale portrait
						$new_w=$maxX;
						$new_h=$new_w/$r;
					} else {
						//Si x plus grand que y et taille finale paysage
						if ($rs<$r) {
							$new_w=$maxX;
							$new_h=$new_w/$r;
						} else {
							$new_h=$maxY;
							$new_w=$new_h*$r;
						}
					}
				} else {
					$new_h = $tailley ;
					$new_w = $taillex ;
				}
				$dst_img=imagecreatetruecolor($new_w,$new_h);
				ImageSaveAlpha($dst_img, true);
				ImageAlphaBlending($dst_img, false);
				imagefilledrectangle($dst_img,0,0,$maxX,$maxY,imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
				imagecopyresampled($dst_img,$src_img,0,0,0,0,$new_w,$new_h,ImageSX($src_img),ImageSY($src_img));
				imagepng($dst_img);
				return $dst_img;
			}
		}else{
			print file_get_contents("./images/vide.png");
		}
	}

	public function get_vign_url($mode=""){
		global $opac_url_base;
		return $opac_url_base."cms_vign.php?type=".$this->type."&id=".$this->id."&mode=".$mode;
	}

	public function format_datas(){
		return array(
			'small_vign' => $this->get_vign_url("small_vign"),
			'vign' =>		$this->get_vign_url("vign"),
			'small' =>		$this->get_vign_url("small"),
			'medium' =>		$this->get_vign_url("medium"),
			'big' =>		$this->get_vign_url("big"),
			'large' =>		$this->get_vign_url("large"),
			'custom' =>		$this->get_vign_url("custom_"),
			'exists' =>		($this->data ? true : false)
		);
	}


	public static function get_format_data_structure(){
		global $msg;
		return array(
			array(
				'var' => "small_vign",
				'desc' => $msg['cms_module_common_datasource_desc_small_vign']
			),
			array(
				'var' => "vign",
				'desc' => $msg['cms_module_common_datasource_desc_vign']
			),
			array(
				'var' => "small",
				'desc' => $msg['cms_module_common_datasource_desc_small']
			),
			array(
				'var' => "medium",
				'desc' => $msg['cms_module_common_datasource_desc_medium']
			),
			array(
				'var' => "big",
				'desc' => $msg['cms_module_common_datasource_desc_big']
			),
			array(
				'var' => "large",
				'desc' => $msg['cms_module_common_datasource_desc_large']
			),
			array(
				'var' => "custom",
				'desc' => $msg['cms_module_common_datasource_desc_custom']
			),
			array(
				'var' => "exists",
				'desc' => $msg['cms_module_common_datasource_desc_logo_exists']
			)
		);
	}
}