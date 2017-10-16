//<?php

// --------------------
// Snippet: NewsListing
// --------------------
// Originated as NewsListing v1.0 by Alex
// Revised as NewsListingRevised by mrruben5
// Revised as NewsListingRev02 by LePrince
// Revised as NewsListingRev03 v1.0 by Lloyd Borrett (lloyd@borrett.id.au)
// Revised as NewsListingRev04 v1.0 by Ian Smith (www.n-vent.com)
//
// Showing the date-time is now optional.
//
// Option to output dates and times as local time.
// This would be typically done using the Etomite configuration 
// "server offset time" value. But you can specify a different value.
//
// Option to get only active documents, or all documents, in the folder.

// Revised as NewsListingRev03 v1.1 by Lloyd Borrett (lloyd@borrett.id.au)
// Added options to specify the sort field and sort direction as arguments.
//
// Revised as NewsListingRev04 by Ian Smith (www.n-vent.com)
// Changed output to be semantically correct unordered list.
// Last Modified: 02-Oct-2007


//===== STYLES: ==============
// .newsList -the class of the unordered news list
// .newsLi -the class of each newslist LI item
// .newsTitle -the class of the H1 news title
// .postInfo -the class of the paragraph containing the date and author information for each post
// .oldernewsheading -the h2 class for the "older news" heading
// .oldnews -the class of the unordered "older news" list

//===== USAGE: ===============
// [[NewsListing]] 
// [[NewsListing?newsid=8]]
// [[NewsListing?newsid=8&complete_news_id=10]]
// and so on for all the acceptable arguments.

//===== ARGUMENTS: ===============

// the folder that contains news entries
// passed as argument "newsid" or default to current document id 
$resourceparent = isset($newsid) ? $newsid : $etomite->documentIdentifier;

// set this to the id of the document which contains the 
// snippetcall where ALL news-articles are to be shown.
// the "Older news" text serves as hyperlink
// -1 = ignore this
if (!isset($complete_news_id)) {
    $complete_news_id = 8;
}

// should the author be displayed?   1=yes,   0=no
if (!isset($show_author)) {
    $show_author =1;
}

// should the date-time be displayed?   1=yes,   0=no
if (!isset($show_date)) {
    $show_date = 1;
}

// how many "older news" shall be displayed
// -1 = all of them
if (!isset($older_news)) {
    $older_news = 20;
}

// should the pagetitle, which is used as heading, be clickable?
if (!isset($title_as_link)) {
    $title_as_link = 1;
}

// should the news item text be omitted?   1=yes,   0=no
// goes nicely with $title_as_link!
if (!isset($title_only)) {
    $title_only = 0;
}

// should only active documents be displayed?   1=yes,   0=no
// yes - returns only active documents (i.e. published and not deleted) in the folder
// no - returns all documents (i.e. published, unpublished, deleted) in the folder
if (!isset($show_only_active)) {
    $show_only_active = 1;
}

// What field should the news items be sorted on?
// Typically this would be 'createdon', but there may be
// times where 'menuindex' or another field is preferred.
if (!isset($sortfield)) {
    $sortfield = 'createdon';
}

// What direction should the news items be sorted on?
// Typically this would be 'DESC' descending, but there may
// be times when 'ASC' is preferred.
if (!isset($sortdir)) {
    $sortdir = 'DESC';
}

//===== end of arguments =========

//===== CONFIGURATION: ===============

// number of news items to show a "short story" of
$nrblogs = 5;

// total number of news items to retrieve
// archives is $nrblogstotal minus $nrblogs
$nrblogstotal = 25;

// how many characters to show of news item if splitter not being used
$lentoshow = 256;

// what indicates the "full story", i.e. the end of the 
// short portion and the beginning of the
// "full story" - this has to be "written" manually in 
// the article's text in HTML-mode (without the apostrophes of course)!
// the <!-- --> makes it a comment which is automatically 
// invisible within the article.
$splitter = "<!--FullStory-->";

// the text to show as full story link
$fullstorytext = "Full Story";

// shall the article be cut off at the *splitter*?   1=yes,   0=no
$use_splitter = 0;

// the text to show for no entries found
$noentriestext = "No entries found.";

// the text to show as heading for older news 
// Note that you must write special characters as html entities. 
$oldernewstext = "News Archives";

// the default author name
$noauthortext = "";
// the date-time format to display based on strftime() 
$timeformat = "%A, %d-%b-%Y %H:%M %p";  #  ( Thursday, 08-Oct-2005 02:00 PM )

// get server offset time in hours
$server_offset_time = $etomite->config['server_offset_time'];
if (!$server_offset_time) {
    $server_offset_time = 0;
} else {
    $server_offset_time = ($server_offset_time / 60 / 60);
}
 
// default time offset (+/-) in hours to use
// typically set to the Etomite configuration "server offset time" value
// using the $server_offset_time value calculated above
$default_time_diff = $server_offset_time;

//===== end of configuration =========

// initialise the news variable 
$output = '';
$output = "<ul class=\"newsList\">";

// time adjustment
$timeadjust = ($default_time_diff * 60 * 60);

if ($show_only_active == 1) {
    $resource = $etomite->getActiveChildren($resourceparent, $sortfield, $sortdir, $fields = 'id, pagetitle, description, content, createdon, createdby');
} else {
    $resource = $etomite->getAllChildren($resourceparent, $sortfield, $sortdir, $fields = 'id, pagetitle, description, content, createdon, createdby');
}

$limit = count($resource);
if ($limit < 1) {
    $output .= $noentriestext."<br />\n";
}

$nrblogs = $nrblogs<$limit ? $nrblogs : $limit;
if ($limit > 0) {
    for ($x = 0; $x < $nrblogs; $x++) {
        $tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."manager_users";
        $sql = "SELECT username FROM $tbl WHERE $tbl.id = ".$resource[$x]['createdby'];
        $rs2 = $etomite->dbQuery($sql);
        $limit2 = $etomite->recordCount($rs2);
        if ($limit2 < 1) {
            $username .= $noauthortext;
        } else {
            $resourceuser = $etomite->fetchRow($rs2);
            $username = $resourceuser['username'];

   //====== Splitter to be used? =====
            if ($use_splitter == 1) {
                if (is_string(strstr ($resource[$x]['content'], $splitter))) {
                  // Does the content contain the socalled "splitter"?
                    $rest = array();

                    // HTMLarea/XINHA encloses it in paragraph's
                    $rest = explode('<p>'.$splitter.'</p>', $resource[$x]['content']);

                    // For TinyMCE or if it isn't wrapped inside paragraph tags
                    $rest = explode($splitter, $rest['0']);

                    $rest = $rest['0'].'<p><a class="readmore" href="[~'.$resource[$x]['id'].'~]">'.$fullstorytext."</a></p>\n";
                } else {
                    $rest = $resource[$x]['content'];
                }
    //======= End of splitter part ===
            } else {
    //=== no splitter ... normal behaviour: ===
               // strip the content
                if (strlen($resource[$x]['content']) > $lentoshow) {
                    $rest = substr($resource[$x]['content'], 0, $lentoshow);
                    $rest .= "...<br />\n";
                    $rest .= "<a href=\"[~".$resource[$x]['id']."~]\">Read more...</a>";
                } else {
                    $rest = $resource[$x]['content'];
                }
    //======= End of no splitter part ===
            }

            $output .= "\n<li class=\"newsLi\"><h1 class=\"newsTitle\">";
            if ($title_as_link == 1) {
                $output .= "<a href=\"[~".$resource[$x]['id']."~]\">";
            }
            $output .= $resource[$x]['pagetitle'];
            if ($title_as_link == 1) {
                $output .= "</a>";
            }
            $output .= "</h1>\n";
      
            if ($title_only != 1) {
                $output .= $rest."</p>\n";
            }

            if (($show_author == 1) || ($show_date == 1)) {
                $output .= "<p class=\"postInfo\">Posted ";
                if ($show_author == 1) {
                    $output .= "by ".$username." ";
                }
                if ($show_date == 1) {
                    $output .= "on ".strftime($timeformat, $resource[$x]['createdon'] + $timeadjust);
                }
                $output .= "</p>";
            }
            $output .= "</li>\n";
        }
    }
}
$output .= "</ul>\n";
if ($limit > $nrblogs) {
    $output .= "<h2 class=\"oldernewsheading\">";

   //=== is there a document where *all* news are to be displayed? If so
   //=== here is the hyperlink
    if ($complete_news_id != -1) {
        $output .= "<a href=\"[~$complete_news_id~]\">";
    }

   //=== older news text:
    $output .= "$oldernewstext";

   //=== is there a document where *all* news are to be displayed? If so
   //=== close the hyperlink
    if ($complete_news_id != -1) {
        $output .= "</a>";
    }
    $output .= "</h2>\n";

   //=== count of older news to be shown:
    if ($older_news == -1) {
        $older_news = $limit;
    } else {
        $older_news += $nrblogs;
    }

   
    $output .= "<ul class=\"oldnews\">\n";
    for ($x = $nrblogs; $x < $limit; $x++) {
        //=== show only a certain amount of older news:
        if ($x < $older_news) {
            $output .= "<li><a href=\"[~".$resource[$x]['id']."~]\"";
            if (($show_author == 1) || ($show_date == 1)) {
                $output .= " title=\"Posted ";
                if ($show_author == 1) {
                    $output .= "by ".$username." ";
                }
                if ($show_date == 1) {
                    $output .= "on ".strftime($timeformat, $resource[$x]['createdon'] + $timeadjust);
                }
                $output .= "\"";
            }
            $output .= ">".$resource[$x]['pagetitle']."</a></li>\n";
        }
    }
    $output .= "</ul>\n";
}

return $output;
