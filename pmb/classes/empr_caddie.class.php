<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_caddie.class.php,v 1.10.2.5 2016-05-28 13:58:44 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des paniers

define( 'CADDIE_ITEM_NULL', 0 );
define( 'CADDIE_ITEM_OK', 1 );
define( 'CADDIE_ITEM_DEJA', 1 ); // identique car on peut ajouter des liés avec l'item et non pas l'item saisi lui-même ...
define( 'CADDIE_ITEM_IMPOSSIBLE_BULLETIN', 2 );
define( 'CADDIE_ITEM_EXPL_PRET' , 3 );
define( 'CADDIE_ITEM_BULL_USED', 4) ;
define( 'CADDIE_ITEM_NOTI_USED', 5) ;
define( 'CADDIE_ITEM_SUPPR_BASE_OK', 6) ;
define( 'CADDIE_ITEM_INEXISTANT', 7 );
define( 'CADDIE_ITEM_RESA', 8 );

class empr_caddie {
	// propriétés
	public $idemprcaddie ;
	public $name = ''			;	// nom de référence
	public $comment = ""		;	// description du contenu du panier
	public $nb_item = 0		;	// nombre d'enregistrements dans le panier
	public $nb_item_pointe = 0		;	// nombre d'enregistrements pointés dans le panier
	public $autorisations = ""		;	// autorisations accordées sur ce panier
	public $classementGen = ""		;	// classement
	public $liaisons = array("mailing" => array()); // Liaisons associées à un panier
	public $acces_rapide = 0;		//accès rapide au panier
	public $creation_user_name = '';		//Créateur du panier
	public $creation_date = '';		//Date de création du panier
	
	// ---------------------------------------------------------------
	//		empr_caddie($id) : constructeur
	// ---------------------------------------------------------------
	public function empr_caddie($empr_caddie_id=0) {
		if($empr_caddie_id) {
			$this->idemprcaddie = $empr_caddie_id;
			$this->getData();
		} else {
			$this->idemprcaddie = 0;
			$this->getData();
		}
	}

	// ---------------------------------------------------------------
	//		getData() : récupération infos caddie
	// ---------------------------------------------------------------
	protected function getData() {
		global $dbh;
		if(!$this->idemprcaddie) {
			// pas d'identifiant.
			$this->name	= '';
			$this->comment	= '';
			$this->nb_item	= 0;
			$this->autorisations	= "";
			$this->classementGen	= "";
			$this->acces_rapide	= 0;
			$this->creation_user_name = '';
			$this->creation_date = '0000-00-00 00:00:00';
		} else {
			$requete = "SELECT * FROM empr_caddie WHERE idemprcaddie='$this->idemprcaddie' ";
			$result = @pmb_mysql_query($requete, $dbh);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				pmb_mysql_free_result($result);
				$this->idemprcaddie = $temp->idemprcaddie;
				$this->name = $temp->name;
				$this->comment = $temp->comment;
				$this->autorisations = $temp->autorisations;
				$this->classementGen = $temp->empr_caddie_classement;
				$this->acces_rapide = $temp->acces_rapide;
				$this->creation_user_name = $temp->creation_user_name;
				$this->creation_date = $temp->creation_date;
				
				//liaisons
				$req="SELECT id_planificateur, num_type_tache, libelle_tache FROM planificateur WHERE num_type_tache=8 AND param REGEXP 's:11:\"empr_caddie\";s:[0-9]+:\"".$this->idemprcaddie."\";'";
				$res=pmb_mysql_query($req,$dbh);
				if($res && pmb_mysql_num_rows($res)){
					while ($ligne=pmb_mysql_fetch_object($res)){
						$this->liaisons["mailing"][]=array("id"=>$ligne->id_planificateur,"id_bis"=>$ligne->num_type_tache,"lib"=>$ligne->libelle_tache);
					}
				}
			} else {
				// pas de caddie avec cet id
				$this->idemprcaddie = 0;
				$this->name = '';
				$this->comment = '';
				$this->autorisations = "";
				$this->classementGen = "";
				$this->acces_rapide	= 0;
			}
			$this->compte_items();
		}
	}

	public function set_properties_from_form() {
		global $cart_autorisations;
		global $cart_name;
		global $cart_comment;
		global $classementGen_empr_caddie;
		global $acces_rapide;
		
		if (is_array($cart_autorisations)) {
			$autorisations=implode(" ",$cart_autorisations);
		} else {
			$autorisations="1";
		}
		$this->autorisations = $autorisations;
		$this->name = stripslashes($cart_name);
		$this->comment = stripslashes($cart_comment);
		$this->classementGen = stripslashes($classementGen_empr_caddie);
		$this->acces_rapide = (isset($acces_rapide)?1:0);
	}

	// liste des paniers disponibles
	static public function get_cart_list($restriction_panier="",$acces_rapide = 0) {
		global $dbh, $PMBuserid;
		$cart_list=array();
		$requete = "SELECT * FROM empr_caddie where 1 ";
		if ($PMBuserid!=1) $requete .= " and (autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid') ";
		if ($acces_rapide) {
			$requete .= " and acces_rapide=1";
		}
		$requete.=" order by name ";
		$result = @pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($result)) {
			while ($temp = pmb_mysql_fetch_object($result)) {
				$nb_item = 0 ;
				$nb_item_pointe = 0 ;
				$rqt_nb_item="select count(1) from empr_caddie_content where empr_caddie_id='".$temp->idemprcaddie."' ";
				$nb_item = pmb_mysql_result(pmb_mysql_query($rqt_nb_item, $dbh), 0, 0);
				$rqt_nb_item_pointe = "select count(1) from empr_caddie_content where empr_caddie_id='".$temp->idemprcaddie."' and (flag is not null and flag!='') ";
				$nb_item_pointe = pmb_mysql_result(pmb_mysql_query($rqt_nb_item_pointe, $dbh), 0, 0);
	
				$cart_list[] = array( 
					'idemprcaddie' => $temp->idemprcaddie,
					'name' => $temp->name,
					'comment' => $temp->comment,
					'autorisations' => $temp->autorisations,
					'empr_caddie_classement' => $temp->empr_caddie_classement,
					'nb_item' => $nb_item,
					'nb_item_pointe' => $nb_item_pointe
					);
			}
		} 
		return $cart_list;
	}

	// création d'un panier vide
	public function create_cart() {
		global $dbh,$PMBuserid;
		
		$requete_bis = '';
		$requete = "SELECT CONCAT(prenom, ' ', nom) as name FROM users WHERE userid=".$PMBuserid;
		$result = @pmb_mysql_query($requete, $dbh);
		if (pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$requete_bis = ", creation_user_name='".addslashes(trim($row->name))."', creation_date='".date("Y-m-d H:i:s")."'";
		}
		
		$requete = "insert into empr_caddie set name='".addslashes($this->name)."', comment='".addslashes($this->comment)."', autorisations='".$this->autorisations."', empr_caddie_classement='".addslashes($this->classementGen)."', acces_rapide='".$this->acces_rapide."' ";
		$requete .= $requete_bis;
		$result = @pmb_mysql_query($requete, $dbh);
		$this->idemprcaddie = pmb_mysql_insert_id($dbh);
		$this->compte_items();
	}


	// ajout d'un item
	public function add_item($item=0) {
		global $dbh;
		
		if (!$item) return CADDIE_ITEM_NULL ;
		
		$requete = "replace into empr_caddie_content set empr_caddie_id='".$this->idemprcaddie."', object_id='".$item."' ";
		$result = @pmb_mysql_query($requete, $dbh);
		return CADDIE_ITEM_OK ;
	}

	// suppression d'un item
	public function del_item($item=0) {
		global $dbh;
		$requete = "delete FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and object_id='".$item."' ";
		$result = @pmb_mysql_query($requete, $dbh);
		$this->compte_items();
	}

	public function del_item_base($item=0) {
		global $dbh;
		
		if (!$item) return CADDIE_ITEM_NULL ;
		
		$verif_empr_item = $this->verif_empr_item($item); 
		if (!$verif_empr_item) {
			emprunteur::del_empr($item);
			return CADDIE_ITEM_SUPPR_BASE_OK ;
		} elseif ($verif_empr_item == 1) {
			return CADDIE_ITEM_EXPL_PRET ;
		} else {
			return CADDIE_ITEM_RESA ;
		}
					
	}

	// suppression d'un item de tous les caddies du même type le contenant
	public function del_item_all_caddies($item) {
		global $dbh;
		$requete = "select idemprcaddie FROM empr_caddie ";
		$result = pmb_mysql_query($requete, $dbh);
		for($i=0;$i<pmb_mysql_num_rows($result);$i++) {
			$temp=pmb_mysql_fetch_object($result);
			$requete_suppr = "delete from empr_caddie_content where empr_caddie_id='".$temp->idemprcaddie."' and object_id='".$item."' ";
			$result_suppr = pmb_mysql_query($requete_suppr, $dbh);
		}
	}

	public function del_item_flag() {
		global $dbh;
		$requete = "delete FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and (flag is not null and flag!='') ";
		$result = @pmb_mysql_query($requete, $dbh);
		$this->compte_items();
	}
	
	public function del_item_no_flag() {
		global $dbh;
		$requete = "delete FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and (flag is null or flag='') ";
		$result = @pmb_mysql_query($requete, $dbh);
		$this->compte_items();
	}

	// Dépointage de tous les items
	public function depointe_items() {
		global $dbh;
		$requete = "update empr_caddie_content set flag=null where empr_caddie_id='".$this->idemprcaddie."' ";
		$result = @pmb_mysql_query($requete, $dbh);
		$this->compte_items();
	}	

	public function pointe_item($item=0) {
		global $dbh;
		$requete = "update empr_caddie_content set flag='1' where empr_caddie_id='".$this->idemprcaddie."' and object_id='".$item."' ";
		$result = @pmb_mysql_query($requete, $dbh);
		$this->compte_items();
		return CADDIE_ITEM_OK ;
	}

	public function depointe_item($item=0) {
		global $dbh;
	
		if ($item) {
			$requete = "update empr_caddie_content set flag=null where empr_caddie_id='".$this->idemprcaddie."' and object_id='".$item."' ";
			$result = @pmb_mysql_query($requete, $dbh);
			if ($result) {
				$this->compte_items();
				return 1;
			} else {
				return 0;
			}
		}
	}

	// suppression d'un panier
	public function delete() {
		global $dbh;
		$requete = "delete FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' ";
		$result = @pmb_mysql_query($requete, $dbh);
		$requete = "delete FROM empr_caddie where idemprcaddie='".$this->idemprcaddie."' ";
		$result = @pmb_mysql_query($requete, $dbh);
	}

	// sauvegarde du panier
	public function save_cart() {
		global $dbh;
		$requete = "update empr_caddie set name='".addslashes($this->name)."', comment='".addslashes($this->comment)."', autorisations='".$this->autorisations."', empr_caddie_classement='".addslashes($this->classementGen)."', acces_rapide='".$this->acces_rapide."' where idemprcaddie='".$this->idemprcaddie."'";
		$result = @pmb_mysql_query($requete, $dbh);
	}


	// get_cart() : ouvre un panier et récupère le contenu
	public function get_cart($flag="") {
		global $dbh;
		$cart_list=array();
		switch ($flag) {
			case "FLAG" :
				$requete = "SELECT * FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and (flag is not null and flag!='') ";
				break ;
			case "NOFLAG" :
				$requete = "SELECT * FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and (flag is null or flag='') ";
				break ;
			case "ALL" :
			default :
				$requete = "SELECT * FROM empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' ";
				break ;
			}
		$result = @pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($result)) {
			while ($temp = pmb_mysql_fetch_object($result)) {
				$cart_list[] = $temp->object_id;
			}
		} 
		return $cart_list;
	}

	// compte_items 
	public function compte_items() {
		global $dbh;
		$this->nb_item = 0 ;
		$this->nb_item_pointe = 0 ;
		$rqt_nb_item="select count(1) from empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' ";
		$this->nb_item = pmb_mysql_result(pmb_mysql_query($rqt_nb_item, $dbh), 0, 0);
		$rqt_nb_item_pointe = "select count(1) from empr_caddie_content where empr_caddie_id='".$this->idemprcaddie."' and (flag is not null and flag!='') ";
		$this->nb_item_pointe = pmb_mysql_result(pmb_mysql_query($rqt_nb_item_pointe, $dbh), 0, 0);
	}

	public function verif_empr_item($id) {
	
		global $dbh;
		if ($id) {
			//Prêts en cours
			$query = "select count(1) from pret where pret_idempr=".$id." limit 1 ";
			$result = pmb_mysql_query($query, $dbh);
			if(pmb_mysql_result($result, 0, 0)){
				return 1 ;
			} else {
				//Réservations validées
				$query = "select count(1) from resa where resa_idempr=".$id." and resa_confirmee=1 limit 1 ";
				$result = pmb_mysql_query($query, $dbh);
				if(pmb_mysql_result($result, 0, 0)){
					return 2 ;
				} else {
					return 0 ;
				}
			}		
		} else return 0 ;
	}

	static public function show_actions($id_caddie = 0) {
		global $msg,$empr_cart_action_selector,$empr_cart_action_selector_line;
	
		//Le tableau des actions possibles
		$array_actions = array();
		$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_suppr_panier"], 'location' => './circ.php?categ=caddie&sub=action&quelle=supprpanier&action=choix_quoi&idemprcaddie='.$id_caddie.'&item=');
		$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_transfert"], 'location' => './circ.php?categ=caddie&sub=action&quelle=transfert&action=transfert&idemprcaddie='.$id_caddie.'&item=');
		$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_edition"], 'location' => './circ.php?categ=caddie&sub=action&quelle=edition&action=choix_quoi&idemprcaddie='.$id_caddie.'&item='.$id_caddie.'&item=0');
		$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_mailing"], 'location' => './circ.php?categ=caddie&sub=action&quelle=mailing&action=envoi&idemprcaddie='.$id_caddie.'&item='.$id_caddie.'&item=0');
		$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_selection"], 'location' => './circ.php?categ=caddie&sub=action&quelle=selection&action=&idemprcaddie='.$id_caddie.'&item='.$id_caddie.'&item=0');
		$array_actions[] = array('msg' => $msg["empr_caddie_menu_action_suppr_base"], 'location' => './circ.php?categ=caddie&sub=action&quelle=supprbase&action=choix_quoi&idemprcaddie='.$id_caddie.'&item=');
		
		//On crée les lignes du menu
		$lines = '';
		foreach($array_actions as $item_action){
			$tmp_line = str_replace('!!cart_action_selector_line_location!!',$item_action['location'],$empr_cart_action_selector_line);
			$tmp_line = str_replace('!!cart_action_selector_line_msg!!',$item_action['msg'],$tmp_line);
			$lines.= $tmp_line;
		}
		
		//On récupère le template
		$to_show = str_replace('!!cart_action_selector_lines!!',$lines,$empr_cart_action_selector);
	
		return $to_show;
	}
	
	public function get_info_creation() {
		global $msg;
		
		if ($this->creation_date != '0000-00-00 00:00:00') {
			$create_date = new DateTime($this->creation_date);
			return sprintf($msg["empr_caddie_creation_info"], $create_date->format('d/m/Y'),$this->creation_user_name);
		} else {
			return $msg['empr_caddie_creation_no_info'];
		}
	}
	
} // fin de déclaration de la classe
  
