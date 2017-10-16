//<?php

$id = isset($id) ? $id : $etomite->documentIdentifier;
$sortby = "menuindex";
$sortdir = "ASC";
$fields = "id, pagetitle, description, parent, alias";

$indentString="";

if (!isset($indent)) {
    $indent = "";
    $indentString .= "";
} else {
    for ($in=0; $in<$indent; $in++) {
        $indentString .= "";
    }
    $indentString .= "";
}

$children = $etomite->getActiveChildren($id, $sortby, $sortdir, $fields);
$menu = "";
$childrenCount = count($children);
$active="";

if ($children==false) {
    return false;
}
for ($x=0; $x<$childrenCount; $x++) {
    if ($children[$x]['id']==$etomite->documentIdentifier) {
        $active="class='sidelink'";
    } else {
        $active="";
    }
    if ($children[$x]['id']==$etomite->documentIdentifier || $children[$x]['id']==$etomite->documentObject['parent']) {
        $menu .= "<a class='sidelink' href='[~".$children[$x]['id']."~]'>$indentString".$children[$x]['pagetitle']."</a><span class='hide'> | </span>";
    } else {
        $menu .= "<a class='sidelink' href='[~".$children[$x]['id']."~]'>$indentString".$children[$x]['pagetitle']."</a><span class='hide'> | </span>";
    }
}

$menu .= "";
return $menu."";
