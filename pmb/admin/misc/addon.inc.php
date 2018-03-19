<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: addon.inc.php,v 1.5.4.72 2017-06-08 13:25:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function traite_rqt($requete="", $message="") {

	global $dbh,$charset;
	$retour="";
	if($charset == "utf-8"){
		$requete=utf8_encode($requete);
	}
	$res = pmb_mysql_query($requete, $dbh) ;
	$erreur_no = pmb_mysql_errno();
	if (!$erreur_no) {
		$retour = "Successful";
	} else {
		switch ($erreur_no) {
			case "1060":
				$retour = "Field already exists, no problem.";
				break;
			case "1061":
				$retour = "Key already exists, no problem.";
				break;
			case "1091":
				$retour = "Object already deleted, no problem.";
				break;
			default:
				$retour = "<font color=\"#FF0000\">Error may be fatal : <i>".pmb_mysql_error()."<i></font>";
				break;
			}
	}
	return "<tr><td><font size='1'>".($charset == "utf-8" ? utf8_encode($message) : $message)."</font></td><td><font size='1'>".$retour."</font></td></tr>";
}
echo "<table>";

/******************** AJOUTER ICI LES MODIFICATIONS *******************************/

switch ($pmb_bdd_subversion) {
	case '0' :
		// DB - Modification de la table resarc (id resa_planning pour resa issue d'une prévision)
		$rqt = "alter table resa_archive add resarc_resa_planning_id_resa int(8) unsigned not null default 0";
		echo traite_rqt($rqt,"alter resa_archive add resarc_resa_planning_id_resa");
	case '1' :
		//DG - Champs perso demandes
		$rqt = "create table if not exists demandes_custom (
				idchamp int(10) unsigned NOT NULL auto_increment,
				name varchar(255) NOT NULL default '',
				titre varchar(255) default NULL,
				type varchar(10) NOT NULL default 'text',
				datatype varchar(10) NOT NULL default '',
				options text,
				multiple int(11) NOT NULL default 0,
				obligatoire int(11) NOT NULL default 0,
				ordre int(11) default NULL,
				search INT(1) unsigned NOT NULL DEFAULT 0,
				export INT(1) unsigned NOT NULL DEFAULT 0,
				exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
				pond int not null default 100,
				opac_sort INT NOT NULL DEFAULT 0,
				PRIMARY KEY  (idchamp)) ";
		echo traite_rqt($rqt,"create table if not exists demandes_custom ");

		$rqt = "create table if not exists demandes_custom_lists (
				demandes_custom_champ int(10) unsigned NOT NULL default 0,
				demandes_custom_list_value varchar(255) default NULL,
				demandes_custom_list_lib varchar(255) default NULL,
				ordre int(11) default NULL,
				KEY i_demandes_custom_champ (demandes_custom_champ),
				KEY i_demandes_champ_list_value (demandes_custom_champ,demandes_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists demandes_custom_lists ");

		$rqt = "create table if not exists demandes_custom_values (
				demandes_custom_champ int(10) unsigned NOT NULL default 0,
				demandes_custom_origine int(10) unsigned NOT NULL default 0,
				demandes_custom_small_text varchar(255) default NULL,
				demandes_custom_text text,
				demandes_custom_integer int(11) default NULL,
				demandes_custom_date date default NULL,
				demandes_custom_float float default NULL,
				KEY i_demandes_custom_champ (demandes_custom_champ),
				KEY i_demandes_custom_origine (demandes_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists demandes_custom_values ");

	case '2' :
		// NG - Circulation simplifiée de périodique
		$rqt = "ALTER TABLE serialcirc ADD serialcirc_simple int unsigned not null default 0" ;
		echo traite_rqt($rqt,"ALTER TABLE serialcirc ADD serialcirc_simple ");

		// NG - Script de construction d'étiquette de circulation simplifiée de périodique
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='serialcirc_simple_print_script' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'serialcirc_simple_print_script', '', 'Script de construction d\'étiquette de circulation simplifiée de périodique' ,'',0)";
			echo traite_rqt($rqt,"insert pmb_serialcirc_simple_print_script into parametres");
		}

	case '3' :
		// AP - Nombre maximum de notices à afficher dans une liste sans pagination
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='max_results_on_a_page' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'max_results_on_a_page', '500', 'Nombre maximum de notices à afficher sur une page, utile notamment quand la navigation est désactivée' ,'d_aff_recherche',0)";
			echo traite_rqt($rqt,"insert max_results_on_a_page into parametres");
		}
	case '4' :
		//JP - taille de certains champs blob trop juste
		$rqt = "ALTER TABLE opac_sessions CHANGE session session MEDIUMBLOB NULL DEFAULT NULL";
		echo traite_rqt($rqt,"ALTER TABLE opac_sessions CHANGE session MEDIUMBLOB");
		$rqt = " select 1 " ;
		echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ FAIRE UN NETTOYAGE DE BASE (APRES ETAPES DE MISE A JOUR) / YOU MUST DO A DATABASE CLEANUP (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
	case '5' :
		//JP - bouton vider le cache portail
		$rqt = "ALTER TABLE cms_articles ADD article_update_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL";
		echo traite_rqt($rqt,"ALTER TABLE cms_articles ADD article_update_timestamp");
		$rqt = "UPDATE cms_articles SET article_update_timestamp=article_creation_date";
		echo traite_rqt($rqt,"UPDATE cms_articles SET article_update_timestamp");
		$rqt = "ALTER TABLE cms_sections ADD section_update_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL";
		echo traite_rqt($rqt,"ALTER TABLE cms_sections ADD section_update_timestamp");
		$rqt = "UPDATE cms_sections SET section_update_timestamp=section_creation_date";
		echo traite_rqt($rqt,"UPDATE cms_sections SET section_update_timestamp");
	case '6' :
		//JP - choix notice nouveauté oui/non par utilisateur en création de notice
		$rqt = "ALTER TABLE users ADD deflt_notice_is_new INT( 1 ) UNSIGNED NOT NULL DEFAULT '0'";
		echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_notice_is_new");
	case '7' :
		// JP - paramètre mail_adresse_from pour l'envoi de mails
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='mail_adresse_from' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'mail_adresse_from', '', 'Adresse d\'expédition des emails. Ce paramètre permet de forcer le From des mails envoyés par PMB. Le reply-to reste inchangé (mail de l\'utilisateur en DSI ou relance, mail de la localisation ou paramètre opac_biblio_mail à défaut).\nFormat : adresse_email;libellé\nExemple : pmb@sigb.net;PMB Services' ,'',0)";
			echo traite_rqt($rqt,"insert pmb_mail_adresse_from into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='mail_adresse_from' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'mail_adresse_from', '', 'Adresse d\'expédition des emails. Ce paramètre permet de forcer le From des mails envoyés par PMB. Le reply-to reste inchangé (mail de l\'utilisateur en DSI ou relance, mail de la localisation ou paramètre opac_biblio_mail à défaut).\nFormat : adresse_email;libellé\nExemple : pmb@sigb.net;PMB Services' ,'a_general',0)";
			echo traite_rqt($rqt,"insert opac_mail_adresse_from into parametres");
		}
	case '8' :
		// JP - blocage des prolongations autorisées si relance sur le prêt
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='pret_prolongation_blocage' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'pret_prolongation_blocage', '0', 'Bloquer la prolongation s\'il y a un niveau de relance validé sur le prêt ?\n0 : Non 1 : Oui' ,'a_general',0)";
			echo traite_rqt($rqt,"insert opac_pret_prolongation_blocage into parametres");
		}
	case '9' :
		// JP - Export tableur des prêts dans le compte emprunteur
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='empr_export_loans' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'empr_export_loans', '0', 'Afficher sur le compte emprunteur un bouton permettant d\'exporter les prêts dans un tableur ?\n0 : Non 1 : Oui' ,'a_general',0)";
			echo traite_rqt($rqt,"insert opac_empr_export_loans into parametres");
		}
	case '10' :
		//Alexandre - Ajout des modes d'affichage avec sélection par étoiles
		$rqt = "UPDATE parametres SET comment_param=CONCAT(comment_param,'\n 4 : Affichage de la note sous la forme d\'étoiles, choix de la note sous la forme d\'étoiles.\n 5 : Affichage de la note sous la forme textuelle et d\'étoiles, choix de la note sous la forme d\'étoiles.') WHERE type_param= 'pmb' AND sstype_param='avis_note_display_mode'";
		echo traite_rqt($rqt,"UPDATE pmb_avis_note_display_mode into parametres");
		$rqt = "UPDATE parametres SET comment_param=CONCAT(comment_param,'\n 4 : Affichage de la note sous la forme d\'étoiles, choix de la note sous la forme d\'étoiles.\n 5 : Affichage de la note sous la forme textuelle et d\'étoiles, choix de la note sous la forme d\'étoiles.') WHERE type_param= 'opac' AND sstype_param='avis_note_display_mode'";
		echo traite_rqt($rqt,"UPDATE opac_avis_note_display_mode into parametres");
	case '11' :
		//JP - paramètre utilisateur : localisation par défaut en bulletinage
		// deflt_bulletinage_location : Identifiant de la localisation par défaut en bulletinage
		$rqt = "ALTER TABLE users ADD deflt_bulletinage_location INT( 6 ) UNSIGNED NOT NULL DEFAULT 0 AFTER deflt_collstate_location";
		echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_bulletinage_location");
		$rqt = "UPDATE users SET deflt_bulletinage_location=deflt_docs_location";
		echo traite_rqt($rqt,"UPDATE users SET deflt_bulletinage_location=deflt_docs_location");
	case '12' :
		//MB - last_sync_date : Date de la dernière synchronisation du connecteur
		$rqt = "ALTER TABLE connectors_sources ADD last_sync_date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL";
		echo traite_rqt($rqt,"ALTER TABLE connectors_sources ADD last_sync_date");
	case '13' :
		//JP - audit sur le contenu éditorial
		$res=pmb_mysql_query("SELECT id_section, section_creation_date, section_update_timestamp FROM cms_sections");
		if($res && pmb_mysql_num_rows($res)){
			while ($r=pmb_mysql_fetch_object($res)){
				$rqt = "INSERT INTO audit SET type_obj='".AUDIT_EDITORIAL_SECTION."', object_id='".$r->id_section."', user_id='0', user_name='', type_modif=1, quand='".$r->section_creation_date." 00:00:00', info='' ";
				pmb_mysql_query($rqt);
				if ($r->section_update_timestamp != $r->section_creation_date.' 00:00:00') {
					$rqt = "INSERT INTO audit SET type_obj='".AUDIT_EDITORIAL_SECTION."', object_id='".$r->id_section."', user_id='0', user_name='', type_modif=2, quand='".$r->section_update_timestamp."', info='' ";
					pmb_mysql_query($rqt);
				}
			}
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"INSERT editorial_sections INTO audit ");
		}
		$res=pmb_mysql_query("SELECT id_article, article_creation_date, article_update_timestamp FROM cms_articles");
		if($res && pmb_mysql_num_rows($res)){
			while ($r=pmb_mysql_fetch_object($res)){
				$rqt = "INSERT INTO audit SET type_obj='".AUDIT_EDITORIAL_ARTICLE."', object_id='".$r->id_article."', user_id='0', user_name='', type_modif=1, quand='".$r->article_creation_date." 00:00:00', info='' ";
				pmb_mysql_query($rqt);
				if ($r->article_update_timestamp != $r->article_creation_date.' 00:00:00') {
					$rqt = "INSERT INTO audit SET type_obj='".AUDIT_EDITORIAL_ARTICLE."', object_id='".$r->id_article."', user_id='0', user_name='', type_modif=2, quand='".$r->article_update_timestamp."', info='' ";
					pmb_mysql_query($rqt);
				}
			}
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"INSERT editorial_articles INTO audit ");
		}
	case '14' :
		//DG - Paramètre pour afficher ou non le bandeau d'acceptation des cookies
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='cookies_consent' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'cookies_consent', '1', 'Afficher le bandeau d\'acceptation des cookies et des traceurs ? \n0 : Non 1 : Oui','a_general',0)";
			echo traite_rqt($rqt,"insert opac_cookies_consent into parametres");
		}
	case '15' :
		//DG - Entrepôt par défaut en suppression de notices d'un panier
		$rqt = "ALTER TABLE users ADD deflt_agnostic_warehouse INT(6) UNSIGNED DEFAULT 0 NOT NULL " ;
		echo traite_rqt($rqt,"ALTER users ADD deflt_agnostic_warehouse");
	case '16' :
		// NG : ajout dans les préférences utilisateur du statut de publication d'article par défaut en création d'article
		$rqt = "ALTER TABLE users ADD deflt_cms_article_statut INT(6) UNSIGNED NOT NULL DEFAULT '0' " ;
		echo traite_rqt($rqt,"ALTER users ADD deflt_cms_article_statut ");
		// NG : ajout dans les préférences utilisateur du type de contenu par défaut en création d'article
		$rqt = "ALTER TABLE users ADD deflt_cms_article_type INT(6) UNSIGNED NOT NULL DEFAULT '0' " ;
		echo traite_rqt($rqt,"ALTER users ADD deflt_cms_article_type ");
		// NG : ajout dans les préférences utilisateur du type de contenu par défaut en création de rubrique
		$rqt = "ALTER TABLE users ADD deflt_cms_section_type INT(6) UNSIGNED NOT NULL DEFAULT '0' " ;
		echo traite_rqt($rqt,"ALTER users ADD deflt_cms_section_type ");
	case '17' :
		//NG -  DSI: Ajout de bannette_aff_notice_number pour afficher ou pas le nombre de notices envoyées dans le mail
		$rqt = "ALTER TABLE bannettes ADD bannette_aff_notice_number int unsigned NOT NULL default 1 " ;
		echo traite_rqt($rqt,"ALTER TABLE bannettes ADD bannette_aff_notice_number ");
	case '18' :
		//JP - Personnalisation des colonnes pour l'affichage des états des collections en gestion
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='collstate_data' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'collstate_data', '', 'Colonne des états des collections, dans l\'ordre donné, séparé par des virgules : location_libelle,emplacement_libelle,cote,type_libelle,statut_opac_libelle,origine,state_collections,archive,lacune,surloc_libelle,note,#n : id des champs personnalisés\nLes valeurs possibles sont les propriétés de la classe PHP \"pmb/classes/collstate.class.php\".','',0)";
			echo traite_rqt($rqt,"insert pmb_collstate_data = '' into parametres");
		}
	case '19' :
		//JP - champ historique de session trop petit
		$rqt = "ALTER TABLE admin_session CHANGE session session MEDIUMBLOB " ;
		echo traite_rqt($rqt,"ALTER TABLE admin_session CHANGE session MEDIUMBLOB ");
	case '20' :
		//DG - Paramètre pour activer ou non le sélecteur d'accès rapide
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='quick_access' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'quick_access', '1', 'Activer le sélecteur d\'accès rapide ? \n0 : Non 1 : Oui','a_general',0)";
			echo traite_rqt($rqt,"insert opac_quick_access into parametres");
		}
	case '21' :
		// JP - Alertes localisées pour les réservations depuis l'OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='resa_alert_localized' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'resa_alert_localized', '0', 'Si les lecteurs sont localisés, restreindre les notifications par email des nouvelles réservations aux utilisateurs selon le site de gestion des lecteurs par défaut ? \n0 : Non 1 : Oui' ,'',0)";
			echo traite_rqt($rqt,"insert pmb_resa_alert_localized into parametres");
		}
	case '22' :
		//MB - Champ prix trop petit
		$rqt = "ALTER TABLE lignes_actes CHANGE prix prix DOUBLE PRECISION(12,2) unsigned NOT NULL default '0.00'" ;
		echo traite_rqt($rqt,"ALTER lignes_actes CHANGE prix");
	case '23' :
		//MB - Champ montant trop petit
		$rqt = "ALTER TABLE frais CHANGE montant montant DOUBLE PRECISION(12,2) unsigned NOT NULL default '0.00'" ;
		echo traite_rqt($rqt,"ALTER frais CHANGE montant");

		//MB - Champ montant_global trop petit
		$rqt = "ALTER TABLE budgets CHANGE montant_global montant_global DOUBLE PRECISION(12,2) unsigned NOT NULL default '0.00'" ;
		echo traite_rqt($rqt,"ALTER budgets CHANGE montant_global");
	case '24' :
		//DB - script de vérification de saisie d'une notice perso en integration
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='catalog_verif_js_integration' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'pmb', 'catalog_verif_js_integration', '', 'Script de vérification de saisie de notice en intégration','', 0)";
			echo traite_rqt($rqt,"insert catalog_verif_js_integration into parametres");
		}
	case '25' :
		//DB Maj commentaires mail_methode
		$rqt = "update parametres set comment_param= 'Méthode d\'envoi des mails : \n php : fonction mail() de php\n smtp,hote:port,auth,user,pass,(ssl|tls) : en smtp, mettre O ou 1 pour l\'authentification... ' where sstype_param='mail_methode' ";
		echo traite_rqt($rqt,"update mail_methode comments");
	case '26' :
		//DG - accès rapide pour les paniers de lecteurs
		$rqt = "ALTER TABLE empr_caddie ADD acces_rapide INT NOT NULL default 0";
		echo traite_rqt($rqt,"ALTER TABLE empr_caddie ADD acces_rapide");
	case '27' :
		//JP - Autoriser les montants négatifs dans les acquisitions
		$rqt = "ALTER TABLE frais CHANGE montant montant DOUBLE(12,2) NOT NULL DEFAULT '0.00'";
		echo traite_rqt($rqt,"ALTER TABLE frais CHANGE montant");
		$rqt = "ALTER TABLE lignes_actes CHANGE prix prix DOUBLE(12,2) NOT NULL DEFAULT '0.00'";
		echo traite_rqt($rqt,"ALTER TABLE lignes_actes CHANGE prix");
	case '28' :
		//JP - update bannette_aff_notice_number pour les bannettes privées
		$rqt = "UPDATE bannettes SET bannette_aff_notice_number = 1 WHERE proprio_bannette <> 0" ;
		echo traite_rqt($rqt,"UPDATE bannettes SET bannette_aff_notice_number ");
	case '29' :
		//JP - Alerter l'utilisateur par mail des nouvelles demandes d'inscription aux listes de circulation
		$rqt = "ALTER TABLE users ADD user_alert_serialcircmail INT(1) UNSIGNED NOT NULL DEFAULT 0 after user_alert_subscribemail";
		echo traite_rqt($rqt,"ALTER TABLE users add user_alert_serialcircmail default 0");
	case '30' :
		//AP - Ajout du nom de groupe dans la table des circulations en cours
		$rqt = "ALTER TABLE serialcirc_circ ADD serialcirc_circ_group_name varchar(255) NOT NULL DEFAULT ''";
		echo traite_rqt($rqt,"ALTER TABLE serialcirc_circ add serialcirc_circ_group_name default ''");
	case '31' :
		//JP - Enrichir les flux rss générés par les veilles
		$rqt = "ALTER TABLE docwatch_watches ADD watch_rss_link VARCHAR(255) NOT NULL, ADD watch_rss_lang VARCHAR(255) NOT NULL, ADD watch_rss_copyright VARCHAR(255) NOT NULL, ADD watch_rss_editor VARCHAR(255) NOT NULL, ADD watch_rss_webmaster VARCHAR(255) NOT NULL, ADD watch_rss_image_title VARCHAR(255) NOT NULL, ADD watch_rss_image_website VARCHAR(255) NOT NULL";
		echo traite_rqt($rqt,"ALTER TABLE docwatch_watches add watch_rss_link, watch_rss_lang, watch_rss_copyright, watch_rss_editor, watch_rss_webmaster, watch_rss_image_title, watch_rss_image_website");
	case '32' :
		//JP - Possibilité de nettoyer le contenu HTML dans un OAI entrant
		$rqt = "ALTER TABLE connectors_sources ADD clean_html INT(3) UNSIGNED NOT NULL DEFAULT '0'";
		echo traite_rqt($rqt,"ALTER TABLE connectors_sources add clean_html");
	case '33' :
		//JP - Paramètre inutilisé caché
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='categories_categ_sort_records' "))==1){
			$rqt = "update parametres set gestion=1 where type_param= 'opac' and sstype_param='categories_categ_sort_records' ";
			echo traite_rqt($rqt,"update categories_categ_sort_records into parametres");
		}
	case '34' :
		//Alexandre - Préremplissage de la vignette des dépouillements avec la vignette du bulletin
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='bulletin_thumbnail_url_article' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'bulletin_thumbnail_url_article', '0', 'Préremplissage de l\'url de la vignette des dépouillements avec l\'url de la vignette de la notice bulletin en catalogage des périodiques ? \n 0 : Non \n 1 : Oui', '',0) ";
			echo traite_rqt($rqt, "insert pmb_bulletin_thumbnail_url_article=0 into parametres");
		}
	case '35' :
		//JP - date de création et créateur des paniers
		$rqt = "ALTER TABLE caddie ADD creation_user_name VARCHAR(255) NOT NULL DEFAULT '', ADD creation_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
		echo traite_rqt($rqt,"ALTER TABLE caddie add creation_user_name, creation_date");
		$rqt = "ALTER TABLE empr_caddie ADD creation_user_name VARCHAR(255) NOT NULL DEFAULT '', ADD creation_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
		echo traite_rqt($rqt,"ALTER TABLE empr_caddie add creation_user_name, creation_date");
	case '36':
		//TS - Ajout d'une classe CSS sur les cadres du portail
		$rqt = "ALTER TABLE cms_cadres ADD cadre_css_class VARCHAR(255) NOT NULL DEFAULT '' AFTER cadre_modcache";
		echo traite_rqt($rqt,"ALTER TABLE cms_cadres ADD cadre_css_class");
	case '37':
		//DG - Paramètre pour afficher ou non le lien de génération d'un flux RSS de la recherche
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='short_url' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'short_url', '1', 'Afficher le lien permettant de générer un flux RSS de la recherche ? \n0 : Non 1 : Oui','d_aff_recherche',0)";
			echo traite_rqt($rqt,"insert opac_short_url=1 into parametres");
		}
	case '38':
		//VT - Définition du mode de génération du flux rss de recherche (le paramètre opac_short_url doit être activé)
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='short_url_mode' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'short_url_mode', '0', 'Elements générés dans le flux rss de la recherche: \n0 : Nouveautés \n1 : Résultats de la recherche \nPour le mode 1, un nombre de résultats limite peut être ajouté après le mode, il doit être précédé d\'une virgule\nExemple: 1,30\nSi aucune limite n\'est spécifiée, c\'est le paramètre opac_search_results_per_page qui sera pris en compte','d_aff_recherche',0)";
			echo traite_rqt($rqt,"insert opac_short_url_mode=0 into parametres");
		}
	case '39':
		//JP - Message personnalisé sur la page de connexion
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='login_message' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'login_message', '', 'Message à afficher sur la page de connexion','', 0)";
			echo traite_rqt($rqt,"insert pmb_login_message into parametres");
		}
	case '40':
		//JP - éviter les codes-barre identiques en création d'emprunteur
		$rqt = "create table if not exists empr_temp (cb varchar( 255 ) NOT NULL ,sess varchar( 12 ) NOT NULL ,UNIQUE (cb))";
		echo traite_rqt($rqt,"create table  if not exists empr_temp ") ;
	case '41':
		//JP - changement du séparateur des tris de l'opac pour pouvoir utiliser le parse HTML
		$rqt = "UPDATE parametres SET valeur_param=REPLACE(valeur_param,';','||'), comment_param='Afficher la liste déroulante de sélection d\'un tri ? \n 0 : Non \n 1 : Oui \nFaire suivre d\'un espace pour l\'ajout de plusieurs tris sous la forme : c_num_6|Libelle||d_text_7|Libelle 2||c_num_5|Libelle 3\n\nc pour croissant, d pour décroissant\nnum ou text pour numérique ou texte\nidentifiant du champ (voir fichier xml sort.xml)\nlibellé du tri (optionnel)' WHERE type_param='opac' AND sstype_param='default_sort_list'";
		echo traite_rqt($rqt,"update value into parametres") ;
	case '42':
		///JP - Templates de bannette pour dsi privées
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'dsi' and sstype_param='private_bannette_tpl' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
			VALUES ( 'dsi', 'private_bannette_tpl', '0', 'Identifiant du template de bannette à appliquer sur les bannettes privées \nSi vide ou à 0, l\'entête et pied de page par défaut seront utilisés.','', 0)";
			echo traite_rqt($rqt,"insert dsi_private_bannette_tpl into parametres");
		}
	case '43':
		//DG - Modification du commentaire opac_etagere_notices_format
		$rqt = "update parametres set comment_param='Format d\'affichage des notices dans les étagères de l\'écran d\'accueil \n 1 : ISBD seul \n 2 : Public seul \n 4 : ISBD et Public \n 8 : Réduit (titre+auteurs) seul\n 9 : Templates django (Spécifier le nom du répertoire dans le paramètre notices_format_django_directory)' where type_param='opac' and sstype_param='etagere_notices_format'" ;
		echo traite_rqt($rqt,"UPDATE parametres SET comment_param for opac_etagere_notices_format") ;
	case '44':
		//DG - Modification du commentaire opac_bannette_notices_format
		$rqt = "update parametres set comment_param='Format d\'affichage des notices dans les bannettes \n 1 : ISBD seul \n 2 : Public seul \n 4 : ISBD et Public \n 8 : Réduit (titre+auteurs) seul\n 9 : Templates django (Spécifier le nom du répertoire dans le paramètre notices_format_django_directory)' where type_param='opac' and sstype_param='bannette_notices_format'" ;
		echo traite_rqt($rqt,"UPDATE parametres SET comment_param for opac_bannette_notices_format") ;
	case '45':
		// VT - Ajout d'un paramètre permettant de masquer les +/- dans les listes de résultats à l'opac
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='recherche_show_expand' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
			VALUES ( 'opac', 'recherche_show_expand', '1', 'Affichage des boutons de dépliage de notice dans les listes de résultats à l\'OPAC \n0: Boutons non affichés \n1: Boutons affichés','c_recherche', 0)";
			echo traite_rqt($rqt,"insert opac_recherche_show_expand into parametres");
		}
	case '46':
		//DG - Paramètre Portail : Activer l'onglet Toolkits
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'cms' and sstype_param='active_toolkits' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'cms', 'active_toolkits', '0', 'Activer la possibilité de gérer des toolkits.\n 0: non \n 1:Oui','',0)";
			echo traite_rqt($rqt,"insert cms_active_toolkits into parametres");
		}

		// DG - Création de la table pour les toolkits d'aide à la construction d'un portail
		// cms_toolkit_name : Nom du toolkit
		// cms_toolkit_active : Activé Oui/Non
		// cms_toolkit_data : Données
		// cms_toolkit_order : Ordre
		$rqt = "create table if not exists cms_toolkits(
				cms_toolkit_name varchar(255) not null default '' primary key,
				cms_toolkit_active int(1) NOT NULL DEFAULT 0,
				cms_toolkit_data text not null,
				cms_toolkit_order int(3) unsigned not null default 0)";
		echo traite_rqt($rqt,"create table cms_toolkits");
	case '47':
		// DG - Veilles : Option pour filtrer les nouveaux items avec une expression booléènne au niveau de la source
		$rqt = "ALTER TABLE docwatch_datasources ADD datasource_boolean_expression varchar(255) not null default '' after datasource_clean_html" ;
		echo traite_rqt($rqt,"ALTER TABLE docwatch_datasources ADD datasource_boolean_expression ");

		// DG - Veilles : Option pour filtrer les nouveaux items avec une expression booléènne au niveau de la veille
		$rqt = "ALTER TABLE docwatch_watches ADD watch_boolean_expression varchar(255) not null default ''" ;
		echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_boolean_expression ");

		// DG - Items de veilles : Index sans les mots vides
		$rqt = "ALTER TABLE docwatch_items ADD item_index_sew mediumtext not null default ''" ;
		echo traite_rqt($rqt,"ALTER TABLE docwatch_items ADD item_index_sew ");

		// DG - Items de veilles : Index avec les mots vides
		$rqt = "ALTER TABLE docwatch_items ADD item_index_wew mediumtext not null default ''" ;
		echo traite_rqt($rqt,"ALTER TABLE docwatch_items ADD item_index_wew ");

		// DG - Création d'une table pour stocker le source des sites surveillés
		// datasource_monitoring_website_num_datasource : Identifiant de la source
		// datasource_monitoring_website_upload_date : Date du téléchargement
		// datasource_monitoring_website_content : Contenu
		// datasource_monitoring_website_content_hash : Hash du contenu
		$rqt = "create table if not exists docwatch_datasource_monitoring_website(
				datasource_monitoring_website_num_datasource int(10) unsigned not null default 0 primary key,
				datasource_monitoring_website_upload_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				datasource_monitoring_website_content mediumtext not null,
				datasource_monitoring_website_content_hash varchar(255) not null default '')";
		echo traite_rqt($rqt,"create table docwatch_datasource_monitoring_website");
	case '48' :
		//JP - paramètre utilisateur : paniers du catalogue dépliés ou non par défaut
		$rqt = "ALTER TABLE users ADD deflt_catalog_expanded_caddies INT( 1 ) UNSIGNED NOT NULL DEFAULT 1";
		echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_catalog_expanded_caddies");
	case '49' :
		//Alexandre - Suppression du code pour les articles de périodiques
		$rqt = "UPDATE notices SET code='',update_date=update_date WHERE niveau_biblio='a'";
		echo traite_rqt($rqt,"UPDATE notices SET code for niveau_biblio=a");
	case '50' :
		//JP - Ajout du champ de notice Droit d'usage
		$rqt = "CREATE TABLE if not exists notice_usage (id_usage INT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT , usage_libelle VARCHAR( 255 ) NOT NULL default '', PRIMARY KEY ( id_usage ) , INDEX ( usage_libelle ) ) " ;
		echo traite_rqt($rqt,"CREATE TABLE notice_usage ");
		$rqt = "ALTER TABLE notices ADD num_notice_usage INT( 8 ) UNSIGNED DEFAULT 0 NOT NULL ";
		echo traite_rqt($rqt,"ALTER TABLE notices ADD num_notice_usage ");
	case '51' :
		//JP - Pouvoir cacher les documents numériques dans les options d'impression des paniers à l'opac
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='print_explnum' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'print_explnum', '1', 'Activer la possibilité d\'imprimer les documents numériques.\n 0: non \n 1: oui','h_cart',0)";
			echo traite_rqt($rqt,"insert opac_print_explnum into parametres");
		}
	case '52' :
		//JP - Ajout d'index sur la table catégories
		$rqt = "alter table categories drop index i_num_thesaurus";
		echo traite_rqt($rqt,"alter table categories drop index i_num_thesaurus");
		$rqt = "alter table categories add index i_num_thesaurus(num_thesaurus)";
		echo traite_rqt($rqt,"alter table categories add index i_num_thesaurus");
	case '53' :
		// NG - Activer le focus sur le champ de recherche a l'opac
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='focus_user_query' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'opac', 'focus_user_query', '1', 'Activer le focus sur le champ de recherche.\n 0: non \n 1:Oui','c_recherche',0)";
			echo traite_rqt($rqt,"insert opac_focus_user_query into parametres");
		}
	case '54' :
		// VT & DG - Paramètre de transfert de panier anonyme
		// 0 : Non
		// 1 : Sur demande
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='integrate_anonymous_cart' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'integrate_anonymous_cart', '0', 'Proposer le transfert des éléments du panier lors de l\'authentification.\n 0 : Non\n 1 : Sur demande','h_cart',0)";
			echo traite_rqt($rqt,"insert opac_integrate_anonymous_cart into parametres");
		}
	case '55':
		///JP - Nombre de notices diffusées pour dsi privées
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'dsi' and sstype_param='private_bannette_nb_notices' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
		VALUES ( 'dsi', 'private_bannette_nb_notices', '30', 'Nombre maximum par défaut de notices diffusées dans les bannettes privées.','', 0)";
			echo traite_rqt($rqt,"insert dsi_private_bannette_nb_notices into parametres");
		}
	case '56':
		///JP - Affichage simplifié du panier OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='simplified_cart' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
		VALUES ( 'opac', 'simplified_cart', '0', 'Affichage simplifié du panier.\n 0 : non \n 1 : oui','h_cart', 0)";
			echo traite_rqt($rqt,"insert opac_simplified_cart=0 into parametres");
		}
	case '57' :
		//JP - Prix de l'exemplaire dans le paramétrage de l'abonnement
		$rqt = "ALTER TABLE abts_abts ADD prix varchar(255) NOT NULL DEFAULT ''";
		echo traite_rqt($rqt,"ALTER TABLE abts_abts ADD prix");
	case '58' :
		// TS & AP - Création d'une table de liaison entre SESSID et token (pour les SSO)
		// sessions_tokens_SESSID : SESSID
		// sessions_tokens_token : Token
		// sessions_tokens_type : Information sur l'utilisation du token
		$rqt = "create table if not exists sessions_tokens(
						sessions_tokens_SESSID varchar(12) not null default '',
						sessions_tokens_token varchar(255) NOT NULL default '',
						sessions_tokens_type varchar(255) NOT NULL default '',
						primary key (sessions_tokens_SESSID, sessions_tokens_type),
						index i_st_sessions_tokens_type(sessions_tokens_type),
						index i_st_sessions_tokens_token(sessions_tokens_token))";
		echo traite_rqt($rqt,"create table sessions_tokens");
	case '59' :
		//JP - Bloquer les prêts dès qu'un lecteur est en retard
		$rqt = "update parametres set comment_param='Délai à partir duquel le retard est pris en compte pour le blocage\n 0 : dès qu\'un prêt est en retard\n N : au bout de N jours de retard' where type_param= 'pmb' and sstype_param='blocage_delai' ";
		echo traite_rqt($rqt,"update blocage_delai into parametres");
		$rqt = "update parametres set comment_param='Nombre maximum de jours bloqués\n 0 : pas de limite\n N : maxi N\n -1 : blocage levé dès qu\'il n\'y a plus de retard' where type_param= 'pmb' and sstype_param='blocage_max' ";
		echo traite_rqt($rqt,"update blocage_max into parametres");
	case '60' :
		//JP - Ajout d'index sur la table es_searchcache
		$req="SHOW INDEX FROM es_searchcache WHERE key_name LIKE 'i_es_searchcache_date'";
		$res=pmb_mysql_query($req);
		if($res && (pmb_mysql_num_rows($res) == 0)){
			$rqt = "alter table es_searchcache add index i_es_searchcache_date(es_searchcache_date)";
			echo traite_rqt($rqt,"alter table es_searchcache add index i_es_searchcache_date");
		}
	case '61' :
		//JP - Lien vers la documentation des fonctions de parse HTML dans les paramètres
		$rqt = "update parametres set comment_param=CONCAT(comment_param,'\n<a href=\'".$base_path."/includes/interpreter/doc?group=inhtml\' target=\'_blank\'>Consulter la liste des fonctions disponibles</a>') where type_param= 'opac' and sstype_param='parse_html' ";
		echo traite_rqt($rqt,"update parse_html into parametres");
	case '62' :
		//NG - Ajout du champ commentaire de gestion dans les étagères
		$rqt = "ALTER TABLE etagere ADD comment_gestion TEXT NOT NULL " ;
		echo traite_rqt($rqt,"ALTER TABLE etagere ADD comment_gestion ");
	case '63' :
		//MB - Ajout d'index sur la table es_cache_blob
		$req="SHOW INDEX FROM es_cache_blob WHERE key_name LIKE 'i_es_cache_expirationdate'";
		$res=pmb_mysql_query($req);
		if($res && (pmb_mysql_num_rows($res) == 0)){
			$rqt = "alter table es_cache_blob add index i_es_cache_expirationdate(es_cache_expirationdate)";
			echo traite_rqt($rqt,"alter table es_cache_blob add index i_es_cache_expirationdate");
		}
	case '64' :
		//MB - Ajout d'index sur la table cms_cache_cadres
		$req="SHOW INDEX FROM cms_cache_cadres WHERE key_name LIKE 'i_cache_cadre_create_date'";
		$res=pmb_mysql_query($req);
		if($res && (pmb_mysql_num_rows($res) == 0)){
			$rqt = "alter table cms_cache_cadres add index i_cache_cadre_create_date(cache_cadre_create_date)";
			echo traite_rqt($rqt,"alter table cache_cadre_create_date add index i_cache_cadre_create_date");
		}
	case '65' :
		//JP - filtres de relances personnalisables
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='filter_relance_rows' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param,comment_param, section_param, gestion) VALUES ('empr','filter_relance_rows', 'g,cs', 'Critères de filtrage ajoutés aux critères existants pour les relances à faire, saisir les critères séparés par des virgules.\nLes critères disponibles correspondent à l\'attribut value du fichier substituable empr_list.xml', '','0')";
			echo traite_rqt($rqt,"insert empr_filter_relance_rows into parametres");
		}
	case '66' :
		//JP - cacher les amendes et frais de relance dans les lettres et mails de retard
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'mailretard' and sstype_param='hide_fine' "))==0){
			$rqt = "INSERT INTO parametres VALUES (0,'mailretard','hide_fine','0','Masquer les amendes et frais de relance dans les lettres et mails de retard :\n 0 : Non\n 1 : Oui','',0)" ;
			echo traite_rqt($rqt,"insert mailretard_hide_fine into parametres") ;
		}
	case '67' :
		//JP - paramètre pour forcer l'envoi de relance de niveau 2 par lettre si priorite_email = 1
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'mailretard' and sstype_param='priorite_email_2' "))==0){
			$rqt = "INSERT INTO parametres VALUES (0,'mailretard','priorite_email_2','0','Forcer le deuxième niveau de relance par lettre si priorite_email = 1 :\n 0 : Non\n 1 : Oui','',0)" ;
			echo traite_rqt($rqt,"insert mailretard_priorite_email_2 into parametres") ;
		}
	case '68' :
		//JP - Calculer le retard, l'amende et le blocage sur le calendrier d'ouverture de la localisation de l'utilisateur ou de l'exemplaire
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='utiliser_calendrier_location' "))==0){
			$rqt = "INSERT INTO parametres VALUES (0,'pmb','utiliser_calendrier_location','0','Si le paramètre utiliser_calendrier est à 1, choix de la localisation pour calculer le retard, l\'amende et le blocage :\n 0 : calcul sur le calendrier d\'ouverture de la localisation de l\'utilisateur\n 1 : calcul sur le calendrier d\'ouverture de la localisation de l\'exemplaire','',0)" ;
			echo traite_rqt($rqt,"insert pmb_utiliser_calendrier_location into parametres") ;
		}
}

/******************** JUSQU'ICI **************************************************/
/* PENSER à faire +1 au paramètre $pmb_subversion_database_as_it_shouldbe dans includes/config.inc.php */
/* COMMITER les deux fichiers addon.inc.php ET config.inc.php en même temps */

echo traite_rqt("update parametres set valeur_param='".$pmb_subversion_database_as_it_shouldbe."' where type_param='pmb' and sstype_param='bdd_subversion'","Update to $pmb_subversion_database_as_it_shouldbe database subversion.");
echo "<table>";