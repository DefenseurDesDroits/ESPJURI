<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Yves PRATTER                                                   |
// +-------------------------------------------------+
// $Id: doc_num_data.php,v 1.15.2.1 2016-05-13 14:49:55 jpermanne Exp $

// d�finition du minimum n�c�ssaire 
$base_path     = ".";                            
$base_auth     = ""; //"CIRCULATION_AUTH";  
$base_title    = "";    
$base_noheader = 1;
//$base_nocheck  = 1;
$base_nobody   = 1;
$base_nosession   = 1;


require_once ("$base_path/includes/init.inc.php");  
require_once ($class_path."/explnum.class.php"); 

//gestion des droits
require_once($class_path."/acces.class.php");

$explnum = new explnum($explnum_id);

if (!$explnum->explnum_id) {
	exit ;
}

$id_for_rigths = $explnum->explnum_notice;
if($explnum->explnum_bulletin != 0){
	//si bulletin, les droits sont rattach�s � la notice du bulletin, � d�faut du p�rio...
	$req = "select bulletin_notice,num_notice from bulletins where bulletin_id =".$explnum->explnum_bulletin;
	$res = pmb_mysql_query($req);
	if(pmb_mysql_num_rows($res)){
		$row = pmb_mysql_fetch_object($res);
		$id_for_rigths = $row->num_notice;
		if(!$id_for_rigths){
			$id_for_rigths = $row->bulletin_notice;
		}
	}
}

//droits d'acces utilisateur/notice
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$rights = $dom_1->getRights($PMBuserid,$id_for_rigths);
}

if( $rights & 4 || (is_null($dom_1))){
	if ($content = $explnum->get_file_content()) {
		$file_name = $explnum->get_file_name();
		$size = $explnum->get_file_size();
		if ($force_download == 1) {
			if($file_name) header("Content-disposition: attachment; filename=".$file_name);
			header("Content-Transfer-Encoding: application/octet-stream");
			header("Pragma: no-cache");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
			header("Expires: 0");
		} else {
			if ($file_name) header("Content-Disposition: inline; filename=".$file_name);
		}
		
		header("Content-Type: ".$explnum->explnum_mimetype);
		header("Content-Length: ".$size);
		print $content;
		exit ;
	} else print "ERROR".pmb_mysql_error() ;
} else {
	print $msg["forbidden_docnum"];
}