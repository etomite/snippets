//<?php

// ----------------------
// Snippet: SearchableSE
// ----------------------
// Version: 0.6a
// Date: 2005.05.16
// jaredc@honeydewdesign.com
//
// This snippet was designed to add a meta tag to pages
// marked as NON-searchable in Etomite. This is a robots
// meta tag which discourages external searche engines
// such as Google, from indexing this page. This snippet
// should be put in the template head section.
//

// NO CONFIG NECESSARY

// Create meta tag string
$metaTag = '<meta name="robots" content="noindex,nofollow" >'; // was " /> which didn't validate
$metaTags = '<meta name="robots" content="ALL" />'; // was " /> which didn't validate

// Determine if this page has been set to non-searchable
if (!$etomite->documentObject['searchable']) {
    return $metaTag;
} else {
    return $metaTags;
}
