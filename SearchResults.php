//<?php

//  SearchResults
//  Displays results of SearchPrompt snippet

$noResults = "<p>No search results were found.</p>";
$resultsText = "<p>The following results were found:</p>";
$searchString =
isset($_POST['search']) &&
$_POST['search']!= "{{" &&
$_POST['search']!= "[[" &&
$_POST['search']!= "[(" &&
$_POST['search']!= "[~" &&
$_POST['search']!= "[*" ?
$_POST['search'] : "" ;

if (isset($_POST['search']) && $_POST['search']!='') {
    $search = explode(" ", $_POST['search']);
    $sql = "SELECT id, pagetitle, parent, description FROM ".$etomite->db.site_content." WHERE (content LIKE '%".$search[0]."%'";
    for ($x=1; $x < count($search); $x++) {
        $sql .= " AND content like '%$search[$x]%'";
    }
    $sql .= " OR pagetitle LIKE '%".$search[0]."%' ";
    for ($x=1; $x < count($search); $x++) {
        $sql .= " AND pagetitle like '%$search[$x]%'";
    }
    $sql .= " OR description LIKE '%".$search[0]."%' ";
    for ($x=1; $x < count($search); $x++) {
        $sql .= " AND description like '%$search[$x]%'";
    }
    $sql .= ") AND published = 1 AND searchable=1 AND deleted=0;";
    $rs = $etomite->dbQuery($sql);
    $limit = $etomite->recordCount($rs);
    if ($limit>0) {
        $SearchForm .= $resultsText."<p><table cellspacing='0' cellpadding='0'>";
        for ($y = 0; $y < $limit; $y++) {
            $SearchFormsrc=$etomite->fetchRow($rs);
            $SearchForm.="<tr><td style='padding: 1px'><a href='[~".$SearchFormsrc['id']."~]'><b>".$SearchFormsrc['pagetitle']."</b></a></td><td style='padding: 1px'>";
            $SearchForm.=$SearchFormsrc['description']!='' ? " - <small>".$SearchFormsrc['description']."</small>" : "" ;
            $SearchForm .= "</td></tr>";
        }
        $SearchForm .= "</table>";
    } else {
        $SearchForm .= $noResults;
    }
}

return $SearchForm;
