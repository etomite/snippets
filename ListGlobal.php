//<?php

// ------------------------
// Snippet: ListGlobal
// ------------------------
// Version 1.0
// Date: 2008-03-28
// By Ralph A Dahlgren
// Added LGlob_isfolder indicator class support
//
// Version: 0.6e
// Date: 2005.07.01
// jaredc@honeydewdesign.com
//
// Snippet for listing global list of links or sections
// (Published pages who's parent is root). Highlight ability
// for current section.

// $node [ int ]
// Id of what you want to consider the "root" of the site or section.
// For instance, if you branch off several sections from the 0 root level
// based on languages, you might want to set the language home page as
// the root. Otherwise it will default to 0 which is the site root. It
// will also default to 0 when 0 is the literal root. Settable in snippet
// call with $LGlob_node:
// [[ListGlobal?LGlob_node=7]]
   $node = 0;

// STYLES
// .LGlob_list     ul element of list
// .LGlob_active   li of section you are in
// .LGlob_isfolder displays an indicator that this item is a folder

// -----------------------
// END CONFIG
// -----------------------

// Override default node with snippet call if necessary
$node = (isset($LGlob_node)) ? $LGlob_node : $node;

// Determine correct node to consider parent
$tempPageInfo = $etomite->getPageInfo($etomite->documentIdentifier, 0, 'parent');
$thisParent = $tempPageInfo['parent'];

// Change root node if necessary
if ($node && !$thisParent) {
    $node = 0;
}

$globalKids = $etomite->getActiveChildren
(
  $id = $node,
  $sort = 'menuindex',
  $dir = '',
  $fields = 'id, pagetitle, longtitle, description, alias, parent, isfolder, showinmenu',
  $limit = "",
  $showhidden = false
);
$sectionId = $etomite->documentIdentifier;
while (($pageInfo = $etomite->getPageInfo($sectionId, 0, 'parent')) && ($pageInfo['parent'] != $node )) {
    $sectionId = $pageInfo['parent'];
}
$output = '<ul class="LGlob_list">';
$globalCount = 0;
foreach ($globalKids as $kid) {
    $output .= '<li';
  // empty the $class variable
    $class = "";
  // START:conditional class assignments
  // conditionally assign $active
    $active = ($kid['id'] == $sectionId) ? "LGlob_active " : "";
  // conditionally assign $isfolder
    $isfolder = ($kid['isfolder'] == 1) ? "LGlob_isfolder " : "";
  // combine $active and $isfolder and remove any leading or trailing spaces
    $class = trim($active.$isfolder);
  // if both $isfolder and $active are set, assign a combined class
    if ($isfolder != "" && $active != "") {
        $class = "LGlob_isfolder_active";
    }
  // if there is a class assignment, create the class, otherwise assign null
    $class = ($class != "") ? " class=\"$class\"" : null;
  // END:conditional class assignments
    $output .= $class.'><a href="[~'.$kid['id'].'~]" ';
    $output .= 'title="'.$kid['longtitle'].'">';
    $output .= $kid['pagetitle'].'</a></li>';
    $globalCount++;
}
$output .= '</ul>';
return $output;
