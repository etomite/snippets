//<?php

//  Snippet: GetAuthorData
//  Purpose: Returns author information based on sent parameters 
// Revision: 1.2 ships with Etomite Prelude v1.0
//   Author: Ralph A. Dahlgren - rad14701@yahoo.com
// Modified: 2008-04-17

// Usage: [[GetAuthorData?internalKey=[*createdby*]&field=fullname]]
// internalKey=[*editedby*] will return data about the user who last edited a document
// internalKey subset: ([*createdby*], [*editedby*], or a numeric internalKey)
// field can be any column in the user_attributes database table
// field subset: (fullname,email,phone,mobilephone)

// if both parameters were passed, process the request
if (($internalKey != "") && ($field != "")) {
  // process the request (returns false on failure)
    if ($author = $etomite->getAuthorData($internalKey)) {
        // if the request was successful, return the requested data
        return $author[$field];
    }
}

// if all else fails, return empty
return;
