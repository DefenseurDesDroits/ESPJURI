;<?php die('no access'); ?>
/*
;+-------------------------------------------------+
; Parametres spécifiques LDAP
; $Id: ldap_params_ddd.php dbellamy Exp $
;
; Défenseur des droits
; Gestion + OPAC
; V1 (DB-06/10/2016)
; Encodage = utf-8
;+-------------------------------------------------+
;+-------------------------------------------------+
;CONFIGURATION LDAP

[ldap]

;Hote
ldap_host='ldap://192.168.1.16:389'

;Utilisateur
ldap_user='ac\linagora'
ldap_pwd='Li1234'

;+-------------------------------------------------+
;LECTEURS

;Branches à interroger (Lecteurs)
ldap_base_dn[]='ou=ddd,dc=ac,dc=local'

;Filtre de recherche (Lecteurs)
ldap_filter='(objectclass=user)(givenname=*)'

;Attributs à récupérer (Lecteurs)
ldap_attr='samaccountname,sn,givenname,telephonenumber,mail,company,l,department,title'

;Attribut identifiant (Lecteurs)
ldap_logon_attr='samaccountname'

;+-------------------------------------------------+
;VALEURS PAR DEFAUT DANS PMB

[pmb_default_values]

;Identifiants des valeurs par défaut
default_empr_location=1		;Bibliothèque du service documentation
default_empr_categ=1		;Prêt standard
default_empr_codestat=1		;DDD
default_empr_statut=1		:Actif

;Identifiant modèle utilisateur pour création
;default_user_model=2

;+-------------------------------------------------+
;CORRESPONDANCES DIRECTES ATTRIBUTS PMB  <> ATTRIBUTS LDAP

[ldap2pmb]

;Attributs obligatoires
ldap2pmb_attr[empr_login]='samaccountname'
ldap2pmb_attr[empr_cb]='samaccountname'
ldap2pmb_attr[empr_nom]='sn'

;Attributs optionnels
ldap2pmb_attr[empr_prenom]='givenname'
ldap2pmb_attr[empr_mail]='mail'
;ldap2pmb_attr[empr_tel1]=''
ldap2pmb_attr[empr_tel2]='telephonenumber'
;ldap2pmb_attr[empr_sexe]=''
;ldap2pmb_attr[empr_prof]='title'

*/



