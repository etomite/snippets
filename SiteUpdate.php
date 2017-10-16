//<?php

// Snippet name: SiteUpdate
// Snippet description: Returns date of most recent published document update
// Revision: 1.2 ships with Etomite Prelude v1.0

// Author: Ralph A. Dahlgren -- 2005-07-13
// Usage: [!SiteUpdate?dateFormat=%B %e, %Y!] Returns date formatted: July 13, 2005 
// See strftime() documentation for additional formatting options

// Changes:
//   v1.1 by Lloyd Borrett -- 2006-04-07
//     Return local time based on Etomite server offset time
//   v1.2 by Ralph A. Dahlgren -- 2008-04-12
//     Ignore unpublished and deleted documents
//     Use Date and Time formats from configuration

// was $dateFormat sent in snippet call?
if (isset($dateFormat)) {
  // use $dateFormat sent in snippet call
    $format = $dateFormat;
} else {
  // use default Date & Time formats from configuration
    $format = $etomite->config['date_format']." ".$etomite->config['time_format'];
}

// get the Etomite server offset time in seconds
$server_offset_time = $etomite->config['server_offset_time'];
// if no server offset time was found, use zero
if (!$server_offset_time) {
    $server_offset_time = 0;
}

// define our database query
$sql = <<<QUERY
  SELECT editedon 
  FROM {$etomite->db}site_content 
  WHERE published=1
  AND deleted=0
  ORDER BY editedon DESC
QUERY;

// perform the database query
$rs = $etomite->dbQuery($sql);

// check to see if results were returned
if ($etomite->recordCount($rs) > 0) {
  // fetch the first data row (last edited)
    $row = $etomite->fetchRow($rs);
  // add server offset to timestamp
    $update = strftime($format, $row['editedon'] + $server_offset_time);
} else {
  // no results returned so set to null
    $update = null;
}

// return formatted timestamp to caller
return $update;
