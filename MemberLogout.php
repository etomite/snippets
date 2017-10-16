//<?php

# MemberLogout - Etomite 0.6 - 2004-09-07
# Created By: Ralph A. Dahlgren - rad14701@yahoo.com
# Part of MemberMakeMenu - MemberLogin - MemberLogout Snippet Suite
# Edited by Ralph and Jeroen Bosch to fit the PullDownMenu
# Member logout snippet - Doesn't return anything

$_SESSION['MemberLoggedIn'] = false;
header( "Location: index.php" );

# END of Code
