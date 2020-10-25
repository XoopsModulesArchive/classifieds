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
$wysiwyg_folder = XOOPS_ROOT_PATH . '/class/wysiwyg';

    if (file_exists($wysiwyg_folder) && ('1' == $xoopsModuleConfig['ads_koivi'])) {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        require_once XOOPS_ROOT_PATH . '/class/wysiwyg/formwysiwygtextarea.php';
    } else {
        // for XOOPS CODE  by Tom

        require_once(XOOPS_ROOT_PATH . '/include/xoopscodes.php');
    }
    require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

    $myts = MyTextSanitizer::getInstance();

#  function Index
#####################################################
function Index()
{
    global $hlpfile, $xoopsDB, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $myts, $mydirname;

    $mytree = new XoopsTree($xoopsDB->prefix('ads_categories'), 'cid', 'pid');

    xoops_cp_header();

    include('./mymenu.php');

    // photo dir setting checker

    $photo_dir = XOOPS_ROOT_PATH . "/modules/$mydirname/photo/";

    if (!is_writable($photo_dir) || !is_readable($photo_dir)) {
        echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_ADS_CHECKER . '</legend><br>';

        echo "<font color='#FF0000'><b>" . _AM_ADS_DIRPERMS . '' . $photo_dir . "</b></font><br><br>\n";

        echo '</fieldset><br>';
    }

    // photo setting checker

    $photo2_dir = XOOPS_ROOT_PATH . "/modules/$mydirname/photo2/";

    if (!is_writable($photo2_dir) || !is_readable($photo2_dir)) {
        echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_ADS_CHECKER . '</legend><br>';

        echo "<font color='#FF0000'><b>" . _AM_ADS_DIRPERMS . '' . $photo2_dir . "</b></font><br><br>\n";

        echo '</fieldset><br>';
    }

    // resumes dir setting checker

    $photo3_dir = XOOPS_ROOT_PATH . "/modules/$mydirname/photo3/";

    if (!is_writable($photo3_dir) || !is_readable($photo3_dir)) {
        echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_ADS_CHECKER . '</legend><br>';

        echo "<font color='#FF0000'><b>" . _AM_ADS_DIRPERMS . '' . $photo3_dir . "</b></font><br><br>\n";

        echo '</fieldset><br>';
    }

    $result = $xoopsDB->query('select lid, title, date from ' . $xoopsDB->prefix('ads_listing') . " WHERE valid='No' order by lid");

    $numrows = $xoopsDB->getRowsNum($result);

    if ($numrows > 0) {
        echo "<table width='100%' border='0' cellspacing='1' cellpadding='8' style='border: 2px solid #2F5376;'><tr class='bg4'><td valign='top'>\n";

        echo _AM_ADS_THEREIS . " <b>$numrows</b> " . _AM_ADS_WAIT . '<br><br>';

        echo '<table width=100% cellpadding=2 cellspacing=0 border=0>';

        $rank = 1;

        while (list($lid, $title, $date) = $xoopsDB->fetchRow($result)) {
            $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

            $date2 = formatTimestamp($date, 's');

            if (is_int($rank / 2)) {
                $color = 'bg3';
            } else {
                $color = 'bg4';
            }

            echo "<tr class='$color'><td><a href=\"index.php?op=IndexView&lid=$lid\">$title</a></td><td align=right> $date2</td></tr>";

            $rank++;
        }

        echo '</table>';

        echo '</td></tr></table>';

        echo '<br>';
    } else {
        echo "<table width='100%' border='0' cellspacing='1' cellpadding='8' style='border: 2px solid #2F5376;'><tr class='bg4'><td valign='top'>\n";

        echo _AM_ADS_NOANNVAL;

        echo '</td></tr></table>';

        echo '<br>';
    }

    // Modify Annonces

    [$numrows] = $xoopsDB->fetchRow($xoopsDB->query('select COUNT(*) FROM ' . $xoopsDB->prefix('ads_listing') . ''));

    if ($numrows > 0) {
        echo "<table width='100%' border='0' cellspacing='1' cellpadding='8' style='border: 2px solid #2F5376;'><tr class='bg4'><td valign='top'>\n";

        echo '<form method="post" action="index.php">'
            . '<b>' . _AM_ADS_MODANN . '</b><br><br>'
            . '' . _AM_ADS_NUMANN . ' <input type="text" name="lid" size="12" maxlength="11">&nbsp;&nbsp;'
            . '<input type="hidden" name="op" value="ModifyAds">'
            . '<input type="submit" value="' . _AM_ADS_MODIF . '">'
            . '<br><br>' . _AM_ADS_ALLMODANN . ''
            . '</form><center><a href="../index.php">' . _AM_ADS_ACCESMYANN . '</a></center>';

        echo '</td></tr></table>';

        echo '<br>';
    }

    echo "<table width='100%' border='0' cellspacing='1' cellpadding='8' style='border: 2px solid #2F5376;'><tr class='bg4'><td valign='top'>\n";

    echo '<a href="map.php">' . _AM_ADS_GESTCAT . '</a>';

    echo '</td></tr></table>';

    echo '<br>';

    // Add Type

    echo "<table width='100%' border='0' cellspacing='1' cellpadding='8' style='border: 2px solid #2F5376;'><tr class='bg4'><td valign='top'>\n";

    echo '<form method="post" action="index.php">
		<b>' . _AM_ADS_ADDTYPE . '</b><br><br>
		' . _AM_ADS_TYPE . '	<input type="text" name="type" size="30" maxlength="100">	
		<input type="hidden" name="op" value="ListingAddType">
		<input type="submit" value="' . _AM_ADS_ADD . '">
		</form>';

    echo '<br>';

    // Modify Type

    [$numrows] = $xoopsDB->fetchRow($xoopsDB->query('select COUNT(*) FROM ' . $xoopsDB->prefix('ads_type') . ''));

    if ($numrows > 0) {
        echo '<form method="post" action="index.php">
			 <b>' . _AM_ADS_MODTYPE . '</b></font><br><br>';

        $result = $xoopsDB->query('select id_type, nom_type from ' . $xoopsDB->prefix('ads_type') . ' order by nom_type');

        echo '' . _AM_ADS_TYPE . ' <select name="id_type">';

        while (list($id_type, $nom_type) = $xoopsDB->fetchRow($result)) {
            $nom_type = htmlspecialchars($nom_type, ENT_QUOTES | ENT_HTML5);

            echo "<option value=\"$id_type\">$nom_type</option>";
        }

        echo '</select>
		    <input type="hidden" name="op" value="ListingModType"> 
			<input type="submit" value="' . _AM_ADS_MODIF . '">
		    </form>';

        echo '</td></tr></table>';

        echo '<br>';
    }

    // Add Price

    echo "<table width='100%' border='0' cellspacing='1' cellpadding='8' style='border: 2px solid #2F5376;'><tr class='bg4'><td valign='top'>\n";

    echo '<form method="post" action="index.php">
		<b>' . _AM_ADS_ADDPRICE . '</b><br><br>
		' . _AM_ADS_TYPE . '	<input type="text" name="type" size="30" maxlength="100">	
		<input type="hidden" name="op" value="ListingAddPrice">
		<input type="submit" value="' . _AM_ADS_ADD . '">
		</form>';

    echo '<br>';

    // Modify Price

    [$numrows] = $xoopsDB->fetchRow($xoopsDB->query('select COUNT(*) FROM ' . $xoopsDB->prefix('ads_price') . ''));

    if ($numrows > 0) {
        echo '<form method="post" action="index.php">
			<b>' . _AM_ADS_MODPRICE . '</b></font><br><br>';

        $result = $xoopsDB->query('select id_price, nom_price from ' . $xoopsDB->prefix('ads_price') . ' order by nom_price');

        echo '' . _AM_ADS_TYPE . ' <select name="id_price">';

        while (list($id_price, $nom_price) = $xoopsDB->fetchRow($result)) {
            $nom_price = htmlspecialchars($nom_price, ENT_QUOTES | ENT_HTML5);

            echo "<option value=\"$id_price\">$nom_price</option>";
        }

        echo '</select>
		    <input type="hidden" name="op" value="ListingModPrice"> 
			<input type="submit" value="' . _AM_ADS_MODIF . '">
		    </form>';

        echo '</td></tr></table>';

        echo '<br>';
    }

    xoops_cp_footer();
}

#  function IndexView
#####################################################
function IndexView($lid)
{
    global $xoopsDB, $xoopsModule, $xoopsConfig, $xoopsModuleConfig, $myts, $mydirname;

    $mytree = new XoopsTree($xoopsDB->prefix('ads_categories'), 'cid', 'pid');

    xoops_cp_header();

    $result = $xoopsDB->query('select lid, cid, title, expire, type, desctext, tel, price, typeprice, date, email, submitter, town, country, photo, photo2, photo3 from ' . $xoopsDB->prefix('ads_listing') . " WHERE valid='No' AND lid='$lid'");

    $numrows = $xoopsDB->getRowsNum($result);

    if ($numrows > 0) {
        echo "<table width='100%' border='0' cellspacing='1' cellpadding='8' style='border: 2px solid #2F5376;'><tr class='bg4'><td valign='top'>\n";

        echo '<B>' . _AM_ADS_WAIT . '</B><br><br>';

        [$lid, $cid, $title, $expire, $type, $desctext, $tel, $price, $typeprice, $date, $email, $submitter, $town, $country, $photo, $photo2, $photo3] = $xoopsDB->fetchRow($result);

        $date2 = formatTimestamp($date, 's');

        $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

        $expire = htmlspecialchars($expire, ENT_QUOTES | ENT_HTML5);

        $type = htmlspecialchars($type, ENT_QUOTES | ENT_HTML5);

        $desctext = htmlspecialchars($desctext, ENT_QUOTES | ENT_HTML5);

        $tel = htmlspecialchars($tel, ENT_QUOTES | ENT_HTML5);

        $price = htmlspecialchars($price, ENT_QUOTES | ENT_HTML5);

        $typeprice = htmlspecialchars($typeprice, ENT_QUOTES | ENT_HTML5);

        $submitter = htmlspecialchars($submitter, ENT_QUOTES | ENT_HTML5);

        $town = htmlspecialchars($town, ENT_QUOTES | ENT_HTML5);

        $country = htmlspecialchars($country, ENT_QUOTES | ENT_HTML5);

        echo '<form action="index.php" method="post">
			<table><tr>
			<td>' . _AM_ADS_NUMANN . " </td><td>$lid / $date2</td>
			</tr><tr>
			<td>" . _AM_ADS_SENDBY . " </td><td>$submitter</td>
			</tr><tr>
			<td>" . _AM_ADS_EMAIL . " </td><td><input type=\"text\" name=\"email\" size=\"40\" value=\"$email\"></td>
			</tr><tr>
			<td>" . _AM_ADS_TEL . " </td><td><input type=\"text\" name=\"tel\" size=\"50\" value=\"$tel\"></td>
			</tr><tr>
			<td>" . _AM_ADS_TOWN . " </td><td><input type=\"text\" name=\"town\" size=\"40\" value=\"$town\"></td>
			</tr><tr>
			<td>" . _AM_ADS_COUNTRY . " </td><td><input type=\"text\" name=\"country\" size=\"40\" value=\"$country\"></td>
			</tr><tr>
			<td>" . _AM_ADS_TITLE2 . " </td><td><input type=\"text\" name=\"title\" size=\"40\" value=\"$title\"></td>
			</tr><tr>
			<td>" . _AM_ADS_EXPIRE . " </td><td><input type=\"text\" name=\"expire\" size=\"40\" value=\"$expire\"></td>
			</tr><tr>
			<td>" . _AM_ADS_TYPE . ' </td><td><select name="type">';

        $result5 = $xoopsDB->query('select nom_type from ' . $xoopsDB->prefix('ads_type') . ' order by nom_type');

        while (list($nom_type) = $xoopsDB->fetchRow($result5)) {
            $sel = '';

            if ($nom_type == $type) {
                $sel = 'selected';
            }

            echo "<option value=\"$nom_type\" $sel>$nom_type</option>";
        }

        echo '</select></td>
			</tr>';

        $result8 = $xoopsDB->query('select affprice from ' . $xoopsDB->prefix('ads_categories') . " where cid=$cid");

        while (list($affprice) = $xoopsDB->fetchRow($result8)) {
            if ('1' == $affprice) {
                //			echo "<td>"._AM_ADS_PRICE2." </td><td><input type=\"text\" name=\"price\" size=\"10\" value=\"$price\"> ".$xoopsModuleConfig['ads_monnaie']."";

                echo '<td>' . _AM_ADS_PRICE2 . " </td><td><input type=\"text\" name=\"price\" size=\"20\" value=\"$price\"> " . $xoopsModuleConfig['ads_monnaie'] . '';

                $result3 = $xoopsDB->query('select nom_price from ' . $xoopsDB->prefix('ads_price') . ' order by id_price');

                echo " <select name=\"typeprice\"><option value=\"$typeprice\">$typeprice</option>";

                while (list($nom_price) = $xoopsDB->fetchRow($result3)) {
                    echo "<option value=\"$nom_price\">$nom_price</option>";
                }

                echo '</select></td>';
            }
        }

        echo '</tr><tr>
			

			<tr>
			<td>' . _AM_ADS_PHOTO1 . " </td><td><input type=\"text\" name=\"photo\" size=\"40\" value=\"$photo\"></td>
			</tr><tr><tr>
			<td>" . _AM_ADS_PHOTO2 . " </td><td><input type=\"text\" name=\"photo2\" size=\"40\" value=\"$photo2\"></td>
			</tr><tr><tr>
			<td>" . _AM_ADS_PHOTO3 . " </td><td><input type=\"text\" name=\"photo3\" size=\"40\" value=\"$photo3\"></td>
			</tr><tr>
			<td>" . _AM_ADS_ANNONCE . " </td><td><textarea name=\"desctext\" cols=\"40\" rows=\"10\">$desctext</textarea></td>
			</tr><tr><td>" . _AM_ADS_CAT . ' </td><td>';

        $mytree->makeMySelBox('title', 'title', $cid);

        echo '</td>
			</tr><tr>
			<td>&nbsp;</td><td><select name="op">
			<option value="ListingValid"> ' . _AM_ADS_OK . '
			<option value="ListingDel"> ' . _AM_ADS_DEL . '
			</select><input type="submit" value="' . _AM_ADS_GO . '"></td>
			</tr></table>';

        echo '<input type="hidden" name="valid" value="Yes">';

        echo "<input type=\"hidden\" name=\"lid\" value=\"$lid\">";

        echo "<input type=\"hidden\" name=\"date\" value=\"$date\">";

        echo "<input type=\"hidden\" name=\"submitter\" value=\"$submitter\">
			</form>";

        echo '</td></tr></table>';

        echo '<br>';
    }

    xoops_cp_footer();
}

#  function ModifyAds
#####################################################
function ModifyAds($lid)
{
    // for XOOPS CODE by Tom

    //global $xoopsDB, $xoopsModule, $xoopsConfig, $ynprice, ".$xoopsModuleConfig['ads_monnaie'].", $myts;

    global $xoopsDB, $xoopsModule, $xoopsConfig, $xoopsModuleConfig, $myts, $desctext, $mydirname;

    $mytree = new XoopsTree($xoopsDB->prefix('ads_categories'), 'cid', 'pid');

    xoops_cp_header();

    include('./mymenu.php');

    echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_ADS_MODANN . '</legend>';

    $result = $xoopsDB->query('select lid, cid, title, expire, type, desctext, tel, price, typeprice, date, email, submitter, town, country, valid, photo, photo2, photo3 from ' . $xoopsDB->prefix('ads_listing') . " where lid=$lid");

    while (list($lid, $cid, $title, $expire, $type, $desctext, $tel, $price, $typeprice, $date, $email, $submitter, $town, $country, $valid, $photo, $photo2, $photo3) = $xoopsDB->fetchRow($result)) {
        $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

        $expire = htmlspecialchars($expire, ENT_QUOTES | ENT_HTML5);

        $type = htmlspecialchars($type, ENT_QUOTES | ENT_HTML5);

        $desctext = htmlspecialchars($desctext, ENT_QUOTES | ENT_HTML5);

        $tel = htmlspecialchars($tel, ENT_QUOTES | ENT_HTML5);

        $price = htmlspecialchars($price, ENT_QUOTES | ENT_HTML5);

        $typeprice = htmlspecialchars($typeprice, ENT_QUOTES | ENT_HTML5);

        $submitter = htmlspecialchars($submitter, ENT_QUOTES | ENT_HTML5);

        $town = htmlspecialchars($town, ENT_QUOTES | ENT_HTML5);

        $country = htmlspecialchars($country, ENT_QUOTES | ENT_HTML5);

        $date2 = formatTimestamp($date, 's');

        echo '<form action="index.php" method=post>
		    <table border=0><tr>
			<td>' . _AM_ADS_NUMANN . " </td><td>$lid / $date2</td>
			</tr><tr>
			<td>" . _AM_ADS_SENDBY . " </td><td>$submitter</td>
			</tr><tr>
			<td>" . _AM_ADS_EMAIL . " </td><td><input type=\"text\" name=\"email\" size=\"40\" value=\"$email\"></td>
			</tr><tr>
			<td>" . _AM_ADS_TEL . " </td><td><input type=\"text\" name=\"tel\" size=\"50\" value=\"$tel\"></td>
			</tr><tr>
			<td>" . _AM_ADS_TOWN . " </td><td><input type=\"text\" name=\"town\" size=\"40\" value=\"$town\"></td>
			</tr><tr>
			<td>" . _AM_ADS_COUNTRY . " </td><td><input type=\"text\" name=\"country\" size=\"40\" value=\"$country\"></td>
			</tr><tr>
			<td>" . _AM_ADS_TITLE2 . " </td><td><input type=\"text\" name=\"title\" size=\"40\" value=\"$title\"></td>
			</tr><tr>
			<td>" . _AM_ADS_EXPIRE . " </td><td><input type=\"text\" name=\"expire\" size=\"40\" value=\"$expire\"></td>
			</tr><tr>
			<td>" . _AM_ADS_TYPE . ' </td><td><select name="type">';

        $result5 = $xoopsDB->query('select nom_type from ' . $xoopsDB->prefix('ads_type') . ' order by nom_type');

        while (list($nom_type) = $xoopsDB->fetchRow($result5)) {
            $sel = '';

            if ($nom_type == $type) {
                $sel = 'selected';
            }

            echo "<option value=\"$nom_type\" $sel>$nom_type</option>";
        }

        echo '</select></td>
			</tr>';

        $result9 = $xoopsDB->query('select cid, affprice from ' . $xoopsDB->prefix('ads_categories') . " where cid=$cid");

        while (list($affprice) = $xoopsDB->fetchRow($result9)) {
            if ('1' == $affprice) {
                echo '<td>' . _AM_ADS_PRICE2 . " </td><td><input type=\"text\" name=\"price\" size=\"20\" value=\"$price\"> " . $xoopsModuleConfig['ads_monnaie'] . '';

                $result = $xoopsDB->query('select nom_price from ' . $xoopsDB->prefix('ads_price') . ' order by nom_price');

                echo " <select name=\"id_price\"><option value=\"$typeprice\">$typeprice</option>";

                while (list($nom_price) = $xoopsDB->fetchRow($result)) {
                    $nom_price = htmlspecialchars($nom_price, ENT_QUOTES | ENT_HTML5);

                    echo "<option value=\"$nom_price\">$nom_price</option>";
                }

                echo '</select></td>';
            }
        }

        echo '<tr>
			<td>' . _AM_ADS_CAT2 . ' </td><td>';

        $mytree->makeMySelBox('title', 'title', $cid);

        echo '</td>
			</tr><tr>
			<td>' . _AM_ADS_ANNONCE . ' </td><td>';

        $wysiwyg_folder = XOOPS_ROOT_PATH . '/class/wysiwyg';

        if (file_exists($wysiwyg_folder) && ('1' == $xoopsModuleConfig['ads_koivi'])) {
            $wysiwyg_text_area = new XoopsFormWysiwygTextArea('', 'desctext', $desctext, '100%', '200px', 'small');

            echo $wysiwyg_text_area->render();
        } else {
            ob_start();

            $GLOBALS['desctext'] = $desctext;

            xoopsCodeTarea('desctext', 30, 6);

            $xoops_codes_tarea = ob_get_contents();

            ob_end_clean();

            echo $xoops_codes_tarea;

            // add XOOPS CODE by Tom (hidden)
        }

        echo '</td></tr>';

        echo '<tr>
			<td>' . _AM_ADS_PHOTO2 . " </td><td><input type=\"text\" name=\"photo\" size=\"50\" value=\"$photo\"></td>
			</tr><tr>";

        echo '<tr>
			<td>' . _AM_ADS_PHOTO2 . " </td><td><input type=\"text\" name=\"photo2\" size=\"50\" value=\"$photo2\"></td>
			</tr><tr>";

        echo '<tr>
			<td>' . _AM_ADS_PHOTO2 . " </td><td><input type=\"text\" name=\"photo3\" size=\"50\" value=\"$photo3\"></td>
			</tr><tr>";

        $time = time();

        echo '</tr><tr>
			<td>&nbsp;</td><td><select name="op">
			<option value="ModifyAdsS"> ' . _AM_ADS_MODIF . '
			<option value="ListingDel"> ' . _AM_ADS_DEL . '
			</select><input type="submit" value="' . _AM_ADS_GO . '"></td>
			</tr></table>';

        echo '<input type="hidden" name="valid" value="Yes">';

        echo "<input type=\"hidden\" name=\"lid\" value=\"$lid\">";

        echo "<input type=\"hidden\" name=\"date\" value=\"$time\">";

        echo "<input type=\"hidden\" name=\"submitter\" value=\"$submitter\">
		</form><br>";

        echo '</fieldset><br>';

        xoops_cp_footer();
    }
}

#  function ModifyAdsS
#####################################################
function ModifyAdsS($lid, $cat, $title, $expire, $type, $desctext, $tel, $price, $typeprice, $date, $email, $submitter, $town, $country, $valid, $photo, $photo2, $photo3)
{
    global $xoopsDB, $xoopsConfig, $myts, $mydirname;

    $title = $myts->addSlashes($title);

    $expire = $myts->addSlashes($expire);

    $type = $myts->addSlashes($type);

    $desctext = $myts->addSlashes($desctext);

    $tel = $myts->addSlashes($tel);

    $price = $myts->addSlashes($price);

    $typeprice = $myts->addSlashes($typeprice);

    $submitter = $myts->addSlashes($submitter);

    $town = $myts->addSlashes($town);

    $country = $myts->addSlashes($country);

    $xoopsDB->query('update ' . $xoopsDB->prefix('ads_listing') . " set cid='$cat', title='$title', expire='$expire', type='$type', desctext='$desctext', tel='$tel', price='$price', typeprice='$typeprice', date='$date', email='$email', submitter='$submitter', town='$town', country='$country', valid='$valid', photo='$photo', photo2='$photo2', photo3='$photo3' where lid=$lid");

    redirect_header('index.php', 1, _AM_ADS_ANNMOD);

    exit();
}

#  function ListingDel
#####################################################
function ListingDel($lid, $photo, $photo2, $photo3)
{
    global $xoopsDB, $mydirname;

    $xoopsDB->query('delete from ' . $xoopsDB->prefix('ads_listing') . " where lid=$lid");

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

    redirect_header('index.php', 1, _AM_ADS_ANNDEL);

    exit();
}

#  function ListingValid
#####################################################
function ListingValid($lid, $cat, $title, $expire, $type, $desctext, $tel, $price, $typeprice, $date, $email, $submitter, $town, $country, $valid, $photo, $photo2, $photo3)
{
    global $xoopsDB, $xoopsConfig, $myts, $meta, $mydirname;

    $title = $myts->addSlashes($title);

    $expire = $myts->addSlashes($expire);

    $type = $myts->addSlashes($type);

    $desctext = $myts->addSlashes($desctext);

    $tel = $myts->addSlashes($tel);

    $price = $myts->addSlashes($price);

    $typeprice = $myts->addSlashes($typeprice);

    $submitter = $myts->addSlashes($submitter);

    $town = $myts->addSlashes($town);

    $country = $myts->addSlashes($country);

    $xoopsDB->query('update ' . $xoopsDB->prefix('ads_listing') . " set cid='$cat', title='$title', expire='$expire', type='$type', desctext='$desctext', tel='$tel', price='$price', typeprice='$typeprice', date='$date', email='$email', submitter='$submitter', town='$town', country='$country', valid='$valid', photo='$photo', photo2='$photo2', photo3='$photo3'  where lid=$lid");

    //	Specification for Japan:

    //	$message = ""._AM_ADS_HELLO." $submitter,\n\n "._AM_ADS_ANNACCEPT." :\n\n$type $title\n $desctext\n\n\n "._AM_ADS_CONSULTTO."\n ".XOOPS_URL."/modules/$mydirname/index.php?pa=viewannonces&lid=$lid\n\n "._AM_ADS_THANK."\n\n"._AM_ADS_TEAMOF." ".$meta['title']."\n".XOOPS_URL."";

    if ('' == $email) {
    } else {
        $message = "$submitter " . _AM_ADS_HELLO . "\n\n " . _AM_ADS_ANNACCEPT . " :\n\n$type $title\n $desctext\n\n\n " . _AM_ADS_CONSULTTO . "\n " . XOOPS_URL . "/modules/$mydirname/index.php?pa=viewads&lid=$lid\n\n " . _AM_ADS_THANK . "\n\n" . _AM_ADS_TEAMOF . ' ' . $meta['title'] . "\n" . XOOPS_URL . '';

        $subject = '' . _AM_ADS_ANNACCEPT . '';

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

    $tags = [];

    $tags['TITLE'] = $title;

    $tags['TYPE'] = $type;

    $tags['LINK_URL'] = XOOPS_URL . '/modules/' . $mydirname . '/index.php?pa=viewads' . '&lid=' . $lid;

    $sql = 'SELECT title FROM ' . $xoopsDB->prefix('ads_categories') . ' WHERE cid=' . $cat;

    $result = $xoopsDB->query($sql);

    $row = $xoopsDB->fetchArray($result);

    $tags['CATEGORY_TITLE'] = $row['title'];

    $tags['CATEGORY_URL'] = XOOPS_URL . '/modules/' . $mydirname . '/index.php?pa=Adsview&cid="' . $cat;

    $notificationHandler = xoops_getHandler('notification');

    $notificationHandler->triggerEvent('global', 0, 'new_listing', $tags);

    $notificationHandler->triggerEvent('category', $cid, 'new_listing', $tags);

    $notificationHandler->triggerEvent('listing', $lid, 'new_listing', $tags);

    redirect_header('index.php', 1, _AM_ADS_ANNVALID);

    exit();
}

#  function ListingAddType
#####################################################
function ListingAddType($type)
{
    global $xoopsDB, $xoopsConfig, $myts, $mydirname;

    [$numrows] = $xoopsDB->fetchRow($xoopsDB->query('select  COUNT(*)  FROM ' . $xoopsDB->prefix('ads_type') . " where nom_type='$type'"));

    if ($numrows > 0) {
        xoops_cp_header();

        echo "<table width='100%' border='0' cellspacing='1' cellpadding='8' style='border: 2px solid #2F5376;'><tr class='bg4'><td valign='top'>\n";

        echo '<br><center><b>' . _AM_ADS_ERRORTYPE . " $nom_type " . _AM_ADS_EXIST . '</b><br><br>';

        echo '<form method="post" action="index.php">
			<b>' . _AM_ADS_ADDTYPE . '</b><br><br>
			' . _AM_ADS_TYPE . '<input type="text" name="type" size="30" maxlength="100">	
			<input type="hidden" name="op" value="ListingAddType">
			<input type="submit" value="' . _AM_ADS_ADD . '">
			</form>';

        echo '</td></tr></table>';

        xoops_cp_footer();
    } else {
        $type = $myts->addSlashes($type);

        if ('' == $type) {
            $type = '! ! ? ! !';
        }

        $xoopsDB->query('insert into ' . $xoopsDB->prefix('ads_type') . " values (NULL, '$type')");

        redirect_header('index.php', 1, _AM_ADS_ADDTYPE2);

        exit();
    }
}

#  function ListingModType
#####################################################
function ListingModType($id_type, $nom_type)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $myts, $mydirname;

    xoops_cp_header();

    include('./mymenu.php');

    echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_ADS_MODTYPE . '</legend>';

    $result = $xoopsDB->query('select id_type, nom_type from ' . $xoopsDB->prefix('ads_type') . " where id_type=$id_type");

    [$id_type, $nom_type] = $xoopsDB->fetchRow($result);

    $nom_type = htmlspecialchars($nom_type, ENT_QUOTES | ENT_HTML5);

    echo '<form action="index.php" method="post">'
        . '' . _AM_ADS_TYPE . " <input type=\"text\" name=\"nom_type\" value=\"$nom_type\" size=\"51\" maxlength=\"50\"><br>"
        . "<input type=\"hidden\" name=\"id_type\" value=\"$id_type\">"
        . '<input type="hidden" name="op" value="ListingModTypeS">'
        . '<table border="0"><tr><td>'
        . '<input type="submit" value="' . _AM_ADS_SAVMOD . '"></form></td><td>'
        . '<form action="index.php" method="post">'
        . "<input type=\"hidden\" name=\"id_type\" value=\"$id_type\">"
        . '<input type="hidden" name="op" value="ListingDelType">'
        . '<input type="submit" value="' . _AM_ADS_DEL . '"></form></td></tr></table>';

    echo '</td></tr></table>';

    xoops_cp_footer();
}

#  function ListingModTypeS
#####################################################
function ListingModTypeS($id_type, $nom_type)
{
    global $xoopsDB,$xoopsConfig, $myts, $mydirname;

    $nom_type = $myts->addSlashes($nom_type);

    $xoopsDB->query('update ' . $xoopsDB->prefix('ads_type') . " set nom_type='$nom_type' where id_type='$id_type'");

    redirect_header('index.php', 1, _AM_ADS_TYPEMOD);

    exit();
}

#  function ListingDelType
#####################################################
function ListingDelType($id_type)
{
    global $xoopsDB, $mydirname;

    $xoopsDB->query('delete from ' . $xoopsDB->prefix('ads_type') . " where id_type='$id_type'");

    redirect_header('index.php', 1, _AM_ADS_TYPEDEL);

    exit();
}

#  function ListingAddPrice
#####################################################
function ListingAddPrice($type)
{
    global $xoopsDB, $xoopsConfig, $myts, $mydirname;

    [$numrows] = $xoopsDB->fetchRow($xoopsDB->query('select  COUNT(*)  FROM ' . $xoopsDB->prefix('ads_price') . " where nom_price='$type'"));

    if ($numrows > 0) {
        xoops_cp_header();

        include('./mymenu.php');

        echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_ADS_MODANN . '</legend>';

        echo '<br><center><b>' . _AM_ADS_ERRORPRICE . " $nom_price " . _AM_ADS_EXIST . '</b><br><br>';

        echo '<form method="post" action="index.php">
			<b>' . _AM_ADS_ADDPRICE . '</b><br><br>
			' . _AM_ADS_TYPE . '	<input type="text" name="type" size="30" maxlength="100">	
			<input type="hidden" name="op" value="ListingAddPrice">
			<input type="submit" value="' . _AM_ADS_ADD . '">
			</form>';

        echo '</td></tr></table>';

        xoops_cp_footer();
    } else {
        $type = $myts->addSlashes($type);

        if ('' == $type) {
            $type = '! ! ? ! !';
        }

        $xoopsDB->query('insert into ' . $xoopsDB->prefix('ads_price') . " values (NULL, '$type')");

        redirect_header('index.php', 1, _AM_ADS_ADDPRICE2);

        exit();
    }
}

#  function ListingModPrice
#####################################################
function ListingModPrice($id_price, $nom_type)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $myts;

    xoops_cp_header();

    include('./mymenu.php');

    echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_ADS_MODPRICE . '</legend>';

    echo '<b>' . _AM_ADS_MODPRICE . '</b><br><br>';

    $result = $xoopsDB->query('select nom_price from ' . $xoopsDB->prefix('ads_price') . " where id_price=$id_price");

    [$nom_price] = $xoopsDB->fetchRow($result);

    $nom_price = htmlspecialchars($nom_price, ENT_QUOTES | ENT_HTML5);

    echo '<form action="index.php" method="post">'
        . '' . _AM_ADS_TYPE . " <input type=\"text\" name=\"nom_price\" value=\"$nom_price\" size=\"51\" maxlength=\"50\"><br>"
        . "<input type=\"hidden\" name=\"id_price\" value=\"$id_price\">"
        . '<input type="hidden" name="op" value="ListingModPriceS">'
        . '<table border="0"><tr><td>'
        . '<input type="submit" value="' . _AM_ADS_SAVMOD . '"></form></td><td>'
        . '<form action="index.php" method="post">'
        . "<input type=\"hidden\" name=\"id_price\" value=\"$id_price\">"
        . '<input type="hidden" name="op" value="ListingDelPrice">'
        . '<input type="submit" value="' . _AM_ADS_DEL . '"></form></td></tr></table>';

    echo '</td></tr></table>';

    xoops_cp_footer();
}

#  function ListingModPriceS
#####################################################
function ListingModPriceS($id_price, $nom_price)
{
    global $xoopsDB, $xoopsConfig, $myts;

    $nom_price = $myts->addSlashes($nom_price);

    $xoopsDB->query('update ' . $xoopsDB->prefix('ads_price') . " set nom_price='$nom_price' where id_price='$id_price'");

    redirect_header('index.php', 1, _AM_ADS_PRICEMOD);

    exit();
}

#  function ListingDelPrice
#####################################################
function ListingDelPrice($id_price)
{
    global $xoopsDB;

    $xoopsDB->query('delete from ' . $xoopsDB->prefix('ads_price') . " where id_price='$id_price'");

    redirect_header('index.php', 1, _AM_ADS_PRICEDEL);

    exit();
}

#####################################################
foreach ($_POST as $k => $v) {
    ${$k} = $v;
}

$pa = $_GET['pa'] ?? '';

if (!isset($_POST['lid']) && isset($_GET['lid'])) {
    $lid = $_GET['lid'];
}
if (!isset($_POST['op']) && isset($_GET['op'])) {
    $op = $_GET['op'];
}
if (!isset($op)) {
    $op = '';
}

switch ($op) {
    case 'IndexView':
    IndexView($lid);
    break;
    case 'ListingDelPrice':
    ListingDelPrice($id_price);
    break;
    case 'ListingModPrice':
    ListingModPrice($id_price, $nom_price);
    break;
    case 'ListingModPriceS':
    ListingModPriceS($id_price, $nom_price);
    break;
    case 'ListingAddPrice':
    ListingAddPrice($type);
    break;
    case 'ListingDelType':
    ListingDelType($id_type);
    break;
    case 'ListingModType':
    ListingModType($id_type, $nom_type);
    break;
    case 'ListingModTypeS':
    ListingModTypeS($id_type, $nom_type);
    break;
    case 'ListingAddType':
    ListingAddType($type);
    break;
    case 'ListingDel':
    ListingDel($lid, $photo, $photo2, $photo3);
    break;
    case 'ListingValid':
    ListingValid($lid, $cid, $title, $expire, $type, $desctext, $tel, $price, $typeprice, $date, $email, $submitter, $town, $country, $valid, $photo, $photo2, $photo3);
    break;
    case 'ModifyAds':
    ModifyAds($lid);
    break;
    case 'ModifyAdsS':
    ModifyAdsS($lid, $cid, $title, $expire, $type, $desctext, $tel, $price, $id_price, $date, $email, $submitter, $town, $country, $valid, $photo, $photo2, $photo3);
    break;
    default:
    Index();
    break;
}
