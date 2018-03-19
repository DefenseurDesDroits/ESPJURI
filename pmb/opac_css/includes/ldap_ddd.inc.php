<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ldap_ddd.inc.php dbellamy Exp $
//
// Défenseur des droits
// Gestion + OPAC
// V1 (DB-06/10/2016)
// +-------------------------------------------------+

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path.'/log.class.php');


class ldap_params_ddd {

	protected static $params=array();

	public static function get_params($renew=false, $log=false) {

		global $include_path;

		if($log) {
			log::print_message('get_params()');
		}
		if($renew || !count(static::$params)) {
			$ini_file = $include_path.'/ldap_params_ddd.inc.php';
			static::$params = array();
			if(file_exists($ini_file)) {
				static::$params = parse_ini_file($ini_file,true);
			} else {
				die("Configuration file \".$ini_file.\" not found.");
			}
		}
		return static::$params;
	}

}

class ldap_query_ddd extends ldap_query {
	
	public function __construct() {
	
		$params = ldap_params_ddd::get_params();
		$this->ldap_host = $params['ldap']['ldap_host'];
		$this->ldap_user = $params['ldap']['ldap_user'];
		$this->ldap_pwd = $params['ldap']['ldap_pwd'];
		$this->ldap_base_dn = $params['ldap']['ldap_base_dn'];
		$this->ldap_filter = $params['ldap']['ldap_filter'];
		$this->ldap_attr = explode(',',$params['ldap']['ldap_attr']);
		$this->ldap_logon_attr=$params['ldap']['ldap_logon_attr'];
	}
	
}

class ldap_empr_ddd extends ldap_empr {

	public $default_empr_location = 1;		//Bibliothèque du service documentation
	public $default_empr_categ = 1;			//Prêt standard
	public $default_empr_codestat = 1;		//DDD
	public $default_empr_statut = 1;		//Actif

	public function __construct() {

		$params = ldap_params_ddd::get_params();
		$this->default_empr_location = $params['pmb_default_values']['default_empr_location'];
		$this->default_empr_categ = $params['pmb_default_values']['default_empr_categ'];
		$this->default_empr_codestat = $params['pmb_default_values']['default_empr_codestat'];
		$this->default_empr_statut = $params['pmb_default_values']['default_empr_statut'];
	}

}


class ldap_query {

	public $ldap_host="ldap://localhost:389";
	public $ldap_base_dn=array();
	public $ldap_base_dn_backend=array();
	public $ldap_user=NULL;
	public $ldap_pwd=NULL;
	public $ldap_conn=false;
	public $ldap_bind=false;

	public $ldap_attr=array();
	public $ldap_filter='(objectclass=*)';
	public $ldap_logon_attr='samaccountname';
	public $search_limit=0;
	public $search_result=array();

	public $log = false;


	public function __construct() {
	}


	public function connect() {

		if(!$this->ldap_conn) $this->ldap_conn=@ldap_connect($this->ldap_host);
		ldap_set_option($this->ldap_conn,LDAP_OPT_PROTOCOL_VERSION,3);
		ldap_set_option($this->ldap_conn,LDAP_OPT_REFERRALS,0);
		if ($this->log) {
			if ($this->ldap_conn) {
				log::print_message("LDAP Connection OK");
			} else {
				log::print_message("LDAP Connection KO");
			}
		}
	}


	public function bind($user='',$pwd='') {

		if (!$user) $user = $this->ldap_user;
		if (!$pwd) $pwd = $this->ldap_pwd;
		if(!$this->ldap_conn) $this->connect();
		if (!$this->ldap_bind) {
			$this->ldap_bind=@ldap_bind($this->ldap_conn,(($user)?$user:NULL),(($pwd)?$pwd:NULL));
		}
		if ($this->log) {
			if ($this->ldap_bind) {
				log::print_message("LDAP Bind OK");
			} else {
				log::print_message("LDAP Bind KO");
			}
		}
	}


	public function authenticate_empr($user='',$pwd='') {

		$ret=false;

		if($user && $pwd) {

			if(!$this->ldap_conn) $this->connect();
			$this->search($this->ldap_logon_attr.'='.$user,array('dn'));
			$sr = $this->get_result();
			if(count($sr)) {
				$this->ldap_bind=false;
				$this->bind($sr[0][0]['dn'],$pwd);
				$ret=$this->ldap_bind;
			}

		}

		if ($this->log) log::print_message("Authentication $user ".(($ret)?"OK":"KO"));
		$this->close();

		return $ret;
	}


	public function authenticate_user($user='',$pwd='') {

		global $dbh;

		$ret=false;
		$chk_pwd = false;
		if ($user && $pwd) {

			if($this->ldap_base_dn_backend) {
				$tmp_ldap_base_dn = $this->ldap_base_dn;
				$this->ldap_base_dn = $this->ldap_base_dn_backend;
			}

			if(!$this->ldap_conn) $this->connect();
			$this->search($this->ldap_logon_attr.'='.$user,array('dn'));
			$sr = $this->get_result();
			if(count($sr)) {
				$this->ldap_bind=false;
				$this->bind($sr[0][0]['dn'],$pwd);
				$chk_pwd = $this->ldap_bind;
			}

			$q1 = "SELECT 1 FROM users WHERE username='".addslashes($user)."' ";
			if(!$chk_pwd) {
				$q1.= "AND pwd=password('".addslashes($pwd)."') ";
			}
			$r1 = pmb_mysql_query($q1, $dbh);
			if (mysql_num_rows($r1)==1) {
				$ret = true;
			}

   			if($this->ldap_base_dn_backend) {
				$this->ldap_base_dn = $tmp_ldap_base_dn;
			}

		}
		return $ret;
	}


	public function close() {

		@ldap_close($this->ldap_conn);
		$this->ldap_bind=false;
		$this->ldap_conn=false;
		if($this->log) log::print_message("LDAP connection closed");
	}


	public function search($filter='',$attr=array(),$limit=0) {

		if (!$filter) $filter = $this->ldap_filter;
		if (!count($attr)) $attr = $this->ldap_attr;
		if (!$limit) $limit = $this->search_limit;
		$this->search_result=array();
		$this->connect();
		if ($this->ldap_conn) {
			$this->bind();
			if($this->ldap_bind) {
				foreach($this->ldap_base_dn as $bd) {
					$lcn=0;
					$lge=array();
					$sr=ldap_search($this->ldap_conn,$bd,$filter,$attr,0,$limit);
					if($sr) {
						$lcn = ldap_count_entries($this->ldap_conn,$sr);
					}
					if($sr && $lcn) {
						$lge = ldap_get_entries($this->ldap_conn,$sr);
						$this->search_result[]=$lge;
					}
					if($this->log) {
						log::print_message("LDAP Base dn : $bd");
						log::print_message("LDAP Search : filter=$filter");
						log::print_message("$lcn entries found");
						log::print_message($lge);
					}
				}
			}
		}
		$this->close();
	}


	public function get_result() {

		return $this->search_result;
	}

	public function charset_decode($value='') {

		global $charset;
		$r = '';
		$r = trim($value);
		if($charset != 'utf-8') {
			$r = utf8_decode($r);
		}

		return $r;
	}


	static public function ldap_accountexpires_2_date($account_expires='', $date_format= "Y-m-d") {

		$account_never_expires = 9223372036854775807;
		$ret = false;
		if (!$account_expires) $account_expires = $account_never_expires;
		if ($account_expires!=9223372036854775807) {
			$unix_timestamp = ($account_expires / 10000000) - 11644560000;
			$ret = date($date_format, $unix_timestamp);
		}
		return $ret;
	}


}


class ldap_empr {

	public $id_empr					= 0;
	public $empr_cb					= '';
	public $empr_nom				= '';
	public $empr_prenom				= '';
	public $empr_adr1				= '';
	public $empr_adr2				= '';
	public $empr_cp					= '';
	public $empr_ville				= '';
	public $empr_pays				= '';
	public $empr_mail				= '';
	public $empr_tel1				= '';
	public $empr_tel2				= '';
	public $empr_prof				= '';
	public $empr_year				= '';
	public $empr_categ				= 0;
	public $empr_codestat			= 0;
	public $empr_creation			= '';
	public $empr_modif				= '';
	public $empr_sexe				= 0;
	public $empr_login				= '';
	public $empr_password			= '';
	public $empr_date_adhesion		= '';
	public $empr_date_expiration	= '';
	public $empr_msg				= '';
	public $empr_lang				= 'fr_FR';
	public $empr_ldap				= 0;
	public $type_abt				= 0;
	public $last_loan_date			= '';
	public $empr_location			= 0;
	public $date_fin_blocage		= '';
	public $total_loans				= 0;
	public $empr_statut				= 0;
	public $cle_validation			= '';
	public $empr_sms				= 0;

	public $log						= false;

	public $duree_adhesion			= 365;

	public $default_empr_categ		= 1;		//Individuel
	public $default_empr_codestat	= 3;		//Indéterminé
	public $default_empr_statut		= 1;		//Actif
	public $default_empr_location	= 1;		//Centre de documentation

	public $cps_verified = false;
	public $cps=array();

	protected $ldap_logon_attr = 'uid';

	public function __construct() {
	}


	public function set_duree_adhesion() {

		global $dbh;

		if(!$this->empr_categ) $this->set_empr_categ();
		if($this->empr_categ) {
			$q="select duree_adhesion from empr_categ where id_categ_empr='".$this->empr_categ."' ";
			$r = pmb_mysql_query($q,$dbh);
			if (mysql_num_rows($r)) {
				$this->duree_adhesion = pmb_mysql_result($r,0,0);
			}
		}
		if($this->log) {
			log::print_message("set_duree_adhesion()");
			log::print_message("id categ => $this->empr_categ");
			log::print_message($q);
			log::print_message("duree adhesion => $this->duree_adhesion");
		}
	}


	public function set_empr_location($empr_location=0) {

		global $dbh;

		$empr_location+=0;
		if (!$empr_location) $empr_location = $this->empr_location;
		if (!$empr_location) $empr_location = $this->default_empr_location;
		$q = "select idlocation from docs_location where idlocation='".$empr_location."' ";
		$r = pmb_mysql_query($q, $dbh);
		if (mysql_num_rows($r)) {
			$this->empr_location = $empr_location;
		} else {
			$this->empr_location=$this->default_empr_location;
		}
	}


	public function set_empr_statut($empr_statut=0) {

		global $dbh;

		if (!$empr_statut) $empr_statut = $this->empr_statut;
		if (!$empr_statut) $empr_statut = $this->default_empr_statut;
		$q = "select idstatut from empr_statut where idstatut='".$empr_statut."' ";
		$r = pmb_mysql_query($q, $dbh);
		if (mysql_num_rows($r)) {
			$this->empr_statut = $empr_statut;
		} else {
			$this->empr_statut=$this->default_empr_statut;
		}
	}


	public function set_empr_codestat($empr_codestat=0) {

		global $dbh;

		$empr_codestat+=0;
		if (!$empr_codestat) $empr_codestat = $this->empr_codestat;
		if (!$empr_codestat) $empr_codestat = $this->default_empr_codestat;
		$q = "select idcode from empr_codestat where idcode='".$empr_codestat."' ";
		$r = pmb_mysql_query($q, $dbh);
		if (mysql_num_rows($r)) {
			$this->empr_codestat = $empr_codestat;
		} else {
			$this->empr_codestat = $this->default_empr_codestat;
		}
	}


	public function set_empr_categ($empr_categ=0) {

		global $dbh;

		$empr_categ+=0;
		if (!$empr_categ) $empr_categ = $this->empr_categ;
		if (!$empr_categ) $empr_categ = $this->default_empr_categ;
		$q = "select id_categ_empr from empr_categ where id_categ_empr='".$empr_categ."' ";
		$r = pmb_mysql_query($q, $dbh);
		if (mysql_num_rows($r)) {
			$this->empr_categ = $empr_categ;
		} else {
			$this->empr_categ = $this->default_empr_categ;
		}
	}


	public function save(&$nb=0) {

		global $dbh;

		if($this->empr_cb && $this->empr_nom) {

			$this->verify();

			$q_empr = "insert into empr set ";
			$q_empr.= "empr_cb='".addslashes($this->empr_cb)."', ";
			$q_empr.= "empr_nom='".addslashes($this->empr_nom)."', ";
			$q_empr.= "empr_prenom='".addslashes($this->empr_prenom)."', ";
			$q_empr.= "empr_adr1='".addslashes($this->empr_adr1)."', ";
			$q_empr.= "empr_adr2='".addslashes($this->empr_adr2)."', ";
			$q_empr.= "empr_cp='".addslashes($this->empr_cp)."', ";
			$q_empr.= "empr_ville='".addslashes($this->empr_ville)."', ";
			$q_empr.= "empr_pays='".addslashes($this->empr_pays)."', ";
			$q_empr.= "empr_mail='".addslashes($this->empr_mail)."', ";
			$q_empr.= "empr_tel1='".addslashes($this->empr_tel1)."', ";
			$q_empr.= "empr_tel2='".addslashes($this->empr_tel2)."', ";
			$q_empr.= "empr_prof='".addslashes($this->empr_prof)."', ";
			$q_empr.= "empr_year='".addslashes($this->empr_year)."', ";
			$q_empr.= "empr_categ='".$this->empr_categ."',";
			$q_empr.= "empr_codestat='".$this->empr_codestat."',";
			$q_empr.= "empr_creation=now(), ";
			$q_empr.= "empr_modif=now(), ";
			$q_empr.= "empr_sexe='".$this->empr_sexe."', ";
			$q_empr.= "empr_login='".addslashes($this->empr_login)."', ";
			$q_empr.= "empr_password='".addslashes($this->empr_password)."', ";
			$q_empr.= "empr_date_adhesion=now(), ";
			$q_empr.= "empr_date_expiration=".(($this->empr_date_expiration)?"'".$this->empr_date_expiration."'":'adddate(curdate(), interval '.$this->duree_adhesion.' day )').", ";
			$q_empr.= "empr_msg='".addslashes($this->empr_msg)."', ";
			$q_empr.= "empr_lang='".$this->empr_lang."', ";
			$q_empr.= "empr_ldap='".$this->empr_ldap."', ";
			$q_empr.= "type_abt='".$this->type_abt."', ";
			$q_empr.= "last_loan_date='".$this->last_loan_date."', ";
			$q_empr.= "empr_location='".$this->empr_location."', ";
			$q_empr.= "date_fin_blocage='".$this->date_fin_blocage."', ";
			$q_empr.= "total_loans='".$this->total_loans."', ";
			$q_empr.= "empr_statut='".$this->empr_statut."', ";
			$q_empr.= "cle_validation='".$this->cle_validation."', ";
			$q_empr.= "empr_sms='".$this->empr_sms."' ";
			if ($this->log) log::print_message($q_empr);
			$r = pmb_mysql_query($q_empr,$dbh);
			if ($r) {
				$this->id_empr = pmb_mysql_insert_id($dbh);
				$nb++;
				if ($this->log) {
					log::print_message("Ajout Compte ".$this->empr_cb." (".$this->empr_prenom.(($this->empr_prenom)?" ":"").$this->empr_nom.")");
				}
			} else {
				if ($this->log) {
					log::print_message("Echec ajout Compte ".$this->empr_cb." (".$this->empr_prenom.(($this->empr_prenom)?" ":"").$this->empr_nom.")");
				}
			}
		}  else {
			log::print_message("Erreur >>  empr_cb=".$this->empr_cb." -- empr_nom=".$this->empr_nom);;
		}
	}


	public function update(&$nb=0) {

		global $dbh;

		if($this->id_empr && $this->empr_cb && $this->empr_nom) {

			$this->verify();

			$q_empr = "update empr set ";
			$q_empr.= "empr_cb='".addslashes($this->empr_cb)."', ";
			$q_empr.= "empr_nom='".addslashes($this->empr_nom)."', ";
			$q_empr.= "empr_prenom='".addslashes($this->empr_prenom)."', ";
			if($this->empr_adr1) {
				$q_empr.= "empr_adr1='".addslashes($this->empr_adr1)."', ";
			}
			if($this->empr_adr2) {
				$q_empr.= "empr_adr2='".addslashes($this->empr_adr2)."', ";
			}
			if($this->empr_cp) {
				$q_empr.= "empr_cp='".addslashes($this->empr_cp)."', ";
			}
			if($this->empr_ville) {
				$q_empr.= "empr_ville='".addslashes($this->empr_ville)."', ";
			}
			$q_empr.= "empr_pays='".addslashes($this->empr_pays)."', ";
			$q_empr.= "empr_mail='".addslashes($this->empr_mail)."', ";
			if($this->empr_tel1) {
				$q_empr.= "empr_tel1='".addslashes($this->empr_tel1)."', ";
			}
			if($this->empr_tel2) {
				$q_empr.= "empr_tel2='".addslashes($this->empr_tel2)."', ";
			}
			$q_empr.= "empr_prof='".addslashes($this->empr_prof)."', ";
			//$q_empr.= "empr_year='".addslashes($this->empr_year)."', ";
			$q_empr.= "empr_categ='".$this->empr_categ."',";
			$q_empr.= "empr_codestat='".$this->empr_codestat."',";
			//$q_empr.= "empr_creation=now(), ";
			$q_empr.= "empr_modif=now(), ";
			$q_empr.= "empr_sexe='".$this->empr_sexe."', ";
			$q_empr.= "empr_login='".addslashes($this->empr_login)."', ";
			//$q_empr.= "empr_password='".addslashes($this->empr_password)."', ";
			//$q_empr.= "empr_date_adhesion=now(), ";
			$q_empr.= "empr_date_expiration=".(($this->empr_date_expiration)?"'".$this->empr_date_expiration."'":'adddate(curdate(), interval '.$this->duree_adhesion.' day )').", ";
			//$q_empr.= "empr_msg='".addslashes($this->empr_msg)."', ";
			//$q_empr.= "empr_lang='".$this->empr_lang."', ";
			$q_empr.= "empr_ldap='".$this->empr_ldap."' ";
			//$q_empr.= "type_abt='".$this->type_abt."', ";
			//$q_empr.= "last_loan_date='".$this->last_loan_date."', ";
			//$q_empr.= "empr_location='".$this->empr_location."' ";
			//$q_empr.= "date_fin_blocage='".$this->date_fin_blocage."', ";
			//$q_empr.= "total_loans='".$this->total_loans."', ";
			//$q_empr.= "empr_statut='".$this->empr_statut."', ";
			//$q_empr.= "cle_validation='".$this->cle_validation."', ";
			//$q_empr.= "empr_sms='".$this->empr_sms."' ";
			$q_empr.= "where id_empr='".$this->id_empr."' ";
			if ($this->log) log::print_message($q_empr);
			$r = pmb_mysql_query($q_empr,$dbh);
			if ($r) {
				$nb++;
				if ($this->log) {
					log::print_message("Mise a jour Compte ".$this->empr_cb." (".$this->empr_prenom.(($this->empr_prenom)?" ":"").$this->empr_nom.")");
				}
			} else {
				if ($this->log) {
					log::print_message("Echec Mise a jour Compte ".$this->empr_cb." (".$this->empr_prenom.(($this->empr_prenom)?" ":"").$this->empr_nom.")");
				}
			}
		} else {
			log::print_message("Erreur >> id_empr=".$this->id_empr." -- empr_cb=".$this->empr_cb." -- empr_nom=".$this->empr_nom);
		}
	}


	public function verify() {

		if(!$this->empr_categ) $this->empr_categ=$this->default_empr_categ;
		if(!$this->empr_codestat) $this->empr_codestat=$this->default_empr_codestat;
		if(!$this->empr_statut) $this->empr_statut=$this->default_empr_statut;
		if(!$this->empr_location) $this->empr_location=$this->default_empr_location;
	}


	public function set_genre_from_civilite($civilite='') {

		$civilite = strtolower(trim($civilite));
		$tm = array('m','m.','monsieur');
		$tf = array('f','mme','mlle','madame','mademoiselle');
		$this->empr_sexe = 0;
		if(in_array($civilite,$tm)) {
			$this->empr_sexe = 1;
		} elseif (in_array($civilite,$tf)) {
			$this->empr_sexe = 2;
		}

	}


	public function add_to_caddie($caddie_name='',$id_empr=0) {

		global $dbh;

		if(!$caddie_name) return;
		if(!$id_empr && !$this->id_empr) return;
		if (!$id_empr) {
			$id_empr = $this->id_empr;
		}
		$id_caddie=0;
		$q = "select idemprcaddie from empr_caddie where name='".addslashes($caddie_name)."' ";
		$r = pmb_mysql_query($q,$dbh);
		if(pmb_mysql_num_rows($r)) {
			$id_caddie = pmb_mysql_result($r,0,0);
		} else {
			$q1 = "insert into empr_caddie (name,autorisations) values ('".addslashes($caddie_name)."',' 1 ') ";
			$r1 = pmb_mysql_query($q1,$dbh);
			$id_caddie = pmb_mysql_insert_id($dbh);
		}
		if ($id_caddie) {
			$q2 = "insert ignore into empr_caddie_content (empr_caddie_id,object_id,flag) values ('".$id_caddie."', '".$id_empr."',NULL)";
			pmb_mysql_query($q2,$dbh);
		}
	}


	public static function truncate_caddie($caddie_name='') {

		global $dbh;

		if(!$caddie_name) return;
		$id_caddie=0;
		$q = "select idemprcaddie from empr_caddie where name='".addslashes($caddie_name)."' ";
		$r = pmb_mysql_query($q,$dbh);
		if(pmb_mysql_num_rows($r)) {
			$id_caddie = pmb_mysql_result($r,0,0);
		}
		if ($id_caddie) {
			$q2 = "delete from empr_caddie_content where empr_caddie_id='".$id_caddie."'";
			pmb_mysql_query($q2,$dbh);
		}

	}


	function verify_cps() {

		global $dbh;
		if (!$this->cps_verified && count($this->cps)) {

			foreach($this->cps as $cp=>$value) {
				if(!$this->cps[$cp]) {
					$q = "select idchamp from empr_custom where name='".addslashes($cp)."' ";
					if($this->log) {log::print_message($q);}
					$r = pmb_mysql_query($q,$dbh);
					if(!pmb_mysql_error() && pmb_mysql_num_rows($r)) {
						$this->cps[$cp] = pmb_mysql_result($r,0,0);
					} else {
						if($this->log) log::print_message("Champ personnalisé '".$cp."' inexistant");
					}
				}
			}
			$this->cps_verified=true;
		}
	}


	public function del_cp($cp='') {

		global $dbh;
		$this->verify_cps();

		if ($this->cps[$cp] && $this->id_empr) {
			$q_del = "delete from empr_custom_values where empr_custom_origine='".$this->id_empr."' and empr_custom_champ='".$this->cps[$cp]."' ";
			if($this->log) log::print_message($q_del);
			$r_del = pmb_mysql_query($q_del,$dbh);
		}
	}


	public function set_cp_smalltext($cp='',$val='') {

		global $dbh;
		$this->verify_cps();

		if ($this->cps[$cp] && $this->id_empr) {
			$q_val = "insert into empr_custom_values set empr_custom_origine='".$this->id_empr."', empr_custom_champ='".$this->cps[$cp]."', empr_custom_small_text='".addslashes($val)."' ";
			if($this->log) log::print_message($q_val);
			$r_val = pmb_mysql_query($q_val,$dbh);
		}
	}


	public function set_cp_text($cp='',$val='') {

		global $dbh;
		$this->verify_cps();

		if ($this->cps[$cp] && $this->id_empr) {
			$q_val = "insert into empr_custom_values set empr_custom_origine='".$this->id_empr."', empr_custom_champ='".$this->cps[$cp]."', empr_custom_text='".addslashes($val)."' ";
			if($this->log) log::print_message($q_val);
			$r_val = pmb_mysql_query($q_val,$dbh);
		}
	}


	public function set_cp_list_integer($cp='',$label='') {

		global $dbh;
		$this->verify_cps();

		if ($this->cps[$cp] && $this->id_empr && $label ) {

			$q_val = "select empr_custom_list_value from empr_custom_lists where empr_custom_champ='".$this->cps[$cp]."' and empr_custom_list_lib='".addslashes($label)."' ";
			if($this->log) log::print_message($q_val);
			$r_val = pmb_mysql_query($q_val,$dbh);
			if(pmb_mysql_num_rows($r_val)) {
				$val = pmb_mysql_result($r_val,0,0);
			} else {
				$q_val1 = "select ifnull(max(empr_custom_list_value*1)+1,1) from empr_custom_lists where empr_custom_champ='".$this->cps[$cp]."' ";
				if($this->log) log::print_message($q_val1);
				$r_val1 = pmb_mysql_query($q_val1,$dbh);
				$val = pmb_mysql_result($r_val1,0,0);

				$q_val2 = "insert into empr_custom_lists set empr_custom_champ='".$this->cps[$cp]."', empr_custom_list_value='".$val."', empr_custom_list_lib='".addslashes($label)."' ";
				if($this->log) log::print_message($q_val2);
				$r_val2 = pmb_mysql_query($q_val2,$dbh);
			}
			$q_val3 = "insert into empr_custom_values set empr_custom_origine='".$this->id_empr."', empr_custom_champ='".$this->cps[$cp]."', empr_custom_integer='".$val."' ";
			if($this->log) log::print_message($q_val3);
			$r_val3 = pmb_mysql_query($q_val3,$dbh);

		}
	}


	static public function not_in_ldap($log=false) {

		global $dbh;

		$q="select count(*) from empr where empr_ldap='0' ";
		if ($log) log::print_message($q);
		pmb_mysql_query($q,$dbh);
		return pmb_mysql_result(pmb_mysql_query($q,$dbh),0,0);
	}


	static public function not_in_ldap_to_caddie($caddie_name='', $log=false) {

		global $dbh;
		if(!$caddie_name) {
			$caddie_name = "Lecteurs hors LDAP";
		}
		$id_caddie=0;
		$q = "select idemprcaddie from empr_caddie where name='".addslashes($caddie_name)."' ";
		if ($log) log::print_message($q);
		$r = pmb_mysql_query($q,$dbh);
		if(pmb_mysql_num_rows($r)) {
			$id_caddie = pmb_mysql_result($r,0,0);
		} else {
			$q1 = "insert into empr_caddie (name,autorisations) values ('".addslashes($caddie_name)."',' 1 ') ";
			if($log) log::print_message($q1);
			$r1 = pmb_mysql_query($q1,$dbh);
				$id_caddie = pmb_mysql_insert_id($dbh);
		}
		if ($id_caddie) {
			$q2 = "insert into empr_caddie_content (select '".$id_caddie."', id_empr,NULL from empr where empr_ldap=0 )";
			if($log) log::print_message($q2);
			pmb_mysql_query($q2,$dbh);
		}
	}


	static public function raz_ldap_flag($log=false) {

		global $dbh;

		$q="update empr set empr_ldap='0' where empr_ldap='1' ";
		if ($log) {
			log::print_message($q);
		}
		pmb_mysql_query($q,$dbh);
	}


	static public function search_cb($empr_cb,$log=false) {

		global $dbh;

		$ret=0;
		$q="select id_empr from empr where empr_cb='".addslashes($empr_cb)."'";
		if ($log) log::print_message($q);
		$r = pmb_mysql_query($q,$dbh);
		if(pmb_mysql_num_rows($r)) {
			$ret = pmb_mysql_result($r,0,0);
		}
		if ($log) log::print_message("id_empr=".$ret);
		return $ret;
	}


	static public function search_login($empr_login,$log=false) {

		global $dbh;

		$ret=0;
		$q="select id_empr from empr where empr_login='".addslashes($empr_login)."'";
		if ($log) log::print_message($q);
		$r = pmb_mysql_query($q,$dbh);
		if(pmb_mysql_num_rows($r)) {
			$ret = pmb_mysql_result($r,0,0);
		}
		if ($log) {
			log::print_message('search_login('.$empr_login.')');
			log::print_message("id_empr=>".$ret);
			log::print_message($q);
		}
		return $ret;
	}


	static public function search_name($empr_nom='',$empr_prenom='',$log=false) {

		global $dbh;

		$ret=0;
		$empr_nom=trim($empr_nom);
		$empr_prenom=trim($empr_prenom);
		if($empr_nom) {
			$q="select id_empr from empr where empr_nom='".addslashes($empr_nom)."' and empr_prenom='".addslashes($empr_prenom)."' ";
			if ($log) log::print_message($q);
			$r = pmb_mysql_query($q,$dbh);
			if(pmb_mysql_num_rows($r)==1) {
				$ret = pmb_mysql_result($r,0,0);
			}
		}
		if ($log) log::print_message("id_empr=".$ret);
		return $ret;
	}


	static public function search_mail($empr_mail='',$log=false) {
	
		global $dbh;
	
		$ret=0;
		$empr_mail=trim($empr_mail);
		if($empr_mail) {
			$q="select id_empr from empr where empr_mail='".addslashes($empr_mail)."' ";
			if ($log) log::print_message($q);
			$r = pmb_mysql_query($q,$dbh);
			if(pmb_mysql_num_rows($r)==1) {
				$ret = pmb_mysql_result($r,0,0);
			}
		}
		if ($log) log::print_message("id_empr=".$ret);
		return $ret;
	}
	
	
	static public function delete_old_groups($log=false) {

		global $dbh;

		$q="delete from groupe where id_groupe not in (select groupe_id from groupe) ";
		if ($log) log::print_message($q);
		$r = pmb_mysql_query($q,$dbh);
	}

}


