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
function ads_search($queryarray, $andor, $limit, $offset, $userid)
{
    global $xoopsDB, $xoopsModuleConfig;

    $sql = 'select lid,title,type,desctext,price,typeprice,date,usid,town,photo FROM ' . $xoopsDB->prefix('ads_listing') . " WHERE valid='Yes' AND date<=" . time() . '';

    if (0 != $userid) {
        $sql .= ' AND usid=' . $userid . ' ';
    }

    // because count() returns 1 even if a supplied variable

    // is not an array, we must check if $querryarray is really an array

    if (is_array($queryarray) && $count = count($queryarray)) {
        $sql .= " AND ((title LIKE '%$queryarray[0]%' OR type LIKE '%$queryarray[0]%' OR desctext LIKE '%$queryarray[0]%'OR price LIKE '%$queryarray[0]%' OR typeprice LIKE '%$queryarray[0]%' OR town LIKE '%$queryarray[0]%' )";

        for ($i = 1; $i < $count; $i++) {
            $sql .= " $andor ";

            $sql .= "(title LIKE '%$queryarray[$i]%' OR type LIKE '%$queryarray[$i]%' OR desctext LIKE '%$queryarray[$i]%' OR price LIKE '%$queryarray[$i]%' OR typeprice LIKE '%$queryarray[$i]%' OR town LIKE '%$queryarray[$i]%' )";
        }

        $sql .= ') ';
    }

    $sql .= 'ORDER BY date DESC';

    $result = $xoopsDB->query($sql, $limit, $offset);

    $ret = [];

    $i = 0;

    while ($myrow = $xoopsDB->fetchArray($result)) {
        $ret[$i]['image'] = 'images/cat/default.gif';

        $ret[$i]['link'] = 'index.php?pa=viewads&amp;lid=' . $myrow['lid'] . '';

        $ret[$i]['title'] = $myrow['title'];

        $ret[$i]['type'] = $myrow['type'];

        $ret[$i]['price'] = $myrow['price'];

        $ret[$i]['typeprice'] = $myrow['typeprice'];

        $ret[$i]['town'] = $myrow['town'];

        $ret[$i]['desctext'] = $myrow['desctext'];

        $ret[$i]['nophoto'] = 'images/nophotosm.gif';

        $ret[$i]['photo'] = $myrow['photo'];

        $ret[$i]['sphoto'] = 'photo/' . $myrow['photo'] . '';

        $ret[$i]['time'] = $myrow['date'];

        $ret[$i]['uid'] = $myrow['usid'];

        $i++;
    }

    return $ret;
}
