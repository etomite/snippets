//<?php

$resourceparent = isset($newsid) ? $newsid : $etomite->documentIdentifier;
         // the folder that contains blog entries
$output = '';
         // initialise the blog variable
$nrblogs = 3;
         // nr of blogs to show a short portion of
$nrblogstotal = 100;
         // total nr of blogs to retrieve
$lentoshow = 80;
         // how many characters to show of blogs

$resource = $etomite->getActiveChildren($resourceparent, 'createdon', 'DESC', $fields = 'id, pagetitle, description, content, createdon, createdby');
$limit=count($resource);
if ($limit<1) {
    $output .= "No News.<br />";
}
$nrblogs = $nrblogs<$limit ? $nrblogs : $limit;
if ($limit>0) {
    for ($x = 0; $x < $nrblogs; $x++) {
        $tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."manager_users";
        $sql = "SELECT username FROM $tbl WHERE $tbl.id = ".$resource[$x]['createdby'];
        $rs2 = $etomite->dbQuery($sql);
        $limit2 = $etomite->recordCount($rs2);
        if ($limit2<1) {
            $username .= "anonymous";
        } else {
            $resourceuser = $etomite->fetchRow($rs2);
            $username = $resourceuser['username'];
            // strip the content
            if (strlen($resource[$x]['content'])>$lentoshow) {
                $rest = substr($resource[$x]['content'], 0, $lentoshow);
                $rest .= "...<br />&nbsp;&nbsp;&nbsp;&nbsp;<a href='[~".$resource[$x]['id']."~]'>weiterlesen ></a>";
            } else {
                $rest = $resource[$x]['content'];
            }
            $output .= "<b>".strftime("%d.%m.%y", $resource[$x]['createdon'])."</b><br />".$rest."<br /><br />";
        }
    }
}

if ($limit>$nrblogs) {
    $output .= "<br /><b>Oldest News</b><br />";
    for ($x = $nrblogs; $x < $limit; $x++) {
        $output .= "> <a href='[~".$resource[$x]['id']."~]'>".$resource[$x]['pagetitle']."</a><br />";
    }
}

return $output;
