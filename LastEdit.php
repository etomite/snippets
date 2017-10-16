//<?php

// SiteUpdate - Displays the date of the last document update
// Could be modified to only parse published documents

$tbl = $etomite->dbConfig['dbase'].".".$etomite->dbConfig['table_prefix'];
$sql = "SELECT editedon FROM ".$tbl."site_content ORDER BY editedon DESC";

$rs = $etomite->dbQuery($sql);
$limit = $etomite->recordCount($rs);

if ($limit >= 1) {
    $row = $etomite->fetchRow($rs);
    $output= strftime("%A %e %B %Y at %I:%M%p", $row['editedon']);
} else {
    $output="Never Updated";
}

return $output;
