<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: 

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$tpl_vue_facettes=
"
<hr/>
<h3>".htmlentities($msg['title_tab_facette'],ENT_QUOTES,$charset)."</h3>
<div class='row'>
	<table>
		<tr>
			<th>".htmlentities($msg['facette_order'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['intitule_vue_facette'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['critP_vue_facette'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['ssCrit_vue_facette'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['nbRslt_vue_facette'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['sort_view_facette'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['visible_facette'],ENT_QUOTES,$charset)."</th>
		</tr>
		!!lst_facette!!
	</table>
	<div class='row'>
		<input class='bouton' type='button' value='".htmlentities($msg['lib_nelle_facette_form'],ENT_QUOTES,$charset)."' onClick=\"document.location='./admin.php?categ=opac&sub=facette_search_opac&section=facette&action=edit&id=0'\"/>	
		<input class='bouton' type='button' value='".htmlentities($msg['facette_order_bt'],ENT_QUOTES,$charset)."' onClick=\"document.location='./admin.php?categ=opac&sub=facette_search_opac&section=facette&action=order'\"/>
	</div>
</div>
";