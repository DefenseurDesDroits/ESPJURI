<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: access_rights.inc.php,v 1.1.2.3 2017-02-06 08:20:48 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/notice_tpl_gen.class.php');
require_once($class_path.'/progress_bar.class.php');
require_once($class_path.'/acces.class.php');

if($idcaddie) {
	
	$ac= new acces();
	if ($gestion_acces_user_notice==1) {
		$dom_1= $ac->setDomain(1);
	}
	if ($gestion_acces_empr_notice==1) {
		$dom_2= $ac->setDomain(2);
	}
	
	$myCart= new caddie($idcaddie);
	print pmb_bidi(aff_cart_titre ($myCart));

	@set_time_limit(0);
	$nb_elements_flag=$nb_elements_no_flag=0;
	$liste_0=$liste_1= array();
	if ($elt_flag) {
		$liste_0 = $myCart->get_cart("FLAG", $elt_flag_inconnu) ;
		$nb_elements_flag=count($liste_0);
	}	
	if ($elt_no_flag) {
		$liste_1= $myCart->get_cart("NOFLAG", $elt_no_flag_inconnu) ;
		$nb_elements_no_flag=count($liste_1);
	}	
	$liste= array_merge($liste_0,$liste_1);
	$nb_elements_total=count($liste);
	
	
	if($nb_elements_total){
		$pb=new progress_bar($msg['caddie_situation_access_rights_encours'],$nb_elements_total,5);
		if ($myCart->type=='NOTI'){
			while(list($cle, $object) = each($liste)) {
		    	if ($gestion_acces_user_notice==1) {
		    		$dom_1->delRessource($object);
		    		$dom_1->applyRessourceRights($object);
		    	}
		    	if ($gestion_acces_empr_notice==1) {
		    		$dom_2->delRessource($object);
		    		$dom_2->applyRessourceRights($object);
		    	}
		    	$pb->progress();
			}
		}elseif($myCart->type=='BULL'){
			while(list($cle, $object) = each($liste)) {
				$requete="SELECT bulletin_titre, num_notice FROM bulletins WHERE bulletin_id='".$object."'";
				$res=pmb_mysql_query($requete);
				if(pmb_mysql_num_rows($res)){
					$element=pmb_mysql_fetch_object($res);
					if(trim($element->bulletin_titre)){
						$requete="UPDATE bulletins SET index_titre=' ".addslashes(strip_empty_words($element->bulletin_titre))." ' WHERE bulletin_id='".$object."'";
						pmb_mysql_query($requete);
					}
					if($element->num_notice){
						if ($gestion_acces_user_notice==1) {
							$dom_1->delRessource($element->num_notice);
							$dom_1->applyRessourceRights($element->num_notice);
				    	}
				    	if ($gestion_acces_empr_notice==1) {
				    		$dom_2->delRessource($element->num_notice);
				    		$dom_2->applyRessourceRights($element->num_notice);
				    	}
					}

				}
				$pb->progress();
			}
		}elseif($myCart->type=='EXPL'){
			while(list($cle, $object) = each($liste)) {
				$requete="SELECT expl_notice, expl_bulletin FROM exemplaires WHERE expl_id='".$object."' ";
				$res=pmb_mysql_query($requete);
				if(pmb_mysql_num_rows($res)){
					$row=pmb_mysql_fetch_object($res);
					if($row->expl_notice){
						if ($gestion_acces_user_notice==1) {
				    		$dom_1->delRessource($row->expl_notice);
				    		$dom_1->applyRessourceRights($row->expl_notice);
				    	}
				    	if ($gestion_acces_empr_notice==1) {
				    		$dom_2->delRessource($row->expl_notice);
				    		$dom_2->applyRessourceRights($row->expl_notice);
				    	}
					}else{
						$requete="SELECT bulletin_titre, num_notice FROM bulletins WHERE bulletin_id='".$row->expl_bulletin."'";
						$res2=pmb_mysql_query($requete);
						if(pmb_mysql_num_rows($res2)){
							$element=pmb_mysql_fetch_object($res2);
							if(trim($element->bulletin_titre)){
								$requete="UPDATE bulletins SET index_titre=' ".addslashes(strip_empty_words($element->bulletin_titre))." ' WHERE bulletin_id='".$row->expl_bulletin."'";
								pmb_mysql_query($requete);
							}
							if($element->num_notice){
								if ($gestion_acces_user_notice==1) {
						    		$dom_1->delRessource($element->num_notice);
						    		$dom_1->applyRessourceRights($element->num_notice);
						    	}
						    	if ($gestion_acces_empr_notice==1) {
						    		$dom_2->delRessource($element->num_notice);
						    		$dom_2->applyRessourceRights($element->num_notice);
						    	}
							}
						}
					}
				}
				$pb->progress();
			}
		}
		$pb->hide();
	}
	
	print "<br /><h3>".$msg['caddie_situation_access_rights']."</h3>";
	print sprintf($msg["caddie_action_flag_processed"],$nb_elements_flag)."<br />";
	print sprintf($msg["caddie_action_no_flag_processed"],$nb_elements_no_flag)."<br />";
	print "<b>".sprintf($msg["caddie_action_total_processed"],$nb_elements_total)."</b><br /><br />";
	print aff_cart_nb_items ($myCart) ;
	echo "<input type='button' class='bouton' value='".$msg["caddie_menu_action_suppr_panier"]."' onclick='document.location=&quot;./catalog.php?categ=caddie&amp;sub=action&amp;quelle=supprpanier&amp;action=choix_quoi&amp;object_type=NOTI&amp;idcaddie=".$idcaddie."&amp;item=0&amp;elt_flag=".$elt_flag."&amp;elt_no_flag=".$elt_no_flag."&quot;' />";

} else aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=caddie&sub=action&quelle=access_rights", "choix_quoi", $msg["caddie_select_access_rights"], "", 0, 0, 0);
