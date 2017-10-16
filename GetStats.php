//<?php

// Snippet name: GetStats
// Snippet description: Fetches the visitor statistics totals from the database
// Revision: 1.1 ships with Etomite 0.6.1-Final

$tmpArray = $etomite->getSiteStats();

$output = "
<table>
  <thead>
    <tr style=\"text-align:right;\">
        <td width=\"25%\">&nbsp;</td>
        <td width=\"25%\"><b>Pages</b></td>
        <td width=\"25%\"><b>Visits</b></td>
        <td width=\"25%\"><b>Visitors</b></td>
    </tr>
  </thead>
    <tr style=\"text-align:right;\">
        <td><b>Today</b></td>
        <td>".number_format($tmpArray['piDay'])."</td>
        <td>".number_format($tmpArray['viDay'])."</td>
        <td>".number_format($tmpArray['visDay'])."</td>
    </tr>
    <tr style=\"text-align:right;\">
        <td><b>This Month</b></td>
        <td>".number_format($tmpArray['piMonth'])."</td>
        <td>".number_format($tmpArray['viMonth'])."</td>
        <td>".number_format($tmpArray['visMonth'])."</td>
    </tr>
    <tr style=\"text-align:right;\">
        <td><b>All Time</b></td>
        <td>".number_format($tmpArray['piAll'])."</td>
        <td>".number_format($tmpArray['viAll'])."</td>
        <td>".number_format($tmpArray['visAll'])."</td>
    </tr>
</table>
";

return $output;
