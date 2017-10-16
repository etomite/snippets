//<?php

// Snippet: GetSidebar
// Author: Ralph A. Dahlgren
// Origin: Conceptual design by Ian Smith (cathode) of http://www.n-vent.com
// Created: 2007-03-01
// Revised: Lloyd Borrett on 2007-03-16
// Revision: 20070316
// Revised: Ralph A. Dahlgren on 2008-04-17
// Revision: 20080417
//
// Purpose: Displays sidebar content stored in unpublished documents.
//
// Usage: [ !getSidebar
//          ?doc=sidebar
//          &chunk=chunk_name
//          &class=[false|0] | someclass
//            if not specified then class = column
//            if false|0 then the div isn't output
//          &column=pagetitle
//          &folder=123
//          &recursive=[false|0] (anything else equates to true|1)
//          &wildcard=[true|1] (anything else equates to false|0)
//          &wrapper=[false|0] | somewrapper
//            if not specified then wrapper = "sidebar_wrapper"
//            if false|0 then the div isn't output
//        ! ]
/*
Typical snippet call: [ !getSidebar?doc=sidebar! ]

This snippet looks for an unpublished child document with a page title of "sidebar".
If not found, it looks above for an unpublished sibling titled "sidebar".
If not found, it looks for an unpublished doc adjacent to it's parent...
and so-on to the topmost level.

An example: Picture a website with 4 top level sections, each section has
multiple documents underneath it. Each section needs a single sidebar of
related information, examples would be "downloads", "links" or "more info."
Every document in each section needs this same sectional sidebar. To make this
work, just put [ !getSidebar?doc=downloads! ] into the template. Then, within
each section, create an unpublished document with the page title of "downloads."
This will display the proper downloads sidebar for every document in the
website. If one section doesn't need a sidebar, simply do not create one for
that section. Also, if a single document needs it's own version of a sidebar,
just give it a child document with the same title and it'll override the
parent sidebar.

Other possible uses: Using CSS and customized snippet call parameters you can
do quite a bit with this snippet. The snippet can be used in page Templates,
in Documents, or within Chunks. Use it to display multiple category specific
images. Use it to display content related information. Configure it via
snippet call parameters to do whatever you need it to do - it's that flexible.
*/

// Define what to look for as part of a wildcard search (default="sidebar")
// This will cause the query to look for "sidebar", "sidebar2", "somesidebar", etc...
// Example: doc=MoreInfo would find "MoreInfo1", "MoreInfo2", etc...
$doc = isset($doc) ? $doc : "sidebar";

// Define the table column to use for lookups (default="pagetitle")
// Example: column=longtitle would cause the longtitle column to be used for querying
$column = isset($column) ? $column : "pagetitle";

// Define an optional folder id to use instead of the current parent folder
// Example: folder=123 would force the query to search folder id 123 for results
$folder = isset($folder) ? $folder : $etomite->documentIdentifier;

// Define the css class to assign to each sidebar item (defaults to the value of $column)
// If $column is used then each item can have its own css style
// CSS Example: .sidebar{...} .sidebar2{...} .somesidebar{...}
// Example: class=sideBar would cause .sideBar to be used for all items
$class = isset($class) ? $class : "";
if (in_array($class, array("false","0"))) {
    $class = "false";
}
if ($class == "") {
    $class = "{".$column."}";
}

// Determine whether or not a recursive search back to the docroot should be performed
// The only values that will stop recursion are recursive=[false|0]
$recursive = (isset($recursive) && in_array($recursive, array("false","0"))) ? false : true;

// Determine whether or not a wildcard search should be performed
// The only values that will stop a wildcard search are wildcard=[true|1]
$wildcard = (isset($wildcard) && in_array($wildcard, array("true","1"))) ? true : false;

// Define optional wrapper open and close markup which will surround all sidebar markup
// The default action is to use the wrapper code below which can be modifed as needed
$wrapper = isset($wrapper) ? $wrapper : "";
if (in_array($wrapper, array("false","0"))) {
    $wrapper = "false";
}
if ($wrapper == "") {
    $wrapper = "sidebar_wrapper";
}

if ($wrapper = "false") {
    $wrapper_open = "";
    $wrapper_close = "";
} else {
    $wrapper_open = "<div class=\"" . $wrapper . "\">";
    $wrapper_close = "</div> <!-- end " . $wrapper . " -->";
}

// Define the template markup to be used in rendering each resultset item
// Note: an alternate method would be to use $tpl = $etomite->getChunk("chunk_name");
// to retrieve markup stored in a Chunk.
if (isset($chunk)) {
    $tpl = $etomite->getChunk($chunk);
} else {
    if ($class == "false") {
        $tpl =
        <<<END
<h1>{longtitle}</h1>
{content}
END;
    } else {
        $tpl =
        <<<END
<div class="$class">
<h1>{longtitle}</h1>
{content}
</div> <!-- end $class -->
END;
    }
}

// Define the document columns to be returned in the result set
// Examples: "*" for all columns OR "alias,content"
$fields = "id,pagetitle,longtitle,description,alias,content";

// Look for sidebars by recursively moving back towards the doc root
while (($folder >= 0) && (count($rs) < 1)) {
  // Define the MySQL WHERE clause to be used by the query
  // If wildcard=[true|1] then we do a wildcard search, otherwise an exact search
    if ($wildcard) {
        $where =
        "`parent`=$folder
        AND `$column` LIKE '%$doc%'
        AND `published`=0"
        ;
    } else {
        $where =
        "`parent`=$folder
        AND `$column`='$doc'
        AND `published`=0"
        ;
    }
  // Perform the actual database query
  // Variables such as $from, $sort, and $dir have been hard-coded but could
  // be made dynamic if needed
  // See getIntTableRows() API function documentation for more information
    $rs = $etomite->getIntTableRows
    (
    $fields,
    $from = "site_content",
    $where,
    $sort = "alias",
    $dir = "ASC",
    $limit = "",
    $push = true,
    $addPrefix = true
    );
  // Retrieve the next parent if $recursive=true and $folder > 0, otherwise
  // set to -1 to stop the loop
    $folder = ($folder > 0 && $recursive) ? $etomite->parents[$folder] : -1;
}

// If results were returned process them using our template markup
// See mergeCodeVariables() API function documentation for more information
if ($rs) {
  // START::Pre-processing routines
  // Example: (Not exactly a practical example, but it gets the idea across.)
  /*
  foreach($rs as $row) {
    // Capitalize the first character of each word in pagetitle
    $row['pagetitle'] = ucwords(strtolower($row['pagetitle']));
    ... additional per-record pre-processing code ...
  }
  */
  // END::Pre-processing routines

    $output .= ($wrapper_open != "") ? $wrapper_open : "";
    $output .= $etomite->mergeCodeVariables
    (
    $tpl,
    $rs,
    $prefix = "{",
    $suffix = "}",
    $oddStyle = "",
    $evenStyle = "",
    $tag = ""
    );
    $output .= ($wrapper_close != "") ? $wrapper_close : "";
}

// START::Post-processing routine(s)
// Example:
// Remove any empty header tags
$output = str_replace("<h1></h1>", "", $output);
// END::Post-processing routine(s)

// Return the rendered markup to caller
return $output;
// END::getSidebar
