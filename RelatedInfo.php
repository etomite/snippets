//<?php

// Snippet name: RelatedInfo
// Snippet description: Outputs a related chunk into the document
// Revision: 1.0 ships with Etomite 0.6.1-Final
// Author: Lloyd Borrett - 2006-04-09

// Description:
//   Outputs a chunk into the document based on the 
//   document's alias/id, and optionally, a prefix 
//   passed to the chunk.

// Usage:
//   Insert [[RelatedInfo]] or [[RelatedInfo?prefix=<YourPrefix>]]
//   in your template where you want the required document 
//   specific content to appear.
//   e.g. [[RelatedInfo]], [[RelatedInfo?prefix=RightColumn]],
//   [[RelatedInfo?prefix=MiddleColumn]] etc.
//   Create a chunk called "RelatedInfo-<value>" and/or 
//   "<YourPrefix>-<value>" for each document where you want 
//   to display document related content.

//   "<value>" is the document alias (if specified), or the 
//   document id number if there is no alias value.
//   "<YourPrefix>" is a prefix value passed to the chunk.

//   For example: "RelatedInfo-home", "RelatedInfo-1",
//   "MiddleColumn-home", "MiddleColumn-1" etc.

//   The snippet will insert the content of your "RelatedInfo-<value>" and/or
//   "<YourPrefix>-<value>" chunks into the appropriate document.

//   If there is no output from "RelatedInfo-<value>" and/or "<YourPrefix>-<value>",
//   typically because the chunks don't exist, the you can request that the default
//   "RelatedInfo" or "<YourPrefix>" chunks are used, if they exist.

// Problem 1:
//   You want to be able to put some content into a section of 
//   your document template. But you would like to be able to have 
//   different content for different documents.
//   Typically this might be document related content for another 
//   column or something similar.

// Solution 1:
//   Insert [[RelatedInfo]] into your template where you want the content to appear.
//   Now create a chunk called "RelatedInfo-<value>" for each document where you want
//   document specific content to appear.
//   For example, if you have a document with an alias "productx", 
//   the chunk would be called "RelatedInfo-productx".
//   If the document has no alias, then it's id value is used, 
//   for example "RelatedInfo-23".
//   Into the "RelatedInfo-<value>" chunk you can put the content you want,
//   and/or calls to other chunks and snippets.
//   If you don't have document specific content for that document, 
//   then you can let the default content from the "RelatedInfo" chunk 
//   be output, if it exists.
//   If there is no "RelatedInfo-<value>" or "RelatedInfo" chunks, 
//   then nothing is output.

// Problem 2:
//   Okay so you can use [[RelatedInfo]] to do document specific 
//   content for say your middle column.
//   But on some documents you might also might want some page 
//   specific extra content in another part of the document. 
//   For example, the right column.

// Solution 2:
//   Insert [[RelatedInfo?prefix=<YourPrefix>]] into your template 
//   where you want the content to appear.
//   Now create a chunk called "<YourPrefix>-<value>" for each page 
//   where you want this document specific content to appear.
//   For example, you want to put some content into the right 
//   column of your template.
//   So you put [[RelatedInfo?prefix=RightColumn]] into your template.
//   And if you have a document with an alias "productx", the chunk would be
//   called "RightColumn-productx". If the document has no alias, 
//   then it's id value is used, for example "RightColumn-23".
//   Into the "<YourPrefix>-<value>" chunk you can put the content you want,
//   and/or calls to other chunks and snippets.
//   If you don't have document specific content for that document, 
//   you can let the default content from the "<YourPrefix>" chunk 
//   be output, if it exists.
//   If there is no "<YourPrefix>-<alias>" or "<YourPrefix>" chunks, 
//   then nothing is output.

// Explanation:
//   The power of this snippet is that a chunk doesn't have to just 
//   contain raw HTML code.
//   The chunk can also have calls to other chunks, and even calls to snippets.
//   Thus each document specific chunk you create can be extremely 
//   flexible in what gets  put into your document, with you having 
//   to duplicate raw HTML code here, there and everywhere.

//   (So much to explain such a simple snippet!)


// Configuration Setting

// $showDefaultCrumb [true | false]
// If there is no output from calling the crumb for the specific document, 
// which typically occurs if you haven't created the crumb, 
// instead of getting nothing you can ask for a default crumb 
// to be used by setting this value to true. 
// Of course, if you don't set up a default crumb, you still get nothing. 
// If you don't want to have this feature enabled just set this value to false.
$showDefaultCrumb = true;

$output = "";

if (!isset($prefix)) {
    $prefix = "RelatedInfo";
} else {
    if ($prefix == "") {
        $prefix = "RelatedInfo";
    }
}

$alias = $etomite->documentObject['alias'];
if ($alias == "") {
    $alias = $etomite->documentIdentifier;
}

$output .= $etomite->putChunk($prefix.'-'.$alias);
if ($output == "" && $showDefaultCrumb) {
    $output .= $etomite->putChunk($prefix);
}

return $output;
