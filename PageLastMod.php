//<?php

// by: Jeroen Bosch
// date: 10 feb 2005
// snippet: HomeMadeMenu
// use: [[PageLastMod]]
// displays the date the current document was last edited
// like: Feb 10th 2005
// See http://www.php.net/date if you want to change
// the format.

$output="<span class='PageLastMod'>".Date("M jS Y", $etomite->documentObject['editedon'])."</span>";

return $output;
