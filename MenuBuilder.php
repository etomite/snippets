//<?php

// default snippet amended to NOT show section pages when a section in the menu is clicked

if (!isset($id)) {
    $id = $etomite->documentIdentifier; //current document
}

$indentString="";

if (!isset($indent)) {
    $indent = "";
    $indentString .= "";
} else {
    for ($in=0; $in<$indent; $in++) {
        $indentString .= "&nbsp;";
    }
    $indentString .= "&raquo;&nbsp;";
}

$children=$etomite->getActiveChildren($id);
$menu = "";
$childrenCount = count($children);
$active="";
if ($children==false) {
    return false;
}
for ($x=0; $x<$childrenCount; $x++) {
    if ($children[$x]['id']==$etomite->documentIdentifier) {
        $active="class='highLight'";
    } else {
        $active="";
    }
    if ($children[$x]['id']==$etomite->documentIdentifier || $children[$x]['id']==$etomite->documentObject['parent']) {
        $menu .= "<a ".$active." href='[~".$children[$x]['id']."~]'>$indentString".$children[$x]['pagetitle']."</a>";
//[[MenuBuilder?id=".$children[$x]['id']."&indent=2]]";	
    } else {
        $menu .= "<a href='[~".$children[$x]['id']."~]'>$indentString".$children[$x]['pagetitle']."</a>";
    }
}
return $menu."";
