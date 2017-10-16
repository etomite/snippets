//<?php

// by: Jeroen Bosch
// date: 10 feb 2005
// snippet: HomeMadeMenu
// use: [[HomeMadeMenu?id=]]
// use the id to set the Root folder.

// Config
// You can specify a root-folder. If you don't the default siteRoot wil be used

 $siteRoot = 0;

# If Etomite Folder ID is not sent, use ID siteRoot
if (!isset($id)) {
    $id = $siteRoot;
}
    
$output = "";
    
 $fields='id,pagetitle';
 $children = $etomite->getActiveChildren($id, 'menuindex', 'ASC', $fields);
 
foreach ($children as $child) {
    $output .= "<b>".$child['pagetitle']."</b></br>"; // print folder title
  
    $children2 = $etomite->getActiveChildren($child['id'], 'menuindex', 'ASC', $fields); // Get those kids!!

    foreach ($children2 as $child2) {
        $output .= '<li><a href="[~'.$child2['id'].'~]">'.$child2['pagetitle'].'</a></li>'; //  print name in menu
    }
}

return $output;
