//<?php

# PullDownMenu - Etomite 0.6 - 2004
# Created By: Jeroen Bosch - bosch.jeroen@gmail.com
# Part of PullDownMenu - MemberLogin - MemberLogout - menu.inc.js Snippet Suite
# Edited by Ralph A. Dahlgren - rad14701@yahoo.com - and Jeroen Bosch to
# fit the PullDownMenu. You need 4 files to make this menu work.
# for full explanation visit http://bosch.ondergrond.net/ else view
# separated readme.txt for installation details.

# Config
# $siteRoot [int]
# The parent ID of your root. Default 0.
 $siteRoot = 0;

# ID of Menu Folder Document for log prompt display - Set to -1 for no prompt
$logPromptMenuID = 76;

# Do you want an extra logout button in each membermenu?
$extraLogPrompt=false;

# ID's of the documents containing the [[MemberLogin]] and [[MemberLogout]] snippet calls
$loginPageID = 16;
$logoutPageID = 17;

# Login and Logout text strings to display if required
$loginText = "Login";
$logoutText = "Logout";

# No changes need to be made below here for normal operation

# If Etomite Folder ID is not sent, use ID of current document (folder)
if (!isset($id)) {
    $id = $etomite->documentIdentifier;
}
$listParent=$id; // just to keep my head from spinning

# If $deny array was sent, verify member authorization, else return null result
if (isset($deny)) {
    $deny = explode(",", $deny);
    if (in_array($_SESSION['MemberName'], $deny)) {
          return null;
    }
}

# If $allow array was sent, verify member authorization, else return null result
if (isset($allow)) {
    $allow = explode(",", $allow);
    if (!in_array($_SESSION['MemberName'], $allow)) {
          return null;
    }
}


# Check to see if login authorization is required and successful
if ((($auth == "1") && ($_SESSION['MemberLoggedIn'] == true)) || (($auth == "") || ($auth =="0"))) {
# Here is where I made the big change
# as you can see I removed the function call and
# used the big if-statement (above) to start the
# the code. I know, it looks awfull (like someone
# who doesn't know what recursion is)
# But it does the job and it does it well.

    if ($title !="") {
           $output = '<div class="navigationHead2"><span>'. $title.'</span></div>';
    } else {
        $output = "";
    }
    
    $output .= '<div class="navigation2"><ul id="'.$title.'">';
    
    $fields='id,pagetitle,isfolder'; // I use the isfolder to determine if you need to do another query. (thus not by a query)
    $children = $etomite->getActiveChildren($listParent, 'menuindex', 'ASC', $fields);
 
    foreach ($children as $child) {
        $output .= '<li><a href="[~'.$child['id'].'~]">'.$child['pagetitle'].'</a>'; //  print name in menu
 
        if ($child['isfolder']) { // check for subfolders (this is where you should want the recursion
              $children2 = $etomite->getActiveChildren($child['id'], 'menuindex', 'ASC', $fields); // Get those kids!!
            $output .='<ul>';// make new submenu
    
            foreach ($children2 as $child2) {
                $output .= '<li><a href="[~'.$child2['id'].'~]">'.$child2['pagetitle'].'</a></li>'; //  print name in menu
            }
    
            if ($child['id'] == $logPromptMenuID) { // You want the login prompt in this submenu?
                if ($_SESSION['MemberLoggedIn'] == true) {
                    $output.= '<li><a href="[~'.$logoutPageID.'~]">'.$logoutText.'</a></li>';
                } else {
                    $output.='<li><a href="[~'.$loginPageID.'~]">'.$loginText.'</a></li>';
                }
            }
    
            $output .= '</ul>'; // end submenu
        }

        if ($listParent == $logPromptMenuID) { // Or do you want it here?
            if ($_SESSION['MemberLoggedIn'] == true) {
                $output.= '<li><a href="[~'.$logoutPageID.'~]">'.$logoutText.'</a></li>';
            } else {
                $output.='<li><a href="[~'.$loginPageID.'~]">'.$loginText.'</a></li>';
            }
        }
        $output .= '</li>'; // looks silly, but submenu's must be nested in one list-item (IE doesn't care, firefox does ;)
    }
    if ($extraLogPrompt==true && $auth==true) {
        $output .='<li><a href="[~'.$logoutPageID.'~]">'.$logoutText.'</a></li>';
    }

    $output .= '</ul></div>';
    return $output;
}
    
# END of code
