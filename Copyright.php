//<?php

/*
Snippet name: Copyright
Snippet description: Returns a copyright statement
Revision: 1.1 ships with Etomite 0.6.1-Final

Description:
  Returns a copyright statement of the form:
    'Copyright (c) 2006 by Lloyd Borrett. All rights reserved.'
    'Copyright (c) 2005-2006 by Lloyd Borrett. All rights reserved.'
    'Copyright (c) 2005-2006. All rights reserved.'

  (Note: the (c) is replaced with the actual copyright symbol character.
         the '-' is replaced with an en dash.)

  The year range being from either a site start year (if provided) 
  to the current year, or from the year the document was created 
  to the current year. All year values are based on local time 
  using the Etomite server offset time configuration setting.
  The copyright holder can be the one entity for the 
  whole web site (if provided), or the author of the current
  document (if provided).

Snippet Author:
  Lloyd Borrett (lloyd@borrett.id.au)
  
Snippet Category:
  Miscellaneous

See in use at:
  www.raydon.com.au           

Usage:
  Insert [[Copyright]] anywhere in the appropriate section
  of your template. (Usually the footer.)
*/

// *** Configuration Settings ***

// Start Year of the web site.
// To use the document creation date, set this to ''
$site_start_year = '';

// Copyright holder for the whole web site.
// To use the document author details of each document, set this to ''
$site_copyholder = $etomite->config['site_name'];

// *** Start

// Get the document details.

$docInfo = $etomite->getDocument($etomite->documentIdentifier);

// *** AUTHOR ***
if ($site_copyholder == '') {
    $authorid = $docInfo['createdby'];
    $tbl = $etomite->dbConfig['dbase'].".".$etomite->dbConfig['table_prefix']."user_attributes";
    $query = "SELECT fullname FROM $tbl WHERE $tbl.id = $authorid";
    $rs = $etomite->dbQuery($query);
    $limit = $etomite->recordCount($rs);
    if ($limit=1) {
        $resourceauthor = $etomite->fetchRow($rs);
        $authorname = $resourceauthor['fullname'];
    }
   // Trim and replace double quotes with entity
    $copyholder = str_replace('"', '&#34;', trim($authorname));
} else {
    $copyholder = $site_copyholder;
}

// *** COPYRIGHT ***
// get the Etomite server offset time in seconds
$server_offset_time = $etomite->config['server_offset_time'];
if (!$server_offset_time) {
    $server_offset_time = 0;
}
// get the current time and apply the offset
$timestamp = time() + $server_offset_time;
// Set the current year
$today_year = date('Y', $timestamp);
$createdon = date('Y', $docInfo['createdon']);
if ($site_start_year == '') {
    if ($today_year != $createdon) {
        $copydate = $createdon."&#8211;".$today_year;
    } else {
        $copydate = $today_year;
    }
} else {
    if ($today_year != $site_start_year) {
        $copydate = $site_start_year."&#8211;".$today_year;
    } else {
        $copydate = $today_year;
    }
}
if ($copyholder == '') {
    $copyname = $copyholder;
} else {
    $copyname = " by ".$copyholder;
}

// *** BUILD COPYRIGHT STATEMENT & RETURN RESULTS ***

$Copyright = "Copyright &#169; ";
$Copyright .= $copydate.$copyname.". All rights reserved.";

return $Copyright;
