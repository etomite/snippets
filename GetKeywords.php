//<?php

// Snippet: GetKeywords
// Purpose: Fetches the keywords attached to the document.
// Revision: 1.1

$results = $etomite->getKeywords();

if (count($results) > 0) {
    $keywords = join($results, ",");
    return "<meta http-equiv=\"keywords\" content=\"{$keywords}\" />";
}
