<?php

/*
 * Ce script effectue une redirection vers la notice dont le champ perso "cp_eli" est appele dans l'URL.
 * Il est place dans le repertoire pmb/opac_css
 * Il necessite une reecriture d'URL dans ce meme repertoire :
 * 
 * RewriteEngine On
 * RewriteRule ^eli/ /eli.php? [L]
 * 
 */

$base_path='.';

require_once($base_path."/includes/init.inc.php");
require_once($base_path."/includes/error_report.inc.php") ;
require_once($base_path."/includes/global_vars.inc.php");
require_once($base_path.'/includes/opac_config.inc.php');

//Attaques XSS et injection SQL
if(isset($nbr_lignes)){
    $nbr_lignes+=0;
}
if(isset($page)){
    $page+=0;
}
if(isset($main)){
    $main+=0;
}
// récupération paramètres MySQL et connection á la base
if (file_exists($base_path.'/includes/opac_db_param.inc.php')) require_once($base_path.'/includes/opac_db_param.inc.php');
else die("Fichier opac_db_param.inc.php absent / Missing file Fichier opac_db_param.inc.php");

require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();

//Sessions !! Attention, ce doit être impérativement le premier include (à cause des cookies)
require_once($base_path."/includes/session.inc.php");

require_once($base_path.'/includes/start.inc.php');
//ATTENTION avec les vues. à partir d'ici et jusqu'au chargement des vues: les variables globales sont celles par défauts et non celles de la vue

$str_eli = '';
$pos_eli = stripos($_SERVER['REQUEST_URI'],'/eli/');
$id_eli = 0;

if ($pos_eli !== false) {
    $str_eli = substr($_SERVER['REQUEST_URI'],$pos_eli);
    
   $q  = 'select notices_custom_origine from notices_custom_values where notices_custom_champ=(select idchamp from notices_custom where name="cp_eli") and notices_custom_small_text like "%'.addslashes($str_eli).'" ';
   $r = pmb_mysql_query($q, $dbh);
   
   if(pmb_mysql_num_rows($r) == 1) {
       $id_eli=pmb_mysql_result($r,0,0);
      header('Location: '.$opac_url_base.'index.php?lvl=notice_display&id='.$id_eli);
   } else {
       //Oups !
       //Non trouvé ou trop de résultats 
       header('Location: '.$opac_url_base.'index.php?');
   }
}