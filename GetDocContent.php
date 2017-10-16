//<?php

# Snippet:  GetDocContent -- Etomite Prelude v1.0
# Author:   Ralph A. Dahlgren
# Created:  2005-04-17
# Modified: 2008-04-17
# Purpose:  Returns additional document content for inclusion in a page
# Usage: [[GetDocContent?id=nn]] where nn = id of the document being requested

// if a document id was sent, fetch the document content
if (isset($id)) {
  // we only want the content column
    $fields = "content";
  // query the database for our record
    $doc = $etomite->getDocument($id, $fields);
  // if our record was found, return the content
    if ($doc) {
        return $doc['content'];
    }
}

// if all else fails, return empty
return;
