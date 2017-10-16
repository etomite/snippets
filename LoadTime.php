//<?php

// [[LoadTime]] - Displays total time for page to load
function getmicrotime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
$time_start = getmicrotime();
for ($i=0; $i <1000; $i++) {
}
$time_end = getmicrotime();
$time = $time_end - $time_start;
$time = round($time, 6);
$LoadTime = "This page loaded in $time seconds.";
return $LoadTime;
