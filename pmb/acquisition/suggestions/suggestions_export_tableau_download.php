<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions_export_tableau_download.php,v 1.1.2.3 2017-06-02 13:20:21 jpermanne Exp $

$base_path="../..";
$base_auth = "ACQUISITION_AUTH";
$base_title = "";
$base_noheader=1;
$base_nosession=1;
require_once ($base_path."/includes/init.inc.php");

header("Content-Type: application/x-msexcel; name=\""."suggestions.xlsx"."\"");
header("Content-Disposition: inline; filename=\""."suggestions.xlsx"."\"");
$fh=fopen($fname, "rb");
fpassthru($fh);
unlink($fname);
print "<script type='text/javascript'>location.href='acquisition.php?categ=sug';</script>";