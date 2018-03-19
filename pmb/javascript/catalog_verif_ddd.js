// +-------------------------------------------------+
// $Id: catalog_verif_ddd dbellamy Exp $
//
// Defenseur Des Droits
// Gestion : pmb/catalog
// ATTENTION : fichier en UTF-8
// V1 (DB-14/08/2017)
// +-------------------------------------------------+


//Types de documents pris en compte pour generation NOR >> natures de texte correspondantes
var typdocs_nor = new Array();
typdocs_nor['b'] = 'S';	//Decisions
typdocs_nor['n'] = 'V'; //Avis au parlement 
typdocs_nor['l'] = 'P'; //Rapports et etudes
typdocs_nor['o'] = 'S';	//Propositions de reforme

var datas_nor = new Array();
datas_nor['AAA'] = '';
datas_nor['S'] = '';
datas_nor['YY'] = '';
datas_nor['OOOOO'] = '';
datas_nor['N'] = '';

//Types de documents pris en compte pour generation ELI >> natures detexte correspondantes
var typdocs_eli = new Array();
typdocs_eli['b'] = 'decision';	//Decisions
typdocs_eli['n'] = 'avis'; //Avis au parlement 
typdocs_eli['l'] = 'rapport'; //Rapports et etudes
typdocs_eli['o'] = 'decision';	//Propositions de reforme
typdocs_eli['m'] = 'decision';	//Règlements amiables

var datas_eli = new Array();
datas_eli['url'] = '';
datas_eli['type'] = '';
datas_eli['annee'] = '';
datas_eli['mois'] = '';
datas_eli['jour'] = '';
datas_eli['identifiant_naturel'] = '';
datas_eli['version'] = '';
datas_eli['level']= 'texte';


/*
 * Les monographies
 */
function check_perso_form(){
	
	datas_nor['AAA'] = '';
	datas_nor['S'] = '';
	datas_nor['YY'] = '';
	datas_nor['OOOOO'] = '';
	datas_nor['N'] = '';
	
	datas_eli['url'] = '';
	datas_eli['type'] = '';
	datas_eli['annee'] = '';
	datas_eli['mois'] = '';
	datas_eli['jour'] = '';
	datas_eli['identifiant_naturel'] = '';
	datas_eli['version'] = '';
	datas_eli['level']= 'texte';
	
	form_catalog_error_msg = '';
	var check = true;
	var error_msg = 'Certaines informations sont manquantes : \n';
	
	var typdoc = document.getElementById('typdoc').value;

	var check_nor = is_nor(typdoc);
	var check_eli = is_eli(typdoc);

	if(!check_titre_propre()) {
		check = false;
	}

	if((check_nor || check_eli)) {

		if(!check_date_signature()) {
			check = false;
		}
		if(!check_numero_ordre()) {
			check = false;
		}

	}
	
	if(check_nor){
		
		if(!check_auteur_principal()) {
			check = false;
		}
	
		if(!check_auteur_secondaire()) {
			check = false;
		}
		
		if (check && '' != datas_nor['AAA'] && '' != datas_nor['S'] && '' != datas_nor['YY'] && '' != datas_nor['OOOO'] && '' != datas_nor['N'] ) {
		
			try {
				var cp_nor = document.getElementById('cp_nor'); 
				cp_nor.value = datas_nor['AAA'] + datas_nor['S'] + datas_nor['YY'] + datas_nor['OOOOO'] + datas_nor['N'];
			} catch(err) {}
		}
		
	}

	if (check_eli) {
		
		if (!check_url_eli()) {
			check = false;
		}

		if (!check_version_eli()) {
			check = false;
		}

		if (check && datas_eli['url'] && '' != datas_eli['type'] && '' != datas_eli['annee'] && '' != datas_eli['mois'] && '' != datas_eli['jour'] && '' != datas_eli['identifiant_naturel'] && '' != datas_eli['version'] && '' != datas_eli['level'] ) {
			try {
				var cp_eli = document.getElementById('cp_eli_link');
				cp_eli.value = datas_eli['url'] + 'eli' + '/' + datas_eli['type'] + '/' + datas_eli['annee'] + '/' + datas_eli['mois'] + '/' + datas_eli['jour'] + '/' + datas_eli['identifiant_naturel'] + '/' + datas_eli['version'] + '/' + datas_eli['level'];
			} catch(err) {
				
			}
		}
	}

	if (!check) {
		alert(error_msg + form_catalog_error_msg);		
	}
	
	return check;
}


/*
 * Les articles
 */
function check_perso_analysis_form(){
	return true;	
}

/*
 * Les bulletins
 */
function check_perso_bull_form(){
	return true;	
}

function is_nor(typdoc) {
	
	var check = false;
	
	if (document.getElementById('cp_generer_nor_N').checked) {
		return false;
	}
	if(typdocs_nor[typdoc]) {
		datas_nor['N'] = typdocs_nor[typdoc];
		check = true;
	}

	return check;
}


function is_eli(typdoc) {

	var check = false;

	if (document.getElementById('cp_generer_eli_N').checked) {
		return false;
	}
	if(typdocs_eli[typdoc]) {
		datas_eli['type'] = typdocs_eli[typdoc];
		check = true;
	}

	return check;
	
}


/*
 * Titre propre
 */
function check_titre_propre(){
	
	var check = false;
	
	try {
		var titre_propre = document.notice.f_tit1.value;
	
		if('' != titre_propre) {
			check = true;
		} else {
			form_catalog_error_msg+=get_message('titre_propre');
		}
	} catch(err){}
	
	return check;
}


/*
 * Auteur principal
 */
function check_auteur_principal(){
	
	var check = false;

	try {
		var auteur_principal = document.getElementById('f_aut0_id').value;
	
		if( '' != auteur_principal && 0 != auteur_principal) {
			check = true;
		} else {
			form_catalog_error_msg+=get_message('auteur_principal');			
		}
		
		if(check && !check_cp_identifiant_autorite(auteur_principal) ) {
			form_catalog_error_msg+=get_message('cp_identifiant_autorite');
			check = false;
		}
	} catch(err){}
	
	return check;
}


/*
 * Identifiant autorite
 */
function check_cp_identifiant_autorite (id_auteur){

	var check = false;
	
	if(id_auteur) {
		check = true;
	}
	
	if (check) {		
		try {
			var url= './ajax_catalog_verif_ddd.php?action=get_cp_identifiant_autorite&author_id='+id_auteur;
			var hr = new http_request();
			if(hr.request(url)){
				check = false;
			} else {
				
				cp_identifiant_autorite = hr.get_text();
			}	
			
			if (check) {
				check = /^[A-Z]{3}$/.test(cp_identifiant_autorite);
			}
			if (check) {
				datas_nor['AAA'] = cp_identifiant_autorite;
			}
		} catch(err){}	
	}
	
	return check;
}


/*
 * Auteur secondaire
 */
function check_auteur_secondaire(){
	
	var check = false;
	
	try {
		var auteur_secondaire = document.getElementById('f_aut2_id0').value;
	
		if('' != auteur_secondaire && 0 != auteur_secondaire) {
			check = true;
		} else {
			form_catalog_error_msg+=get_message('auteur_secondaire');
		}
	
		if(check && !check_cp_identifiant_service(auteur_secondaire)) {
			form_catalog_error_msg+=get_message('cp_identifiant_service');
			check = false;
		}
	} catch(err){}
	
	return check;
}


/*
 * Identifiant service
 */
function check_cp_identifiant_service(id_auteur) {

	var check = false;
	
	if(id_auteur) {
		check = true;
	}
	if (check) {
		try {
			var url= './ajax_catalog_verif_ddd.php?action=get_cp_identifiant_service&author_id='+id_auteur;
			var hr = new http_request();
			if(hr.request(url)){
				check = false;
			} else {
				cp_identifiant_service = hr.get_text();
			}
			
			if (check) {
				check = /^[A-Z]{1}$/.test(cp_identifiant_service);
			}
			if(check){
				datas_nor['S'] = cp_identifiant_service;
			}
		} catch(err){}	
	}
		
	return check;
}


/*
 * Annee
 */
function check_date_signature(){
	
	var check = false;
	
	try {
		var date_signature =  document.getElementById('f_year').value;
		//Format JJ(J)/MM(M)/AAAA(AA)
		check = /^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2}$/.test(date_signature);
		if(!check) {
			check = /^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/.test(date_signature);
		}
		if(!check){
			form_catalog_error_msg+=get_message('date_signature');	
		}
		if(check) {
			var d = date_signature.split('/');
			var yy = d[2];
	
			if (1==yy.length) {
				yy = '0'+yy;
			}
			yy = yy.substr(yy.length-2,2);
			datas_nor['YY']=yy;
		
			datas_eli['annee'] = d[2];
			datas_eli['mois'] = d[1];
			datas_eli['jour'] = d[0];
		}
	} catch(err){}

	return check;
}


function check_numero_ordre(){
	
	var check = false;
	
	try {
		var numero_ordre = document.getElementById('f_cb').value;
		 
		var pos = numero_ordre.lastIndexOf('-'); 
		
		if ('-1' != pos) {
			numero_ordre = numero_ordre.substr(pos+1);
		}
		l = numero_ordre.length;
		if(l>5) {
		  numero_ordre =  numero_ordre.substr(l-5);
		}
		if (l<5) {
		  pad = '00000';
		  pad0 = pad.substr(pad.length-5+l);
		  numero_ordre =  pad.substr(pad.length-5+l) + numero_ordre;
		}
		check = /^[0-9]{5}$/.test(numero_ordre);
		if(!check){
			if (5==numero_ordre.length) 
			form_catalog_error_msg+=get_message('numero_ordre');	
		}
		if(check) {
			datas_nor['OOOOO'] = numero_ordre;
			datas_eli['identifiant_naturel'] = numero_ordre;
		}
	} catch(err){}
	
	return check;
}


function check_url_eli() {

	var check = false;
	
	try {
		var url= './ajax_catalog_verif_ddd.php?action=get_url_eli';
		var hr = new http_request();
		if(hr.request(url)){
			form_catalog_error_msg+=get_message('url_opac');	
		} else {
			check = true;
			datas_eli['url'] = hr.get_text();
		}	
	} catch(err){}
	
	return true;
}


function check_version_eli() {

	var check = false;
	
	try {
		var version_eli =  document.getElementById('cp_version_eli').value;
		check = /^[A-Z|a-z]{2}$/.test(version_eli);
		if(!check){
			form_catalog_error_msg+=get_message('cp_version_eli');	
		}
		if (check) {
			datas_eli['version'] = version_eli;
		}
	}catch(err){} 

	return check;
}


/*
 * Messages d'erreurs
 */
function get_message(elem,valeur){
	var message = '\n -';
	switch(elem){
		case 'titre_propre' :
			message += ' Veuillez renseigner un titre propre';
			break;
		case 'auteur_principal' :
			message += ' Veuillez renseigner un auteur principal';
			break;
		case 'cp_identifiant_autorite' :	
			message += ' Veuillez renseigner le champ identifiant d\'autorité (AAA) de l\'auteur principal';
			break;
		case 'auteur_secondaire' :
			message += ' Veuillez renseigner un auteur secondaire';
			break;
		case 'cp_identifiant_service' :	
			message += ' Veuillez renseigner le champ identifiant de service (S) de l\'auteur secondaire';
			break;
		case 'date_signature' :
			message += ' Veuillez renseigner une date de signature sous la forme JJ(J)/MM(M)/AAAA(AA)';
			break;
		case 'numero_ordre' :
			message += ' Veuillez renseigner un numéro d\'ordre à 5 chiffres';
			break;
		case 'url_opac' :
			message += ' L\'URL de l\'OPAC n\'est pas définie';
			break;
		case 'cp_version_eli' :
			message += ' Veuillez renseigner une version pour générer l\'identifiant ELI';
			break;
	}
	return message;
}
