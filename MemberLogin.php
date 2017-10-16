//<?php

# MemberLogin - Etomite 0.6 - 2004-09-07
# Created By: Ralph A. Dahlgren - rad14701@yahoo.com
# Part of MemberMakeMenu - MemberLogin - MemberLogout Snippet Suite
# Edited by Ralph and Jeroen Bosch to fit the PullDownMenu
# Allows login of Etomite Users/Admins
# Sets $_SESSION['MemberLoggedIn'] session variable TRUE on SUCCESS
# Repeats on FAILURE

# Array of Etomite Users/Admins not allowed to login here - modify as required
$disallow = array("secure","guestbook");

# Snippet variable assignments - Should not require changes
$LoginName = $_POST['LoginName'];
$LoginPassword = $_POST['LoginPassword'];
$formaction = "index.php";
$pageid=$etomite->documentIdentifier; # ID of the calling document
$tbl = $etomite->dbConfig['dbase'].".".$etomite->dbConfig['table_prefix'];

if (($LoginName != "") && (LoginPassword != "")) {
  # Query for the user ID (id) for the GuestBook owner
    $sql = "SELECT * FROM ".$tbl."manager_users WHERE ".$tbl."manager_users.username='$LoginName';";
    $rs = $etomite->dbQuery($sql);
    $limit = $etomite->recordCount($rs);

  # Check to make sure $LoginName has been created and assigned and check $LoginPassword
    if ($limit == 1) {
        $userrec = $etomite->fetchRow($rs);

        # Valid LoginUsername and LoginPassword found
        if (md5($LoginPassword) === $userrec['password']) {
            $output = true;
            $_SESSION['MemberLoggedIn'] = true;
            header( "Location: index.php" );
        } # LoginPassword incorrect
        else {
            $output = false;
            $_SESSION['MemberLoggedIn'] = false;
            header( "Location: index.php" );
        }
    } # LoginName not found
    else {
        $output = false;
        $_SESSION['MemberLoggedIn'] = false;
        header( "Location: index.php" );
    }
} # Create login prompt form
else {
    $output = "
  <fieldset><legend>Member Login</legend>
  <form name='guestbook' method='post' action='$formaction'>
    <input type='hidden' name='id' value='$pageid' />
    <table align='center' cellpadding='0' cellspacing='0' class='body'>
      <tr>
        <td align='right' valign='middle'>Username:</td>
        <td><input type='text' name='LoginName' class='textbox' style='width:100px' /></td>
      </tr>
      <tr>
        <td align='right' valign='middle'>Password:</td>
        <td><input type='password' name='LoginPassword' class='textbox' style='width:100px' /></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align='center'>
          <input type='submit' name='submit' value='Submit' class='button' />
        </td>
      </tr>
    </table>
  </form></fieldset>\n";
}

# If user is not allowed to access this menu, login fails without notification of reason
if (in_array($LoginName, $disallow)) {
    $output = false;
    $_SESSION['MemberLoggedIn'] = false;
    header( "Location: index.php" );
}

# Return Binanry Logic Result
return $output;

# END of Code
