//<?php

// --------------------
// Snippet: ListMenu
// --------------------
// Version: 0.6f
// Date: 2005.08.04
// jaredc@honeydewdesign.com
//
// This snippet was designed to show navigation
// in a nested lists of arbitrary depth.

// Configuration Settings

   // $removeNewLines
   // This setting will remove white space from between <ul> and <li>
   // items so the list can be styled in IE. This makes it hard to read
   // the source code- set to false for troubleshooting before launch.
   // False is also handy if the list will be horizontal.
   $removeNewLines = true;

   // $currentAsLink
   // If you would like to turn off the link for the
   // current page, set to false.
   $currentAsLink = true;

   // $LM_node [ int ]
   // Settable in snippet call only. Allows you to set an arbitrary
   // anchor for this menu. Should be id number of initial document.
   // For ListMenu this is typically the current page so that the
   // snippet will generate a list back to the site root.
   // [[ListMenu?LM_node=56]]

   // $levelsDeep [ int ]
   // Specifies how many levels of menu you want to show starting
   // with the children of current document working back to root. For
   // example, 3 levels would give you the children of the current
   // page, the current page and its siblings, and the current page's
   // parent and its siblings. The child generation is counted regardless
   // of wether or not there are children.
   //
   // You can also specify negative  values. Negative values indicate
   // how many levels NOT to include, starting at the root. For example,
   // if you want all BUT the root level showing, you can declare -1.
   // This would be a main section menu - but without the "global" options.
   //
   // The default of 0 will show current children all the  back to root.
   // This represents the complete menu. You can also set this in a snippet
   // call with LM_levels:
   // [[ListMenu?LM_levels=1]]
   $levelsDeep = 0;

   // $directGeneology [ true | false ]
   // Set this option if you only want to show the direct geneolgy. For
   // instance, with this set to true, and you were on examplePage 5
   // levels deep, you would see all of examplePage's child pages (if
   // any), examplePage's parent, but NOT siblings of any parent above
   // the current page.
   $directGeneology = true;
   
   // $showGlobals [ true | false ]
   // Leave the root level folders showing, even if $directGeneology is
   // true. NOTE- this will NOT override a set $levelsDeep. Settable in
   // snippet call with LM_globals (0=false, 1=true)
   // [[ListMenu?LM_globals=1]]
   $showGlobals = true;

   // $sortWiz [ array ]
   // You can specify any number of sort columns and directions, so you
   // are not limited to menuindex - for instance you might want alphabetic
   // or folders first or whatever. Format each sort like this:
   // $sortWiz[] = array("sortColumn","direction");
   // Where sortColumn is the sort column like isfolder, pagetitle, etc.
   // and direction is "ASC" for ascending and "DESC" is descending.
   // Default is:
   // $sortWiz[] = array("menuindex","ASC");
   // If you wanted folders first, THEN contents in alphabetical order:
   // $sortWiz[] = array("isfolder","ASC");
   // $sortWiz[] = array("pagetitle","ASC");
   $sortWiz[] = array("menuindex","ASC");
   
   // $alternateRows [ true | false ]
   // Append "_alt" to style class of alternate rows (true)
   $alternateRows = false;
   
   // $showDescription [ true | false ]
   // Show the description under the link- usually not necessary
   // Set in snippet call with LM_desc:
   // [[ListMenu?LM_desc=1]]
   $showDescription = false;

// STYLES used
//
// #LM_level_N      menu level where N is the number of the depth
//                  starting at 0
// #LM_youAreHere   menu item of current location
// .LM_expanded     expanded menu item with children
// .LM_collapsed    menu item with childen, but not expanded
// .LM_endPage      menu item with children
// .LM_description  menu item description
// 

// ########################################
// End Config
// The rest takes care of itself
// ########################################

// Adjust for snippet variables
$showGlobals = (isset($LM_globals))? $LM_globals : $showGlobals ;
$levelsDeep = (isset($LM_levels))? $LM_levels : $levelsDeep ;
$showDescription = (isset($LM_desc))? $LM_desc : $showDescription ;

// Make adjustment for new lines 
$ieSpace = ($removeNewLines)? "" : "\n";

// Create Geneology

$fullGeneology = array();
$geneologyMarker = (isset($LM_node))? $LM_node : $etomite->documentIdentifier;
while ($currentMarker=$etomite->getPageInfo($geneologyMarker, null, 'id,parent')) {
    $fullGeneology[] = $currentMarker['id'];
    $geneologyMarker = $currentMarker['parent'];
}
$fullGeneology[] = 0;

// alter geneology for correct depth
$geneology = array();
if (($levelsDeep > 0) && (count($fullGeneology) > $levelsDeep)) {
    for ($i = 0; $i < $levelsDeep; $i++) {
        $geneology[] = $fullGeneology[$i];
    }
} elseif (($levelsDeep) < 0 && (count($fullGeneology) > abs($levelsDeep))) {
    for ($i = 0; $i < -$levelsDeep; $i++) {
        array_pop($fullGeneology);
    }
    $geneology = $fullGeneology;
} else {
    $geneology = $fullGeneology;
}

// Build lists

// Initialize
$currentParent = $geneology[0];


$listSoFar = '';
$lookForChild = 0;
// Assemble sort string
$sortString = '';
foreach ($sortWiz as $sortCriteria) {
    $sortString .= $sortCriteria[0] . " " . $sortCriteria[1] . ", ";
}
$sortString = substr($sortString, 0, strlen($sortString)-2);

for ($geneCount=0; $geneCount < count($geneology); $geneCount++) {
    $childrenList = $etomite->getActiveChildren($geneology[$geneCount], $sortString, null, 'id, pagetitle, longtitle, parent, isfolder, description');
    if ($childrenList) {
        $currentLevelList = '<ul id="LM_level_'.(count($geneology)-$geneCount).'">'.$ieSpace;
        $listPosition = 0;
        foreach ($childrenList as $childItem) {
            if (!$directGeneology || $geneCount==0 || (($geneCount==1)&&(!$etomite->getActiveChildren($geneology[0]))) || ($directGeneology && in_array($childItem['id'], $geneology)) || ($showGlobals && ($geneology[$geneCount]==0))) {
                if ($childItem['isfolder']) {
                    $cssStyle = (in_array($childItem['id'], $geneology))? ' class="LM_expanded': ' class="LM_collapsed';
                } else {
                    $cssStyle = ' class="LM_endPage';
                }
                $cssStyle .= ($alternateRows && ($listPosition%2))? '_alt"' : '"';
                    $currentLevelList .= '<li'.$cssStyle.'>';
                if ((!$currentAsLink) && ($childItem['id'] == $etomite->documentIdentifier)) {
                    $currentLevelList .= '<span id="LM_youAreHere">';
                    $currentLevelList .= $childItem['pagetitle'].'</span>';
                } else {
                    $linkTitle = ($childItem['longtitle'])? $childItem['longtitle'] : $childItem['pagetitle'] ;
                        $currentLevelList .= '<a href="[~'.$childItem['id'].'~]" title="' . $linkTitle .'">';
                        $currentLevelList .= $childItem['pagetitle'].'</a>';
                }
                $currentLevelList .= ($showDescription)? '<div class="LM_description">'.$childItem['description'].'</div>' : '' ;
                if ($lookForChild == $childItem['id']) {
                    $currentLevelList .= $listSoFar;
                }
                    $currentLevelList .= '</li>'.$ieSpace;
            }
            $listPosition++;
        }
        $currentLevelList .= '</ul>'.$ieSpace;
    }
    $listSoFar = $currentLevelList;
    $lookForChild = $geneology[$geneCount];
}

// send to parser
return $listSoFar;
