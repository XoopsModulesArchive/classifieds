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

function ads_show()
{
    global $xoopsDB, $myts, $xoopsModuleConfig;

    $mydirname = basename(dirname(__FILE__, 2));

    $myts = MyTextSanitizer::getInstance();

    $block = [];

    $block['title'] = _MB_ADS_TITLE;

    $rs = $xoopsDB->query('SELECT conf_value FROM ' . $xoopsDB->prefix('config') . " WHERE conf_name='newclassified_ads'");

    while (list($val) = $xoopsDB->fetchRow($rs)) {
        $newclassified_ads = $val;
    }

    $query = 'select lid, title, type FROM ' . $xoopsDB->prefix('ads_listing') . " WHERE valid='Yes' ORDER BY date DESC LIMIT $newclassified_ads";

    $result = $xoopsDB->query($query);

    while (list($lid, $title, $type) = $xoopsDB->fetchRow($result)) {
        $type = htmlspecialchars($type, ENT_QUOTES | ENT_HTML5);

        $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

        if (!XOOPS_USE_MULTIBYTES) {
            if (mb_strlen($title) >= 14) {
                $title = mb_substr($title, 0, 18) . '...';
            }
        }

        $a_item['type'] = $type;

        $a_item['link'] = '<a href="' . XOOPS_URL . "/modules/$mydirname/index.php?pa=viewads&amp;lid=$lid\"><b>$title</b></a>";

        $block['items'][] = $a_item;
    }

    $block['link'] = '<a href="' . XOOPS_URL . "/modules/$mydirname/\"><b>" . _MB_ADS_ALLANN . '</b></a></div>';

    $block['add'] = '<a href="' . XOOPS_URL . "/modules/$mydirname/\"><b>" . _MB_ADS_ADDNOW . '</b></a></div>';

    return $block;
}
