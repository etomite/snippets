//<?php

/**
| --------------------------------------------------------------------------
| Snippet Title:     SectionPageMenu
| Snippet Version:   1.0 (final)
| Etomite Version:   0.6 +
|
| Description:       Outputs Section and Page menus.
|                    If the current page is the home page, or if the current page is
|                    a section parent page with no child pages, nothing is output.
|
|                    For the Section menu, we find the root level section parent page
|                    of the current page, and output a list of links to the children
|                    of that parent page. If the current page is the section parent page,
|                    we're then done.
|
|                    For the Page menu, if current page has children, we want the current
|                    page as parent of the Page menu. Otherwise if current parent is not
|                    the section parent page, we want a Page menu of our parent.
|
| Snippet Author:    Lloyd Borrett (lloyd@borrett.id.au)
|
| Version History:   1.0 - Lloyd Borrett
|
| Snippet Category:  Menus & Navigation
|
| Usage:             Insert [[SectionMenu]] into your template where you want the menu
|                    to go.
|
| Credits:           Thanks to jaredc for his help.
| ---------------------------------------------------------------------------
*/

$menu = "";

$id = $etomite->documentIdentifier; //current document

// If we're at home, do nothing
$atHome = ($id == $etomite->config['site_start'])? true : false ;
if ($atHome) {
    return $menu;
}

// Get the document id of the section parent page
$sectionId = $id;
while (($pageInfo = $etomite->getPageInfo($sectionId, 0, 'parent')) && ($pageInfo['parent'] != 0 )) {
    $sectionId = $pageInfo['parent'];
}

// Get the children of the section parent page
$children = $etomite->getActiveChildren($sectionId);
$childrenCount = count($children);

// If there are no children, i.e. the page was the section parent page, do nothing
if ($children==false) {
    return $menu;
}

$menu = " <br />\n";

// Output the link to the section parent page
$menu .= '<a class="crumbs" ';
$pageArray = $etomite->getPageInfo($sectionId, 1, 'pagetitle');
$pageTitle = $pageArray['pagetitle'];
$menu .= 'href="[~'.$sectionId.'~]" title="'.$pageTitle.'">'.strtoupper($pageTitle).'</a>';
if ($sectionId==$id) {
    $menu .= " <";
}
$menu .= "<br />\n";

// Output the links to the children of the section parent page
for ($x=0; $x<$childrenCount; $x++) {
    $menu .= '> <a class="crumbs" href="[~'.$children[$x]['id'].'~]" title="'.$children[$x]['pagetitle'].'">'.$indentString.strtolower($children[$x]['pagetitle']).'</a>';
    if ($children[$x]['id']==$id) {
        $menu .= " <";
    }
    $menu .= "<br />\n";
}

// If the current page is the section parent page, we're done.
if ($sectionId==$id) {
    return $menu;
}

// Either we're in a lower level or at its top
// If current has children, we want the current page as parent of the menu.
// Otherwise if current parent is not the section parent page, we want a menu of our parent.

// Get the children of the current page
$children = $etomite->getActiveChildren($id);
$childrenCount = count($children);
if ($children==true) {
//	Reposition this as the parent page
    $pageInfo = $etomite->getPageInfo($id, 0, 'parent');
    $parentId = $id;
} else {
    $pageInfo = $etomite->getPageInfo($id, 0, 'parent');
    $parentId = $pageInfo['parent'];
    if ($parentId==$sectionId) {
        return $menu;
    }
    $children = $etomite->getActiveChildren($parentId);
    $childrenCount = count($children);
}

// Let's output a menu.

$menu .= " <br />\n";

// Output the link to the parent page
$menu .= '<a class="crumbs" ';
$pageArray = $etomite->getPageInfo($parentId, 1, 'pagetitle');
$pageTitle = $pageArray['pagetitle'];
$menu .= 'href="[~'.$parentId.'~]" title="'.$pageTitle.'">'.strtoupper($pageTitle).'</a>';
if ($parentId==$id) {
    $menu .= " <";
}
$menu .= "<br />\n";

// Output the links to the children of the parent page
for ($x=0; $x<$childrenCount; $x++) {
    $menu .= '> <a class="crumbs" href="[~'.$children[$x]['id'].'~]" title="'.$children[$x]['pagetitle'].'">'.strtolower($children[$x]['pagetitle']).'</a>';
    if ($children[$x]['id']==$id) {
        $menu .= " <";
    }
    $menu .= "<br />\n";
}

return $menu;
