<?php

//               Classified Ads Module for Xoops                             //
//      Redesigned by John Mordo user jlm69 at www.xoops.org                 //
//      Started with the MyAds module and made MANY changes                  //
//               Original credits below                                      //
//                                                                           //
//
// ------------------------------------------------------------------------- //
//               E-Xoops: Content Management for the Masses                  //
//                       < http://www.e-xoops.com >                          //
// ------------------------------------------------------------------------- //
// Original Author: Pascal Le Boustouller
// Author Website : pascal.e-xoops@perso-search.com
// Licence Type   : GPL
// ------------------------------------------------------------------------- //
include 'header.php';
if (isset($_GET['lid'])) {
    $lid = (int)$_GET['lid'];
} else {
    redirect_header('index.php', 1, _ADS_VALIDATE_FAILED);
}
xoops_header();

global $xoopsUser, $xoopsConfig, $xoopsTheme, $xoopsDB, $xoops_footer, $xoopsLogger;
$currenttheme = getTheme();

$result = $xoopsDB->query('select photo FROM ' . $xoopsDB->prefix('ads_listing') . " WHERE lid = '$lid'");
$recordexist = $xoopsDB->getRowsNum($result);

if ($recordexist) {
    [$photo] = $xoopsDB->fetchRow($result);

    echo "<br><br><center><img src=\"photo/$photo\" border=0></center>";
}

echo "<table><tr><td><center><a href=#  onClick='window.close()'>" . _ADS_CLOSEF . '</a></center></td></tr></table>';

xoops_footer();
