<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: delete_empr_passwords.inc.php,v 1.1.4.2 2017-05-23 13:03:03 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$v_state=urldecode($v_state);
// taille de la jauge pour affichage
$jauge_size = GAUGE_SIZE;

print "<br /><br /><h2 align='center'>".htmlentities($msg["deleting_empr_passwords"], ENT_QUOTES, $charset)."</h2>";

$v_state .= "<br /><img src=../../images/d.gif hspace=3>".htmlentities($msg["deleting_empr_passwords"], ENT_QUOTES, $charset)." : ";
$query = "show tables like 'empr_passwords'";
if (pmb_mysql_num_rows(mysql_query($query,$dbh))) {
	$query = "DROP TABLE empr_passwords";
	pmb_mysql_query($query);
}
$v_state.= "OK";

$spec = $spec - DELETE_EMPR_PASSWORDS;

// mise à jour de l'affichage de la jauge
print "<table border='0' align='center' width='$table_size' cellpadding='0'><tr><td class='jauge'>
  			<img src='../../images/jauge.png' width='$jauge_size' height='16'></td></tr></table>
 			<div align='center'>100%</div>";
print "
	<form class='form-$current_module' name='process_state' action='./clean.php' method='post'>
		<input type='hidden' name='v_state' value=\"".urlencode($v_state)."\">
		<input type='hidden' name='spec' value=\"$spec\">
		<input type='hidden' name='pass2' value=\"2\">	
	</form>
	<script type=\"text/javascript\"><!--
		document.forms['process_state'].submit();
		-->
	</script>";