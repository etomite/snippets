//<?php

/**
 * GoogleSiteMap_XML Snippet for Etomite CMS
 * Version 0.8 2006-11-17
 *
 * Parameters:
 * [!GoogleSiteMap_XML?validate=true!] or [!GoogleSiteMap_XML?validate=1!]
 * tells the snippet to output the additional headers required to validate
 * your Sitemap file against a schema.
 *
 * Useage:
 * Create a snippet: GoogleSiteMap_XML
 * with the content of this file.
 * Update the configuration options below to suit your needs.
 * Create a template: GoogleSiteMap_Template
 * with the content "[!GoogleSiteMap_XML!]".
 * Create a page in your repository: Google Site Map
 * with no content, the alias "google-sitemap",
 * using the GoogleSiteMap_Template, not searchable,
 * not cacheable, with content type "text/xml".
 *
 * Goto the Google Webaster Tools site at https://www.google.com/webmasters/tools/
 * Create an account, or login using your existing account.
 * Enter http://www.<your domain name>/ in the add site box and click OK.
 * Click on "Verify your site".
 * Choose "Add a META tag" as your verification option.
 * Add the generated meta tag to the head section of your home page template.
 * Back in Google Webmaster Tools, click on "Verify".
 * Click on the "Sitemaps" button.
 * Click on "Add a Sitemap".
 * Select "Add General Web Sitemap".
 * Enter "http://www.<your domain name>/google-sitemap.htm" as your sitemap URL.
 * Click on "Add Web Sitemap".
 *
 *
 * Ryan Nutt - http://blog.nutt.net
 * v0.1 - June 4, 2005
 * v0.2 - June 5, 2005 - Fixed a stupid mistake :-)
 *
 * Changes by Lloyd Borrett - http://www.borrett.id.au
 *
 * v0.3 - Sep 22, 2005
 * Only list searchable pages (Mod suggested by mplx)
 * Added configuration settings.
 * Made the site URL a configuration option.
 * Made displaying lastmoddate, priority and/or changefreq optional.
 * Added ability to display long date & time for lastmoddate
 * Made the long or short timeformat optional.
 *
 * v0.4 - 05-Feb-2006
 * Changed the snippet to output the local time for all date values
 * based on the Etomite server offset time
 *
 * v0.5 - 15-Feb-2006
 * Fixed incorrect local GMT offset value
 *
 * v0.6 - 7-Apr-2006
 * Get the base URL from Etomite instead of it being a configuration option.
 *
 * v0.7 - 30-Apr-2006
 * Get the base URL from Etomite using the new available
 * method built in to Etomite 0.6.1 Final. If using an earlier
 * version of Etomite, you'll still need to provide the URL
 * as a configuration option.
 *
 * v0.8 - 17-Nov-2006
 * Updated to identify itself as using the Sitemap 0.9 protocol.
 * Added ability to force the change frequency to a set value for all documents.
 * Added ability to output the additional headers required to validate the sitemap format.
 * Additional comments added.
 * Code layout made consistent.
 *
 * Based on the ListSiteMap snippet by
 * JaredDC
 *
 * datediff function from
 * www.ilovejackdaniels.com
 */

// Overcome single use limitation on functions
global $MakeMapDefined;

// Get the validate parameter, if any
$validateschema = false;
if (isset($validate)) {
    if (($validate == "1") || ($validate == "true")) {
        $validateschema = true;
    }
}

// Determine values required to convert the lastmod date and
// time to local time. 
// get the Etomite server offset time in seconds
global $server_offset_time;
global $GMT_value;
$server_offset_time = $etomite->config['server_offset_time'];
if (!$server_offset_time) {
    $server_offset_time = 0;
}

// Get the server GMT offset in seconds
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

if (!function_exists(datediff)) {
    function datediff($interval, $datefrom, $dateto, $using_timestamps = false)
    {
        /**
         * $interval can be:
         * yyyy - Number of full years
         * q - Number of full quarters
         * m - Number of full months
         * y - Difference between day numbers
         * (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
         * d - Number of full days
         * w - Number of full weekdays
         * ww - Number of full weeks
         * h - Number of full hours
         * n - Number of full minutes
         * s - Number of full seconds (default)
         */

        if (!$using_timestamps) {
            $datefrom = strtotime($datefrom, 0);
            $dateto = strtotime($dateto, 0);
        }

        $difference = $dateto - $datefrom; // Difference in seconds
        
        switch ($interval) {
            case 'yyyy': // Number of full years
                $years_difference = floor($difference / 31536000);
                if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom) + $years_difference) > $dateto) {
                    $years_difference--;
                }
                if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto) - ($years_difference + 1)) > $datefrom) {
                    $years_difference++;
                }
                $datediff = $years_difference;
                break;

            case "q": // Number of full quarters
                $quarters_difference = floor($difference / 8035200);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($quarters_difference * 3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }
                $quarters_difference--;
                $datediff = $quarters_difference;
                break;

            case "m": // Number of full months
                $months_difference = floor($difference / 2678400);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }
                $months_difference--;
                $datediff = $months_difference;
                break;

            case 'y': // Difference between day numbers
                $datediff = date("z", $dateto) - date("z", $datefrom);
                break;

            case "d": // Number of full days
                $datediff = floor($difference / 86400);
                break;

            case "w": // Number of full weekdays
                $days_difference = floor($difference / 86400);
                $weeks_difference = floor($days_difference / 7); // Complete weeks
                $first_day = date("w", $datefrom);
                $days_remainder = floor($days_difference % 7);
                $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
                if ($odd_days > 7) { // Sunday
                    $days_remainder--;
                }
                if ($odd_days > 6) { // Saturday
                    $days_remainder--;
                }
                $datediff = ($weeks_difference * 5) + $days_remainder;
                break;

            case "ww": // Number of full weeks
                $datediff = floor($difference / 604800);
                break;

            case "h": // Number of full hours
                $datediff = floor($difference / 3600);
                break;

            case "n": // Number of full minutes
                $datediff = floor($difference / 60);
                break;

            default: // Number of full seconds (default)
                $datediff = $difference;
                break;
        }

        return $datediff;
    }
}

if (!isset($MakeMapDefined)) {
    function MakeMap($funcEtomite, $listParent)
    {
        global $server_offset_time;
        global $GMT_value;

        // ***********************************
        // Configuration Settings
        // ***********************************

        // $websiteURL [string]
        // Provide the full base path URL of your web site,
        // or let Etomite get it (v0.6.1 Final).
        // For example: http://www.yourdomain.com/
        // NOTE: You must put a / on the end of the web site URL.
        //
        // Original hard coded way to specify $websiteURL
        // $websiteurl = 'http://www.<your domain name>/';
        //
        // Early Etomite way to get $websiteURL automatically
        // $websiteurl = $etomite->config['www_base_path'];
        //
        // Etomite 0.6.1 Final way to get $websiteURL automatically
        global $ETOMITE_PAGE_BASE;
        $websiteurl = $ETOMITE_PAGE_BASE['www'];

        // $showlastmoddate [true | false]
        // You can choose to disable providing the last modification
        // date, or get it from the documents.
        // true  - Get time from documents
        // false - Disabled, do not write it
        $showlastmoddate = true;

        // $showlongtimeformat [ true | false ]
        // You can choose to provide the time format as:
        // true  - Long time format (with time, e.g. 2006-09-29T13:43:51+11:00)
        // false - Short time format (date only, e.g. 2006-11-17)
        $showlongtimeformat = true;

        // $showpriority [ true | false ]
        // You can choose to disable prividing the priority
        // of a document relative to the whole set of documents,
        // or calculate it based on the date difference.
        // true  - Provide the priority
        // false - Disabled, do not write it
        $showpriority = true;

        // $showchangefreq [true | false]
        // You can choose to disable prividing the update
        // (change) frequency of a document relative to the
        // whole set of documents, or calculate it based on
        // the date difference.
        // true  - Provide the change frequency
        // false - Disabled, do not write it
        $showchangefreq = true;

        // $forcechangefreq [string]
        // You can choose to force the change frequency for all
        // documents to one of the valid values.
        // By specifying nothing, the snippet will calculate the
        // change frequency of a document relative to the
        // whole set of documents, or calculate it based on
        // the date difference.
        // "always", "hourly", "daily", "weekly", "monthly",
        // "yearly", "never" - Force this value for every document
        // "" - Calculate change frequency from last mod date
        $forcechangefreq = "";

        // ***********************************
        // END CONFIG SETTINGS
        // THE REST SHOULD TAKE CARE OF ITSELF
        // ***********************************

        $children = $funcEtomite->getActiveChildren($listParent, "menuindex", "ASC", "id, editedon, searchable");
        foreach ($children as $child) {
            $id = $child['id'];
            $url = $websiteurl . "[~" . $id . "~]";

            $date = $child['editedon'];
            $lastmoddate = $date;
            $date = date("Y-m-d", $date);

            $searchable = $child['searchable'];
            if ($searchable) {
                // Get the date difference
                $datediff = datediff("d", $date, date("Y-m-d"));
                if ($datediff <= 1) {
                    $priority = "1.0";
                    $update = "daily";
                } elseif (($datediff > 1) && ($datediff <= 7)) {
                    $priority = "0.75";
                    $update = "weekly";
                } elseif (($datediff > 7) && ($datediff <= 30)) {
                    $priority = "0.50";
                    $update = "weekly";
                } else {
                    $priority = "0.25";
                    $update = "monthly";
                }

                $output .= "<url>\n";

                $output .= "<loc>$url</loc>\n";

                if ($showlastmoddate) {
                    if (!$showlongtimeformat) {
                        $lastmoddate = date("Y-m-d", $lastmoddate + $server_offset_time);
                    } else {
                        $lastmoddate = date("Y-m-d\TH:i:s", $lastmoddate + $server_offset_time) . $GMT_value;
                    }
                    $output .= "<lastmod>$lastmoddate</lastmod>\n";
                }

                if ($showchangefreq) {
                    if ($forcechangefreq == "") {
                        $output .= "<changefreq>$update</changefreq>\n";
                    } else {
                        $output .= "<changefreq>$forcechangefreq</changefreq>\n";
                    }
                }

                if ($showpriority) {
                    $output .= "<priority>$priority</priority>\n";
                }

                $output .= "</url>\n";
            }

            if ($funcEtomite->getActiveChildren($child['id'])) {
                $output .= MakeMap($funcEtomite, $child['id']);
            }
        }
        return $output;
    }
    $MakeMapDefined = true;
}

$out = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
if ($validateschema) {
    $out .= "<urlset xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n";
    $out .= "         xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemaps/0.9\n";
    $out .= "         http://www.sitemaps.org/schemas/sitemaps/sitemap.xsd\"\n";
    $out .= "         xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
} else {
    // $out .= "<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">\n";
    $out .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
}

// Produce the sitemap for the main web site
$out .= MakeMap($etomite, 0);

// To also list documents in unpublished repository folders,
// place an additional call to MakeMap here for each one, e.g. 
// $out .= MakeMap($etomite, 8);
// where 8 is the document id of the unpublished repository folder.

$out .= "</urlset>";

return $out;
