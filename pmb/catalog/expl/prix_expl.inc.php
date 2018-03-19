<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: prix_expl.inc.php,v 1.1.2.1 2016-11-01 19:43:44 Alexandre Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if ($pmb_prefill_prix) {
	function prefill_prix ($id_notice=0,$prix='') {
		global $dbh;
		if (!$prix) {
			$requete = "SELECT prix FROM notices WHERE notice_id='$id_notice' ";
			$result = @pmb_mysql_query($requete, $dbh);
			$res_prix = pmb_mysql_fetch_object($result);
			return $res_prix->prix;
		} else {
			return $prix;
		}
	}
} else {
	function prefill_prix ($id_notice=0,$prix='') {
		return $prix;
	}
}
