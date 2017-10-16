//<?php

// Snippet name: SearchPrompt
// Snippet description: Search prompt snippet for use with SearchResults
// Revision: 1.00 ships with Etomite 0.6.1-Final
// Use: [!SearchPrompt?resultsid=###!] snippet call where ### is the
//   document id of the page which contains the [!SearchResults!] snippet call

$resultsDefault = "14";  // Document id to use if $resultsid not sent
$resultsid = isset($resultsid) ? $resultsid : $resultsDefault;

$prompt = "Search this site";  // Search box label text
$submit = "Search";  // Submit button label

$output =
<<<END
<form id="SearchForm" action="[~{$resultsid}~]" method="post"> 
  <div class="searchbox" style="text-align:center;">
    <p>{$prompt}</p>
    <p><input type="text" name="search" value="" /></p>
    <p><input type="submit" name="sub" class="button" value="{$submit}" /></p>
  </div>
</form>
END;

return $output;
