//<?php

// Snippet name: ListDocuments
// Snippet description: Displays document listings based on provided criteria 
// Revision: 1.2 ships with Etomite 0.6.1-Final

// Advanced version of the NewsListing snippet created by Ralph A. Dahlgren
//   from the earlier version by Alex Butter

// Displays brief listing of new article summaries with links to full articles
// All passed variable assignments are optional and can be customized as needed
// Usage: [!ListDocuments?ids=123&numListings=5&maxListings=20&maxChars=200!]
///   Or: [!ListDocuments!] to use default settings for curent folder document
//    Or: [!ListDocuments?ids=11,27,34,119&numListings=5&maxListings=5&maxChars=400!]
//    Or: [!ListDocuments?createdby=1&createdon=20050514!]
// Note:  createdon = YYYYMMDD or MM/DD/YY[YY] formats
//        createdby = user/manager internalKey number

// Changes:
//   v1.1 by Ralph A. Dahlgren -- 2005-07-13

//   v1.2 by Lloyd Borrett -- 2006-04-07
//     Return local time based on Etomite server offset time

//----------  Start of inline style variables  ----------//
//  can be changed to use CSS by using: $object_css = "class='object_class_name'";

$entry_box_css = "style=\"background-color:#F0F8FF; padding:5px; border:1px solid black;\"";
$title_css = "style=\"color:red; font-weight:bold; display:block;\"";
$author_css = "style=\"color:green; font-weight:bold; display:inline;\"";
$date_css = "style=\"color:green; display:inline;\"";
$parent_css = "style=\"color:#FFA500; font-weight:bold\"";
$text_css = "style=\"color:black; font-weight:normal; margin-top:.5em;\"";
$more_css = "style=\"float:right; color:#FFA500; font-weight:bold;\"";
 
//----------  Start of configuration variable assignments  ----------//

// array of folder id's that contain news listings, or current folder document (default)
$ids = isset($ids) ? $ids : $etomite->documentIdentifier;

// number of news listings to summarize
$numListings = isset($numListings) ? $numListings : 3;

// maximum number of news listings to display
$maxListings = isset($maxListings) ? $maxListings : 100;
 
//  maximum character count of news listing summaries to display
$maxChars = isset($maxChars) ? $maxChars : 150;

// assign message to return if no news listings are found
$noResults = "No entries were found.<br />";
 
// Date & Time format based on PHP function strftime() {"%d-%m-%y %H:%M:%S"}
$date_time_format = "%Y-%m-%d";
 
// text for author label
$author = "Author: ";

// Text to display as link to full news listing
$more = "Read the complete article &raquo;";

// Text to display above additional articles list
$olderNews = "Additional Recent Articles:";

// Text to display between Author and Date fields
$between = "&#8212;";
 
//----------  End of configuration variable assignments  ----------//

// get the Etomite server offset time in seconds
$server_offset_time = $etomite->config['server_offset_time'];
if (!$server_offset_time) {
    $server_offset_time = 0;
}

// initialize the data variable to be returned
$output = '';

// assign which data fields to extract from table rows

$fields = '
  id,
  pagetitle,
  description,
  content,
  createdon,
  createdby
';

// assign selection criteria for WHERE clause
$where = "parent IN($ids) AND published=1 AND deleted=0";

// if $createdon was sent, convert it to a timestamp and use it
if (isset($createdon)) {
    $where .= " AND createdon=".strtotime($createdon);
}

// if $createdby (user internalKey) was sent, add to where clause
if (isset($createdby)) {
    $where .= " AND createdby=".$createdby;
}

// retrieve child documents that are published and not deleted using getIntTableRows() API function
$rs = $etomite->getIntTableRows(
  $fields,
  $from = "site_content",
  $where,
  $sort = "createdon",
  $dir = "DESC",
  $limit
);

// return a message if no listings were found
$limit = count($rs);
if ($limit < 1) {
    $output .= $noResults;
} else {
  // determine how many listings to process
    $numListings = ($numListings < $limit) ? $numListings : $limit;
  
  // process the proper number of listings
    for ($x = 0; $x < $numListings; $x++) {
        // retrieve the authors full username using getAuthorData() API function
        $userdata = $etomite->getAuthorData($rs[$x]['createdby']);
        $username = $userdata['fullname'];
    
        // if the listing is longer than $maxChars, strip the HTML tags from the content
        $stripped = strip_tags($rs[$x]['content']);
        if (strlen($stripped)>$maxChars) {
            $rest = substr($stripped, 0, $maxChars);
            $rest .= "...<br />";
        } else {
            $rest = $rs[$x]['content'];
        }
    
        // format the news listing for display
        $output .= "
    <div ".$entry_box_css.">
      
      <div ".$title_css.">
        ".$rs[$x]['pagetitle']."
      </div>
      
      <div ".$text_css.">
        
        ".$rest."
        <div style=\"clear:both; margin-bottom:.5em;\"></div>
        
        <div style=\"text-align:left; float:left;\">
          ".$author."
          <div ".$author_css.">
            ".$username."
          </div>
          ".$between." ".strftime($date_time_format, $rs[$x]['createdon'] + $server_offset_time)."
        </div>
        <div style='text-align:right; float:right;'>
          <a href=\"[~".$rs[$x]['id']."~]\">".$more."</a>
        </div>
      
      </div>
      
      <br />
    
    </div>
    <br />";
    }
}

// display list of links to older news articles
if (($limit > $numListings) && ($numListings < $maxListings)) {
    $output .= "<br /><br /><b>".$olderNews."</b><br />";
    for ($x = $numListings; $x < $limit; $x++) {
        $output .= "<a href=\"[~".$rs[$x]['id']."~]\">".$rs[$x]['pagetitle']."</a><br />";
    }
}

// return snippet results for display
return $output;
