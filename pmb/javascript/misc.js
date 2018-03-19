// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc.js,v 1.6.4.4 2017-03-17 08:32:43 jpermanne Exp $


// Fonction check_checkbox : Permet de changer l'�tats d'une liste de checkbox.
// checkbox_list : Liste d'id des checkbox s�par�e par |
// level: 1 (checked) ou 0;
function check_checkbox(checkbox_list,level) {
	var ids,id,state;
	if(level) state=true; else state=false;
	ids=checkbox_list.split('|');
	while (ids.length>0) {
		id=ids.shift();
		document.getElementById(id).checked = state;
	}
}


/* -------------------------------------------------------------------------------------
 *		D�roulement du menu vertical sur clic, enregistrement
 *		des pr�f�rences sur ctrl+clic avec ajax
 *
 *		menuHide - setMenu - menuSelectH3 - setMenuHide - menuAutoHide
 * ----------------------------------------------------------------------------------- */

/* -----------------------------------------------------------------------------------
 * Fonction menuHide
 * gestionnaire g�n�ral pour masquer le menu, declenche sur onclick du <span>
 */
// si l'utilisateur n'enregistre pas de pr�f�rences,  on r�tracte/d�plie le menu.
function menuHide(obj,event){
	var ctrl = event.ctrlKey || event.metaKey;
	if (ctrl){setMenu(event);}
	else {menuHideObject(obj);}
}

/* -----------------------------------------------------------------------------------
 * Fonction setMenu
 * sauve-restaure les preferences sur le d�roulement par d�faut du menu selectionne
 */
// Variables globales
var hlist=new Array();
var hclasses=new Array();

function setMenu(){
	var menu = document.getElementById("menu");
	var childs = menu.childNodes;
	var parseH3=0;
	
	//on rel�ve l'etat du menu
	var values="";
	var j=1;
	for(i=0; i<childs.length; i++){
		if(childs[i].tagName=='H3'){
			hlist[j]=childs[i];
			hclasses[j]=hlist[j].className;
			parseH3=1;
			j++;
		} else if (childs[i].tagName=='UL' && parseH3==1){
			if(childs[i].style.display=='none'){values+='f,';}
			else{values+='t,';}
			parseH3=0;
		}
	}
	//requete ajax pour sauvegarder l'etat
	savehide = new http_request();
	var url= "./ajax.php?module=ajax&categ=menuhide&fname=setpref";
	url=encodeURI(url) 
	var page = document.getElementById("body_current_module").getAttribute('page_name');
	page=encodeURI(page)
	values=encodeURI(values)
	savehide.request(url,1,"&page="+page+"&values="+values);
	if(savehide.get_text()!=0){
		alert(savehide.get_text());
	} else {
		for(i=1; i<hlist.length; i++){
			setTimeout("hlist["+i+"].className=\"setpref\"",i*15);
			setTimeout("hlist["+i+"].className=hclasses["+i+"]",i*15+500);
		}
	}
}

/* -------------------------------------------------------------------------------------
 * Fonction menuHideObject
 * Masque ou affiche le menu sous le H3 s�lectionn�
 */
function menuHideObject(obj,force) {
	var pointer=obj;
	do{
		pointer=pointer.nextSibling;
		if (pointer.tagName=='H3' || pointer.tagName=='DIV'){
			break;
		}
		if (pointer.tagName=='UL'){
			if (force==undefined){
				if (pointer.style.display=='none'){
					pointer.style.display='block';
					menuSelectH3(pointer,"");
				}
				else {
					pointer.style.display='none';
					menuSelectH3(pointer,"selected");
				}
			} else {
				if (force==0){
					pointer.style.display='block';
					menuSelectH3(pointer,"");
				}
				else {
					pointer.style.display='none';
					menuSelectH3(pointer,"selected");
				}
			}
		}
	}while(pointer.nextSibling);
}
/* -------------------------------------------------------------------------------------
 * Fonction menuSelectH3()
 * Attribue au menuH3 selectionne une nouvelle classe css (a priori purement esthetique)
 */
function menuSelectH3(ulChild,selectState){
	prec=ulChild.previousSibling;
	if(navigator.appName != "Microsoft Internet Explorer"){
		prec=prec.previousSibling;
	}
	if(prec.tagName=='H3'){
		prec.className=selectState;
	}
}

/* --------------------------------------------------------------------------------------
 * Fonction menuGlobalHide
 * Force le depliement d'une liste de menus, masque tous les autres.
 */
function menuGlobalHide(boollist){
	var boollist=boollist.split(",");	
	var menu = document.getElementById("menu");
	var fils = menu.childNodes;
	var j=0;
	for(i=0; i<fils.length; i++){
		if(fils[i].tagName=='H3'){
			if(boollist[j]=='t'){
				menuHideObject(fils[i],0);
			} else {
				menuHideObject(fils[i],1);
			}
			j++;
		}
	}
}

/* --------------------------------------------------------------------------------------
 * Fonction menuAutoHide
 * Recuppere les preferences d'affichage de l'user, si != 0 elles sont d�finies
 * et on deplie/replie les menus avec l'appel � menuGlobalHide
 */
function menuAutoHide(){
	if (!trueids) {
		var getHide = new http_request();
		var url = "./ajax.php?module=ajax&categ=menuhide&fname=getpref";
		url=encodeURI(url)
		var page = document.getElementById("body_current_module").getAttribute('page_name');
		page=encodeURI(page)
		getHide.request(url,1,"&page="+page);	
		if(getHide.get_text()!=0){
			menuGlobalHide(getHide.get_text());	
		}
	} else if (trueids!="0") menuGlobalHide(trueids);	
}

/* --------------------------------------------------------------------------------------
 * Fonction addLoadEvent
 * Empile les diff�rentes fonctions � appeler quand la page est charg�e
 */
function addLoadEvent(func) {
  if (window.addEventListener)
    window.addEventListener("load", func, false);
  else if (window.attachEvent)
    window.attachEvent("onload", func);
  else { // fallback
    var old = window.onload;
    window.onload = function() {
      if (old) old();
      func();
    };
  }
}

var pmbForm = {
    fieldToObject: function fieldToObject(inputNode){

        var ret = null;
        if(inputNode){
            var _in = inputNode.name, type = (inputNode.type || "").toLowerCase();
            if(_in && type && !inputNode.disabled){
            	if(type == "textarea" && inputNode.id !="" && inputNode.value == ""){ //Test tinymce
            		if(typeof tinyMCE != 'undefined' && tinyMCE.get(inputNode.id)){
            			return tinyMCE.get(inputNode.id).getContent();
            		}
            	}
                if(type == "radio" || type == "checkbox"){
                    if(inputNode.checked){
                        ret = inputNode.value;
                    }
                }else if(inputNode.multiple){
                    ret = [];
                    var nodes = [inputNode.firstChild];
                    while(nodes.length){
                        for(var node = nodes.pop(); node; node = node.nextSibling){
                            if(node.nodeType == 1 && node.tagName.toLowerCase() == "option"){
                                if(node.selected){
                                    ret.push(node.value);
                                }
                            }else{
                                if(node.nextSibling){
                                    nodes.push(node.nextSibling);
                                }
                                if(node.firstChild){
                                    nodes.push(node.firstChild);
                                }
                                break;
                            }
                        }
                    }
                }else{
                    ret = inputNode.value;
                }
            }
        }
        
        if(!ret && pmbForm.include.indexOf(type)!= -1){
        	var form = inputNode.form;
        	var widgetNode = form.querySelector('[widgetid="'+inputNode.name+'"]');
        	if(widgetNode){
        		var widget = dijit.byId(widgetNode.getAttribute('widgetid'));
        	} else {
        		var widgetNode2 = form.querySelector('[widgetid="'+inputNode.name+'_form"]');
            	if(widgetNode2){
            		var widget = dijit.byId(widgetNode2.getAttribute('widgetid'));
            	}
        	}
        	if (widget) {
        		return widget.get('value') ? widget.get('value') : '';
        	}
        }
        return ret;
    },
    setValue: function(obj, name, value){
    	if(value === null){
    		return;
    	}
    	var val = obj[name];
    	if(typeof val == "string"){
    		obj[name] = [val, value];
    	}else if(Array.isArray(val)){
    		val.push(value);
    	}else{
    		obj[name] = value;
    	}
	},
	exclude: ["file", "submit", "image", "reset", "button"],
	include: ['text', 'hidden', 'textarea'],
    toObject: function formToObject(formNode){
        var ret = {}, elems = document.getElementById(formNode).elements;
        for(var i = 0, l = elems.length; i < l; ++i){
            var item = elems[i], _in = item.name, type = (item.type || "").toLowerCase();
            if(_in && type && pmbForm.exclude.indexOf(type) < 0 && !item.disabled){
                pmbForm.setValue(ret, _in, pmbForm.fieldToObject(item));
                if(type == "image"){
                    ret[_in + ".x"] = ret[_in + ".y"] = ret[_in].x = ret[_in].y = 0;
                }
            }
        }
        return ret; 
    },

    toQuery: function formToQuery(formNode){
        return ioq.objectToQuery(pmbForm.toObject(formNode));
    },

    toJson: function formToJson(formNode,prettyPrint){

        return JSON.stringify(pmbForm.toObject(formNode), null, prettyPrint ? 4 : 0);
    }
};