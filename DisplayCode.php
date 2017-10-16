//<?php

// Snippet: DisplayCode
// Purpose: Text display of Chunk, Snippet, or Template code
// Created By: Ralph A. Dahlgren
// Last Modified: 2008-04-17
// Usage: [[DisplayCode?type=chunk|file|snippet|template&name=Name]]
// Note: Remember that this snippet uses resource names instead of id's so name changes will effect existing snippet calls
// Also: Files use absolute paths so use ./path/from/Etomite/root

// Snippet messages (the messages below were used for development purposes)
$usage = "Error -- Usage: [ [DisplayCode?type=chunk|file|snippet|template&name=Name] ]";
$error = "Error -- " . $type ." '" . $name . "' is either an invalid name or is empty.";

// Set fieldset amd legend styles (can be inline style or reference to stylesheet components)
// $fieldsetCSS = "class='contentarea'"; // example using a CSS class
$fieldsetCSS = "style='background-color:#ffffff; border:1px solid #000000;'";
$legendCSS = "style='background-color:#ffffff; border:1px solid #000000;'";

// Define which Etomite tags to be rendered non-functional in the returned code
// $oldtags are listed in their original format
$oldtags = array("[[","[!","[*","[(","[~","{{","[^");
// $newtags are listed with a blank space between tag elements
$newtags = array("[ [","[ !","[ *","[ (","[ ~","{ {","[ ^");

// If Chunk or Template
if (($type == "template") || ($type == "chunk")) {
    if ($name == '') {
        return $usage;
    } else {
        // Set the appropriate field to extract from the record
        $field = ($type == "template") ? "content" : "snippet";
        // Query the appropriate database table for the desired record
        if ($type == "template") {
            $rs = $etomite->getIntTableRows($field, "site_templates", "templatename='$name'");
        } elseif ($type == "chunk") {
            $rs = $etomite->getIntTableRows($field, "site_htmlsnippets", "name='$name'");
        }
        // If a record was returned then get the code
        if (is_array($rs)) {
            $code = str_replace($oldtags, $newtags, $rs[0][$field]);
            $code = "<div class=\"code\">".nl2br(str_replace(" ", "&nbsp;", htmlentities($code)))."</div>";
        } else {
            return $error;
        }
    }
}

// If Snippet
if ($type == "snippet") {
    if ($name == '') {
        return $usage;
    } else {
        // Cache snippet code into a variable
        $rs = $etomite->getIntTableRows("snippet", "site_snippets", "name='$name'");
        // If snippet code is not found, display message
        if (!is_array($rs)) {
            return $error;
        } // If snippet code exists, process it for display
        else {
            // Add PHP Begin and End tags and use highlight_string() to colorize the code
            $code = highlight_string("<?php\n".chr(13).str_replace($oldtags, $newtags, $rs[0]['snippet'])."?>", true);
            $code = str_replace("<code>", "", $code);
            $code = str_replace("</code>", "", $code);
        }
    }
}

// If File
if ($type == "file") {
    if ($name == '') {
        return $usage;
    } else {
        // If snippet code is not found, display message
        $code = file_get_contents($name);
        if ($code == "") {
            return $error;
        } else {
            // If this is PHP then highlight the code
            if ((substr($name, -3) == "php") || (substr($name, -4) == "phps")) {
                $code = highlight_string(str_replace($oldtags, $newtags, $code), true);
            } // All other code gets processed for display
            else {
                $code = "<code>".str_replace(" ", "&nbsp;", htmlentities(str_replace($oldtags, $newtags, $code)))."</code>";
            }
        }
    }
}

// Open a fieldset, insert code, and return
$type = ucfirst($type);
return "<fieldset><legend>{$type}: {$name}</legend>{$code}</fieldset>";
