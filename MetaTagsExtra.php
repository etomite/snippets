//<?php

/**
 * ---------------------------
 * Snippet: MetaTagsExtra
 * ---------------------------
 * Version: 2.6 2007-04-30
 * Etomite Version: 0.6 +
 *
 * Description:
 *  Returns XHTML for document meta tags:
 *   Content-Type, Content-Language, Generator,
 *   Title, Description, Keywords, Abstract, Author, Copyright,
 *   Robots, Googlebot, Cache-Control, Pragma, Expires, Last Modified,
 *   Google Map Key, Google and Yahoo Sitemap Verification Keys,
 *   SafeSurf / PICS-Label, No-Email-Collection, Distribution and Rating.
 *  Can also return XHTML for Dublin Core Metadata Initiative meta tags:
 *   DC.format, DC.language, DC.title,
 *   DC.description, DC.subject, DC.title.alternative,
 *   DC.publisher, DC.creator, DC.rights,
 *   DC.date.created, DC.date.modified, DC.date.valid and DC.identifier.
 *  Can also return the GeoURL and GeoTags meta tags:
 *   DC.title, ICBM, geo.position, geo.placename and geo.region.
 *
 * Snippet Author:
 *   Miels with mods by Lloyd Borrett (lloyd@borrett.id.au)
 *
 * Version History:
 * 1.3 - Lloyd Borrett added the Robots meta tag based
 * on the idea in the SearchableSE snippet by jaredc
 *
 * 1.4 - Lloyd Borrett added the Abstract meta tag
 * based on the Site Name and the Long Title.
 * Also added the Generator meta tag based on the Etomite version details.
 * The Robots meta tag is now only output if the document is non-searchable,
 * to reduce XHTML bloat. The Googlebot meta tag is now also output
 * when the document is non-searchable.
 *
 * 1.5 - Lloyd Borrett added no-cache directives via the Cache-Control
 * and Pragma meta tags if the document is non-cacheable.
 * Abstract meta tag uses the document description if long title not set.
 * Cleaned up some other tests.
 *
 * 1.6 - 2006-01-26 - Lloyd Borrett cleaned up some code.
 *
 * 1.7 - 2006-01-27 - Lloyd Borrett
 * Added support for the Distribution and Rating meta tags.
 * Copyright meta tag can now include a year range being from either
 * a site creation year to the current year, or from the year the
 * document was created to the current year, e.g. 2005-2006.
 * Added ability to specify a site wide author, and thus be able to
 * skip looking up document author details.
 *
 * 1.8 - 2006-01-27 - Lloyd Borrett
 * Current year now based on local time using the Etomite
 * Server Offset Time configuration setting
 *
 * 1.9 - 2006-03-08 - Lloyd Borrett
 * Dates in meta tags can be output in your choice of ISO 8601
 * or RFC 822 formats.
 * Dates in the meta tags are now corrected to local time.
 * Fixed problem with the generation of the "description" meta tag.
 *
 * 2.0 - 2006-03-10 - Lloyd Borrett
 * Moved the generation of the "content-type", "content-language"
 * and "title" meta tags into this snippet.
 * Added in support for the Dublin Core Metadata Initiative meta tags.
 *
 * 2.1 - 2006-03-15 - Lloyd Borrett
 * Dropped the choice of date formats. Dublin Core tags now use ISO dates.
 * Others tags use RFC 822 dates. This is what is properly supported.
 * Added in support for GeoURL (www.geourl.org) and
 * GeoTags (www.geotags.com) meta tags.
 *
 * 2.2 - 2006-04-07 - Lloyd Borrett
 * Get the base URL from Etomite instead of it being a configuration option.
 *
 * 2.3 - 2006-04-21 - Lloyd Borrett
 * Changed the way the "generator" meta tag is produced so that it
 * better reports the Etomite version details.
 *
 * 2.4 - 2006-05-04 - Lloyd Borrett
 * Couple of fixes as picked up by "bungo".
 * Put in the new way to get the base URL from Etomite 0.6.1 Prelude.
 * Dropped the use of HTML entities in the copyright details.
 * Added a sort to the keywords.
 *
 * 2.5 - 2006-10-25 - Lloyd Borrett
 * Made displaying the cache tags optional.
 *
 * 2.6 - 2007-04-30 - Lloyd Borrett
 * Added support for the no-email-collection meta tag.
 * Added support for the Google Map Key, plus the Google
 * and Yahoo Sitemap Verification keys.
 * Added support for SafeSurf / PICS-Label / ICRA Labels.
 * Get the content-type from Etomite system setting.
 * Added support for favicon links.
 *
 * Snippet Category: Search Engines
 *
 * Usage:
 * Insert [[MetaTagsExtra]] anywhere in the head section of your template.
 * Don't forget to set the full name of all document authors.
 * You can find it at "Manage users" -> your username -> "full name".
 * This value is used for the Author and Copyright meta tags.
 *
 * When you mark a page as "NOT searchable" - a Robots meta tag
 * with "noindex, nofollow" is inserted to keep web search engines
 * from indexing that document. After all, there's little value in
 * making your Etomite document unsearchable to Etomite, when
 * Google still knows where it is! For "searchable" documents, no
 * Robots meta tag is inserted. The default is "index, follow", so not
 * putting it in reduced HTML bloat.
 * A Googlebot meta tag with "noindex, nofollow, noarchive, nosnippet"
 * is also output, to tell Google to clean out its cache.
 *
 * When you mark a page as "non cacheable", no-cache directives
 * are inserted via the Cache-Control and Pragma meta tags.
 */

// 
// ===== CONFIGURATION: ===================================

// Provide the content type setting.
// Can set this...
// e.g. $content_type = "text/html; charset=iso-8859-1";
// or have Etomite generate it for you
$content_type = "[*contentType*]; charset=[(etomite_charset)]";

// Provide the language setting.
$language = "en-au";

// Distribution can be "global", "local" or "iu"
// If you want no Distribution meta tag use ''
$distribution = 'global';

// Rating can be "14 years", "general", "mature", "restricted" or "safe for kids"
// If you want no Rating meta tag use ''
$rating = 'general';

// Start Date of the web site as used for the copyright meta tag
// To use the document creation date, set this to ''
$site_start_year = '2006';

// Site Author can be used for the Author and Copyright meta tags
// To use the document author details of each document, set this to ''
$site_author = 'Insite Architects Pty Ltd';

// Provide the full URL of your web site.
// For example: http://www.yourdomain.com/
// NOTE: Please put a / on the end of the web site URL.
// Used to build the DC.identifier tag
// This code worked with 0.6.1 RTM
// global $ETOMITE_PAGE_BASE;
// $websiteurl = $ETOMITE_PAGE_BASE['www'];
// This code works with 0.6.1 Prelude
$websiteurl = $etomite->config['www_base_path'];

// Legal Notice / Terms and Conditions Page
// for the no-email-collection meta tag.
// If you want no no-email-collection meta tag use ''
$legalterms = "legal-notices.htm";

// Favicon file name for links
// If you want no favicon links use ''
$favicon = "favicon.ico";

// Google Map Key
// If you want no Google Map meta tag use ''
$gmapkey = "ABQIAAAATHlEPcEjlaOVBbl3hAih5xSCipRF7c95Y7oXHbDT8JTwO0PaDRTU9loAjaq4zJ2Jof4w8k8ZFAcRVg";

// Google Sitemap Verification Key
// If you want no Google Sitemap Verification meta tag use ''
$gsitemapkey = "";

// Yahoo Sitemap Verification Key
// If you want no Yahoo Sitemap Verification meta tag use ''
$ysitemapkey = "";

// Provide the latitude of the resource
$latitude = "-37.76991667";

// Provide the longitude of the resource
$longitude = "145.04075";

// Provide the place name of the resource
$placename = "Ivanhoe, Victoria, Australia";

// Provide the ISO 3166 region code of the resource
$region = "au-vi";

// Safesurf Tags is used to specify if the SafeSurf
// meta tags should also be generated.
// Set to true to generate them, false otherwise.
$safesurf_tags = true;

// Cache Tags is used to specify if the Cache and Pragma
// meta tags should also be generated.
// Set to true to generate them, false otherwise.
$cache_tags = false;

// DC Tags is used to specify if the Dublin Core Metadata Initiative
// meta tags should also be generated.
// Set to true to generate them, false otherwise.
$dc_tags = true;

// Geo Tags is used to specify if the Geo Tags
// meta tags should also be generated.
// Set to true to generate them, false otherwise.
$geo_tags = true;

// ===== end of configuration =========

// ===== do not edit below except you know what you are doing ...
// and always BACKUP before changing!!
// ========================================================

// Initialise variables
$MetaType = "";
$MetaLanguage = "";
$MetaTitle = "";
$MetaGenerator = "";
$MetaDesc = "";
$MetaKeys = "";
$MetaAbstract = "";
$MetaAuthor = "";
$MetaCopyright = "";
$MetaRobots = "";
$MetaGooglebot = "";
$MetaCache = "";
$MetaPragma = "";
$MetaExpires = "";
$MetaEditedOn = "";
$MetaDistribution = "";
$MetaRating = "";
$MetaNoEC = "";
$MetaGMap = "";
$MetaGSMV = "";
$MetaYSMV = "";
$MetaSS = "";
$MetaFaviconLinks = "";

// The data format of the resource
$DC_format = "";

// The language of the content of the resource
$DC_language = "";

// The name given to the resource
$DC_title = "";

// A textual description of the content and/or purpose of the resource
// Equivalent to "description"
$DC_description = "";

// The subject and topic of the resource that succinctly
// describes the content of the resource.
// Equivalent to "keywords"
$DC_subject = "";

// Any form of the title used as a substitute or alternative
// to the formal title of the resource.
// Equivalent to "abstract"
$DC_title_alternative = "";

// The name of the entity responsible for making the resource available
// Equivalent to "author"
$DC_publisher = "";

// An entity primarily responsible for making the content of the resource
// Equivalent to "author"
$DC_creator = "";

// A statement or pointer to a statement about the
// rights management information for the resource
// Equivalent to "copyright"
$DC_rights = "";

// The date the resource was created in its current form
$DC_date_created = "";

// The date the resource was last modified or updated
$DC_date_modified = "";

// The date of validity of the resource.
// Specified as from the creation date to the expiry date
$DC_date_valid = "";

// A unique identifier for the resource
$DC_identifier = "";

// The latitude and longitude of the resource
$Geo_position = "";

// The latitude and longitude of the resource
$Geo_icbm = "";

// The place name of the resource
$Geo_placename = "";

// The region of the resource
$Geo_region = "";

// *** FUNCTIONS ***

function get_local_GMT_offset($server_offset_time)
{
 // Get the local GMT offset when given the
  // local to Etomite server offset time in seconds
    $GMT_offset = date("O");
    $GMT_hr = substr($GMT_offset, 1, 2);
    $GMT_min = substr($GMT_offset, 4, 2);
    $GMT_sign = substr($GMT_offset, 0, 1);
    $GMT_secs = (intval($GMT_hr) * 3600) + (intval($GMT_min) * 60);
    if ($GMT_sign == '-') {
         $GMT_secs = $GMT_secs * (-1);
    }

  // Get the local GMT offset in seconds
    $GMT_local_seconds = $GMT_secs + $server_offset_time;
    $GMT_local_secs = abs($GMT_local_seconds);

 // round down to the number of hours
    $GMT_local_hours = intval($GMT_local_secs / 3600);

 // round down to the number of minutes
    $GMT_local_minutes = intval(($GMT_local_secs - ($GMT_local_hours * 3600)) / 60);
    if ($GMT_local_seconds < 0) {
        $GMT_value = "-";
    } else {
         $GMT_value = "+";
    }
    $GMT_value .= sprintf("%02d:%02d", $GMT_local_hours, $GMT_local_minutes);
    return $GMT_value;
}

function get_local_iso_8601_date($int_date, $server_offset_time)
{
 // Return an ISO 8601 style local date
  // $int_date: current date in UNIX timestamp
    $GMT_value = get_local_GMT_offset($server_offset_time);
    $local_date = date("Y-m-d\TH:i:s", $int_date + $server_offset_time);
    $local_date .= $GMT_value;
    return $local_date;
}

function get_local_rfc_822_date($int_date, $server_offset_time)
{
 // return an RFC 822 style local date
 // $int_date: current date in UNIX timestamp
    $GMT_value = get_local_GMT_offset($server_offset_time);
    $local_date = date("D, d M Y H:i:s", $int_date + $server_offset_time);
    $local_date .= " " . str_replace(':', '', $GMT_value);
    return $local_date;
}

// ########################################################
// === here begins the actual work: ===

// *** Start Creating Meta Tags ***

// *** CONTENT-TYPE ***
$MetaType = " <meta http-equiv=\"content-type\" content=\"" . $content_type . "\" />\n";

// *** DC.FORMAT ***
if ($dc_tags) {
    $DC_format = " <meta name=\"DC.format\" content=\"" . $content_type . "\" />\n";
}

// *** CONTENT-LANGUAGE ***
$MetaLanguage = " <meta http-equiv=\"content-language\" content=\"" . $language . "\" />\n";

// *** DC.LANGUAGE ***
if ($dc_tags) {
    $DC_language = " <meta name=\"DC.language\" content=\"" . $language . "\" />\n";
}

// *** GENERATOR ***
$version = $etomite->getVersionData();
$generator = trim($version['full_appname']);
if (($generator != "")) {
    $MetaGenerator = " <meta name=\"generator\" content=\"";
    $MetaGenerator .= $generator . "\" />\n";
}

$docInfo = $etomite->getDocument($etomite->documentIdentifier);

// *** DESCRIPTION ***
// Trim and replace double quotes with entity
$description = $docInfo['description'];
$description = str_replace('"', '&#34;', trim($description));
if (!$description == "") {
    $MetaDesc = " <meta name=\"description\" content=\"$description\" />\n";
 // *** DC.DESCRIPTION ***
    if ($dc_tags) {
         $DC_description = " <meta name=\"DC.description\"";
         $DC_description .= " content=\"$description\" />\n";
    }
}

// *** KEYWORDS ***
$keywords = $etomite->getKeywords();
if (count($keywords) > 0) {
    asort($keywords);
    $keys = join($keywords, ", ");
    $MetaKeys = " <meta name=\"keywords\" content=\"$keys\" />\n";
 // *** DC.SUBJECT ***
    if ($dc_tags) {
         $keys = join($keywords, "; ");
          $DC_subject = " <meta name=\"DC.subject\"";
         $DC_subject .= " content=\"$keys\" />\n";
    }
}

// *** ABSTRACT ***
// Use the Site Name and the documents Long Title (or Description)
// to build an Abstract meta tag.
$sitename = $etomite->config['site_name'];

// Trim and replace double quotes with entity
$sitename = str_replace('"', '&#34;', trim($sitename));

$abstract = trim($docInfo['longtitle']);
if ($abstract == "") {
    $abstract = $description;
}

// Replace double quotes with entity
$abstract = str_replace('"', '&#34;', $abstract);

if (($sitename != "") || ($abstract != "")) {
    $separator = " - ";
    if ($sitename == "") {
          $separator = "";
    }
    $MetaAbstract = " <meta name=\"abstract\" content=\"" . $sitename . $separator . $abstract . "\" />\n";

  // *** DC.TITLE.ALTERNATIVE ***
    if ($dc_tags) {
         $DC_title_alternative = " <meta name=\"DC.title.alternative\"";
         $DC_title_alternative .= " content=\"" . $sitename . $separator . $abstract . "\" />\n";
    }
}

// *** TITLE ***
// Use the Site Name and the documents Page Title and Long Title
// to build the Title meta tag.
// Start with the site name
$title = $sitename;

// Get the pagetitle, trim and replace double quotes with entity
$pagetitle = str_replace('"', '&#34;', trim($docInfo['pagetitle']));
if ($pagetitle != "") {
    if ($title == "") {
        $title = $pagetitle;
    } else {
        $title .= " - " . $pagetitle;
    }
}

// Get the longtitle, trim and replace double quotes with entity
$longtitle = str_replace('"', '&#34;', trim($docInfo['longtitle']));
if ($longtitle != "") {
    if ($title == "") {
         $title = $longtitle;
    } else {
        $title .= " - " . $longtitle;
    }
}
if ($title != "") {
    $MetaTitle = " <title>" . $title . "</title>\n";
 // *** DC.TITLE ***
    if ($dc_tags || $geo_tags) {
          $DC_title = " <meta name=\"DC.title\"";
         $DC_title .= " content=\"" . $title . "\" />\n";
    }
}

// *** AUTHOR ***
if ($site_author == '') {
    $authorid = $docInfo['createdby'];
    $tbl = $etomite->dbConfig['dbase'] . "." . $etomite->dbConfig['table_prefix'] . "user_attributes";
    $query = "SELECT fullname FROM " . $tbl . " WHERE " . $tbl . ".id = " . $authorid . ";";
    $rs = $etomite->dbQuery($query);
    $limit = $etomite->recordCount($rs);
    if ($limit == 1) {
        $resourceauthor = $etomite->fetchRow($rs);
        $authorname = $resourceauthor['fullname'];
    }
  // Trim and replace double quotes with entity
    $authorname = str_replace('"', '&#34;', trim($authorname));
} else {
    $authorname = $site_author;
}
if (!$authorname == "") {
    $MetaAuthor = " <meta name=\"author\" content=\"$authorname\" />\n";
 // *** DC.PUBLISHER & DC.CREATOR ***
    if ($dc_tags) {
        $DC_publisher = " <meta name=\"DC.publisher\" content=\"$authorname\" />\n";
        $DC_creator = " <meta name=\"DC.creator\" content=\"$authorname\" />\n";
    }
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
        $copydate = $createdon . "-" . $today_year;
    } else {
         $copydate = $today_year;
    }
} else {
    if ($today_year != $site_start_year) {
        $copydate = $site_start_year . "-" . $today_year;
    } else {
         $copydate = $today_year;
    }
}
if ($authorname == '') {
    $copyname = $authorname;
} else {
    $copyname = " by " . $authorname;
}
$MetaCopyright = " <meta name=\"copyright\" content=\"Copyright (c) ";
$MetaCopyright .= $copydate . $copyname . ". All rights reserved.\" />\n";

// *** DC.RIGHTS ***
if ($dc_tags) {
    $DC_rights = " <meta name=\"DC.rights\" content=\"Copyright (c) ";
    $DC_rights .= $copydate . $copyname . ". All rights reserved.\" />\n";
}

// *** ROBOTS and GOOGLEBOT ***
// Determine if this document has been set to non-searchable.
// As the default for the Robots and Googlebot Meta Tags are index and follow,
// these tags are only needed when we don't want the document searched.
if (!$etomite->documentObject['searchable']) {
    $MetaRobots = " <meta name=\"robots\" content=\"noindex, nofollow\" />\n";
    $MetaGooglebot = " <meta name=\"googlebot\" content=\"noindex, nofollow, noarchive, nosnippet\" />\n";
}

// *** CACHE-CONTROL and PRAGMA ***
// Output no-cache directives via the Cache-Control and Pragma meta tags
// if this document is set to non-cacheable.
if ($cache_tags) {
    $cacheable = $docInfo['cacheable'];
    if (!$cacheable) {
          $MetaCache = " <meta http-equiv=\"cache-control\" content=\"no-cache\" />\n";
          $MetaPragma = " <meta http-equiv=\"pragma\" content=\"no-cache\" />\n";
    }
}

// *** DC.DATE.CREATED ***
if ($dc_tags) {
    $createdon = get_local_iso_8601_date($docInfo['createdon'], $server_offset_time);
    $created = substr($createdon, 0, 10);
    $DC_date_created = " <meta name=\"DC.date.created\" content=\"";
    $DC_date_created .= $created . "\" />\n";
}

// *** EXPIRES ***
$unpub_date = $docInfo['unpub_date'];
if ($unpub_date > 0) {
    $unpubdate = get_local_rfc_822_date($unpub_date, $server_offset_time);
    $MetaExpires = " <meta http-equiv=\"expires\" content=\"$unpubdate\" />\n";
  // *** DC.DATE.VALID ***
    if ($dc_tags) {
        $dcunpubdate = get_local_iso_8601_date($unpub_date, $server_offset_time);
        $valid = substr($dcunpubdate, 0, 10);
        $DC_date_valid = " <meta name=\"DC.date.valid\" content=\"";
        $DC_date_valid .= $created . "/" . $valid . "\" />\n";
    }
}

// *** LAST MODIFIED ***
$editedon = get_local_rfc_822_date($docInfo['editedon'], $server_offset_time);
$MetaEditedOn = " <meta http-equiv=\"last-modified\" content=\"$editedon\" />\n";

// *** DC.DATE.MODIFIED ***
if ($dc_tags) {
    $dceditedon = get_local_iso_8601_date($docInfo['editedon'], $server_offset_time);
    $modified = substr($dceditedon, 0, 10);
    $DC_date_modified = " <meta name=\"DC.date.modified\" content=\"";
    $DC_date_modified .= $modified . "\" />\n";
}

// *** DISTRIBUTION ***
if (!$distribution == '') {
    $MetaDistribution = " <meta name=\"distribution\" content=\"" . $distribution . "\" />\n";
}

// *** RATING ***
if (!$rating == '') {
    $MetaRating = " <meta name=\"rating\" content=\"" . $rating . "\" />\n";
}

// *** NO EMAIL COLLECTION ***
if (!$legalterms == '') {
    $MetaNoEC = " <meta name=\"no-email-collection\" content=\"" . $websiteurl . $legalterms . "\" />\n";
}

// *** GOOGLE MAP KEY ***
if (!$gmapkey == '') {
    $MetaGMap = " <meta name=\"gmapkey\" content=\"" . $gmapkey . "\" />\n";
}

// *** GOOGLE SITEMAP VERIFICATION KEY ***
if (!$gsitemapkey == '') {
    $MetaGSMV = " <meta name=\"verify-v1\" content=\"" . $gsitemapkey . "\" />\n";
}

// *** YAHOO SITEMAP VERIFICATION KEY ***
if (!$ysitemapkey == '') {
    $MetaYSMV = " <meta name=\"y_key\" content=\"" . $ysitemapkey . "\" />\n";
}

// *** SAFESURF CODE ***
// Note: This is a default general SafeSurf and ICRA tag. If your web site requires
// something different, you'll need to change this.
if ($safesurf_tags) {
    $MetaSS = "\n <link rel=\"meta\" href=\"" . $websiteurl . "labels.rdf\" 
  type=\"application/rdf+xml\" title=\"ICRA labels\" />
 <meta http-equiv=\"PICS-Label\" content='(PICS-1.1
  \"http://www.icra.org/pics/vocabularyv03/\" l gen true 
  for \"" . $websiteurl . "\" r (n 0 s 0 v 0 l 0 oa 0 ob 0 oc 0 od 0 oe 0 of 0 og 0 oh 0 c 0) 
  gen true for \"" . $websiteurl . "\" r (n 0 s 0 v 0 l 0 oa 0 ob 0 oc 0 od 0 oe 0 of 0 og 0 oh 0 c 0)
  \"http://www.classify.org/safesurf/\" L gen true 
  for \"" . $websiteurl . "\" r (SS~~000 1)
 )' />\n";
}

// *** FAVICON FILE LINKS ***
if (!$favicon == '') {
    $MetaFaviconLinks = "\n <link rel=\"shortcut icon\" href=\"" . $favicon . "\" type=\"image/x-icon\" />\n";
}

if ($dc_tags) {
 // *** DC.IDENTIFIER ***
    $url = $websiteurl . "[~" . $etomite->documentIdentifier . "~]";
    $DC_identifier = " <meta name=\"DC.identifier\" content=\"" . $url . "\" />\n";
}

if ($geo_tags) {
    if (($latitude != "") && ($longitude != "")) {
        // *** GEO.ICBM ***
        $Geo_icbm = " <meta name=\"ICBM\"";
        $Geo_icbm .= " content=\"" . $latitude . ", " . $longitude . "\" />\n";
        // *** GEO.POSITION ***
        $Geo_position = " <meta name=\"geo.position\"";
        $Geo_position .= " content=\"" . $latitude . ";" . $longitude . "\" />\n";
    }

    if ($region != "") {
        // *** GEO.REGION ***
        $Geo_region = " <meta name=\"geo.region\"";
        $Geo_region .= " content=\"" . $region . "\" />\n";
    }

    if ($placename != "") {
       // *** GEO.PLACENAME ***
        $Geo_placename = " <meta name=\"geo.placename\"";
        $Geo_placename .= " content=\"" . $placename . "\" />\n";
    }
}

// *** RETURN RESULTS ***
$output = $MetaType . $MetaLanguage . $MetaGenerator;
$output .= $MetaTitle . $MetaDesc . $MetaKeys;
$output .= $MetaAbstract . $MetaAuthor . $MetaCopyright;
$output .= $MetaRobots . $MetaGooglebot;

if ($cache_tags) {
    $output .= $MetaCache . $MetaPragma;
}

$output .= $MetaExpires . $MetaEditedOn;
$output .= $MetaDistribution . $MetaRating;
$output .= $MetaNoEC . $MetaGMap;
$output .= $MetaGSMV . $MetaYSMV;

if ($dc_tags) {
    $dc_output = $DC_format . $DC_language . $DC_title;
    $dc_output .= $DC_description . $DC_subject . $DC_title_alternative;
    $dc_output .= $DC_publisher . $DC_creator . $DC_rights;
    $dc_output .= $DC_date_created . $DC_date_modified . $DC_date_valid;
    $dc_output .= $DC_identifier;
    if ($dc_output != "") {
         $output .= " \n" . $dc_output;
    }
}

if ($geo_tags) {
    $geo_output = "";
    if (!$dc_tags) {
          $geo_output .= $DC_title;
    }
    $geo_output .= $Geo_icbm;
    $geo_output .= $Geo_position . $Geo_region . $Geo_placename;
    if ($geo_output != "") {
        $output .= " \n" . $geo_output;
    }
}

$output .= $MetaSS;
$output .= $MetaFaviconLinks;

return $output;
