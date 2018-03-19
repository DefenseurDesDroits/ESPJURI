<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_document.class.php,v 1.8.2.6 2017-03-15 14:10:43 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/explnum.inc.php");
require_once($class_path."/storages/storages.class.php");
create_tableau_mimetype();

class cms_document {
	public $id=0;
	public $title="";
	public $description="";
	public $filename="";
	public $mimetype="";
	public $filesize="";
	public $vignette="";
	public $url="";
	public $path ="";
	public $create_date="";
	public $num_storage=0;
	public $type_object="";
	public $num_object=0;
	protected $human_size = 0;
	protected $storage;
	public $used=array();
	
	public function __construct($id=0){
		$this->id = $id*1;
		$this->fetch_datas_cache();
	}
	
	protected function fetch_datas_cache(){
		if($tmp=cms_cache::get_at_cms_cache($this)){
			$this->restore($tmp);
		}else{
			$this->fetch_datas();
			cms_cache::set_at_cms_cache($this);
		}
	}
	
	protected function restore($cms_object){
		foreach(get_object_vars($cms_object) as $propertieName=>$propertieValue){
			$this->{$propertieName}=$propertieValue;
		}
	}
	
	protected function fetch_datas(){
		if($this->id){
			$query = "select document_title,document_description,document_filename,document_mimetype,document_filesize,document_vignette,document_url,document_path,document_create_date,document_num_storage,document_type_object,document_num_object from cms_documents where id_document = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->title = $row->document_title;
				$this->description = $row->document_description;
				$this->filename = $row->document_filename;
				$this->mimetype = $row->document_mimetype;
				$this->filesize = $row->document_filesize;
				$this->vignette = $row->document_vignette;
				$this->url = $row->document_url;
				$this->path = $row->document_path;
				$this->create_date = $row->document_create_date;
				$this->num_storage = $row->document_num_storage;
				$this->type_object = $row->document_type_object;
				$this->num_object = $row->document_num_object;
			}
			if($this->num_storage){
				$this->storage = storages::get_storage_class($this->num_storage);
			}
			
			//r�cup�ration des utilisations
			$query = "select * from cms_documents_links where document_link_num_document = '".$this->id."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row=pmb_mysql_fetch_object($result)){
					if(!$this->used[$row->document_link_type_object]) $this->used[$row->document_link_type_object] = array();
					$this->used[$row->document_link_type_object][] = $row->document_link_num_object;
				}
			}
		}
	}
	
	public function get_item_render($edit_js_function="openEditDialog"){
		global $msg,$charset;
		$item = "
		<div class='document_item' id='document_".$this->id."'>
			<div class='document_item_content'>
			<img src='".$this->get_vignette_url()."'/>
			<br/>
			<p> <a href='#' onclick='".$edit_js_function."(".$this->id.");return false;' title='".htmlentities($msg['cms_document_edit_link'])."'>".htmlentities(($this->title ? $this->title : $this->filename),ENT_QUOTES,$charset)."</a><br />
			<span style='font-size:.8em;'>".htmlentities($this->mimetype,ENT_QUOTES,$charset).($this->filesize ? " - (".$this->get_human_size().")" : "")."</span></p>
			</div>
		</div>";
		return $item;
	}
	
	public function get_item_form($selected = false,$edit_js_function="openEditDialog"){
		global $msg,$charset;
		$item = "
		<div class='document_item".($selected? " document_item_selected" : "")."' id='document_".$this->id."'>
			<div class='document_item_content'>
				<img src='".$this->get_vignette_url()."'/>
				<br/>
				<p> <a href='#' onclick='".$edit_js_function."(".$this->id.");return false;' title='".htmlentities($msg['cms_document_edit_link'])."'>".htmlentities(($this->title ? $this->title : $this->filename),ENT_QUOTES,$charset)."</a><br />
				<span style='font-size:.8em;'>".htmlentities($this->mimetype,ENT_QUOTES,$charset).($this->filesize ? " - (".$this->get_human_size().")" : "")."</span></p>
			</div>
			<div class='document_checkbox'>
				<input name='cms_documents_linked[]' onchange='document_change_background(".$this->id.");' type='checkbox'".($selected ? "checked='checked'" : "")." value='".htmlentities($this->id,ENT_QUOTES,$charset)."'/>
			</div>
		</div>";
		return $item;
	}
	
	public function get_vignette_url(){
		global $opac_url_base;
		return "./ajax.php?module=cms&categ=document&action=thumbnail&id=".$this->id;
	}
		
	public function get_document_url(){
		global $opac_url_base;
		return "./ajax.php?module=cms&categ=document&action=render&id=".$this->id;
	}
			
	public function get_human_size(){
		$units = array("o","Ko","Mo","Go");
		$i=0;
		do{
			if(!$this->human_size)$this->human_size = $this->filesize;
			$this->human_size = $this->human_size/1024;	
			$i++;
		}while($this->human_size >= 1024);
		return round($this->human_size,1)." ".$units[$i];
	}
	
	public function get_form($action="./ajax.php?module=cms&categ=documents&action=save_form&id="){
		global $msg,$charset,$opac_url_base;
		global $_mimetypes_bymimetype_;
		
		$form = "
		<form name='cms_document_form' id='cms_document_form' method='POST' action='".$action.$this->id."' style='width:800px;' enctype='multipart/form-data'>
			<div class='form-contenu'>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_document_title'>".htmlentities($msg['cms_document_title'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						<input type='text' name='cms_document_title' value='".htmlentities($this->title,ENT_QUOTES,$charset)."'/>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_document_description'>".htmlentities($msg['cms_document_description'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						<textarea name='cms_document_description' >".htmlentities($this->description,ENT_QUOTES,$charset)."</textarea>
					</div>
				</div>";
		if($this->url){
			$form.= "
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_document_url'>".htmlentities($msg['cms_document_url'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						<input type='text' name='cms_document_url' value='".htmlentities($this->url,ENT_QUOTES,$charset)."'/>
					</div>
				</div>";
		}
		
		create_tableau_mimetype();
		$selector_mimetype = "<select id='cms_document_mime_vign' name='cms_document_mime_vign'><option value=''>".htmlentities($msg['explnum_no_mimetype'], ENT_QUOTES, $charset)."</option>";
		foreach($_mimetypes_bymimetype_ as $key=>$val){
			$selector_mimetype .= "<option value='".$key."' $selected >".htmlentities($key, ENT_QUOTES, $charset)."</option>";
		}
		$selector_mimetype .= "</select>";
		
		if($this->id){	
			$form.= "
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_document_vign_file'>".htmlentities($msg['explnum_vignette'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						<input id='cms_document_vign_file' class='saisie-80em' type='file' size='65' name='cms_document_vign_file'>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_document_mime_vign'>".htmlentities($msg['explnum_mime_label'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						".$selector_mimetype."
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_document_vign'>".htmlentities($msg['cms_document_vign'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						<input type='checkbox' name='cms_document_vign' value='1'/>
					</div>
				</div>";
		}
		$form.="
				<div class='row'>&nbsp;</div>
				<div class='row'>
					<div class='colonne3'>
						<label>".htmlentities($msg['cms_document_filename'],ENT_QUOTES,$charset)."</label>
						<br />
						<label>".htmlentities($msg['cms_document_mimetype'],ENT_QUOTES,$charset)."</label>
						<br />
						<label>".htmlentities($msg['cms_document_filesize'],ENT_QUOTES,$charset)."</label>
						<br />
						<label>".htmlentities($msg['cms_document_date'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						<span>".htmlentities($this->filename,ENT_QUOTES,$charset)."</span>
						<br />
						<span>".htmlentities($this->mimetype,ENT_QUOTES,$charset)."</span>
						<br />
						<span>".htmlentities($this->get_human_size(),ENT_QUOTES,$charset)."</span>
						<br />
						<span>".htmlentities(format_date($this->create_date),ENT_QUOTES,$charset)."</span>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label>".htmlentities($msg['cms_document_storage'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						".$this->storage->get_storage_infos()."
					</div>
				</div>";
		if($this->id){
			$form.="
				<div class='row'>
					<div class='colonne3'>
						<label>".htmlentities($msg['cms_document_url'],ENT_QUOTES,$charset)."</label>
					</div>
					<div class='colonne_suite'>
						".$opac_url_base."ajax.php?module=cms&categ=document&action=render&id=".$this->id."
					</div>
				</div>";
		}
		$form.="
				<div class='row'>&nbsp;</div>
				<div class='row'>";
		//utilisation
		if(count($this->used)){
			$form.="
					<label>".htmlentities($msg['cms_document_use'],ENT_QUOTES,$charset)."</label>
					<table>
						<tr>
							<th><input class='bouton' type='button' title='".$msg['cms_document_check_all']."' name='button_cms_document_check_all' value='+' onclick='cms_document_check_all(this.form);'></th>
							<th>".htmlentities($msg['cms_document_used_type'],ENT_QUOTES,$charset)."</th>
							<th>".htmlentities($msg['cms_document_used_title'],ENT_QUOTES,$charset)."</th>
						</tr>";
			foreach($this->used as $type => $used){
				$query ="";
				switch($type){
					case "article" :
						$query = "select id_article as id ,article_title as title from cms_articles where id_article in (".implode(",",$used).") order by 2";
						$use_link = "./cms.php?categ=article&sub=edit&id=";
						break;
					case "section" :
						$query = "select id_section as id,section_title as title from cms_sections where id_section in (".implode(",",$used).") order by 2";
						$use_link = "./cms.php?categ=section&sub=edit&id=";
						break;
				}
				if($query){
					$result = pmb_mysql_query($query) ;
					if(pmb_mysql_num_rows($result)){
						$id=1;
						$array_ids = array();
						while($row = pmb_mysql_fetch_object($result)){
							$form.="
						<tr>
							<td><input type='checkbox' value='".$type."_".$row->id."' name='used[]' id='used_".$id."'/></td>
							<td>".htmlentities($msg['cms_document_used_type_'.$type],ENT_QUOTES,$charset)."</td>
							<td><a target='_blank' href='".$use_link.$row->id."' >".htmlentities($row->title,ENT_QUOTES,$charset)."</a></td>
						</tr>";
							$array_ids[]=$id;
							$id++;
						}
					}
				}
			}
			$form.="
					</table>
					<input type='hidden' name='used_list' value='".implode('|',$array_ids)."'>
				";
		}else{
			$form.="
					<label>".htmlentities($msg['cms_document_not_use'],ENT_QUOTES,$charset)."</label>";
		}
		$form.="
				</div>
				<hr />
				<div class='row'>
					<div class='left'>
						<input type='submit' class='bouton'  value='".htmlentities($msg['cms_document_save'],ENT_QUOTES,$charset)."'/>
					</div>
					<div class='right'>
						<input type='button' class='bouton' id='use_del_button' value='".htmlentities($msg['cms_document_delete_use'],ENT_QUOTES,$charset)."'/>	
						<input type='button' class='bouton' id='doc_del_button' value='".htmlentities($msg['cms_document_delete'],ENT_QUOTES,$charset)."'/>
					</div>
				</div>
				<div class='row'></div>
			</div>
		</form>
		<script>
			function cms_document_check_all(form){
				console.log(form.used);
				y=form.used_list.value;
				ids=y.split('|');
				while (ids.length>0) {
					id=ids.shift();
					document.getElementById('used_'+id).click();
				}
			}								
			
			require(['dojo/dom-construct',
			        'dojo/on',
			        'dojo/request',
			        'dojo/dom-form',
       				'dojo/_base/lang',
					'dojo/request/iframe'
			], function(domConstruct, on, request, domForm, lang, iframe) {		
								
				form = dojo.byId('cms_document_form');	
				form.setAttribute('accept-charset','utf-8');
				dojo.connect(form, 'onsubmit', function(event) {								
					dojo.stopEvent(event);
								
					iframe('".$action.$this->id."',{		
						form : 'cms_document_form',
						handleAs: 'html'
					}).then(function(data) {
 							domConstruct.place(data.body,'document_".$this->id."','replace');
 							dijit.byId('dialog_document').hide();	
						
					}, function(err){console.log('err', err);});								
				});									
				dojo.connect(dojo.byId('doc_del_button'),'onclick',function(event){
					if(confirm('".addslashes($msg['cms_document_confirm_delete'])."')){
						var xhrArgs = {
							url : '".str_replace("action=save_form","action=delete",$action).$this->id."',
							handleAs: 'text',
							load: function(data){
								if(data == 1){
									dojo.byId('document_".$this->id."').parentNode.removeChild(dojo.byId('document_".$this->id."'));
								}else{
									alert(data);
								}
								dijit.byId('dialog_document').hide();
							}
						};
						dojo.xhrGet(xhrArgs);
					}
				});
				dojo.connect(dojo.byId('use_del_button'),'onclick',function(event){
					if(confirm('".addslashes($msg['cms_document_confirm_use_delete'])."')){
						var xhrArgs = {
							url : '".str_replace("action=save_form","action=delete_use",$action).$this->id."',
							handleAs: 'text',
							form : dojo.byId('cms_document_form'),
							load: function(data){
								if(data == 1){
									dijit.byId('dialog_document').hide();
									window.setTimeout(function(){openEditDialog('".$this->id."')},1000);
 									
								}else{
									alert(data);
								}
								dijit.byId('dialog_document').hide();
							}
						};
						dojo.xhrPost(xhrArgs);
					}
				});
			});
		</script>";
		return $form;
	}
	
	function save_form($caller="collection"){
		global $msg, $charset;
		global $cms_document_title, $cms_document_description, $cms_document_url, $cms_document_vign;
		global $cms_document_mime_vign;
		global $cms_document_collection, $base_path;
		global $prefix_url_image;
		
		$this->title = stripslashes($cms_document_title);
		$this->description = stripslashes($cms_document_description);
		$this->url = stripslashes($cms_document_url);
		$flag_collection_change=0;
		if($cms_document_collection){
			if($this->num_object != $cms_document_collection){
				$this->num_object = $cms_document_collection*1;
				$flag_collection_change=1;
			}
		}
		// gestion de la vignette
		if(!$cms_document_mime_vign && $vignette_name = $_FILES['cms_document_vign_file']['name']) {
			$vignette_temp = $_FILES['cms_document_vign_file']['tmp_name'];
			$vignette_moved = basename($vignette_temp);
			$vignette_name = preg_replace("/ |'|\\|\"|\//m", "_", $vignette_name);
			
			move_uploaded_file($vignette_temp,$base_path.'/temp/'.$vignette_moved);
			$this->vignette = construire_vignette($vignette_moved, $userfile_moved);
			$maj_vignette = 1 ;
		}elseif(!$cms_document_mime_vign && $cms_document_vign){
			$this->calculate_vignette();
			$maj_vignette = 1 ;
		}else if($cms_document_mime_vign) {
			if ($prefix_url_image) {
				$tmpprefix_url_image = $prefix_url_image;
			}else {
				$tmpprefix_url_image = "./";
			}
			$vignette = $tmpprefix_url_image."images/mimetype/".icone_mimetype($cms_document_mime_vign, "");
			$fp = fopen($vignette , "r" );
			if ($fp) $contenu_vignette = fread ($fp, filesize($vignette));
			if($contenu_vignette) {
				$this->vignette = $contenu_vignette;
				$maj_vignette = 1 ;
			}
		}

		if($this->id){
			$query = "update cms_documents set ";
			$clause = " where id_document = ".$this->id;
		}else{
			$query = "insert into cms_documents set ";
			$clause="";
		}
		
		$query.= "
			document_title = '".addslashes($this->title)."',
			document_description = '".addslashes($this->description)."',
			document_url = '".addslashes($this->url)."'";
		if($maj_vignette){
			$query.= ",
			document_vignette = '".addslashes($this->vignette)."'";	
		}
		if(pmb_mysql_query($query.$clause)){
			if($caller = "editorial_form"){
				return $this->get_item_form(true,"openEditDialog");
			}else{
				return $this->get_item_render("openEditDialog");
			}
		}
	}
	
	function delete(){
		global $msg;
	
		//v�rification d'utilisation
		$query = "select * from cms_documents_links where document_link_num_document = '".$this->id."'";
		$result = pmb_mysql_query($query);
		if(!pmb_mysql_num_rows($result)){
			//suppression physique
			if($this->storage->delete($this->path.$this->filename)){
				//il ne reste plus que la base
				if(pmb_mysql_query("delete from cms_documents where id_document = '".$this->id."'")){
					return true;
				}
			}else{
				return $msg['cms_document_delete_physical_error'];
			}
		}else{
			return $msg['cms_document_delete_document_used'];
		}
		return false;
	}
	
	function calculate_vignette(){
		error_reporting(null);
		global $base_path,$include_path,$class_path;
		$path = $this->get_document_in_tmp();
		if($path){
			switch($this->mimetype){
				case "application/bnf+zip" :
					require_once($class_path."/docbnf_zip.class.php");
					$doc = new docbnf_zip($path);
					$this->vignette = construire_vignette($doc->getCover());
					break;
				case "application/epub+zip" :
					require_once($class_path."/epubData.class.php");
					$doc = new epub_Data($path);
					file_put_contents($path, $doc->getCoverContent());
					$this->vignette = construire_vignette($path);
					break;
				default :
					$this->vignette = construire_vignette($path);
					break;
			}
			unlink($path);
		}
	}
	
	function regen_vign(){
		$this->calculate_vignette();
		pmb_mysql_query("update cms_documents set document_vignette = '".addslashes($this->vignette)."' where id_document = ".$this->id);
	}
	
	function get_document_in_tmp(){
		$this->clean_tmp();
		global $base_path;
		$path = tempnam($base_path."/temp/", "cms_document_");
 		if($this->storage->duplicate($this->path.$this->filename,$path)){
 			return $path;
 		}
		return false;
	}
	
	protected function clean_tmp(){
		global $base_path;
		$dh = opendir($base_path."/temp/");
		if (!$dh) return;
		$files = array();
		while (($file = readdir($dh)) !== false){
			if ($file != "." && $file != ".." && substr($file,0,strlen("cms_document_")) == "cms_document_") {
				$stat = stat($base_path."/temp/".$file);
				$files[$file] = array("mtime"=>$stat['mtime']);
			}
		}
		closedir($dh);
		$deleteList = array();
		foreach ($files as $file => $stat) {
			//si le dernier acc�s au fichier est de plus de 3h, on vide...
			if(time() - $stat["mtime"] > (3600*3)){
				if(is_dir($base_path."/temp/".$file)){
					$this->rrmdir($base_path."/temp/".$file);
				}else{
					unlink($base_path."/temp/".$file);
				}
			}
		}
	}
	
	function rrmdir($dir){
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir"){
						$this->rrmdir($dir."/".$object);
					}else{
						unlink($dir."/".$object);
					}
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
	
	public function format_datas(){
		$collection = new cms_collection($this->num_object);
		
		$datas = array(
			'id' => $this->id,
			'name' => $this->title,
			'description' => $this->description,
			'filename' => $this->filename,
			'mimetype' => $this->mimetype,
			'filesize' => array(
				'human' => $this->get_human_size(),
				'value' => $this->filesize
			),
			'url' => $this->get_document_url(),
			'create_date' => $this->create_date,
			'thumbnails_url' => $this->get_vignette_url()
		);
		$datas['collection'] = $collection->get_infos();
		return $datas;
	}
	
	public function get_format_data_structure(){
		global $msg;
		$format_datas = array();
		$format_datas[] = array(
			'var' => "id",
			'desc'=> $msg['cms_document_format_data_id']
		);
		$format_datas[] = array(
			'var' => "name",
			'desc'=> $msg['cms_document_format_data_name']
		);	
		$format_datas[] = array(
			'var' => "description",
			'desc'=> $msg['cms_document_format_data_description']
		);
		$format_datas[] = array(
			'var' => "filename",
			'desc'=> $msg['cms_document_format_data_filename']
		);		
		$format_datas[] = array(
			'var' => "mimetype",
			'desc'=> $msg['cms_document_format_data_mimetype']
		);
		$format_datas[] = array(
			'var' => "filesize",
			'desc'=> $msg['cms_document_format_data_filesize'],
			'children' => array(
				array(
					'var' => "filesize.human",
					'desc'=> $msg['cms_document_format_data_filesize_human']
				),
				array(
					'var' => "filesize.value",
					'desc'=> $msg['cms_document_format_data_filesize_value']
				)
			)
		);	
		$format_datas[] = array(
				'var' => "url",
				'desc'=> $msg['cms_document_format_data_url']
		);
		$format_datas[] = array(
				'var' => "create_date",
				'desc'=> $msg['cms_document_format_data_create_date']
		);
		$format_datas[] = array(
				'var' => "thumbnails_url",
				'desc'=> $msg['cms_document_format_data_thumbnails_url']
		);	
		$format_datas[] = array(
			'var' => "collection",
			'desc'=> $msg['cms_document_format_data_collection'],
			'children' => array(
				array(
					'var' => "collection.id",
					'desc'=> $msg['cms_document_format_data_collection_id']
				),
				array(
					'var' => "collection.name",
					'desc'=> $msg['cms_document_format_data_collection_name']
				),
				array(
					'var' => "collection.description",
					'desc'=> $msg['cms_document_format_data_collection_description']
				)
			)
		);
		return $format_datas;
	}
	
	public function render_thumbnail(){
		header('Content-Type: image/png');
		if($this->vignette){
 			print $this->vignette;	
		}else{
			global $prefix_url_image ;
			if ($prefix_url_image) $tmpprefix_url_image = $prefix_url_image;
			else $tmpprefix_url_image = "./" ;
			print file_get_contents($tmpprefix_url_image."images/mimetype/".icone_mimetype($this->mimetype,substr($this->filename,strrpos($this->filename,".")+1)));
		}
	}
	
	public function render_doc(){
		$content = $this->storage->get_content($this->path.$this->filename);
		if($content){
			header('Content-Type: '.$this->mimetype);
			header("Content-Disposition: inline; filename=".$this->filename."");
			if($this->filesize) header("Content-Length: ".$this->filesize);
			print $content;
		}
	}
	
	public function delete_use(){
		global $used;
		
		$elem =array();
		for($i=0 ; $i<count($used) ; $i++){
			$tmp = explode("_",$used[$i]);
			$elem[$tmp[0]][]=$tmp[1];
		}
		foreach($elem as $type=> $elem){
			//TODO, v�rifier utilisation du document dans l'association
			$query = "delete from cms_documents_links where document_link_type_object = '".$type."' and document_link_num_object in (".implode(",",$elem).") and document_link_num_document = ".$this->id;
			$result = pmb_mysql_query($query);
			if(!$result) return false;
		}
		return true;
		
	}
}