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
require_once dirname(__DIR__, 3) . '/include/cp_header.php';
    $mydirname = basename(dirname(__FILE__, 2));

require_once(XOOPS_ROOT_PATH . "/modules/$mydirname/include/gtickets.php");
require_once(XOOPS_ROOT_PATH . "/modules/$mydirname/include/functions.php");
require_once XOOPS_ROOT_PATH . "/modules/$mydirname/class/xoopstree.php";

#  function AdsNewCat
#####################################################
function AdsNewCat($cat)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $myts, $mydirname;

    $mytree = new XoopsTree($xoopsDB->prefix('ads_categories'), 'cid', 'pid');

    // 文字化け対策 by Tom

    xoops_cp_header();

    include('./mymenu.php');

    echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_ADS_ADDSUBCAT . '</legend>';

    ShowImg();

    echo '<form method="post" action="category.php" name="imcat"><input type="hidden" name="op" value="AdsAddCat"></font><br><br>
		<table border=0>
    <tr>
      <td>' . _AM_ADS_CATNAME . ' </td><td colspan=2><input type="text" name="title" size="30" maxlength="100">&nbsp; ' . _AM_ADS_IN . ' &nbsp;';

    $result = $xoopsDB->query('select pid, title, img, ordre from ' . $xoopsDB->prefix('ads_categories') . " where cid=$cat");

    [$pid, $title, $imgs, $ordre] = $xoopsDB->fetchRow($result);

    $mytree->makeMySelBox('title', 'title', $cat, 1);

    echo '</td>
	</tr>
    <tr>
      <td>' . _AM_ADS_IMGCAT . '  </td><td colspan=2><select name="img" onChange="showimage()">';

    $rep = XOOPS_ROOT_PATH . "/modules/$mydirname/images/cat";

    $handle = opendir($rep);

    while ($file = readdir($handle)) {
        $filelist[] = $file;
    }

    asort($filelist);

    while (list($key, $file) = each($filelist)) {
        if (!preg_match('.gif|.jpg|.png', $file)) {
            if ('.' == $file || '..' == $file) {
                $a = 1;
            }
        } else {
            if ('default.gif' == $file) {
                echo "<option value=$file selected>$file</option>";
            } else {
                echo "<option value=$file>$file</option>";
            }
        }
    }

    echo '</select>&nbsp;&nbsp;<img src="' . XOOPS_URL . "/modules/$mydirname/images/cat/default.gif\" name=\"avatar\" align=\"absmiddle\"> </td></tr><tr><td>&nbsp;</td><td colspan=2>" . _AM_ADS_REPIMGCAT . " /modules/$mydirname/images/cat/</td></tr>";

    echo '<tr><td>' . _AM_ADS_DISPLPRICE2 . ' </td><td colspan=2><input type="radio" name="affprice" value="1">' . _AM_ADS_OUI . '&nbsp;&nbsp; <input type="radio" name="affprice" value="0">' . _AM_ADS_NON . ' (' . _AM_ADS_INTHISCAT . ')</td></tr>';

    if ($xoopsModuleConfig['ads_classm'] = 'ordre') {
        echo '<tr><td>' . _AM_ADS_ORDRE . ' </td><td><input type="text" name="ordre" size="4"></td><td><input type="submit" value="' . _AM_ADS_ADD . '"></td></tr>';
    } else {
        echo '<tr><td colspan=3><input type="submit" value="' . _AM_ADS_ADD . '"></td></tr>';
    }

    echo '</table>
	    </form>';

    echo '<br>';

    echo '</fieldset><br>';

    xoops_cp_footer();
}

#  function AdsModCat
#####################################################
function AdsModCat($cid)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $myts, $mydirname;

    $mytree = new XoopsTree($xoopsDB->prefix('ads_categories'), 'cid', 'pid');

    xoops_cp_header();

    include('./mymenu.php');

    echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_ADS_MODIFCAT . '</legend>';

    ShowImg();

    $result = $xoopsDB->query('select pid, title, img, ordre, affprice from ' . $xoopsDB->prefix('ads_categories') . " where cid=$cid");

    [$pid, $title, $imgs, $ordre, $affprice] = $xoopsDB->fetchRow($result);

    $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

    echo '<form action="category.php" method="post" name="imcat">
		<table border="0"><tr>
	<td>' . _AM_ADS_CATNAME . "   </td><td><input type=\"text\" name=\"title\" value=\"$title\" size=\"30\" maxlength=\"50\">&nbsp; " . _AM_ADS_IN . ' &nbsp;';

    $mytree->makeMySelBox('title', 'title', $pid, 1);

    echo '</td></tr><tr>
	<td>' . _AM_ADS_IMGCAT . '  </td><td><select name="img" onChange="showimage()">';

    $rep = XOOPS_ROOT_PATH . "/modules/$mydirname/images/cat";

    $handle = opendir($rep);

    while ($file = readdir($handle)) {
        $filelist[] = $file;
    }

    asort($filelist);

    while (list($key, $file) = each($filelist)) {
        if (!preg_match('.gif|.jpg|.png', $file)) {
            if ('.' == $file || '..' == $file) {
                $a = 1;
            }
        } else {
            if ($file == $imgs) {
                echo "<option value=$file selected>$file</option>";
            } else {
                echo "<option value=$file>$file</option>";
            }
        }
    }

    echo '</select>&nbsp;&nbsp;<img src="' . XOOPS_URL . "/modules/$mydirname/images/cat/$imgs\" name=\"avatar\" align=\"absmiddle\"> </td></tr><tr><td>&nbsp;</td><td>" . _AM_ADS_REPIMGCAT . " /modules/$mydirname/images/cat/</td></tr>";

    echo '<tr><td>' . _AM_ADS_DISPLPRICE2 . ' </td><td colspan=2><input type="radio" name="affprice" value="1"';

    if ('1' == $affprice) {
        echo 'checked';
    }

    echo '>' . _AM_ADS_OUI . '&nbsp;&nbsp; <input type="radio" name="affprice" value="0"';

    if ('0' == $affprice) {
        echo 'checked';
    }

    echo '>' . _AM_ADS_NON . ' (' . _AM_ADS_INTHISCAT . ')</td></tr>';

    if ($xoopsModuleConfig['ads_classm'] = 'ordre') {
        echo '<tr><td>' . _AM_ADS_ORDRE . " </td><td><input type=\"text\" name=\"ordre\" size=\"4\" value=\"$ordre\"></td></tr>";
    }

    echo '</table><P>';

    echo "<input type=\"hidden\" name=\"cidd\" value=\"$cid\">"
        . '<input type="hidden" name="op" value="AdsModCatS">'
        . '<table border="0"><tr><td>'
        . '<input type="submit" value="' . _AM_ADS_SAVMOD . '"></form></td><td>'
        . '<form action="category.php" method="post">'
        . "<input type=\"hidden\" name=\"cid\" value=\"$cid\">"
        . '<input type="hidden" name="op" value="AdsDelCat">'
        . '<input type="submit" value="' . _AM_ADS_DEL . '"></form></td></tr></table>';

    echo '</fieldset><br>';

    xoops_cp_footer();
}

#  function AdsModCatS
#####################################################
function AdsModCatS($cidd, $cid, $img, $title, $ordre, $affprice)
{
    global $xoopsDB, $xoopsConfig, $myts, $mydirname;

    $title = $myts->addSlashes($title);

    $xoopsDB->query('update ' . $xoopsDB->prefix('ads_categories') . " set title='$title', pid='$cid', img='$img', ordre='$ordre', affprice='$affprice' where cid=$cidd");

    redirect_header('map.php', 1, _AM_ADS_CATSMOD);

    exit();
}

    #  function AdsAddCat
#####################################################
function AdsAddCat($title, $cid, $img, $ordre, $affprice)
{
    global $xoopsDB, $xoopsConfig, $myts, $mydirname;

    $title = $myts->addSlashes($title);

    if ('' == $title) {
        $title = '! ! ? ! !';
    }

    $xoopsDB->query('insert into ' . $xoopsDB->prefix('ads_categories') . " values (NULL, '$cid', '$title', '$img', '$ordre', '$affprice')");

    redirect_header('map.php', 1, _AM_ADS_CATADD);

    exit();
}

#  function AdsDelCat
#####################################################
function AdsDelCat($cid, $ok = 0)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $mydirname;

    if (1 == (int)$ok) {
        $xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();

        $xoopsDB->queryF('delete from ' . $xoopsDB->prefix('ads_categories') . " where cid=$cid or pid=$cid");

        $xoopsDB->queryF('delete from ' . $xoopsDB->prefix('ads_listing') . " where cid=$cid");

        redirect_header('map.php', 1, _AM_ADS_CATDEL);

        exit();
    }

    xoops_cp_header();

    OpenTable();

    echo '<br><center><b>' . _AM_ADS_SURDELCAT . '</b><br><br>';

    echo "[ <a href=\"category.php?op=AdsDelCat&cid=$cid&ok=1\">" . _AM_ADS_OUI . '</a> | <a href="map.php">' . _AM_ADS_NON . '</a> ]<br><br>';

    CloseTable();

    xoops_cp_footer();
}

#####################################################
foreach ($_POST as $k => $v) {
    ${$k} = $v;
}

$ok = $_GET['ok'] ?? '';

if (!isset($_POST['cid']) && isset($_GET['cid'])) {
    $cid = $_GET['cid'];
}
if (!isset($_POST['op']) && isset($_GET['op'])) {
    $op = $_GET['op'];
}

switch ($op) {
    case 'AdsNewCat':
    AdsNewCat($cid);
    break;
    case 'AdsAddCat':
    AdsAddCat($title, $cid, $img, $ordre, $affprice);
    break;
    case 'AdsDelCat':
    AdsDelCat($cid, $ok);
    break;
    case 'AdsModCat':
    AdsModCat($cid);
    break;
    case 'AdsModCatS':
    AdsModCatS($cidd, $cid, $img, $title, $ordre, $affprice);
    break;
    default:
    Index();
    break;
}
