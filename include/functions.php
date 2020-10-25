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
    $mydirname = basename(dirname(__FILE__, 2));

    require_once(XOOPS_ROOT_PATH . "/modules/$mydirname/include/gtickets.php");

function showNewAds()
{
    global $myts, $xoopsDB, $xoopsTpl, $xoopsModuleConfig, $mf, $xoopsUser, $mydirname;

    require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

    $perpage = $xoopsModuleConfig['ads_perpage'];

    $mytree = new XoopsTree($xoopsDB->prefix('ads_categories'), 'cid', 'pid');

    // Add 'typeprice' by Tom

    $result = $xoopsDB->query('select lid, title, type, price, typeprice, date, town, country, valid, photo, view FROM ' . $xoopsDB->prefix('ads_listing') . " WHERE valid='Yes' ORDER BY date DESC LIMIT " . $xoopsModuleConfig['newclassified_ads'] . '');

    if ($result) {
        $xoopsTpl->assign('last_head', _ADS_THE . ' ' . $xoopsModuleConfig['newclassified_ads'] . ' ' . _ADS_LASTADD);

        $xoopsTpl->assign('last_head_title', _ADS_TITLE);

        $xoopsTpl->assign('last_head_price', _ADS_PRICE);

        $xoopsTpl->assign('last_head_date', _ADS_DATE);

        $xoopsTpl->assign('last_head_local', _ADS_LOCAL2);

        $xoopsTpl->assign('last_head_views', _ADS_VIEW);

        $xoopsTpl->assign('last_head_photo', _ADS_PHOTO);

        $rank = 1;

        // Add $typeprice by Tom

        while (list($lid, $title, $type, $price, $typeprice, $date, $town, $country, $valid, $photo, $vu) = $xoopsDB->fetchRow($result)) {
            $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

            $type = htmlspecialchars($type, ENT_QUOTES | ENT_HTML5);

            $price = htmlspecialchars($price, ENT_QUOTES | ENT_HTML5);

            $town = htmlspecialchars($town, ENT_QUOTES | ENT_HTML5);

            $country = htmlspecialchars($country, ENT_QUOTES | ENT_HTML5);

            $a_item = [];

            $useroffset = '';

            if ($xoopsUser) {
                $timezone = $xoopsUser->timezone();

                if (isset($timezone)) {
                    $useroffset = $xoopsUser->timezone();
                } else {
                    $useroffset = $xoopsConfig['default_TZ'];
                }
            }

            $date = ($useroffset * 3600) + $date;

            $date = formatTimestamp($date, 's');

            if ($xoopsUser) {
                if ($xoopsUser->isAdmin()) {
                    $a_item['admin'] = "<a href='admin/index.php?op=ModifyAds&amp;lid=$lid'><img src='images/modif.gif' border=0 alt=\"" . _ADS_MODADMIN . '"></a>';
                }
            }

            $a_item['type'] = $type;

            $a_item['title'] = "<a href='index.php?pa=viewads&amp;lid=$lid'>$title</a>";

            if ($price > 0) {
                $a_item['price'] = '' . $xoopsModuleConfig['ads_monnaie'] . " $price";

                // Add $price_typeprice by Tom

                $a_item['price_typeprice'] = (string)$typeprice;
            } else {
                $a_item['price'] = '';

                $a_item['price_typeprice'] = (string)$typeprice;
            }

            $a_item['date'] = $date;

            $a_item['local'] = '';

            if ($town) {
                $a_item['local'] .= $town;
            }

            if ($country) {
                $a_item['local'] .= $country;
            }

            if ($photo) {
                $a_item['photo'] = "<a href='index.php?pa=viewads&amp;lid=$lid'><img src=\"photo/$photo\" width=\"100\" alt=\"$title\"></a>";
            }

            $a_item['views'] = $vu;

            $rank++;

            $xoopsTpl->append('items', $a_item);
        }
    }
}

function showViewAds($debut, $cid, $perpage, $nbe)
{
    global $xoopsDB, $xoopsConfig, $xoopsTpl, $xoopsModuleConfig, $xoopsUser, $myts, $mydirname;

    $perpage = $xoopsModuleConfig['ads_perpage'];

    $result3 = $xoopsDB->query('select lid, cid, title, type, price, typeprice, date, town, country, valid, photo, view from ' . $xoopsDB->prefix('ads_listing') . " where  valid='yes' AND cid=$cid order by date DESC  LIMIT $debut,$perpage");

    $xoopsTpl->assign('data_rows', $nbe);

    if ('0' == $nbe) {
        $xoopsTpl->assign('no_data', _ADS_NOANNINCAT);
    } else {
        $xoopsTpl->assign('last_head', _ADS_THE . ' ' . $xoopsModuleConfig['newclassified_ads'] . ' ' . _ADS_LASTADD);

        $xoopsTpl->assign('last_head_title', _ADS_TITLE);

        $xoopsTpl->assign('last_head_price', _ADS_PRICE);

        $xoopsTpl->assign('last_head_date', _ADS_DATE);

        $xoopsTpl->assign('last_head_local', _ADS_LOCAL2);

        $xoopsTpl->assign('last_head_views', _ADS_VIEW);

        $xoopsTpl->assign('last_head_photo', _ADS_PHOTO);

        $rank = 1;

        // Add 'typeprice' by Tom

        //while(list($lid, $cid, $title, $type, $price, $date, $town, $country, $valid, $photo, $vu)=$xoopsDB->fetchRow($result3))

        while (list($lid, $cid, $title, $type, $price, $typeprice, $date, $town, $country, $valid, $photo, $vu) = $xoopsDB->fetchRow($result3)) {
            $a_item = [];

            $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

            $type = htmlspecialchars($type, ENT_QUOTES | ENT_HTML5);

            $price = htmlspecialchars($price, ENT_QUOTES | ENT_HTML5);

            $town = htmlspecialchars($town, ENT_QUOTES | ENT_HTML5);

            $country = htmlspecialchars($country, ENT_QUOTES | ENT_HTML5);

            $useroffset = '';

            if ($xoopsUser) {
                $timezone = $xoopsUser->timezone();

                if (isset($timezone)) {
                    $useroffset = $xoopsUser->timezone();
                } else {
                    $useroffset = $xoopsConfig['default_TZ'];
                }
            }

            $date = ($useroffset * 3600) + $date;

            $date = formatTimestamp($date, 's');

            if ($xoopsUser) {
                if ($xoopsUser->isAdmin()) {
                    $a_item['admin'] = "<a href='admin/index.php?op=ModifyAds&amp;lid=$lid'><img src='images/modif.gif' border=0 alt=\"" . _ADS_MODADMIN . '"></a>';
                }
            }

            $a_item['type'] = $type;

            $a_item['title'] = "<a href='index.php?pa=viewads&amp;lid=$lid'>$title</a>";

            if ($price > 0) {
                $a_item['price'] = '' . $xoopsModuleConfig['ads_monnaie'] . " $price";

                // Add $price_typeprice by Tom

                $a_item['price_typeprice'] = (string)$typeprice;
            }

            $a_item['date'] = $date;

            $a_item['local'] = '';

            if ($town) {
                $a_item['local'] .= $town;
            }

            if ($country) {
                $a_item['local'] .= $country;
            }

            if ($photo) {
                $a_item['photo'] = "<a href='index.php?pa=viewads&amp;lid=$lid'><img src=\"photo/$photo\" width=\"100\" alt=\"$title\"></a>";
            }

            $a_item['views'] = $vu;

            $rank++;

            $xoopsTpl->append('items', $a_item);
        }
    }
}

function showMyAds($debut, $cid, $perpage, $nbe)
{
    global $xoopsDB, $xoopsConfig, $xoopsTpl, $xoopsModuleConfig, $xoopsUser, $myts, $mydirname;

    $perpage = $xoopsModuleConfig['ads_perpage'];

    if (isset($_GET['usid'])) {
        $usid = (int)$_GET['usid'];
    } else {
        $usid = 0;
    }

    $result3 = $xoopsDB->query('select lid, cid, title, type, price, typeprice, date, town, country, valid, photo, view, comments from ' . $xoopsDB->prefix('ads_listing') . " where valid='yes' AND usid=$usid order by date DESC LIMIT $debut,$perpage");

    $xoopsTpl->assign('data_rows', $nbe);

    if ('0' == $nbe) {
        $xoopsTpl->assign('no_data', _ADS_NOANNINCAT);
    } else {
        $xoopsTpl->assign('my_ads_head', _ADS_MY_ADS);

        $xoopsTpl->assign('my_ads_head_title', _ADS_TITLE);

        $xoopsTpl->assign('my_ads_head_price', _ADS_PRICE);

        $xoopsTpl->assign('my_ads_head_date', _ADS_DATE);

        $xoopsTpl->assign('my_ads_head_local', _ADS_LOCAL2);

        $xoopsTpl->assign('my_ads_head_views', _ADS_VIEW);

        $xoopsTpl->assign('my_ads_head_photo', _ADS_PHOTO);

        $rank = 1;

        // Add 'typeprice' by Tom

        //while(list($lid, $cid, $title, $type, $price, $date, $town, $country, $valid, $photo, $vu)=$xoopsDB->fetchRow($result3))

        while (list($lid, $cid, $title, $type, $price, $typeprice, $date, $town, $country, $valid, $photo, $vu, $comments) = $xoopsDB->fetchRow($result3)) {
            $a_item = [];

            $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

            $type = htmlspecialchars($type, ENT_QUOTES | ENT_HTML5);

            $price = htmlspecialchars($price, ENT_QUOTES | ENT_HTML5);

            $town = htmlspecialchars($town, ENT_QUOTES | ENT_HTML5);

            $country = htmlspecialchars($country, ENT_QUOTES | ENT_HTML5);

            $comments = htmlspecialchars($comments, ENT_QUOTES | ENT_HTML5);

            $useroffset = '';

            if ($xoopsUser) {
                $timezone = $xoopsUser->timezone();

                if (isset($timezone)) {
                    $useroffset = $xoopsUser->timezone();
                } else {
                    $useroffset = $xoopsConfig['default_TZ'];
                }
            }

            $date = ($useroffset * 3600) + $date;

            $date = formatTimestamp($date, 's');

            if ($xoopsUser) {
                if ($xoopsUser->isAdmin()) {
                    $a_item['admin'] = "<a href='admin/index.php?op=ModifyAds&amp;lid=$lid'><img src='images/modif.gif' border=0 alt=\"" . _ADS_MODADMIN . '"></a>';
                }
            }

            $a_item['type'] = $type;

            $a_item['title'] = "<a href='index.php?pa=viewads&amp;lid=$lid'>$title</a>";

            if ($price > 0) {
                $a_item['price'] = '' . $xoopsModuleConfig['ads_monnaie'] . " $price";

                // Add $price_typeprice by Tom

                $a_item['price_typeprice'] = (string)$typeprice;
            }

            $a_item['date'] = $date;

            $a_item['local'] = '';

            if ($town) {
                $a_item['local'] .= $town;
            }

            if ($country) {
                $a_item['local'] .= $country;
            }

            if ($photo) {
                $a_item['photo'] = "<a href='index.php?pa=viewads&amp;lid=$lid'><img src=\"photo/$photo\" width=\"100\" alt=\"$title\"></a>";
            }

            $a_item['views'] = $vu;

            $a_item['comments'] = $comments;

            $rank++;

            $xoopsTpl->append('items', $a_item);
        }
    }
}

function ExpireAd()
{
    global $xoopsDB, $xoopsConfig, $xoopsModuleConfig, $myts, $meta, $mydirname;

    $datenow = time();

    $result5 = $xoopsDB->query('select lid, title, expire, type, desctext, date, email, submitter, photo, photo2, photo3, view, comments FROM ' . $xoopsDB->prefix('ads_listing') . " WHERE valid='Yes'");

    while (list($lids, $title, $expire, $type, $desctext, $dateann, $email, $submitter, $photo, $photo2, $photo3, $lu, $comments) = $xoopsDB->fetchRow($result5)) {
        $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

        $expire = htmlspecialchars($expire, ENT_QUOTES | ENT_HTML5);

        $type = htmlspecialchars($type, ENT_QUOTES | ENT_HTML5);

        $desctext = htmlspecialchars($desctext, ENT_QUOTES | ENT_HTML5);

        $submitter = htmlspecialchars($submitter, ENT_QUOTES | ENT_HTML5);

        $supprdate = $dateann + ($expire * 86400);

        if ($supprdate < $datenow) {
            //for xoops2//	$xoopsDB->query("delete from ".$xoopsDB->prefix("ads_listing")." where lid='$lids'");

            $xoopsDB->queryF('delete from ' . $xoopsDB->prefix('ads_listing') . " where lid='$lids'");

            $destination = XOOPS_ROOT_PATH . "/modules/$mydirname/photo";

            $destination2 = XOOPS_ROOT_PATH . "/modules/$mydirname/photo2";

            $destination3 = XOOPS_ROOT_PATH . "/modules/$mydirname/photo3";

            if ($photo) {
                if (file_exists("$destination/$photo")) {
                    unlink("$destination/$photo");
                }
            }

            if ($photo2) {
                if (file_exists("$destination2/$photo2")) {
                    unlink("$destination2/$photo2");
                }
            }

            if ($photo3) {
                if (file_exists("$destination3/$photo3")) {
                    unlink("$destination3/$photo3");
                }
            }

            //	Specification for Japan:

            //	$message = ""._ADS_HELLO." $submitter,\n\n"._ADS_STOP2."\n $type : $title\n $desctext\n"._ADS_STOP3."\n\n"._ADS_VU." $lu "._ADS_VU2."\n\n"._ADS_OTHER." ".XOOPS_URL."/modules/myAds\n\n"._ADS_THANK."\n\n"._ADS_TEAM." ".$meta['title']."\n".XOOPS_URL."";

            if ($email) {
                $message = "$submitter " . _ADS_HELLO . " \n\n" . _ADS_STOP2 . "\n $type : $title\n $desctext\n" . _ADS_STOP3 . "\n\n" . _ADS_VU . " $lu " . _ADS_VU2 . "\n\n" . _ADS_OTHER . ' ' . XOOPS_URL . "/modules/$mydirname\n\n" . _ADS_THANK . "\n\n" . _ADS_TEAM . ' ' . $meta['title'] . "\n" . XOOPS_URL . '';

                $subject = '' . _ADS_STOP . '';

                $mail = getMailer();

                $mail->useMail();

                $mail->setFromName($meta['title']);

                $mail->setFromEmail($xoopsConfig['adminmail']);

                $mail->setToEmails($email);

                $mail->setSubject($subject);

                $mail->setBody($message);

                $mail->send();

                echo $mail->getErrors();
            }
        }
    }
}

//updates rating data in itemtable for a given user
function updateUrating($sel_id)
{
    global $xoopsDB, $xoopsUser;

    if (isset($_GET['usid'])) {
        $usid = (int)$_GET['usid'];
    } else {
        $usid = 0;
    }

    $query = 'select rating FROM ' . $xoopsDB->prefix('ads_user_votedata') . " WHERE usid='$sel_id'";

    //echo $query;

    $voteresult = $xoopsDB->query($query);

    $votesDB = $xoopsDB->getRowsNum($voteresult);

    $totalrating = 0;

    while (list($rating) = $xoopsDB->fetchRow($voteresult)) {
        $totalrating += $rating;
    }

    $finalrating = $totalrating / $votesDB;

    $finalrating = number_format($finalrating, 4);

    $query = 'UPDATE ' . $xoopsDB->prefix('ads_listing') . " SET user_rating=$finalrating, user_votes=$votesDB WHERE usid='$sel_id'";

    //echo $query;

    $xoopsDB->query($query) || exit();
}

//updates rating data in itemtable for a given user
function updateIrating($sel_id)
{
    global $xoopsDB, $xoopsUser;

    if (isset($_GET['lid'])) {
        $lid = (int)$_GET['lid'];
    } else {
        $lid = 0;
    }

    $query = 'select rating FROM ' . $xoopsDB->prefix('ads_item_votedata') . " WHERE lid='$sel_id'";

    //echo $query;

    $voteresult = $xoopsDB->query($query);

    $votesDB = $xoopsDB->getRowsNum($voteresult);

    $totalrating = 0;

    while (list($rating) = $xoopsDB->fetchRow($voteresult)) {
        $totalrating += $rating;
    }

    $finalrating = $totalrating / $votesDB;

    $finalrating = number_format($finalrating, 4);

    $query = 'UPDATE ' . $xoopsDB->prefix('ads_listing') . " SET item_rating=$finalrating, item_votes=$votesDB WHERE lid='$sel_id'";

    //echo $query;

    $xoopsDB->query($query) || exit();
}

function getTotalItems($sel_id, $status = '')
{
    global $xoopsDB, $mytree;

    $pfx = $xoopsDB->prefix('ads_listing');

    $count = 1;

    $arr = [];

    $status_q = '';

    if ($status) {
        if (_YES == $status) {
            $status_q = " and valid='Yes'";
        } else {
            $status_q = " and valid='No'";
        }
    }

    $query = "select lid from $pfx where cid=" . $sel_id . (string)$status_q;

    $result = $xoopsDB->query($query);

    $count = $xoopsDB->getRowsNum($result);

    $arr = $mytree->getAllChildId($sel_id);

    $size = count($arr);

    for ($i = 0; $i < $size; $i++) {
        $query2 = "select lid from $pfx where cid=" . $arr[$i] . (string)$status_q;

        $result2 = $xoopsDB->query($query2);

        $count += $xoopsDB->getRowsNum($result2);
    }

    return $count;
}

function ShowImg()
{
    global $mydirname;

    echo "<script type=\"text/javascript\">\n";

    echo "<!--\n\n";

    echo "function showimage() {\n";

    echo "if (!document.images)\n";

    echo "return\n";

    echo "document.images.avatar.src=\n";

    echo "'" . XOOPS_URL . "/modules/$mydirname/images/cat/' + document.imcat.img.options[document.imcat.img.selectedIndex].value\n";

    echo "}\n\n";

    echo "//-->\n";

    echo "</script>\n";
}

//Reusable Link Sorting Functions
function convertorderbyin($orderby)
{
    switch (trim($orderby)) {
    case 'titleA':
        $orderby = 'title ASC';
        break;
    case 'dateA':
        $orderby = 'date ASC';
        break;
    case 'hitsA':
        $orderby = 'hits ASC';
        break;
    case 'ratingA':
        $orderby = 'rating ASC';
        break;
    case 'titleD':
        $orderby = 'title DESC';
        break;
    case 'hitsD':
        $orderby = 'hits DESC';
        break;
    case 'ratingD':
        $orderby = 'rating DESC';
        break;
    case'dateD':
    default:
        $orderby = 'date DESC';
        break;
    }

    return $orderby;
}
function convertorderbytrans($orderby)
{
    if ('hits ASC' == $orderby) {
        $orderbyTrans = '' . _ADS_POPULARITYLTOM . '';
    }

    if ('hits DESC' == $orderby) {
        $orderbyTrans = '' . _ADS_POPULARITYMTOL . '';
    }

    if ('title ASC' == $orderby) {
        $orderbyTrans = '' . _ADS_TITLEATOZ . '';
    }

    if ('title DESC' == $orderby) {
        $orderbyTrans = '' . _ADS_TITLEZTOA . '';
    }

    if ('date ASC' == $orderby) {
        $orderbyTrans = '' . _ADS_DATEOLD . '';
    }

    if ('date DESC' == $orderby) {
        $orderbyTrans = '' . _ADS_DATENEW . '';
    }

    if ('rating ASC' == $orderby) {
        $orderbyTrans = '' . _ADS_RATINGLTOH . '';
    }

    if ('rating DESC' == $orderby) {
        $orderbyTrans = '' . _ADS_RATINGHTOL . '';
    }

    return $orderbyTrans;
}
function convertorderbyout($orderby)
{
    if ('title ASC' == $orderby) {
        $orderby = 'titleA';
    }

    if ('date ASC' == $orderby) {
        $orderby = 'dateA';
    }

    if ('hits ASC' == $orderby) {
        $orderby = 'hitsA';
    }

    if ('rating ASC' == $orderby) {
        $orderby = 'ratingA';
    }

    if ('title DESC' == $orderby) {
        $orderby = 'titleD';
    }

    if ('date DESC' == $orderby) {
        $orderby = 'dateD';
    }

    if ('hits DESC' == $orderby) {
        $orderby = 'hitsD';
    }

    if ('rating DESC' == $orderby) {
        $orderby = 'ratingD';
    }

    return $orderby;
}
