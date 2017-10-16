//<?php

// ------------------------
// Snippet: ListChildOrSibs
// ------------------------
// Version: 0.6k
// Date: 2005.07.22
// jaredc@honeydewdesign.com
//
// This snippet was designed to show all child pages, or siblings
// if there are no children pages, with or without descriptions, in a
// list.

// Configuration Settings

   // $removeNewLines [ true | false ]
   // This setting will remove white space from between <ul> and <li>
   // items in HORIZONTAL lists, so the list can be styled in IE.
   // This makes it hard to read the source code- set to false for
   // troubleshooting or if your CSS doesn't quite seem to work.
   $removeNewLines = true;

  // $titleOfList [ string ]
  // If you would like to give your list a generic title this is the place to do it.
  // If you don't want a title, set this to be an empty string
  // $titleOfList = '';
  // If you want to use the current page title as the title
  // then set to 'p'
  // $titleOfList = 'p';
  // Or set in the snippet call with $title:
  // [[ListChildOrSibs?title=Your title here]]
  $titleOfList = '';

  // $includeDescription [ true | false ]
  // Set to true if you want to include the description, can be
  // overridden by snippet variable $LCOS_showDesc [ 0 | 1 ]
  $includeDescription = false;
  
  // $linkTitle [ 'pagetitle' | 'description' | 'longtitle' ]
  // The title of links (in the <a> tag) can be set to use either the
  // description, the longtitle, or pagetitle. The title will default to
  // pagetitle if the desired field is empty.
  $linkTitle = 'description';

  // $maxChildSibs [ int ]
  // Don't let lists of links get away from you. If you want to limit
  // them, provide a max number. 0 = unlimited.
  $maxChildSibs = 0;

  // $subSibsForChildren [ true | false ]
  // If you want to substitute sibling documents for children when
  // there are no children, set to true.
  $subSibsForChildren = true;
  
  // $showSibsOfUnPubParent [ true | false ]
  // Sometimes when you have an archive folder (unpublished) filled with
  // LOTS of documents, you don't want all those documents appearing as
  // siblings (assuming the current document has no children of its own).
  // This option, when set to false, will first try to show children as
  // usual. If there are no children, it will look at the parent (to
  // show siblings). If the parent is unpublished however, it will move
  // to its parent and so on until it finds a published folder with
  // published children to list.
  $showSibsOfUnPubParent = false;
  
  // $showSelfAsSib [ true | false ]
  // When a document has no children, and no other siblings, this variable
  // will determine if the menu shows itself (sibling mode,
  // $showSelfAsSib = true) or all children of the next higher folder
  // ($showSelfAsSib = false).
  $showSelfAsSib = true;
  
  // $titleAsLink [ true | false ]
  // If the title document is not the current document, true will make the
  // title a link to the title document. This happens on lists showing
  // siblings of current page.
  $titleAsLink = true;


// Styles
  // These are the styles used in this snippet:

  //
  // .LCOS_box           A <div> surrounding the entire thing, list and title
  // .LCOS_title         Title of the list
  // .normal          The class of the <ul> itself
  // .LCOS_child         The class of the <li>
  // .LCOS_current       A <span> wrapped around the current page if in list
  //                     instead of an <a> tag.
  // .LCOS_description   The <span> surrounding the description

// -------------------------------
// End Config
// The rest takes care of itself
// -------------------------------

// Initialize things
$nonIeSpace = "\n";
$nlSpace = ($removeNewLines)? "" : $nonIeSpace ;
$showDescription = (isset($LCOS_showDesc))? $LCOS_showDesc :$includeDescription ;
$titleOfList = isset($title)? $title : $titleOfList;
$lcosOutput = '';

// Get current page info if page is cached
$pageCached = ($etomite->documentObject)? false : true ;
if ($pageCached) {
    $currentPageInfo = $etomite->getPageInfo($etomite->documentIdentifier, 0, 'pagetitle,parent');
}
$currentPagetitle = ($pageCached)? $currentPageInfo['pagetitle'] : $etomite->documentObject['pagetitle'] ;
$currentPageParent = ($pageCached)? $currentPageInfo['parent'] : $etomite->documentObject['parent'] ;

// Build lists
$children = $etomite->getActiveChildren($etomite->documentIdentifier, 'menuindex ASC,pagetitle', 'ASC', 'id,pagetitle,description,longtitle');
$useParent = false;

if (!$children && $subSibsForChildren && $currentPageParent!=0) {
    if (!$showSibsOfUnPubParent) {
        $goodParentId = $currentPageParent;
        do {
            // get parent document info
            $pDoc = $etomite->getPageInfo($goodParentId, 0, $fields = 'id, pagetitle, published, deleted, parent');
            // check for pub status and children
            $children = $etomite->getActiveChildren($pDoc['id'], 'menuindex ASC,pagetitle', 'ASC', 'id,pagetitle,description,longtitle');
            if ($pDoc['published'] &&
              !$pDoc['deleted'] &&
              (
              count($children) > 1 ||
              $children && $showSelfAsSib ||
              (count($children) == 1 && !$showSelfAsSib && $etomite->documentIdentifier != $children[0]['id'] ) ||
              (count($children) > 1 && !$showSelfAsSib)
              )
              ) {
                $pubParent = true; // trigger end of searching
                $useParent = true; // to use correct page title
                $parentName = $pDoc['pagetitle']; // assign correct page title
                $parentId = $pDoc['id'];
            } else {
                $goodParentId = $pDoc['parent'];
                $pubParent = false;
                $children = '';
            }
        } while (!$pubParent && $goodParentId != 0);
    } else { // end if !$showSibsOfUnPubParent
        $children = $etomite->getActiveChildren($currentPageParent, 'menuindex ASC,pagetitle', 'ASC', 'id,pagetitle,description,longtitle');
        $useParent = true;
        $parentId = $currentPageParent;
        $parent = $etomite->getPageInfo($currentPageParent);
        $parentName = $parent['pagetitle'];
    }
}

if ($children) {
    if (!$useParent) {
        $titleOfList = ($titleOfList == 'p')? $currentPagetitle : $titleOfList ;
    } else {
        if ($titleOfList == 'p') {
            $titleOfList = ($titleAsLink)? '<a href="[~'.$parentId.'~]" title="'.$parentName.'">' . $parentName . '</a>' : $parentName ;
        } else {
            $titleOfList = $titleOfList;
        }
    }
  // Div around the whole
    $lcosOutput .= '<div class="LCOS_box">'.$nonIeSpace;
  // Title if any
    $lcosOutput .= ($titleOfList)? '<div class="LCOS_title">'. $titleOfList.'</div>'.$nonIeSpace : '' ;
  // Start list
    $lcosOutput .= '<ul class="normal">'.$nlSpace;

    for ($c=0; ($c < count($children)) && (!$maxChildSibs || ($c < $maxChildSibs)); $c++) {
        $onCurrentPage = ($children[$c]['id']==$etomite->documentIdentifier)? true : false ;
        $lcosOutput .= '<li class="LCOS_child">';
        if ($onCurrentPage) {
            $lcosOutput .= '<span class="LCOS_current">';
        } else {
            $lcosOutput .= '<a href="[~'.$children[$c]['id'].'~]" ';
            $useLinkTitle =($children[$c][$linkTitle])? $children[$c][$linkTitle] : $children[$c]['pagetitle'] ;
            $lcosOutput .= 'title="'.$useLinkTitle.'">';
        }
        $lcosOutput .= $children[$c]['pagetitle'];
        $lcosOutput .= ($onCurrentPage)? '</span>' : '</a>';
        $lcosOutput .= ($showDescription )?'<span class="LCOS_description">'.$children[$c]['description'].'</span>' : '';
        $lcosOutput .= '</li>'.$nlSpace;
    } // end foreach
    $lcosOutput .= '</ul>'.$nlSpace; //end list
    $lcosOutput .= '</div>'.$nonIeSpace;
} // end if children exist

// send to parser
return $lcosOutput;
