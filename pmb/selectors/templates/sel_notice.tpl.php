<?php
// +-------------------------------------------------+
// | PMB                                                                      |
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_notice.tpl.php,v 1.11.4.1 2016-09-22 07:55:55 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// templates du sélecteur notices

//--------------------------------------------
//	$nb_per_page : nombre de lignes par page
//--------------------------------------------
// nombre de références par pages
if ($nb_per_page_a_select != "") 
	$nb_per_page = $nb_per_page_a_select ;
	else $nb_per_page = 10;

//-------------------------------------------
//	$sel_header : header
//-------------------------------------------
$sel_header = "
<div class='row'>
	<label for='titre_select_notice' class='etiquette'>$msg[selector_notice]</label>
	</div>
<div class='row'>
";

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------
$jscript = "
<script type='text/javascript'>
<!--
function set_parent(f_caller, id_value, libelle_value, callback)
{
	window.opener.document.forms[f_caller].elements['$param1'].value = id_value;
	window.opener.document.forms[f_caller].elements['$param2'].value = reverse_html_entities(libelle_value);
	if(callback)
		window.opener[callback]('$infield');
	window.close();
}
function copier_modele(location)
{
	window.opener.location.href = location;
	window.close();
}
-->
</script>
";

//-------------------------------------------
//	$sel_search_form : module de recherche
//-------------------------------------------
$sel_search_form ="
<form name='search_form' method='post' action='$base_url'>
<input type='text' name='f_user_input' value=\"!!deb_rech!!\">
	<select id='typdoc-query' name='typdoc_query'>
		!!typdocfield!!
	</select>";
if ($pmb_show_notice_id) {
	$sel_search_form .="<br>".$msg['notice_id_libelle']." <input type='text' name='id_restrict' value=\"!!id_restrict!!\" class='saisie-5em'>";
} else {
	$sel_search_form .="<input type='hidden' name='id_restrict' value=''>";
}
$sel_search_form .="&nbsp;
<input type='submit' class='bouton_small' value='".$msg['142']."' />
</form>
<script type='text/javascript'>
<!--
	document.forms['search_form'].elements['f_user_input'].focus();
-->
</script>
<hr />
";

//-------------------------------------------
//	$sel_footer : footer
//-------------------------------------------
$sel_footer = "
</div>
";
