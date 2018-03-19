<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: input_ascodocpsy.inc.php,v 1.3.4.4 2017-03-03 13:06:52 mbertin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function _get_n_notices_($fi,$file_in,$input_params,$origine) {
	global $base_path,$charset;
	//pmb_mysql_query("delete from import_marc");
	
	$first=true;
	$stop=false;
	$content="";
	$index=array();
	$n=1;
	//Lecture du fichier d'entr�e
	while (($data = fgetcsv($fi,0,"\t",'"')) !== FALSE) {
		$notice=implode("'^'",$data);
		
		if($_SESSION["encodage_fic_source"]){//On a forc� l'encodage
			if(($charset == "utf-8") && ($_SESSION["encodage_fic_source"] == "iso8859")){
				if(function_exists("mb_convert_encoding")){
					$notice = mb_convert_encoding($notice,"UTF-8","Windows-1252");
				}else{
					$notice=utf8_encode($notice);
				}
			}elseif(($charset == "iso-8859-1" && ($_SESSION["encodage_fic_source"] == "utf8"))){
				if(function_exists("mb_convert_encoding")){
					$notice = mb_convert_encoding($notice,"Windows-1252","UTF-8");
				}else{
					$notice = utf8_decode($notice);
				}
			}
		}
		
		//Si c'est la premi�re notice, c'est la ligne d'intitul�s !!
		if ($first) {
			//les champs sont s�par�s par la tabulation
			$cols=explode("'^'",$notice);
			$fcols=fopen("$base_path/temp/".$origine."_cols.txt","w+");
			if ($fcols) {
				fwrite($fcols,serialize($cols));
				fclose($fcols);
			}
			$notice="";
			$first=false;
		}
		if ($notice) {
			$requete="insert into import_marc (no_notice, notice, origine) values($n,'".addslashes($notice)."','$origine')";
			pmb_mysql_query($requete);
			$n++;
			$t=array();
			$t["POS"]=$n;
			$t["LENGHT"]=1;
			$index[]=$t;
		}
	}
	return $index;
}


?>