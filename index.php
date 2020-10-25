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
//               E-Xoops: Content Management for the Masses                  //
//                       < http://www.e-xoops.com >                          //
// ------------------------------------------------------------------------- //
// Original Author: Pascal Le Boustouller
// Author Website : pascal.e-xoops@perso-search.com
// Licence Type   : GPL
// ------------------------------------------------------------------------- //
include 'header.php';

    $mydirname = basename(__DIR__);

    require_once(XOOPS_ROOT_PATH . "/modules/$mydirname/include/gtickets.php");

$myts = MyTextSanitizer::getInstance();
$module_id = $xoopsModule->getVar('mid');

if (is_object($xoopsUser)) {
    $groups = $xoopsUser->getGroups();
} else {
    $groups = XOOPS_GROUP_ANONYMOUS;
}

$gpermHandler = xoops_getHandler('groupperm');

if (isset($_POST['item_id'])) {
    $perm_itemid = (int)$_POST['item_id'];
} else {
    $perm_itemid = 0;
}
//If no access
if (!$gpermHandler->checkRight('ads_view', $perm_itemid, $groups, $module_id)) {
    redirect_header(XOOPS_URL . '/user.php', 3, _NOPERM);

    exit();
}

require XOOPS_ROOT_PATH . '/class/xoopstree.php';
require XOOPS_ROOT_PATH . "/modules/$mydirname/include/functions.php";

$mytree = new Xoopstree($xoopsDB->prefix('ads_categories'), 'cid', 'pid');

#  function index
#####################################################
function index()
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $xoopsUser, $xoopsTpl, $myts, $mytree, $meta, $mid, $mydirname;

    $GLOBALS['xoopsOption']['template_main'] = 'ads_index.html';

    require XOOPS_ROOT_PATH . '/header.php';

    $xoopsTpl->assign('xmid', $xoopsModule->getVar('mid'));

    $xoopsTpl->assign('intro', _ADS_INTRO);

    $xoopsTpl->assign('add_from', _ADS_ADDFROM . ' ' . $xoopsConfig['sitename']);

    // Add Template assign  by Tom

    $xoopsTpl->assign('add_from_title', _ADS_ADDFROM);

    $xoopsTpl->assign('add_from_sitename', $xoopsConfig['sitename']);

    $xoopsTpl->assign('search_listings', _ADS_SEARCH_LISTINGS);

    $xoopsTpl->assign('all_words', _ADS_ALL_WORDS);

    $xoopsTpl->assign('any_words', _ADS_ANY_WORDS);

    $xoopsTpl->assign('exact_match', _ADS_EXACT_MATCH);

    $xoopsTpl->assign('only_pix', _ADS_ONLYPIX);

    $xoopsTpl->assign('search', _ADS_SEARCH);

    if ('1' == $xoopsModuleConfig['ads_moderated']) {
        $result = $xoopsDB->query('select  COUNT(*)  FROM ' . $xoopsDB->prefix('ads_listing') . " WHERE valid='No'");

        [$propo] = $xoopsDB->fetchRow($result);

        $xoopsTpl->assign('moderated', true);

        if ($xoopsUser) {
            if ($xoopsUser->isAdmin()) {
                $xoopsTpl->assign('admin_block', _ADS_ADMINCADRE);

                if (0 == $propo) {
                    $xoopsTpl->assign('confirm_ads', _ADS_NO_CLA);
                } else {
                    $xoopsTpl->assign('confirm_ads', _ADS_THEREIS . " $propo  " . _ADS_WAIT . '<br><a href="admin/index.php">' . _ADS_SEEIT . '</a>');
                }
            }
        }
    }

    $result = $xoopsDB->query('select cid, title, img FROM ' . $xoopsDB->prefix('ads_categories') . ' WHERE pid = 0 ORDER BY ' . $xoopsModuleConfig['ads_classm'] . '') || die('Error');

    [$ncatp] = $xoopsDB->fetchRow($xoopsDB->query('select  COUNT(*)  FROM ' . $xoopsDB->prefix('ads_categories') . ' WHERE pid=0'));

    $count = 0;

    while ($myrow = $xoopsDB->fetchArray($result)) {
        $a_cat = [];

        $cid = $myrow['cid'];

        $title = htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5);

        $totallink = getTotalItems($myrow['cid'], _YES);

        $a_cat['image'] = '<a href="index.php?pa=Adsview&amp;cid=' . $myrow['cid'] . "\"><img src='" . XOOPS_URL . "/modules/$mydirname/images/cat/" . $myrow['img'] . "' align='middle' alt=''></a>";

        $a_cat['link'] = '<a href="index.php?pa=Adsview&amp;cid=' . $myrow['cid'] . "\"><b>$title</b></a>";

        $a_cat['count'] = $totallink;

        if (1 == $xoopsModuleConfig['ads_souscat']) {
            // get child category objects

            $arr = [];

            $arr = $mytree->getFirstChild($myrow['cid'], '' . $xoopsModuleConfig['ads_classm'] . '');

            $space = 0;

            $chcount = 1;

            $subcat = '';

            foreach ($arr as $ele) {
                $chtitle = htmlspecialchars($ele['title'], ENT_QUOTES | ENT_HTML5);

                if ($chcount > $xoopsModuleConfig['ads_nbsouscat']) {
                    $subcat .= ', ...';

                    break;
                }

                if ($space > 0) {
                    $subcat .= '<br>';
                }

                $subcat .= '<a href="index.php?pa=Adsview&amp;cid=' . $ele['cid'] . '">' . $chtitle . '</a>';

                $space++;

                $chcount++;

                $a_cat['subcat'] = $subcat;
            }
        }

        $bis = ($ncatp + 1) / 2;

        $bis = (int)$bis;

        $a_cat['i'] = $count;

        $xoopsTpl->append('categories', $a_cat);

        $count++;
    }

    $xoopsTpl->assign('cat_count', $count - 1);

    [$ann] = $xoopsDB->fetchRow($xoopsDB->query('select  COUNT(*)  FROM ' . $xoopsDB->prefix('ads_listing') . " WHERE valid='Yes'"));

    [$catt] = $xoopsDB->fetchRow($xoopsDB->query('select  COUNT(*)  FROM ' . $xoopsDB->prefix('ads_categories') . ''));

    $xoopsTpl->assign('total_listing', _ADS_ACTUALY . " $ann " . _ADS_ANNONCES . ' ' . _ADS_INCAT . " $catt " . _ADS_CAT2);

    if ('1' == $xoopsModuleConfig['ads_moderated']) {
        $xoopsTpl->assign('total_confirm', _ADS_AND . " $propo " . _ADS_WAIT3);
    }

    if (1 == $xoopsModuleConfig['ads_newad']) {
        showNewAds();
    }

    ExpireAd();
}

#  function view (categories)
#####################################################
function Adsview($cid, $debut)
{
    global $xoopsDB, $xoopsTpl, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $myts, $mytree, $imagecat, $meta, $mydirname;

    $GLOBALS['xoopsOption']['template_main'] = 'ads_category.html';

    require XOOPS_ROOT_PATH . '/header.php';

    require XOOPS_ROOT_PATH . "/modules/$mydirname/class/nav.php";

    $xoopsTpl->assign('add_from', _ADS_ADDFROM . ' ' . $xoopsConfig['sitename']);

    // Add Template assign  by Tom

    $xoopsTpl->assign('add_from_title', _ADS_ADDFROM);

    $xoopsTpl->assign('add_from_sitename', $xoopsConfig['sitename']);

    $xoopsTpl->assign('add_listing', "<a href='addlisting.php?cid=$cid'>" . _ADS_ADDANNONCE2 . '</a>');

    $count = 0;

    if (!$debut) {
        $debut = 0;
    }

    $x = 0;

    $i = 0;

    $requete = $xoopsDB->query('select cid, pid, title from ' . $xoopsDB->prefix('ads_categories') . ' where  cid=' . $cid . '');

    [$ccid, $pid, $title] = $xoopsDB->fetchRow($requete);

    $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

    $varid[$x] = $ccid;

    $varnom[$x] = $title;

    [$nbe] = $xoopsDB->fetchRow($xoopsDB->query('select COUNT(*) FROM ' . $xoopsDB->prefix('ads_listing') . " where valid='Yes' AND cid='$cid'"));

    $pagenav = new PageNav($nbe, $xoopsModuleConfig['ads_perpage'], $debut, "pa=Adsview&amp;cid=$cid&amp;debut", '');

    if (0 != $pid) {
        $x = 1;

        while (0 != $pid) {
            $requete2 = $xoopsDB->query('select cid, pid, title from ' . $xoopsDB->prefix('ads_categories') . ' where cid=' . $pid . '');

            [$ccid, $pid, $title] = $xoopsDB->fetchRow($requete2);

            $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

            $varid[$x] = $ccid;

            $varnom[$x] = $title;

            $x++;
        }

        $x -= 1;
    }

    $subcats = '';

    while (-1 != $x) {
        $subcats .= ' &raquo; <a href="index.php?pa=Adsview&amp;cid=' . $varid[$x] . '">' . $varnom[$x] . '</a>';

        $x -= 1;
    }

    $xoopsTpl->assign('nav_main', '<a href="index.php">' . _ADS_MAIN . '</a>');

    $xoopsTpl->assign('nav_sub', $subcats);

    $xoopsTpl->assign('nav_subcount', $nbe);

    $subresult = $xoopsDB->query('select cid, title, img from ' . $xoopsDB->prefix('ads_categories') . " where pid=$cid ORDER BY " . $xoopsModuleConfig['ads_classm'] . '');

    $numrows = $xoopsDB->getRowsNum($subresult);

    if (0 != $numrows) {
        $scount = 0;

        $xoopsTpl->assign('availability', _ADS_AVAILAB);

        while (list($ccid, $title, $img) = $xoopsDB->fetchRow($subresult)) {
            $a_cat = [];

            $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

            $numrows = getTotalItems($ccid, _YES);

            $a_cat['image'] = '<a href="index.php?pa=Adsview&amp;cid=' . $ccid . "\"><img src='" . XOOPS_URL . "/modules/$mydirname/images/cat/$img' align='middle' alt=''></a><br>";

            $a_cat['link'] = '<a href="index.php?pa=Adsview&amp;cid=' . $ccid . "\"><b>$title</b></a>";

            $a_cat['adcount'] = $numrows;

            $a_cat['i'] = $scount;

            $a_cat['new'] = categorynewgraphic($ccid);

            $scount++;

            if (4 == $scount) {
                $scount = 0;
            }

            $xoopsTpl->append('subcategories', $a_cat);
        }

        if (0 == $count) {
            $cols = 4 - $scount;
        }

        $xoopsTpl->assign('subcat_count', $scount - 1);
    }

    showViewAds($debut, $cid, $xoopsModuleConfig['ads_perpage'], $nbe);

    if (!isset($debut)) {
        $debut = 0;
    }

    //show render nav

    $xoopsTpl->assign('nav_page', $pagenav->renderNav());

    $xoopsTpl->assign('xoops_pagetitle', $title);
}

#  function viewads
#####################################################
function viewads($lid)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $xoopsTpl, $xoopsUser, $myts, $meta, $mydirname;

    $GLOBALS['xoopsOption']['template_main'] = 'ads_item.html';

    require XOOPS_ROOT_PATH . '/header.php';

    // add for Nav by Tom

    require XOOPS_ROOT_PATH . "/modules/$mydirname/class/nav.php";

    if (isset($_GET['lid'])) {
        $lid = (int)$_GET['lid'];
    } else {
        $lid = 0;
    }

    if ('1' == $xoopsModuleConfig['ads_rate_item']) {
        $rate = '1';
    } else {
        $rate = '0';
    }

    $xoopsTpl->assign('rate', $rate);

    $result = $xoopsDB->query('select lid, cid, title, expire, type, desctext, tel, price, typeprice, date, email, submitter, usid, town, country, valid, photo, photo2, photo3, view, item_rating, item_votes, user_rating, user_votes, comments FROM ' . $xoopsDB->prefix('ads_listing') . " WHERE lid = '$lid'");

    $recordexist = $xoopsDB->getRowsNum($result);

    $xoopsTpl->assign('add_from', _ADS_ADDFROM . ' ' . $xoopsConfig['sitename']);

    // Add Template assign  by Tom

    $xoopsTpl->assign('add_from_title', _ADS_ADDFROM);

    $xoopsTpl->assign('add_from_sitename', $xoopsConfig['sitename']);

    $xoopsTpl->assign('ad_exists', $recordexist);

    /* ---- add nav  by Tom ----- */

    $count = 0;

    if (!$debut) {
        $debut = 0;
    }

    $x = 0;

    $i = 0;

    $requete2 = $xoopsDB->query('select cid from ' . $xoopsDB->prefix('ads_listing') . ' where  lid=' . $lid . '');

    [$cid] = $xoopsDB->fetchRow($requete2);

    $requete = $xoopsDB->query('select cid, pid, title from ' . $xoopsDB->prefix('ads_categories') . ' where  cid=' . $cid . '');

    [$ccid, $pid, $ctitle] = $xoopsDB->fetchRow($requete);

    $ctitle = htmlspecialchars($ctitle, ENT_QUOTES | ENT_HTML5);

    $varid[$x] = $ccid;

    $varnom[$x] = $ctitle;

    [$nbe] = $xoopsDB->fetchRow($xoopsDB->query('select COUNT(*) FROM ' . $xoopsDB->prefix('ads_listing') . " where valid='Yes' AND cid='$cid'"));

    $pagenav = new PageNav($nbe, $xoopsModuleConfig['ads_perpage'], $debut, "pa=Adsview&amp;cid=$cid&amp;debut", '');

    if (0 != $pid) {
        $x = 1;

        while (0 != $pid) {
            $requete2 = $xoopsDB->query('select cid, pid, title from ' . $xoopsDB->prefix('ads_categories') . ' where cid=' . $pid . '');

            [$ccid, $pid, $ctitle] = $xoopsDB->fetchRow($requete2);

            $ctitle = htmlspecialchars($ctitle, ENT_QUOTES | ENT_HTML5);

            $varid[$x] = $ccid;

            $varnom[$x] = $ctitle;

            $x++;
        }

        $x -= 1;
    }

    $subcats = '';

    while (-1 != $x) {
        $subcats .= ' &raquo; <a href="index.php?pa=Adsview&amp;cid=' . $varid[$x] . '">' . $varnom[$x] . '</a>';

        $x -= 1;
    }

    $xoopsTpl->assign('nav_main', '<a href="index.php">' . _ADS_MAIN . '</a>');

    $xoopsTpl->assign('nav_sub', $subcats);

    $xoopsTpl->assign('nav_subcount', $nbe);

    /* ---- /nav ----- */

    if ($recordexist) {
        [$lid, $cid, $title, $expire, $type, $desctext, $tel, $price, $typeprice, $date, $email, $submitter, $usid, $town, $country, $valid, $photo, $photo2, $photo3, $view, $item_rating, $item_votes, $user_rating, $user_votes, $comments] = $xoopsDB->fetchRow($result);

        //	Specification for Japan: move after for view count up judge

        //		$xoopsDB->queryf("UPDATE ".$xoopsDB->prefix("ads_listing")." SET view=view+1 WHERE lid = '$lid'");

        //		$useroffset = "";
//    	if($xoopsUser)
//    	{

        //			$timezone = $xoopsUser->timezone();

        //			if(isset($timezone))

        //				$useroffset = $xoopsUser->timezone();

        //			else

        //				$useroffset = $xoopsConfig['default_TZ'];

        //		}

        //	Specification for Japan: add  $viewcount_judge for view count up judge

        $viewcount_judge = true;

        $useroffset = '';

        if ($xoopsUser) {
            $timezone = $xoopsUser->timezone();

            if (isset($timezone)) {
                $useroffset = $xoopsUser->timezone();
            } else {
                $useroffset = $xoopsConfig['default_TZ'];
            }

            //	Specification for Japan: view count up judge

            if ((1 == $xoopsUser->getVar('uid')) || ($xoopsUser->getVar('uid') == $usid)) {
                $viewcount_judge = false;
            }
        }

        //	Specification for Japan: view count up judge

        if (true === $viewcount_judge) {
            $xoopsDB->queryF('UPDATE ' . $xoopsDB->prefix('ads_listing') . " SET view=view+1 WHERE lid = '$lid'");
        }

        if (1 == $item_votes) {
            $votestring = _ADS_ONEVOTE;
        } else {
            $votestring = sprintf(_ADS_NUMVOTES, $item_votes);
        }

        $date = ($useroffset * 3600) + $date;

        $date2 = $date + ($expire * 86400);

        $date = formatTimestamp($date, 's');

        $date2 = formatTimestamp($date2, 's');

        $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

        $expire = htmlspecialchars($expire, ENT_QUOTES | ENT_HTML5);

        $type = htmlspecialchars($type, ENT_QUOTES | ENT_HTML5);

        $desctext = $myts->displayTarea($desctext);

        $tel = htmlspecialchars($tel, ENT_QUOTES | ENT_HTML5);

        $price = htmlspecialchars($price, ENT_QUOTES | ENT_HTML5);

        $typeprice = htmlspecialchars($typeprice, ENT_QUOTES | ENT_HTML5);

        $submitter = htmlspecialchars($submitter, ENT_QUOTES | ENT_HTML5);

        $town = htmlspecialchars($town, ENT_QUOTES | ENT_HTML5);

        $country = htmlspecialchars($country, ENT_QUOTES | ENT_HTML5);

        $printA = "<a href=\"listing-p-f.php?op=PrintAd&amp;lid=$lid\" target=_blank><img src=\"images/print.gif\" border=0 alt=\"" . _ADS_PRINT . '" width=15 height=11></a>&nbsp;';

        if ($usid > 0) {
            $xoopsTpl->assign('submitter', _ADS_VIEW_MY_ADS . " <a href='index.php?pa=viewmyads&amp;usid=$usid'>$submitter</a>");
        } else {
            $xoopsTpl->assign('submitter', _ADS_VIEW_MY_ADS . " $submitter");
        }

        $xoopsTpl->assign('lid', $lid);

        // Add PM by Tom

        //$contact_pm ="<a href=\"javascript:openWithSelfMain('".XOOPS_URL."/pmlite.php?send2=1&amp;to_userid=".$usid."', 'pmlite', 450, 380);\"><img src=\"".XOOPS_URL."/images/icons/pm.gif\" alt=\"".sprintf(_SENDPMTO,$xoopsUser->getVar('uname'))."\"></a>";

        //$xoopsTpl->assign('contact_pm', "$contact_pm");

        $xoopsTpl->assign('read', "$view " . _ADS_VIEW2);

        $xoopsTpl->assign('rating', number_format($item_rating, 2));

        $xoopsTpl->assign('votes', $votestring);

        $xoopsTpl->assign('lang_rating', _ADS_RATINGC);

        $xoopsTpl->assign('lang_ratethisitem', _ADS_RATETHISITEM);

        if ($xoopsUser) {
            $calusern = $xoopsUser->getVar('uid', 'E');

            if ($usid == $calusern) {
                $xoopsTpl->assign('modify', "<a href=\"modify.php?op=ModAd&amp;lid=$lid\"><img src=\"images/modif.gif\" border=0 alt=\"" . _ADS_MODIFANN . "\"></a>&nbsp;<a href=\"modify.php?op=ListingDel&amp;lid=$lid\"><img src=\"images/del.gif\" border=0 alt=\"" . _ADS_SUPPRANN . '"></a>');
            }

            if ($xoopsUser->isAdmin()) {
                $xoopsTpl->assign('admin', "<a href=\"admin/index.php?op=ModifyAds&amp;lid=$lid\"><img src=\"images/modif.gif\" border=0 alt=\"" . _ADS_MODADMIN . '"></a>');
            }
        }

        $xoopsTpl->assign('type', $type);

        $xoopsTpl->assign('title', $title);

        $xoopsTpl->assign('desctext', $desctext);

        $xoopsTpl->assign('xoops_pagetitle', "$ctitle - $type - $title");

        if ($price > 0) {
            // Add Template assign  by Tom

            $xoopsTpl->assign('price', '<b>' . _ADS_PRICE2 . '</b>' . $xoopsModuleConfig['ads_monnaie'] . " $price  - $typeprice");

            $xoopsTpl->assign('price_head', _ADS_PRICE2);

            $xoopsTpl->assign('price_price', '' . $xoopsModuleConfig['ads_monnaie'] . "  $price");

            $xoopsTpl->assign('price_typeprice', $typeprice);
        }

        $contact = '<b>' . _ADS_CONTACT . "</b> <a href=\"contact.php?lid=$lid\">" . _ADS_BYMAIL2 . '</a>';

        // Add Template assign  by Tom

        $xoopsTpl->assign('contact_head', _ADS_CONTACT);

        $xoopsTpl->assign('contact_email', "<a href=\"contact.php?lid=$lid\">" . _ADS_BYMAIL2 . '</a>');

        $xoopsTpl->assign('ads_mustlogin', '' . _ADS_MUSTLOGIN . '');

        if ((1 == $xoopsModuleConfig['ads_login_ok']) || ($xoopsUser)) {
            $xoopsTpl->assign('ads_login_ok', '1');
        } else {
            $xoopsTpl->assign('ads_login_ok', '');
        }

        if ($tel) {
            $contact .= '<br><b>' . _ADS_TEL . "</b> $tel";

            // Add Template assign  by Tom

            $xoopsTpl->assign('contact_tel_head', _ADS_TEL);

            $xoopsTpl->assign('contact_tel', (string)$tel);
        }

        // Layout CHG by Tom

        $contact .= '<br>';

        if ($town) {
            $contact .= '<br><b>' . _ADS_TOWN . "</b> $town";

            // Add Template assign  by Tom

            $xoopsTpl->assign('local_town', (string)$town);
        }

        if ($country) {
            $contact .= '<br><b>' . _ADS_COUNTRY . "</b> $country";

            // Add Template assign  by Tom

            $xoopsTpl->assign('local_country', (string)$country);
        }

        $xoopsTpl->assign('contact', $contact);

        // Add Template assign  by Tom

        $xoopsTpl->assign('local_head', _ADS_LOCAL);

        if ($photo) {
            $xoopsTpl->assign('photo', "<a href=\"display-image.php?lid=$lid\" target=_blank><img src=\"photo/$photo\" alt=\"$title\" width=\"130px\">");
        }

        if ($photo2) {
            $xoopsTpl->assign('photo2', "<a href=\"display-image2.php?lid=$lid\" target=_blank><img src=\"photo2/$photo2\" alt=\"$title\" width=\"130px\">");
        }

        if ($photo3) {
            $xoopsTpl->assign('photo3', "<a href=\"display-image3.php?lid=$lid\" target=_blank><img src=\"photo3/$photo3\" alt=\"$title\" width=\"130px\">");
        }

        $xoopsTpl->assign('date', _ADS_DATE2 . " $date " . _ADS_DISPO . " $date2 &nbsp;&nbsp; $printA");
    } else {
        $xoopsTpl->assign('no_ad', _ADS_NOCLAS);
    }

    $result8 = $xoopsDB->query('select title from ' . $xoopsDB->prefix('ads_categories') . " where cid=$cid");

    [$ctitle] = $xoopsDB->fetchRow($result8);

    $xoopsTpl->assign('friend', "<a href=\"../$mydirname/listing-p-f.php?op=SendFriend&amp;lid=$lid\"><img src=\"../$mydirname/images/friend.gif\" border=\"0\" alt=\"\" width=\"15\" height=\"11\"></a>");

    $xoopsTpl->assign('link_main', "<a href=\"../$mydirname/\">" . _ADS_MAIN . '</a>');

    $xoopsTpl->assign('link_cat', "<a href=\"index.php?pa=Adsview&amp;cid=$cid\">" . _ADS_GORUB . " $ctitle</a>");
}

#  function viewmyads
#####################################################
function viewmyads($usid, $debut)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $xoopsTpl, $xoopsUser, $myts, $meta, $mydirname;

    $GLOBALS['xoopsOption']['template_main'] = 'ads_view_myads.html';

    require XOOPS_ROOT_PATH . '/header.php';

    // add for Nav by Tom

    require XOOPS_ROOT_PATH . "/modules/$mydirname/class/nav.php";

    require XOOPS_ROOT_PATH . '/include/comment_view.php';

    if (isset($_GET['usid'])) {
        $usid = (int)$_GET['usid'];
    } else {
        $usid = 0;
    }

    if ('1' == $xoopsModuleConfig['ads_rate_user']) {
        $rate = '1';
    } else {
        $rate = '0';
    }

    $xoopsTpl->assign('rate', $rate);

    $result = $xoopsDB->query('select lid, cid, title, expire, type, desctext, tel, price, typeprice, date, email, submitter, usid, town, country, valid, photo, photo2, photo3, view, item_rating, item_votes, user_rating, user_votes, comments FROM ' . $xoopsDB->prefix('ads_listing') . " WHERE usid = '$usid' AND valid='Yes' ORDER BY " . $xoopsModuleConfig['ads_classm'] . '');

    $recordexist = $xoopsDB->getRowsNum($result);

    $xoopsTpl->assign('add_from', _ADS_ADDFROM . ' ' . $xoopsConfig['sitename']);

    // Add Template assign  by Tom

    $xoopsTpl->assign('add_from_title', _ADS_ADDFROM);

    $xoopsTpl->assign('add_from_sitename', $xoopsConfig['sitename']);

    $xoopsTpl->assign('usid', $usid);

    $xoopsTpl->assign('ad_exists', $recordexist);

    /* ---- add nav  by Tom ----- */

    $count = 0;

    if (!$debut) {
        $debut = 0;
    }

    $x = 0;

    $i = 0;

    $requete2 = $xoopsDB->query('select cid from ' . $xoopsDB->prefix('ads_listing') . ' where  usid=' . $usid . '');

    [$cid] = $xoopsDB->fetchRow($requete2);

    $requete = $xoopsDB->query('select cid, pid, title from ' . $xoopsDB->prefix('ads_categories') . ' where  cid=' . $cid . '');

    [$ccid, $pid, $title] = $xoopsDB->fetchRow($requete);

    $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

    $varid[$x] = $ccid;

    $varnom[$x] = $title;

    [$nbe] = $xoopsDB->fetchRow($xoopsDB->query('select COUNT(*) FROM ' . $xoopsDB->prefix('ads_listing') . " where valid='Yes' AND usid='$usid'"));

    $pagenav = new PageNav($nbe, $xoopsModuleConfig['ads_perpage'], $debut, "pa=Adsview&amp;cid=$cid&amp;debut", '');

    if (0 != $pid) {
        $x = 1;

        while (0 != $pid) {
            $requete2 = $xoopsDB->query('select cid, pid, title from ' . $xoopsDB->prefix('ads_categories') . ' where cid=' . $pid . '');

            [$ccid, $pid, $title] = $xoopsDB->fetchRow($requete2);

            $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

            $varid[$x] = $ccid;

            $varnom[$x] = $title;

            $x++;
        }

        $x -= 1;
    }

    $subcats = '';

    while (-1 != $x) {
        $subcats .= ' &raquo; <a href="index.php?pa=Adsview&amp;cid=' . $varid[$x] . '">' . $varnom[$x] . '</a>';

        $x -= 1;
    }

    $xoopsTpl->assign('nav_main', '<a href="index.php">' . _ADS_MAIN . '</a>');

    $xoopsTpl->assign('nav_sub', $subcats);

    $xoopsTpl->assign('nav_subcount', $nbe);

    /* ---- /nav ----- */

    if ($recordexist) {
        [$lid, $cid, $title, $expire, $type, $desctext, $tel, $price, $typeprice, $date, $email, $submitter, $usid, $town, $country, $valid, $photo, $photo2, $photo3, $view, $item_rating, $item_votes, $user_rating, $user_votes, $comments] = $xoopsDB->fetchRow($result);

        //	Specification for Japan: move after for view count up judge

        //		$xoopsDB->queryf("UPDATE ".$xoopsDB->prefix("ads_listing")." SET view=view+1 WHERE lid = '$lid'");

        //		$useroffset = "";
//    	if($xoopsUser)
//    	{

        //			$timezone = $xoopsUser->timezone();

        //			if(isset($timezone))

        //				$useroffset = $xoopsUser->timezone();

        //			else

        //				$useroffset = $xoopsConfig['default_TZ'];

        //		}

        //	Specification for Japan: add  $viewcount_judge for view count up judge

        $viewcount_judge = true;

        $useroffset = '';

        if ($xoopsUser) {
            $timezone = $xoopsUser->timezone();

            if (isset($timezone)) {
                $useroffset = $xoopsUser->timezone();
            } else {
                $useroffset = $xoopsConfig['default_TZ'];
            }

            //	Specification for Japan: view count up judge

            if ((1 == $xoopsUser->getVar('uid')) || ($xoopsUser->getVar('uid') == $usid)) {
                $viewcount_judge = false;
            }
        }

        //	Specification for Japan: view count up judge

        if (true === $viewcount_judge) {
            $xoopsDB->queryF('UPDATE ' . $xoopsDB->prefix('ads_listing') . " SET view=view+1 WHERE lid = '$lid'");
        }

        if (1 == $user_votes) {
            $votestring = _ADS_ONEVOTE;
        } else {
            $votestring = sprintf(_ADS_NUMVOTES, $user_votes);
        }

        $date = ($useroffset * 3600) + $date;

        $date2 = $date + ($expire * 86400);

        $date = formatTimestamp($date, 's');

        $date2 = formatTimestamp($date2, 's');

        $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

        $expire = htmlspecialchars($expire, ENT_QUOTES | ENT_HTML5);

        $type = htmlspecialchars($type, ENT_QUOTES | ENT_HTML5);

        $desctext = $myts->displayTarea($desctext);

        $tel = htmlspecialchars($tel, ENT_QUOTES | ENT_HTML5);

        $price = htmlspecialchars($price, ENT_QUOTES | ENT_HTML5);

        $typeprice = htmlspecialchars($typeprice, ENT_QUOTES | ENT_HTML5);

        $submitter = htmlspecialchars($submitter, ENT_QUOTES | ENT_HTML5);

        $town = htmlspecialchars($town, ENT_QUOTES | ENT_HTML5);

        $country = htmlspecialchars($country, ENT_QUOTES | ENT_HTML5);

        $printA = "<a href=\"listing-p-f.php?op=PrintAd&amp;lid=$lid\" target=_blank><img src=\"images/print.gif\" border=0 alt=\"" . _ADS_PRINT . '" width=15 height=11></a>&nbsp;';

        $xoopsTpl->assign('submitter', _ADS_MY_ADS . " $submitter");

        // Add PM by Tom

        //$contact_pm ="<a href=\"javascript:openWithSelfMain('".XOOPS_URL."/pmlite.php?send2=1&amp;to_userid=".$usid."', 'pmlite', 450, 380);\"><img src=\"".XOOPS_URL."/images/icons/pm.gif\" alt=\"".sprintf(_SENDPMTO,$xoopsUser->getVar('uname'))."\"></a>";

        //$xoopsTpl->assign('contact_pm', "$contact_pm");

        $xoopsTpl->assign('read', "$view " . _ADS_VIEW2);

        $xoopsTpl->assign('rating', number_format($user_rating, 2));

        $xoopsTpl->assign('votes', $votestring);

        $xoopsTpl->assign('comments_head', _ADS_COMMENTS_HEAD);

        $xoopsTpl->assign('lang_user_rating', _ADS_USER_RATING);

        $xoopsTpl->assign('lang_ratethisuser', _ADS_RATETHISUSER);

        if ($xoopsUser) {
            $calusern = $xoopsUser->getVar('uid', 'E');

            if ($usid == $calusern) {
                $istheirs = '1';

                $xoopsTpl->assign('modify', "<a href=\"modify.php?op=ModAd&amp;lid=$lid\"><img src=\"images/modif.gif\" border=0 alt=\"" . _ADS_MODIFANN . "\"></a>&nbsp;<a href=\"modify.php?op=ListingDel&amp;lid=$lid\"><img src=\"images/del.gif\" border=0 alt=\"" . _ADS_SUPPRANN . '"></a>');

                $xoopsTpl->assign('istheirs', $istheirs);
            }

            if ($xoopsUser->isAdmin()) {
                $xoopsTpl->assign('admin', "<a href=\"admin/index.php?op=ModifyAds&amp;lid=$lid\"><img src=\"images/modif.gif\" border=0 alt=\"" . _ADS_MODADMIN . '"></a>');
            }
        }

        $xoopsTpl->assign('type', $type);

        $xoopsTpl->assign('title', $title);

        $xoopsTpl->assign('desctext', $desctext);

        if ($price > 0) {
            // Add Template assign  by Tom

            $xoopsTpl->assign('price', '<b>' . _ADS_PRICE2 . '</b>' . $xoopsModuleConfig['ads_monnaie'] . " $price  - $typeprice");

            $xoopsTpl->assign('price_head', _ADS_PRICE2);

            $xoopsTpl->assign('price_price', '' . $xoopsModuleConfig['ads_monnaie'] . "  $price");

            $xoopsTpl->assign('price_typeprice', $typeprice);
        }

        $contact = '<b>' . _ADS_CONTACT . "</b> <a href=\"contact.php?lid=$lid\">" . _ADS_BYMAIL2 . '</a>';

        // Add Template assign  by Tom

        $xoopsTpl->assign('contact_head', _ADS_CONTACT);

        $xoopsTpl->assign('contact_email', "<a href=\"contact.php?lid=$lid\">" . _ADS_BYMAIL2 . '</a>');

        $xoopsTpl->assign('ads_mustlogin', '' . _ADS_MUSTLOGIN . '');

        if ((1 == $xoopsModuleConfig['ads_login_ok']) || ($xoopsUser)) {
            $xoopsTpl->assign('ads_login_ok', '1');
        } else {
            $xoopsTpl->assign('ads_login_ok', '');
        }

        if ($tel) {
            $contact .= '<br><b>' . _ADS_TEL . "</b> $tel";

            // Add Template assign  by Tom

            $xoopsTpl->assign('contact_tel_head', _ADS_TEL);

            $xoopsTpl->assign('contact_tel', (string)$tel);
        }

        // Layout CHG by Tom

        $contact .= '<br>';

        if ($town) {
            $contact .= '<br><b>' . _ADS_TOWN . "</b> $town";

            // Add Template assign  by Tom

            $xoopsTpl->assign('local_town', (string)$town);
        }

        if ($country) {
            $contact .= '<br><b>' . _ADS_COUNTRY . "</b> $country";

            // Add Template assign  by Tom

            $xoopsTpl->assign('local_country', (string)$country);
        }

        $xoopsTpl->assign('contact', $contact);

        // Add Template assign  by Tom

        $xoopsTpl->assign('local_head', _ADS_LOCAL);

        if ($photo) {
            $xoopsTpl->assign('photo', "<a href=\"display-image.php?lid=$lid\" target=_blank><img src=\"photo/$photo\" alt=\"$title\" width=\"130px\">");
        }

        if ($photo2) {
            $xoopsTpl->assign('photo2', "<a href=\"display-image2.php?lid=$lid\" target=_blank><img src=\"photo2/$photo2\" alt=\"$title\" width=\"130px\">");
        }

        if ($photo3) {
            $xoopsTpl->assign('photo3', "<a href=\"display-image3.php?lid=$lid\" target=_blank><img src=\"photo3/$photo3\" alt=\"$title\" width=\"130px\">");
        }

        $xoopsTpl->assign('date', _ADS_DATE2 . " $date " . _ADS_DISPO . " $date2 &nbsp;&nbsp; $printA");
    } else {
        $xoopsTpl->assign('no_ad', _ADS_NOCLAS);
    }

    $result8 = $xoopsDB->query('select title from ' . $xoopsDB->prefix('ads_categories') . " where cid=$cid");

    [$ctitle] = $xoopsDB->fetchRow($result8);

    $xoopsTpl->assign('friend', "<a href=\"../$mydirname/listing-p-f.php?op=SendFriend&amp;lid=$lid\"><img src=\"../$mydirname/images/friend.gif\" border=\"0\" alt=\"\" width=\"15\" height=\"11\"></a>");

    $xoopsTpl->assign('link_main', "<a href=\"../$mydirname/\">" . _ADS_MAIN . '</a>');

    $xoopsTpl->assign('link_cat', "<a href=\"index.php?pa=Adsview&amp;cid=$cid\">" . _ADS_GORUB . " $ctitle</a>");

    showViewAds($debut, $cid, $xoopsModuleConfig['ads_perpage'], $nbe);

    if (!isset($debut)) {
        $debut = 0;
    }

    //show render nav

    $xoopsTpl->assign('nav_page', $pagenav->renderNav());
}

#  function categorynewgraphic
#####################################################
function categorynewgraphic($cat)
{
    global $xoopsDB, $mydirname;

    $newresult = $xoopsDB->query('select date from ' . $xoopsDB->prefix('ads_listing') . " where cid=$cat and valid = 'Yes' order by date desc limit 1");

    [$timeann] = $xoopsDB->fetchRow($newresult);

    $count = 1;

    $startdate = (time() - (86400 * $count));

    if ($startdate < $timeann) {
        return '<img src="' . XOOPS_URL . "/modules/$mydirname/images/newred.gif\">";
    }
}

######################################################

$pa = $_GET['pa'] ?? '';
$lid = $_GET['lid'] ?? '';
$cid = $_GET['cid'] ?? '';
$debut = $_GET['debut'] ?? '';
$usid = $_GET['usid'] ?? '';

switch ($pa) {
    case 'Adsview':
        $GLOBALS['xoopsOption']['template_main'] = 'ads_category.html';
        require XOOPS_ROOT_PATH . '/header.php';
        Adsview($cid, $debut);
        break;
    case 'viewads':
        $GLOBALS['xoopsOption']['template_main'] = 'ads_item.html';
        require XOOPS_ROOT_PATH . '/header.php';
        viewads($lid);
        break;
    case 'viewmyads':
        $GLOBALS['xoopsOption']['template_main'] = 'ads_view_myads.html';
        require XOOPS_ROOT_PATH . '/header.php';
        viewmyads($usid, $debut);
        break;
    default:
        $GLOBALS['xoopsOption']['template_main'] = 'ads_index.html';
        require XOOPS_ROOT_PATH . '/header.php';
        index();
        break;
}

require XOOPS_ROOT_PATH . '/footer.php';
