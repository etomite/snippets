//<?php

//  --------------------------------------------
//     Snippet:  MenuHori by summean
//  --------------------------------------------
//  Based on the MenuBuilder snippet, however 
//  MenuHori does not show subchildren.  Inserts
//  into a template just like MenuBuilder:
//  [[MenuHori?id=__]] where __ is the folder 
//  containing the links/documents.

// --Config:--
// Insert what you would like to appear between
// the links in the " " below.

$seperator="";

// --End Config--

$children = $etomite->getActiveChildren($id);
$menu = "";
$childrenCount = count($children);
if ($children==false) {
    return false;
}

for ($x=0; $x<$childrenCount; $x++) {
//If its the last link/document, we don't want the seperator after it. 

    if ($x==($childrenCount-1)) {
        $menu .= "<a href='[~".$children[$x]['id']."~]'>".$children[$x]['pagetitle']."</a>";
    } else {
        $menu .= "<a href='[~".$children[$x]['id']."~]'>".$children[$x]['pagetitle']."</a><br />";
    }
}

return $menu;
