<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serie_see.inc.php,v 1.29.2.4 2016-07-06 10:02:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// affichage du detail pour une serie
require_once($class_path."/authority.class.php");

if($id) {
	$id+=0;
	
	//LISTE DE NOTICES ASSOCIEES
	$recordslist = "";
	//droits d'acces emprunteur/notice
	$acces_j='';
	if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
		require_once("$class_path/acces.class.php");
		$ac= new acces();
		$dom_2= $ac->setDomain(2);
		$acces_j = $dom_2->getJoin($_SESSION['id_empr_session'],4,'notice_id');
	}
	
	if($acces_j) {
		$statut_j='';
		$statut_r='';
	} else {
		$statut_j=',notice_statut';
		$statut_r="and statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
	}
	
	// affichage des notices associ�es
	$recordslist= "<h3><span class=\"aut_details_liste_titre\">$msg[doc_serie_title]</span></h3>\n";
	
	// comptage des notices associ�es
	if(!$nbr_lignes) {
		//$requete = "SELECT COUNT(notice_id) FROM notices, notice_statut ";
		//$requete .= " where tparent_id='$id' and (statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"")."))";
		$requete = "SELECT COUNT(*) FROM notices $acces_j $statut_j where tparent_id=$id $statut_r ";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr_lignes = @pmb_mysql_result($res, 0, 0);
	
		//Recherche des types doc
		//$requete="select distinct notices.typdoc FROM notices, notice_statut ";
		//$requete .= " where tparent_id='$id' and (statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"")."))";
		$requete="select distinct typdoc,count(explnum_id) as nbexplnum FROM notices left join explnum on explnum_notice=notice_id $acces_j $statut_j where tparent_id=$id $statut_r group by typdoc";
		$res = pmb_mysql_query($requete, $dbh);
	
		$t_typdoc=array();
		$nbexplnum_to_photo=0;
		if ($res) {
			while ($tpd=pmb_mysql_fetch_object($res)) {
				$t_typdoc[]=$tpd->typdoc;
				$nbexplnum_to_photo += $tpd->nbexplnum;
			}
		}
		$l_typdoc=implode(",",$t_typdoc);
	}else if($opac_visionneuse_allow){
		$requete="select count(explnum_id) as nbexplnum FROM notices left join explnum on explnum_notice=notice_id $acces_j $statut_j where tparent_id=$id $statut_r group by typdoc";
		$res = pmb_mysql_query($requete, $dbh);
		$nbexplnum_to_photo=0;
		if ($res) {
			while ($tpd=pmb_mysql_fetch_object($res)) {
				$nbexplnum_to_photo += $tpd->nbexplnum;
			}
		}
	}
	
	
	if(!$page) $page=1;
	$debut =($page-1)*$opac_nb_aut_rec_per_page;
	
	if($nbr_lignes) {
		// on lance la vraie requ�te
		//$requete  = "SELECT notice_id FROM notices, notice_statut WHERE tparent_id='$id' and (statut=id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"")."))";
		$requete  = "SELECT  notice_id FROM notices $acces_j $statut_j where tparent_id=$id $statut_r ";
	
		//gestion du tri
		if (isset($_GET["sort"])) {
			$_SESSION["last_sortnotices"]=$_GET["sort"];
		}
		if ($nbr_lignes>$opac_nb_max_tri) {
			$_SESSION["last_sortnotices"]="";
		}
		if ($_SESSION["last_sortnotices"]!="") {
			$sort = new sort('notices','session');
			$requete = $sort->appliquer_tri($_SESSION["last_sortnotices"], $requete, "notice_id", $debut, $opac_nb_aut_rec_per_page);
		} else {
			$requete .= " LIMIT $debut,$opac_nb_aut_rec_per_page ";
		}
		//fin gestion du tri
	
		$res = @pmb_mysql_query($requete, $dbh);
		if ($opac_notices_depliable) $recordslist.= $begin_result_liste;
			
		//gestion du tri
		if ($nbr_lignes<=$opac_nb_max_tri) {
			$pos=strpos($_SERVER['REQUEST_URI'],"?");
			$pos1=strpos($_SERVER['REQUEST_URI'],"get");
			if ($pos1==0) $pos1=strlen($_SERVER['REQUEST_URI']);
			else $pos1=$pos1-3;
			$para=urlencode(substr($_SERVER['REQUEST_URI'],$pos+1,$pos1-$pos+1));
			$para1=substr($_SERVER['REQUEST_URI'],$pos+1,$pos1-$pos+1);
			$affich_tris_result_liste=str_replace("!!page_en_cours!!",$para,$affich_tris_result_liste);
			$affich_tris_result_liste=str_replace("!!page_en_cours1!!",$para1,$affich_tris_result_liste);
			$recordslist.= $affich_tris_result_liste;
			if ($_SESSION["last_sortnotices"]!="") {
				$recordslist.= "<span class='sort'>".$msg['tri_par']." ".$sort->descriptionTriParId($_SESSION["last_sortnotices"])."<span class=\"espaceResultSearch\">&nbsp;</span></span>";
			}
		} else $recordslist.= "<span class=\"espaceResultSearch\">&nbsp;</span>";
		//fin gestion du tri
	
		$recordslist.= $add_cart_link;
	
		if($opac_visionneuse_allow && $nbexplnum_to_photo){
			$recordslist.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span>".$link_to_visionneuse;
			$sendToVisionneuseByGet = str_replace("!!mode!!","serie_see",$sendToVisionneuseByGet);
			$sendToVisionneuseByGet = str_replace("!!idautorite!!",$id,$sendToVisionneuseByGet);
			$recordslist.= $sendToVisionneuseByGet;
		}
	
		if ($opac_show_suggest) {
			$bt_sugg = "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span><span class=\"search_bt_sugg\"><a href=# ";
			if ($opac_resa_popup) $bt_sugg .= " onClick=\"w=window.open('./do_resa.php?lvl=make_sugg&oresa=popup','doresa','scrollbars=yes,width=600,height=600,menubar=0,resizable=yes'); w.focus(); return false;\"";
			else $bt_sugg .= "onClick=\"document.location='./do_resa.php?lvl=make_sugg&oresa=popup' \" ";
			$bt_sugg.= " title='".$msg["empr_bt_make_sugg"]."' >".$msg[empr_bt_make_sugg]."</a></span>";
			$recordslist.= $bt_sugg;
	
		}
	
		//affinage
		//enregistrement de l'endroit actuel dans la session
		rec_last_authorities();
	
		//affichage
		$recordslist.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"affiner_recherche\"><a href='$base_path/index.php?search_type_asked=extended_search&mode_aff=aff_module' title='".$msg["affiner_recherche"]."'>".$msg["affiner_recherche"]."</a></span>";
		//fin affinage
	
		$recordslist.= "<blockquote>\n";
		$recordslist.= aff_notice(-1);
			while(($obj=pmb_mysql_fetch_object($res))) {
			$recordslist.= pmb_bidi(aff_notice($obj->notice_id));
			}
			$recordslist.= aff_notice(-2);
			$recordslist.= "</blockquote>\n";
		pmb_mysql_free_result($res);
	
			// constitution des liens
	
			$nbepages = ceil($nbr_lignes/$opac_nb_aut_rec_per_page);
// 			$recordslist.= "</div><!-- fermeture aut_details_liste -->\n";
		$recordslist.= "<div id='navbar'><hr /><center>".printnavbar($page, $nbepages, "./index.php?lvl=serie_see&id=$id&page=!!page!!&nbr_lignes=$nbr_lignes&l_typdoc=".rawurlencode($l_typdoc))."</center></div>\n";
	} else {
			$recordslist.= $msg["no_document_found"];
	}
	
	$context = array();
	$context['authority']['recordslist'] = $recordslist;

	$authority = new authority("serie", $id);
	$authority->render($context);

	//FACETTES
	//gestion des facette si active
	require_once($base_path.'/classes/facette_search.class.php');
	$facettes_tpl = '';
	$records = "";
	//comparateur de facettes : on r�-initialise
	$_SESSION['facette']="";
	if($nbr_lignes){
		$requete  = "SELECT  notice_id FROM notices $acces_j $statut_j where tparent_id=$id $statut_r ";
		$facettes_result = pmb_mysql_query($requete,$dbh);
		while($row = pmb_mysql_fetch_object($facettes_result)){
			if($records){
				$records.=",";
			}
			$records.= $row->notice_id;
		}
	
		if(!$opac_facettes_ajax){
			$facettes_tpl .= facettes::make_facette($records);
		}else{
			$_SESSION['tab_result']=$records;
			$facettes_tpl .=facettes::call_ajax_facettes();
		}
		//Formulaire "FACTICE" pour l'application du comparateur et du filtre multiple...
		if($facettes_tpl) {
			$facettes_tpl.= '
			<form name="form_values" style="display:none;" method="post" action="'.$base_path.'/index.php?lvl=more_results&mode=extended">
				<input type="hidden" name="from_see" value="1" />
				'.facette_search_compare::form_write_facette_compare().'
			</form>';
		}
	}	
}