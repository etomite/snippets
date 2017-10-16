//<?php

// Snippet: DateTime
// Purpose: Adjusts and formats document or current Date/Time
// Revision: 1.2 ships with Etomite Prelude v1.0
// Credit: 2004-09-12 -- Alex Butter
// Credit: ????-??-?? -- Bill Wilson
// Credit: 2005-02-07 -- Lloyd Borrett
// Credit: 2008-04-14 -- Ralph A. Dahlgren

/*
   For such a small piece of code, this is an exceedingly 
   powerful and handy snippet. The snippet accepts the
   parameter, 'timestamp'. If this isn't set, the snippet 
   will display the current date and time. If you pass a 
   timestamp (UNIX-style), it will convert that instead. 
   Probably the most useful way of using this snippet is 
   by writing something like the following in your template 
   or document:

   Last edited on: [!DateTime?timestamp=[*editedon*]!]

   This will display the date and time at which the document 
   was last altered on your page!

  Available document related timestamps:
  [*createdon*]  = timestamp when document was originally created
  [*editedon*]   = timestamp when document was last edited
  [*pub_date*]   = timestamp when document should be published
  [*unpub_date*] = timestamp when document should be unpublished
  Sending without [*...*] is also allowed

  Additional examples:
  [!DateTime?format=%Y-%m-%d %r!] returns formatted like 2008-12-31 12:59:00 pm
  [!DateTime!] returns the current time based on the date and time formats set
  in the configuration panel.
  [!DateTime?timestamp=[*editedon*]&format=%Y-%m-%d %r!] is a full featured example
  [!DateTime?timestamp=editedon!] tells the snippet to use the document object
  
  for more strftime() date formatting options see:
    http://us.php.net/manual/en/function.strftime.php
*/

// optionally, timestamp document objects can be sent without tags
if (in_array($timestamp, array('createdon', 'editedon', 'pub_date', 'unpub_date'))) {
    $timestamp = $etomite->documentObject[$timestamp];
}

// if an invalid timestamp was sent, reurn an error
if (isset($timestamp) && empty($timestamp)) {
    return "(timestamp error)";
}
// if $format was sent, use it, otherwise use the configuration panel format
$format = isset($format) ? $format : $etomite->config['date_format']." ".$etomite->config['time_format'];
// get the Etomite server offset time in seconds
$server_offset_time = $etomite->config['server_offset_time'];
// if server offset time is null, set to zero
if (!$server_offset_time) {
    $server_offset_time = 0;
}
// if no timestamp was supplied, use current time
if (!isset($timestamp)) {
    $timestamp=time();
}
// return formatted timestamp to caller
return strftime($format, $timestamp + $server_offset_time);
// the end
