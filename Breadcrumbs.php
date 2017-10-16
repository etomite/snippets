//<?php

// --------------------
// Snippet: Breadcrumbs
// --------------------
// Version: 0.6e
// Date: 2005.05.26
// jaredc@honeydewdesign.com
//
// This snippet was designed to show the
// path through the various levels of
// site structure back to the root. It
// is NOT necessarily the path the user
// took to arrive at a given page. Based on
// PageTrail snippet by: Bill Wilson.
//

// Configuration Settings

   // $maxCrumbs [number]
   // Max number of elements to have in a path.
   // 100 is an arbitrary high number.
   // If you make it smaller, like say 2, but you
   // you are 5 levels deep, it will appear as:
   // Home > ... > Level 4 > Level 5
   // It should be noted that "Home" and the current
   // page do not count. Each of these are configured
   // separately.
   $maxCrumbs = 100;

   // $pathThruUnPub [ true | false ]
   // When you path includes an unpublished folder, setting this to
   // true will show all documents in path EXCEPT the unpublished.
   // Example path (unpublished in caps)
   // home > news > CURRENT > SPORTS > skiiing > article
   // $pathThruUnPub = true would give you this:
   // home > news > skiiing > article
   // $pathThruUnPub = false  would give you this:
   // home > skiiing > article (assuming you have home crumb turned on)
   $pathThruUnPub = true;

   // $showHomeCrumb [true | false]
   // Would you like your crumb string to start
   // with a link to home? Some would not because
   // a home link is usually found in the site logo
   // or elsewhere in the navigation scheme.
   $showHomeCrumb = true;

   // $showCrumbsAtHome [ true | false ]
   // You can use this to turn off the breadcrumbs on the
   // home page.
   $showCrumbsAtHome = true;

   // $showCurrentCrumb [true | false]
   // Show the current page in path
   $showCurrentCrumb = true;

   // $currentAsLink [true | false]
   // If you want the current page crumb to be a
   // link (to itself) then make true.
   $currentAsLink = false;

   // $crumbSeparator [string]
   // Define what you want between the crumbs
   $crumbSeparator = "�";

   // $homeCrumbTitle [string]
   // Just in case you want to have a home link,
   // but want to call it something else
   $homeCrumbTitle = 'Home';

// ***********************************
// END CONFIG SETTINGS
// THE REST SHOULD TAKE CARE OF ITSELF
// ***********************************

// Check for home page
if ($showCrumbsAtHome || (!$showCrumbsAtHome && ($etomite->documentIdentifier != $etomite->config['site_start']))) {
  //initialize crumb array
    $ptarr = array();
  // get current page parent id
    $docInfo = $etomite->getDocument($etomite->documentIdentifier);
    $pid = $docInfo['parent'];
  // show current page, as link or not
    if ($showCurrentCrumb) {
        if ($currentAsLink) {
            $ptarr[] = '<a href="[~'.$etomite->documentIdentifier.'~]" title="'.$docInfo['pagetitle'].'">'.$docInfo['pagetitle'].'</a>';
        } else {
            $ptarr[] = $docInfo['pagetitle'];
        }
    }
  // assemble intermediate crumbs
    $crumbCount = 0;
    $activeOnly = ($pathThruUnPub)? 0 : 1;
    while (($parent=$etomite->getPageInfo($pid, $activeOnly, "id,pagetitle,published,deleted,parent")) && ($crumbCount < $maxCrumbs)) {
        if ($parent['published'] && !$parent['deleted'] && $parent['id'] != $etomite->config['site_start']) {
            $ptarr[] = '<a href="[~'.$parent['id'].'~]" title="'.$parent['pagetitle'].'">'.$parent['pagetitle'].'</a>';
        }
        $pid = $parent['parent'];
        $crumbCount++;
    }
  // insert '...' if maximum number of crumbs exceded
    if ($parent != 0) {
        $ptarr[] = '...';
    }
  // add home link if desired
    if ($showHomeCrumb && ($etomite->documentIdentifier != $etomite->config['site_start'])) {
        $ptarr[] = '<a href="[~'.$etomite->config['site_start'].'~]" title="'.$homeCrumbTitle.'">'.$homeCrumbTitle.'</a>';
    }

    $ptarr = array_reverse($ptarr);
    return join($ptarr, " $crumbSeparator ");
} // end check if home page
