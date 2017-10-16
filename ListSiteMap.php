//<?php

// --------------------
// Snippet: ListSiteMap
// --------------------
// Version: 1.0
// Date: 2008-04-11
// Ralph A. Dahlgren
//

// NOTE: This snippet should use a non-cacheable snippet call
// Use: [!ListSiteMap!] instead of [[ListSiteMap]]

// Based On:
// Version: 0.6h.1
// addapted by TraXas to support authentication and deleted documents
// Date: 2007.11.08
// jaredc@honeydewdesign.com
//
// This snippet was designed to show a nested
// list site map with each pagetitle being a
// link to that page. It will not include
// unpublished folders/pages OR its children,
// even if the children ARE published.

// Configuration
//
// $siteMapRoot [int]
// The parent ID of your root. Default 0. Can be set in
// snippet call with LSM_root (to doc id 10 for example):
// [!ListSiteMap?LSM_root=10!]
$siteMapRoot = 0;

// $showDescription [true | false]
// Specify if you would like to include the description
// with the page title link.
$showDescription = true;

// $titleOfLinks [ string ]
// What database field do you want the title of your links to be?
// The default is pagetitle because it is always a valid (not empty)
// value, but if you prefer it can be any of the following:
// id, pagetitle, description, parent, alias, longtitle
$titleOfLinks = 'pagetitle';

// $removeNewLines [ true | false ]
// If you want new lines removed from code, set to true. This is generally
// better for IE when lists are styled vertically.
$removeNewLines = false;

// $maxLevels [ int ]
// Maximum number of levels to include. The default 0 will allow all
// levels. Also settable with snippet variable LSM_levels:
// [!ListSiteMap?LSM_levels=2!]
$maxLevels = 0;

// $selfAsLink [ true | false ]
// Define if the current page should be a link (true) or not
// (false)
$selfAsLink = false;

// $showUnpubs [ true | false ]
// Decide to include items in unpublished folders. This will show the
// unpublished items as well. No links will be made for the unpublished items
// but they will be shown in the structure. You will not likely want to do
// this but the option is yours.
$showUnpubs = false;

// Styles
//
// .LSM_currentPage    span surrounding current page if $selfAsLink is false
// .LSM_description    description of page
// .LSM_N              ul style where N is the level of nested list- starting at 0

// ###########################################
// End config, the rest takes care of itself #
// ###########################################

// Initialize
$siteMapRoot = (isset($LSM_root))? $LSM_root : $siteMapRoot ;
$maxLevels = (isset($LSM_levels))? $LSM_levels : $maxLevels ;
$ie = ($removeNewLines) ? "" : "\n" ;

// Overcome single use limitation on functions
global $MakeMapDefined;

if (!isset($MakeMapDefined)) {
    function MakeMap($listParent, $listLevel, $description, $titleOfLinks, $maxLevels, $su, $ie)
    {
        global $etomite;
        $children = $etomite->getAllChildren($listParent, 'menuindex', 'ASC', 'id, pagetitle, description, parent, alias, longtitle, published');
        $output .= $ie."<ul class=\"LSM_{$listLevel}\">".$ie;
        foreach ($children as $child) {
            // Checks if the document doesn't require authentication or is deleted
            if (( ($etomite->checkPermissions($child['id'])) || (!$child['authenticate'] )) && (!$child['deleted'] )) {
                // skip unpubs unless desired
                if (!$su && !$child['published']) {
                    continue;
                }
                // for XHTML compliance we need to insert &nbsp; in empty descriptions
                if ($child['description'] == null) {
                    $child['description'] = "&nbsp;";
                }
                $descText = ($description) ? "&nbsp;:&nbsp;<span class=\"LSM_description\">{$child['description']}</span>" : "";
                $output .= '<li>';
                if (!$selfAsLink && ($child['id'] == $etomite->documentIdentifier)) {
                    $output .= "<span class=\"LSM_currentPage\">{$child['pagetitle']}</span>";
                } elseif (!$child['published']) {
                    $output .= "<span class=\"LSM_unpubPage\">{$child['pagetitle']}</span>";
                } else {
                    $output .= "<a href=\"[~{$child['id']}~]\" title=\"{$child[$titleOfLinks]}\">{$child['pagetitle']}</a>";
                }
                $output .= $descText;
                if ($etomite->getAllChildren($child['id']) && (($maxLevels == 0) || ($maxLevels > $listLevel+1 ))) {
                    $output .= MakeMap($child['id'], $listLevel++, $description, $titleOfLinks, $maxLevels, $su, $ie);
                }
                $output .= "</li>".$ie;
            }
        }
        $output .= "</ul>".$ie;
        return $output;
    }
    $MakeMapDefined = true;
}

$output .= $ie."<!-- BEGIN::Site Map -->";
$output .= MakeMap($siteMapRoot, 0, $showDescription, $titleOfLinks, $maxLevels, $showUnpubs, $ie);
$output .= "<!-- END::Site Map -->".$ie;
return $output;
