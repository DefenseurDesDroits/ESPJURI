<?php
// +-----------------------------------------------------+
// © 2004-2008 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-----------------------------------------------------+

/* Ce fichier sert a etablir la correspondance entre la documentation et PMB.
   Ce fichier est distribue avec le zip de la documentation. 
  
   N'EDITEZ PAS CE FICHIER
  
*/

//lien entre les infos postees et la doc
switch ($script_name) {

//TODO
//AFFICHAGE DE LA LICENCE > -----------------------------------------------------------------------------
/*
	case 'main.php' :
		print 'html-install/ch12.html#licence';
    	break;
*/

	
//PREFERENCES > -----------------------------------------------------------------------------
    case 'account.php' :
		print 'co/preferences.html';
		break;

		
//CIRCULATION > -----------------------------------------------------------------------------
	case 'circ.php' :
		switch ($categ) {
			
//CIRCULATION > CIRCULATION			
			case '' :
				print 'co/circ.html';
				break;
			case 'retour':
	    		print 'co/circ_retour.html';
				break;
			case 'ret_todo':
	    		print 'co/circ_ret_todo.html';
				break;
			case 'groups':
	    		print 'co/circ_groups.html';
				break;
			case 'empr_create':
	    		print 'co/circ_empr_create.html';
				break;
			case 'empr_saisie':
	    		print 'co/circ_empr_saisie.html';
				break;
			case 'pret' :
				switch ($sub) {
					case 'show_late':
						print 'co/circ_pret_show_late.html';
						break;
					default :
						print 'co/circ.html';
						break;
				}
				break;
				
//CIRCULATION > PANIERS
			case 'caddie' :
				switch ($sub) {
					case 'gestion':
						switch ($quoi) {
							case 'panier':
								print 'co/circ_caddie_gestion_panier.html';
								break;
							case 'procs' :
								print 'co/circ_caddie_gestion_procs.html';
								break;
							case 'remote_procs':
								print 'co/circ_caddie_gestion_remote_procs.html';
								break;
							case 'barcode':
								print 'co/circ_caddie_gestion_barcode.html';
								break;
							case 'selection':
								print 'co/circ_caddie_gestion_selection.html';
								break;
							case 'pointagebarcode':
								print 'co/circ_caddie_gestion_pointagebarcode.html';
								break;
							case 'pointage':
								print 'co/circ_caddie_gestion_pointage.html';
								break;
							case 'razpointage':
								print 'co/circ_caddie_gestion_razpointage.html';
								break;
							default :
								print 'co/circulation_paniers';
								break;
						}
						break;
					case 'action':
						switch ($quelle) {
							case 'supprpanier':
								print 'co/circ_caddie_action_supprpanier.html';
								break;
							case 'transfert':
								print 'co/circ_caddie_action_transfert.html';
								break;
							case 'edition':
								print 'co/circ_caddie_action_edition.html';
								break;
							case 'mailing':
								print 'co/circ_caddie_action_mailing.html';
								break;
							case 'selection':
								print 'co/circ_caddie_action_selection.html';
								break;
							case 'supprbase':
								print 'co/circ_caddie_action_supprbase.html';
								break;
							default :
								print 'co/circ_caddie_action';
								break;
						}
						break;
					default :
						print 'co/circulation_paniers';
						break;
				} 
				break;
				
//CIRCULATION > VISUALISER
			case 'visu_ex':
	    		print 'co/circ_visu_ex.html';
				break;
			case 'visu_rech':
	    		print 'co/circ_visu_rech.html';
				break;
				
//CIRCULATION > RESERVATIONS		
			case 'listeresa':
				switch ($sub) {
					case 'encours':
			    		print 'co/circ_listeresa_encours.html';
						break;
					case 'depassee' :
			    		print 'co/circ_listeresa_depassee.html';
						break;
					case 'docranger' :
			    		print 'co/circ_listeresa_docranger.html';
						break;
					default :
						print 'co/circulation_reservations.html';
						break;
				}
				break;
				
//CIRCULATION > PREVISIONS						
			case 'resa_planning' :
		    	print 'co/nodoc.html';
				break;
				
//CIRCULATION > RELANCES
			case 'relance':
				switch ($sub) {
					case 'todo':
			    		print 'co/circ_relance_todo.html';
						break;
					case 'recouvr' :
			    		print 'co/circ_relance_recouvr.html';
						break;
					default :
						print 'co/circulation_relances.html';
						break;
				}
				break;

//CIRCULATION > SUGGESTIONS				
			case 'sug' :
				print 'co/circ_sug.html';
				break;

//CIRCULATION > TRANSFERTS 
			case 'trans' :
				switch ($sub) {
					case 'valid' :
						print 'co/circ_trans_valid.html';
						break;
					case 'envoi' :
						print 'co/circ_trans_envoi.html';
						break;
					case 'recep' :
						print 'co/circ_trans_recep.html';
						break;
					case 'retour' :
						print 'co/circ_trans_retour.html';
						break;
					case 'refus' :
						print 'co/circ_trans_refus.html';
						break;
					default :
						print 'co/circulation_transferts.html';
						break;
				}
				break;
				
//CIRCULATION > NON DOCUMENTE
			default :
				print 'co/nodoc.html';
				break;				
		}
		break;


//CATALOGUE > -----------------------------------------------------------------------------
	case 'catalog.php' :
		switch ($categ) {
			
//CATALOGUE > RECHERCHE
			case 'search':
				switch ($mode) {
					case '':
					case '0':
						// Auteur/titre
			    		print 'co/catalog_search_0.html';
						break;
					case '1':
						// Categorie/sujet
						print 'co/catalog_search_1.html';
						break;
					case '5':
						// Termes du thesaurus
						print 'co/catalog_search_5.html';
						break;
					case '2':
						// Editeur/collection
						print 'co/catalog_search_2.html';
						break;		
					case '9':	
						print 'co/catalog_search_9.html';
						break;												
					case '3':
						// Paniers de notices
						print 'co/catalog_search_3.html';
						break;
					case '6':
						// Multi-criteres
						print 'co/catalog_search_6.html';
						break;
					case '8':
						//exemplaires	
						print 'co/catalog_search_8.html';
						break;
					case '7':	
						//externe
						print 'co/catalog_search_7.html';
						break;
//CATALOGUE > RECHERCHE > NON DOCUMENTE						
					default :
						print 'co/catalogue_recherche.html';
						break;
				}
	    		break;		
			case 'last_records':
				//dernieres notices
				print 'co/catalog_last_records.html';
				break;
			case 'search_perso' :
				print 'co/catalog_search_perso.html';
				//prédéfinies
				break;			
				
//CATALOGUE > DOCUMENTS
			case 'create':
				print 'co/catalog_create.html';
				break;
			case 'create_form':
				print 'co/catalog_create.html';
				break;
			case 'avis':
				print 'co/catalog_avis.html';
				break;
			case 'tags':
				print 'co/catalog_tags.html';
				break;
				
//CATALOGUE > PERIODIQUES
			case 'serials':
				switch ($sub) {
					case 'serial_form':
			    		print 'co/catalog_serials_serial_form.html';
						break;
					case 'view':
						switch($view) {
							case 'abon' :
								print 'co/catalog_serials_view_abon.html';
								break;
							case 'modele' :
								print 'co/catalog_serials_view_modele.html';
								break;
							case 'collstate' :
								print 'co/catalog_serials_view_collstate.html';
								break;
							default :
								print 'co/catalog_serials_view.html';
								break;
						}
						break;
					case 'pointage':
						print 'co/catalog_serials_pointage.html';
						break;					
					case 'bulletinage':
						switch ($action) {
							case 'bul_form' :
			    				print 'co/catalog_serials_bulletinage_bul_form.html';
			    				break;
							case 'view':
			    				print 'co/catalog_serials_bulletinage_view.html';
			    				break;
							default :
			    				print 'co/catalog_serials_bulletinage_view.html';
			    				break;
						}
						break;
						
					case 'analysis':
			    		print 'co/catalog_serials_bulletinage_view.html';
						break;		
										
//CATALOGUE > RECHERCHE > PERIODIQUES					
					case '':
					default:
			    		print 'co/catalog_serials.html';
						break;
						
				}
				break;
				
//CATALOGUE > PANIERS				
			case 'caddie':
				switch ($sub) {
					case 'collecte':
						switch ($moyen) {
							case 'douchette':
					    		print 'co/catalog_caddie_collecte_douchette.html';
								break;
							case 'selection' :
					    		print 'co/catalog_caddie_collecte_selection.html';
								break;
							case '' :
							default:
					    		print 'co/catalog_caddie_collecte.html';
								break;
						}
						break;
					case 'pointage':
						switch ($moyen) {
							case 'douchette':
								print 'co/catalog_caddie_pointage_douchette.html';
								break;
							case 'selection' :
								print 'co/catalog_caddie_pointage_selection.html';
								break;
							case 'raz' :
								print 'co/catalog_caddie_pointage_raz.html';
								break;
							case '' :
							default:
					    		print 'co/catalog_caddie_pointage.html';
								break;
						}
						break;
					case 'action':
						switch ($quelle) {
							case 'supprpanier' :
					    		print 'co/catalog_caddie_action_supprpanier.html';
								break;
							case 'transfert' :
					    		print 'co/catalog_caddie_action_transfert.html';
								break;
							case 'edition':
					    		print 'co/catalog_caddie_action_edition.html';
								break;
 							case 'impr_cote' :
					    		print 'co/catalog_caddie_action_impr_cote.html';
 								break;
							case 'export' :
					    		print 'co/catalog_caddie_action_export.html';
								break;
							case 'expdocnum' :
					    		print 'co/catalog_caddie_action_expdocnum.html';
								break;
							case 'selection' :
					    		print 'co/catalog_caddie_action_selection.html';
								break;
							case 'supprbase' :
					    		print 'co/catalog_caddie_action_supprbase.html';
								break;
							case '' :
							default:
					    		print 'co/catalog_caddie_action.html';
								break;
						}
						break;
					case 'gestion':
					default:
						switch ($quoi) {
							case 'procs':
								print 'co/catalog_caddie_gestion_procs';
								break;
							case 'remote_procs':
								print 'co/catalog_caddie_gestion_remote_procs';
								break;
							case 'panier' :
							case '' :
							default:
					    		print 'co/catalog_caddie_gestion_panier';
								break;
						}
						break;
				}
				break;
				
//CATALOGUE > ETAGERES				
			case 'etagere':
				switch ($sub) {
					case 'constitution':
					    print 'co/catalog_etagere_constitution.html';
						break;
					case 'gestion':
					case '':
			    		default:
						print 'co/catalog_etagere.html';
						break;
				}
				break;
				
//CATALOGUE > EXTERNE				
			case 'z3950':
				print 'co/catalog_z3950.html';
				break;
				
//CATALOGUE > SUGGESTIONS
			case 'sug':
				print 'co/catalog_sug.html';
				break;

//CATALOGUE > DOCUMENT NUMERIQUES				
			case 'explnum_create' :
				print 'co/catalog_explnum_create.html';
				break;				
				
//CATALOGUE > NON DOCUMENTE				
			case '' :				
			default:
	    		print 'co/catalogue.html';
	    		break;
		}
		break;

		
//AUTORITES -----------------------------------------------------------------------------
    case 'autorites.php' :
	    switch ($categ) {
	    	
//AUTORITES > AUTORITES	    	
			case '':
	    	case 'auteurs':
	    		print 'co/autorites_auteurs.html';
	    		break;
	    	case 'categories':
	    		print 'co/autorites_categories.html';
	    		break;
	    	case 'editeurs':
	    		print 'co/autorites_editeurs.html';
	    		break;
	    	case 'collections':
    			print 'co/autorites_collections.html';
	    		break;
	    	case 'souscollections';
	    		print 'co/autorites_souscollections.html';
	    		break;
	    	case 'series':
	    		print 'co/autorites_series.html';
	    		break;
	    	case 'titres_uniformes':
	    		print 'co/autorites_titres_uniformes.html';
	    		break;	
	    	case 'indexint':
	    		print 'co/autorites_indexint.html';
	    		break;	
	    		
//AUTORITES > SEMANTIQUE
	    	case 'semantique':
	    		switch ($sub) {
	    			case 'synonyms':  				
	    				print 'co/autorites_semantique_synonyms.html';
	    				break;
	    			case 'empty_words':
	    				print 'co/autorites_semantique_empty_words.html';
	    				break;
	    				
//AUTORITES > SEMANTIQUE > NON DOCUMENTE	    				
	    			default :
	    				print 'co/autorites_semantique.html';
	    				break;
	    		}
	    		break;
	    		
//AUTORITES > NON DOCUMENTE
	    	default :
	    		print 'co/autorites.html';
	    		break;    	
	    }
        break;
        
        
//EDITIONS > -----------------------------------------------------------------------------
	case 'edit.php' :
		switch ($categ) {
			
//EDITIONS > ETATS			
			case 'procs':
	    		print 'co/edit_procs.html';
				break;
				
//EDITIONS > PRETS				
			case 'expl':
				switch ($sub) {
					case 'encours':
			    		print 'co/edit_expl_encours.html';
						break;
					case 'retard':
			    		print 'co/edit_expl_retard.html';
						break;
					case 'retard_par_date':
			    		print 'co/edit_expl_retard_par_date.html';
						break;
 					case 'ppargroupe':
			    		print 'co/edit_expl_ppargroupe.html';
						break;
 					case 'rpargroupe':
			    		print 'co/edit_expl_rpargroupe.html';
						break;
//EDITIONS > PRETS > NON DOCUMENTE									
					default :
						print 'co/editions.html';
						break;
				}
				break;
				
//EDITIONS > RESERVATIONS				
			case 'notices':
				switch ($sub) {
					case 'resa':
			    		print 'co/edit_notices_resa.html';
						break;
					case 'resa_a_traiter':
			    		print 'co/edit_notices_resa_a_traiter.html';
						break;
//EDITIONS > RESERVATIONS > NON DOCUMENTE						
					default :
			    		print 'co/editions.html';
						break;
				}
				break;
				
//EDITIONS > LECTEURS				
			case 'empr':
				switch ($sub) {
					case 'encours':
			    		print 'co/edit_empr_encours.html';
						break;
					case 'limite':
			    		print 'co/edit_empr_limite.html';
						break;
					case 'depasse':
			    		print 'co/edit_empr_depasse.html';
						break;
//EDITIONS > LECTEURS > NON DOCUMENTE							
					default :
			    		print 'co/editions.html';
						break;
				}
				break;
				
//EDITIONS > PERIODIQUE				
			case 'serials':
	    		print 'co/edit_serials_collect.html';
				break;
				
//EDITIONS > CODES BARRES				
			case 'cbgen':
	    		print 'co/edit_cbgen_libre.html';
				break;
				
//EDITIONS > TEMPLATES
			case 'tpl':
	    		print 'co/edit_tpl_notice.html';
				break;

//EDITIONS > TRANSFERTS
 			case 'transferts' :
 				switch ($sub) {
 					case 'validation' :
 						print 'co/edit_transferts_validation.html';
	 				break; 
 					case 'envoi' :
 						print 'co/edit_transferts_envoi.html';
 					break;
 					case 'retours' :
 						print 'co/edit_transferts_retours.html';
 					break;
//EDITIONS > TRANSFERTS > NON DOCUMENTE 					 			}
 					default :
		    			print 'co/editions.html';
	 				break;	
	 			}
	 			break;

//EDITIONS > STATISTIQUES OPAC
 			case 'stat_opac' :
 				print 'co/edit_stat_opac.html';
 				break;
	 			
//EDITIONS > NON DOCUMENTE				
			default :
	    		print 'co/editions.html';
	    		break;
		}
		break;
	
        
//DSI > -----------------------------------------------------------------------------
    case 'dsi.php' :
	    switch ($categ) {
	    	
//DSI > DIFFUSION	    	
			case 'diffuser':
				switch ($sub) {
					case 'lancer':
						print 'co/dsi_diffuser_lancer.html';	
						break;
					case 'auto':
						print 'co/dsi_diffuser_auto.html';	
						break;
					case 'manu':
						print 'co/dsi_diffuser_manu.html';	
						break;
						
//DSI > DIFFUSION > NON DOCUMENTE	    						
					default:
						print 'co/dsi_diffusion.html';	
						break;
				}
				break;
				
//DSI > BANNETTES
			case 'bannettes':
					switch ($sub) {
						case 'pro':
							print 'co/dsi_bannettes_pro.html';	
							break;
						case 'abo':
							print 'co/dsi_bannettes_abo.html';
							break;
//DSI > BANNETTES > NON DOCUMENTE
						default :
							print 'co/dsi.html';
							break;
			}
			break;
			
//DSI > EQUATIONS			
			case 'equations':
				print 'co/dsi_equations.html';
				break;
				
//DSI > OPTIONS				
			case 'options':
				print 'co/dsi_options.html';
				break;

//DSI > FLUX RSS
			case 'fluxrss' :
				print 'co/dsi_fluxrss.html';
				break;
				
//DSI > NON DOCUMENTE
			default:
				print 'co/dsi.html';
	    		break;	
		}
        break;

        
//ACQUISITIONS > -----------------------------------------------------------------------------
    case 'acquisition.php' :	
	    switch ($categ) {
//ACQUISITIONS > ACHATS
	    	case 'ach':
				switch ($sub) {
					case 'cmde':
						print 'co/acquisitions_ach_cmde.html';
						break;
					case 'livr':
						print 'co/acquisitions_ach_livr.html';
						break;
					case 'fact' :
						print 'co/acquisitions_ach_fact.html';
						break;
					case 'fourn' :
						print 'co/acquisitions_ach_fourn.html';
						break;
					case 'devi' :
						print 'co/acquisitions_ach_devi.html';
						break;
					case 'bud' :
						print 'co/acquisitions_ach_bud.html';
						break;
//ACQUISITIONS > ACHATS > NON DOCUMENTE
					default:
						print 'co/acquisitions_achats.html';
						break;
					}
				break;
//ACQUISITIONS > SUGGESTIONS 
			case 'sug':
				switch ($sub) {
					case 'multi' :
						print 'co/acquisitions_sug_multi.html'; 
						break;
					case 'import' :
						print 'co/acquisitions_sug_import.html'; 
						break;
					case 'empr_sug' :
						print 'co/acquisitions_sug_empr_sug.html'; 
						break;
//ACQUISITIONS > SUGGESTIONS > NON DOCUMENTE	
					default :
						print 'co/acquisitions_suggestions.html';
						break;
				}
				break;
//ACQUISITIONS > NON DOCUMENTE
			default:
				print 'co/acquisitions.html';
	    		break;	    	
		}
        break;

        
//EXTENSIONS > -----------------------------------------------------------------------------
    case 'EXTENSIONS.php' :
		print 'co/extensions.html';
    	break;
	

//DEMANDES > -----------------------------------------------------------------------------
    case 'demandes.php' :
	
	    switch ($categ) {
	    	
//DEMANDES > LISTES
	    	case 'list' :
   				print 'co/demandes_list.html';
   				break;
	    		
//DEMANDES > ACTIONS
	    	case 'action' :
				print 'co/demandes_action.html';
   				break;
	    		
//DEMANDES > NON DOCUMENTE
	    	default :
				print 'co/demandes.html';
	    		break;
	    }
	    break;

//FICHES > -----------------------------------------------------------------------------
    case 'fichier.php' :
    
	    switch ($categ) {
	    	  	
//FICHES > CONSULTER
	    	case 'consult' :	
	    		switch ($mode) {

	    			case 'search' :	
	    				break;

	    			case 'multi' :	    				
	    				break;
	    		}
	    		break;
	    		
//FICHES > SAISIE
	    	case 'saisie' :
	    		
	    		break;
	    		
//FICHES > GERER
	    	case 'gerer' :
	    		switch($mode) {
	    			
	    			case 'champs' :
	    				break;
	    			
	    			case 'reindex' :
	    				break;
	    			
	    		}
	    		break;
	    	
	    	default :
	    		break;	
    } 	    
	    
	    
	    
//ADMINISTRATION > -----------------------------------------------------------------------------
    case 'admin.php' :
	
	    switch ($categ) {
//ADMINISTRATION > ADMINISTRATION	    

//ADMINISTRATION > ADMINISTRATION > EXEMPLAIRES	    	
			case 'docs':
				switch ($sub) {
					case 'typdoc':
			    		//print 'html-admin/ch04.html#admin_expl_typdoc';
			    		print 'co/admin_docs_typdoc.html';
						break;
					case 'location':
			    		print 'co/admin_docs_location.html';
						break;
					case 'section':
			    		print 'co/admin_docs_section.html';
						break;
					case 'statut':
			    		print 'co/admin_docs_statut.html';
						break;
					case 'codstat':
			    		print 'co/admin_docs_codstat.html';
						break;
					case 'lenders':
			    		print 'co/admin_docs_lenders.html';
						break;
					case 'perso':
			    		print 'co/admin_docs_perso.html';
						break;
					default :
			    		print 'co/administration_exemplaires';
						break;
				}
				break;
				
//ADMINISTRATION > ADMINISTRATION > NOTICES	    
			case 'notices':
				switch ($sub) {
					case 'orinot':
			    		print 'co/admin_notices_orinot.html';
						break;
					case 'statut':
			    		print 'co/admin_notices_statut.html';
						break;
					case 'perso':
			    		print 'co/admin_notices_perso.html';
						break;
					default:
			    		print 'co/administration_notices.html';
						break;
				}
				break;

//ADMINISTRATION > ADMINISTRATION > DOCUMENTS NUMERIQUES	
			case 'docnum':
				switch ($sub) {
					case 'rep' :
			    		print 'co/admin_docnum_rep.html';
			    		break;
					default :
			    		print 'co/administration_docnum.html';										
			    		break;
				}
				break;

//ADMINISTRATION > ADMINISTRATION > ETATS COLLECTIONS
			case 'collstate':
				switch ($sub) {
					case 'emplacement' :
			    		print 'co/admin_collstate_emplacement.html';
			    		break;
					case 'support' :
			    		print 'co/admin_collstate_support.html';
			    		break;
					case 'statut' :
			    		print 'co/admin_collstate_statut.html';
			    		break;
					case 'perso' :
			    		print 'co/admin_collstate_perso.html';
			    		break;
					default :
			    		print 'co/administration_etats_collections.html';										
			    		break;
				}
				break;

//ADMINISTRATION > ADMINISTRATION > ABONNEMENTS
			case 'abonnements':
				switch ($sub) {
					case 'periodicite' :
			    		print 'co/admin_abonnements_periodicite.html';					
						break;
				default :
						print 'co/administration_abonnements.html';					
						break;
				}
				break;
				
//ADMINISTRATION > ADMINISTRATION > LECTEURS
			case 'empr':
				switch ($sub) {
					case 'categ':
			    		print 'co/admin_empr_categ.html';
						break;					
					case 'statut':
			    		print 'co/admin_empr_statut.html';
						break;
					case 'codstat':
			    		print 'co/admin_empr_codstat.html';
						break;
					case 'implec':
			    		print 'co/admin_empr_implec.html';
						break;
					case 'parperso':
			    		print 'co/admin_empr_parperso.html';
						break;
					default :
			    		print 'co/administration_lecteurs.html';
						break;
				}
				break;
				
//ADMINISTRATION > ADMINISTRATION > UTILISATEURS				
			case 'users':
				switch($sub) {
					case 'users':
	    				print 'co/admin_users_users.html';
	    				break;
					case 'groups':
						print 'co/admin_users_groups.html';
						break;
					default :
						print 'co/administration_utilisateurs.html';
						break;
				}
	    		break;

	    		
//ADMINISTRATION > OPAC 
	    		
//ADMINISTRATION > OPAC > INFOPAGES
			case 'infopages':
				print 'co/administration_infopages.html';
				break;
//ADMINISTRATION > OPAC > RECHERCHES PREDEFINIES
			case 'opac':
				switch($sub) {
					case 'search_persopac':
						print 'co/admin_opac_search_persopac.html';
						break;
//ADMINISTRATION > OPAC > NAVIGATION						
					case 'navigopac':
						print 'co/administration_navigation.html';
						break;
//ADMINISTRATION > OPAC > STATISTIQUES						
					case 'stat':
						print 'co/administration_statistiques.html';
						break;
					default :
						print 'co/nodoc.html';
						break;
				}
				break;
//ADMINISTRATION > OPAC > VISIONNEUSE
			case 'visionneuse':
				switch($sub) {
					case 'class':
						print 'co/admin_visionneuse_class.html';
						break;
					case 'mimetype':
						print 'co/admin_visionneuse_mimetype.html';
						break;
					default :
						print 'co/administration_visionneuse.html';
						break;
				}
				break;
					
//ADMINISTRATION > ACTIONS > PERSONNALISABLES
			case 'proc':
				switch ($sub) {
					case 'proc':
					default:
						print 'co/administration_actions_personnalisables.html';
						break;
					case 'clas':
						print 'co/administration_classements.html';
						break;
				}
				break;
				
//ADMINISTRATION > MODULES > QUOTAS 	    	
	    	case 'quotas':
	    			print 'co/administration_quotas.html';
	    		break;
	    		
//ADMINISTRATION > MODULES > CALENDRIER				
			case 'calendrier':
	    		print 'co/admin_calendrier.html';
	    		break;
	    		
//ADMINISTRATION > MODULES > GESTION FINANCIERE				
			case 'finance':
				switch ($sub) {
					case 'abts':
						print 'co/admin_finance_abts.html';
						break;
					case 'prets':
						print 'co/admin_finance_prets.html';
						break;
					case 'amendes':
						print 'co/admin_finance_amendes.html';
						break;
					case 'amendes_relance':
						print 'co/admin_finance_amendes_relance.html';
						break;
					case 'blocage':
						print 'co/admin_finance_blocage.html';
						break;
					default:
	    				print 'co/administration_gestion_financiere.html';
	    				break;
				}
				break;
				
//ADMINISTRATION > MODULES > IMPORTS				
			case 'import':
				switch ($sub) {
					case 'import':
			    		print 'co/admin_import_import.html';
						break;
					case 'import_expl':
			    		print 'co/admin_import_import_expl.html';
						break;
					case 'pointage_expl':
			    		print 'co/admin_import_pointage_expl.html';
						break;
					default:
			    		print 'co/administration_imports.html';
						break;
				}
				break;
				
//ADMINISTRATION > MODULES > CONVERSIONS/EXPORT		
			case 'convert':
				switch ($sub) {
					case 'import':
			    		print 'co/admin_convert_import.html';
						break;
					case 'export':
			    		print 'co/admin_convert_export.html';
						break;
					case 'paramgestion':
			    		print 'co/admin_convert_paramgestion.html';
						break;
					case 'paramopac':
			    		print 'co/admin_convert_paramopac.html';
						break;	
						default:
			    		print 'co/administration_conversions_exports.html';
						break;
				}
				break;

//ADMINISTRATION > MODULES > OUTILS				

//ADMINISTRATION > MODULES > OUTILS > NETTOYAGE DE BASE
			case 'netbase':
	    		print 'co/admin_netbase.html';
				break;

//ADMINISTRATION > MODULES > OUTILS > VERIFICATION DES LIENS
			case 'chklnk':
	    		print 'co/admin_chklnk.html';			
				break;
 				
//ADMINISTRATION > MODULES > OUTILS > MAJ BASE				
			case 'alter':
	    		print 'co/admin_alter.html';
				break;

//ADMINISTRATION > MODULES > OUTILS					
			case 'misc':
				switch ($sub) {
					case 'tables':
			    		print 'co/admin_misc_tables.html';
						break;
					case 'mysql':
			    		print 'co/admin_misc_mysql.html';
						break;
					default:
	    				print 'co/administration_outils.html';
	    				break;
				}
				break;
			case 'param':
	    		print 'co/admin_param.html';
				break;
				
//ADMINISTRATION > MODULES > Z3950				
			case 'z3950':
				switch ($sub) {
					case 'zbib':
					case 'zattr':
			    		print 'co/admin_z3950_zbib.html';
						break;
					default:
			    		print 'co/administration_z3950.html';
						break;
				}
				break;

//ADMINISTRATION > MODULES > SERVICES EXTERNES
				case 'external_services':
					switch($sub) {
						case 'general':
							print 'co/admin_external_services_general.html';
							break;
						case 'peruser':
							print 'co/admin_external_services_peruser.html';
							break;
						case 'esusers':
							print 'co/admin_external_services_esusers.html';
							break;
						case 'esusergroups':
							print 'co/admin_external_services_esusergroups.html';
							break;
						default:
							print 'co/administration_services_externes.html';
							break;
					}
				break;

//ADMINISTRATION > MODULES > CONNECTEURS
			case 'connecteurs':
				switch ($sub) {
					case 'in':
						print 'co/admin_connecteurs_in.html';
						break;
					case 'categ':
						print 'co/admin_connecteurs_categ.html';
						break;
					case 'out':
						print 'co/admin_connecteurs_out.html';
						break;
					case 'out_auth':
						print 'co/admin_connecteurs_out_auth.html';
						break;
					case 'out_sets':
						print 'co/admin_connecteurs_out_sets.html';
						break;
					case 'categout_sets':
						print 'co/admin_connecteurs_categout_sets.html';
						break;
					default:
						print 'co/administration_connecteurs.html';
						break;
				}
				break;
				
//ADMINISTRATION > MODULES > SAUVEGARDE 			
			case 'sauvegarde':
				switch ($sub) {
					case 'lieux':
			    		print 'co/admin_sauvegarde_lieux.html';
						break;
					case 'tables':
			    		print 'co/admin_sauvegarde_tables.html';
						break;
					case 'gestsauv':
			    		print 'co/admin_sauvegarde_gestsauv.html';
						break;
					case 'launch':
			    		print 'co/admin_sauvegarde_launch.html';
						break;
					case 'list':
			    		print 'co/admin_sauvegarde_list.html';
						break;
					default:
			    		print 'co/administration_sauvegarde.html';
						break;
				}
				break;
				
//ADMINISTRATION > MODULES > ACQUISITIONS 					
			case 'acquisition':
				switch ($sub) {
					case 'entite':
			    		print 'co/admin_acquisition_entite.html';
						break;
					case 'compta':
			    		print 'co/admin_acquisition_compta.html';
						break;
					case 'tva':
						print 'co/admin_acquisition_tva.html';
						break;	
					case 'type':
			    		print 'co/admin_acquisition_type.html';
						break;
					case 'frais':
			    		print 'co/admin_acquisition_frais.html';
						break;
					case 'mode':
			    		print 'co/admin_acquisition_mode.html';
						break;
					case 'budget':
			    		print 'co/admin_acquisition_budget.html';
						break;
					case 'categ':
			    		print 'co/admin_acquisition_categ.html';
						break;
 					case 'src':
			    		print 'co/admin_acquisition_src.html';
 						break; 
					default:
			    		print 'co/administration_acquisitions.html';
						break;
				}
				break;
				
//ADMINISTRATION > MODULES > TRANSFERTS
			case 'transferts':
				switch ($sub) {
					case 'general':
			    		print 'co/admin_transferts_general.html';
			    		break;
					case 'circ':
			    		print 'co/admin_transferts_circ.html';
						break;
					case 'opac':
			    		print 'co/admin_transferts_opac.html';					
						break;
					case 'ordreloc':
			    		print 'co/admin_transferts_ordreloc.html';					
						break;
					case 'statutsdef':
			    		print 'co/admin_transferts_statutsdef.html';										
						break;
					case 'purge':
			    		print 'co/admin_transferts_purge.html';										
						break;
					default:
			    		print 'co/administration_transferts.html';
						break;
				}
			break;

//ADMINISTRATION > MODULES > DROITS D'ACCES
			case 'acces':
				switch ($sub) {
					case 'domain':
						switch ($id) {
							case '1':
								print 'co/admin_acces_domain_user_notice.html';
								break;
							case '2':
								print 'co/admin_acces_domain_empr_notice.html';
								break;
							default:
								print 'co/administration_droits_d_acces.html';
								break;
						}
						break;
					case 'user_prf':
						switch ($id) {
							case '1':
								print 'co/admin_user_prf_user_notice.html';
								break;
							case '2':
								print 'co/admin_user_prf_empr_notice.html';
								break;
							default:
								print 'co/administration_droits_d_acces.html';
								break;
						}
						break;
					case 'res_prf':
						switch ($id) {
							case '1':
								print 'co/admin_res_prf_user_notice.html';
								break;
							case '2':
								print 'co/admin_res_prf_empr_notice.html';
								break;
							default:
								print 'co/administration_droits_d_acces.html';
								break;
						}
						break;
				default:
					print 'co/administration_droits_d_acces.html';
					break;
				}
			break;
 				
//ADMINISTRATION > MODULES > EDITEUR HTML
 			case 'html_editor':
    		print 'co/admin_html_editor.html';
 			break;

//ADMINISTRATION > MODULES > DEMANDES		
			case 'demandes':
				switch ($sub) {
					case 'theme':
						print 'co/admin_demandes_theme.html';
						break;
					case 'type':
						print 'co/admin_demandes_type.html';
						break;
					default:
						print 'co/administration_demandes.html';
						break;
				}
			break;

//ADMINISTRATION > MODULES > NON DOCUMENTE				
			default:
				print 'co/guide_complet_web_section_administration.html';
				break;
		}
        break;

//NON DOCUMENTE        
    default:
        print 'co/nodoc.html';
        break;
        }
?>
