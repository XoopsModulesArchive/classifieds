<?php

//               Classified Ads Module for Xoops                             //
//      Redesigned by John Mordo user jlm69 at www.xoops.org                 //
//          Find it or report problems at www.jlmzone.com                    //
//                                                                           //
//      Started with the MyAds module and made MANY changes                  //
//                                                                           //
// ------------------------------------------------------------------------- //
//                   Original credits below                                  //
// ------------------------------------------------------------------------- //
// Original Author: Pascal Le Boustouller                                    //
// Author Website : pascal.e-xoops@perso-search.com                          //
// Licence Type   : GPL                                                      //
// ------------------------------------------------------------------------- //
include 'admin_header.php';
require_once '../../../include/cp_header.php';
    $mydirname = basename(dirname(__FILE__, 2));
require_once(XOOPS_ROOT_PATH . "/modules/$mydirname/include/gtickets.php");
require_once XOOPS_ROOT_PATH . "/modules/$mydirname/include/functions.php";
require_once XOOPS_ROOT_PATH . "/modules/$mydirname/class/xoopstree.php";
$mytree = new XoopsTree($xoopsDB->prefix('ads_categories'), 'cid', 'pid');
global $mytree, $xoopsDB, $xoopsModuleConfig, $mydirname;
xoops_cp_header();
include('./mymenu.php');
echo "<fieldset style='padding: 5px;'><legend style='font-weight: bold; color: #900;'>" . _AM_ADS_GESTCAT . '</legend>';
echo '<b>' . _AM_ADS_GESTCAT . '</b><br><br>';
echo '<a href="category.php?op=AdsNewCat&amp;cid=0"><img src="' . XOOPS_URL . "/modules/$mydirname/images/plus.gif\" border=0 width=10 height=10  alt=\"" . _AM_ADS_ADDSUBCAT . '"></a> ' . _AM_ADS_ADDCATPRINC . '<br><br>';

$mytree->makeAdSelBox('title', '' . $xoopsModuleConfig['ads_classm'] . '');
echo '<br><hr>';
echo '<p>' . _AM_ADS_HELP1 . ' <p>';
if ('ordre' == $xoopsModuleConfig['ads_classm']) {
    echo '<p>' . _AM_ADS_HELP2 . ' </p>';
}
echo '<br></fieldset><br>';
xoops_cp_footer();
