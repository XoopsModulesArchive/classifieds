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
// Original Author: Pascal Le Boustouller                                    //
// Author Website : pascal.e-xoops@perso-search.com                          //
// Licence Type   : GPL                                                      //
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
if (!$gpermHandler->checkRight('ads_submit', $perm_itemid, $groups, $module_id)) {
    redirect_header(XOOPS_URL . '/user.php', 3, _NOPERM);

    exit();
}

function addindex($cid)
{
    global $xoopsDB, $xoopsConfig, $xoopsUser, $xoopsTheme, $xoopsLogger, $xoopsModule, $xoopsModuleConfig, $mydirname;

    $token = $GLOBALS['xoopsSecurity']->createToken();

    require_once(XOOPS_ROOT_PATH . "/modules/$mydirname/include/functions.php");

    $wysiwyg_folder = XOOPS_ROOT_PATH . '/class/wysiwyg';

    if (file_exists($wysiwyg_folder) && ('1' == $xoopsModuleConfig['ads_koivi'])) {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        require_once XOOPS_ROOT_PATH . '/class/wysiwyg/formwysiwygtextarea.php';
    } else {
        // for XOOPS CODE  by Tom

        require_once(XOOPS_ROOT_PATH . '/include/xoopscodes.php');
    }

    require_once(XOOPS_ROOT_PATH . '/class/xoopstree.php');

    $mytree = new XoopsTree($xoopsDB->prefix('ads_categories'), 'cid', 'pid');

    $howlong = $xoopsModuleConfig['ads_howlong'];

    $photomax = $xoopsModuleConfig['ads_photomax'];

    $photomax1 = $xoopsModuleConfig['ads_photomax'] / 1024;

    //if ($cid =="") {

    //	redirect_header("index.php",1,_ADS_ADDANNONCE);

    //	exit();

    //}

    $photomax1 = $photomax / 1024;

    echo '<script type="text/javascript">
          function verify() {
                var msg = "' . _ADS_VALIDERORMSG . '\\n__________________________________________________\\n\\n";
                var errors = "FALSE";

                if (window.document.add.type.value == "0") {
                        errors = "TRUE";
                        msg += "' . _ADS_VALIDTYPE . '\\n";
                }
				
                if (window.document.add.cid.value == "") {
                        errors = "TRUE";
                        msg += "' . _ADS_VALIDCAT . '\\n";
                }
				
                if (window.document.add.title.value == "") {
                        errors = "TRUE";
                        msg += "' . _ADS_VALIDTITLE . '\\n";
                }


				if (window.document.add.desctext.value == "") {
                        errors = "TRUE";
                        msg += "' . _ADS_VALIDANN . '\\n";
                }
				
				if (window.document.add.email.value == "") {
                        errors = "TRUE";
                        msg += "' . _ADS_VALIDEMAIL . '\\n";
                }
				
				if (window.document.add.town.value == "") {
                        errors = "TRUE";
                        msg += "' . _ADS_VALIDTOWN . '\\n";
                }
				
                if (errors == "TRUE") {
                        msg += "__________________________________________________\\n\\n' . _ADS_VALIDMSG . '\\n";
                        alert(msg);
                        return false;
                }
          }
          </script>';

    [$numrows] = $xoopsDB->fetchRow($xoopsDB->query('select cid, title from ' . $xoopsDB->prefix('ads_categories') . ''));

    if ($numrows > 0) {
        //OpenTable();

        echo "<table width='100%' border='0' cellspacing='1' cellpadding='8'><tr class='bg4'><td valign='top'>\n";

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

        if (!$gpermHandler->checkRight('ads_premium', $perm_itemid, $groups, $module_id)) {
            $grouperm = '1';
        } else {
            $grouperm = '0';
        }

        if ('1' == $xoopsModuleConfig['ads_moderated']) {
            if ('0' != $grouperm) {
                echo '<b>' . _ADS_ADDANNONCE3 . '</b><br><br><center>' . _ADS_ANNMODERATE . " $howlong " . _ADS_DAY . '<br>' . _ADS_NOBIZ . '<br><a href="../../modules/xdirectory/matrix.php">' . _ADS_REDIRECT_BIZ . '</a></center><br><br>';
            } else {
                echo '<b>' . _ADS_ADDANNONCE3 . '<br><br><center>' . _ADS_PREMIUM_MODERATED_HEAD . '<br>' . _ADS_PREMIUM_MEMBER . " $howlong " . _ADS_DAY . '' . _ADS_PREMIUM_DAY . '</center></b><br><br>';
            }
        } else {
            if ('0' != $grouperm) {
                echo '<b>' . _ADS_ADDANNONCE3 . '</b><br><br><center>' . _ADS_ANNNOMODERATE . " $howlong " . _ADS_DAY . '</center><br><br>';
            } else {
                echo '<b>' . _ADS_ADDANNONCE3 . '</b><br><br><center>' . _ADS_PREMIUM_LONG_HEAD . '<br>' . _ADS_PREMIUM_MEMBER . " $howlong " . _ADS_DAY . '' . _ADS_PREMIUM_DAY . '</center><br><br>';
            }
        }

        echo '<form method="post" action="addlisting.php" enctype="multipart/form-data" name="add" onSubmit="return verify();">';

        echo "<table width='70%' class='outer' cellspacing='1'><tr>";

        $result2 = $xoopsDB->query('select nom_type from ' . $xoopsDB->prefix('ads_type') . ' order by nom_type');

        echo "<td class='head'>" . _ADS_TYPE . " </td><td class='even'><select name=\"type\"><option value=\"0\">" . _ADS_SELECTYPE . '</option>';

        while (list($nomtyp) = $xoopsDB->fetchRow($result2)) {
            echo "<option value=\"$nomtyp\">$nomtyp</option>";
        }

        echo '</select></td>
				</tr><tr>';

        echo "<td class='head'>" . _ADS_CAT3 . " </td><td class='even'>";

        $x = 0;

        $i = 0;

        $requete = $xoopsDB->query('select cid, pid, title, affprice from ' . $xoopsDB->prefix('ads_categories') . ' where  cid=' . $cid . '');

        [$ccid, $pid, $title, $affprice] = $xoopsDB->fetchRow($requete);

        $varid[$x] = $ccid;

        $varnom[$x] = $title;

        if (0 != $pid) {
            $x = 1;

            while (0 != $pid) {
                $requete2 = $xoopsDB->query('select cid, pid, title from ' . $xoopsDB->prefix('ads_categories') . ' where cid=' . $pid . '');

                [$ccid, $pid, $title] = $xoopsDB->fetchRow($requete2);

                $varid[$x] = $ccid;

                $varnom[$x] = $title;

                $x++;
            }

            $x -= 1;
        }

        while (-1 != $x) {
            echo ' &raquo; ' . $varnom[$x] . '';

            $x -= 1;
        }

        echo "<input type=\"hidden\" name=\"cid\" value=\"$cid\"></td>
				</tr><tr>
				<td class='head'>" . _ADS_TITLE2 . " </td><td class='even'><input type=\"text\" name=\"title\" size=\"30\" maxlength=\"100\"></td>
				</tr>";

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

        if (!$gpermHandler->checkRight('ads_premium', $perm_itemid, $groups, $module_id)) {
            echo "<tr><td width='30%' class='head'>" . _ADS_WILL_LAST . " </td><td class='odd'>$howlong  " . _ADS_DAY . "</td>
				</tr><input type=\"hidden\" name=\"expire\" value=\"$howlong\">";
        } else {
            echo "<tr>
				<td width='30%' class='head'>" . _ADS_HOW_LONG . " </td><td class='odd'><input type=\"text\" name=\"expire\" size=\"3\" maxlength=\"3\" value=\"$howlong\">  " . _ADS_DAY . '</td>
				</tr>';
        }

        echo "<tr><td class='head'>" . _ADS_ANNONCE . ' <br>' . _ADS_CHARMAX . "</td><td class='even'>";

        $desctext = '';

        $wysiwyg_folder = XOOPS_ROOT_PATH . '/class/wysiwyg';

        if (file_exists($wysiwyg_folder) && ('1' == $xoopsModuleConfig['ads_koivi'])) {
            $wysiwyg_text_area = new XoopsFormWysiwygTextArea(_ADS_ANNONCE, 'desctext', $desctext, '100%', '200px', 'small');

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

        echo "</td></tr><tr>
			<td class='head'>" . _ADS_IMG . "</td><td class='even'><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$photomax\"><input type=file name=\"photo\"> (<  ";

        printf('%.2f KB', $photomax1);

        echo ')</td></tr>';

        echo "</td></tr><tr>
			<td class='head'>" . _ADS_IMG2 . "</td><td class='even'><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$photomax\"><input type=file name=\"photo2\"> (<  ";

        printf('%.2f KB', $photomax1);

        echo ')</td></tr>';

        echo "</td></tr><tr>
			<td class='head'>" . _ADS_IMG3 . "</td><td class='even'><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$photomax\"><input type=file name=\"photo3\"> (<  ";

        printf('%.2f KB', $photomax1);

        echo ')</td></tr>';

        if (1 == $affprice) {
            echo "<tr><td class='head'>" . _ADS_PRICE2 . " </td><td class='even'>" . $xoopsModuleConfig['ads_monnaie'] . '<input type="text" name="price" size="20">';

            $result3 = $xoopsDB->query('select nom_price from ' . $xoopsDB->prefix('ads_price') . ' order by id_price');

            echo '<select name="typeprice">';

            while (list($nom_price) = $xoopsDB->fetchRow($result3)) {
                echo "<option value=\"$nom_price\">$nom_price</option>";
            }

            echo '</select></td>';
        }

        if ($xoopsUser) {
            $iddd = $xoopsUser->getVar('uid', 'E');

            $idd = $xoopsUser->getVar('name', 'E');		// Real name

            $idde = $xoopsUser->getVar('email', 'E');

            // Add by Tom
                $iddn = $xoopsUser->getVar('uname', 'E');	// user name
        }

        $time = time();

        echo "</tr><tr>
				<td class='head'>" . _ADS_SURNAME . " </td><td class='even'><input type=\"text\" name=\"submitter\" size=\"30\" value=\"$iddn\"></td>";

        echo "</tr><tr>
				<td class='head'>" . _ADS_EMAIL . " </td><td class='even'><input type=\"text\" name=\"email\" size=\"30\" value=\"$idde\"></td>
				</tr><tr>
				<td class='head'>" . _ADS_TEL . " </td><td class='even'><input type=\"text\" name=\"tel\" size=\"30\"></td>
				</tr><tr>
				<td class='head'>" . _ADS_TOWN . " </td><td class='even'><input type=\"text\" name=\"town\" size=\"30\"></td>
				</tr></table><br>
				<input type=\"hidden\" name=\"usid\" value=\"$iddd\">
				<input type=\"hidden\" name=\"op\" value=\"AddAnnoncesOk\">";

        echo "<input type=\"hidden\" name=\"token\" value=\"$token\">";

        if ('1' == $xoopsModuleConfig['ads_moderated']) {
            echo '<input type="hidden" name="valid" value="No">';
        } else {
            echo '<input type="hidden" name="valid" value="Yes">';
        }

        echo "<input type=\"hidden\" name=\"lid\" value=\"0\">
				<input type=\"hidden\" name=\"comments\" value=\"\">
				<input type=\"hidden\" name=\"date\" value=\"$time\">
				<input type=\"submit\" value=\"" . _ADS_VALIDATE . '">';

        echo '</form>';

        //CloseTable();

        echo '</td></tr></table>';

        //	copyright();
//			require XOOPS_ROOT_PATH."/footer.php";
    }
}

function AddAnnoncesOk($lid, $cat, $title, $expire, $type, $desctext, $tel, $price, $typeprice, $date, $email, $submitter, $usid, $town, $country, $valid, $photo, $photo_size, $photo_name, $photo2, $photo2_size, $photo2_name, $photo3, $photo3_size, $photo3_name, $_FILES)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $destination, $myts, $xoopsLogger, $mydirname;

    if (!$GLOBALS['xoopsSecurity']->check(true, $_REQUEST['token'])) {
        redirect_header(XOOPS_URL . "/modules/$mydirname/index.php", 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    require XOOPS_ROOT_PATH . "/modules/$mydirname/include/functions.php";

    $howlong = $xoopsModuleConfig['ads_howlong'];

    $photomax = $xoopsModuleConfig['ads_photomax'];

    $maxwide = $xoopsModuleConfig['ads_maxwide'];

    $maxhigh = $xoopsModuleConfig['ads_maxhigh'];

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

    $filename = '';

    if (!empty($_FILES['photo']['name'])) {
        require_once XOOPS_ROOT_PATH . '/class/uploader.php';

        $updir = 'photo/';

        $allowed_mimetypes = ['image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/x-png'];

        $uploader = new XoopsMediaUploader($updir, $allowed_mimetypes, $photomax, $maxwide, $maxhigh);

        $uploader->setTargetFileName($date . '_' . $_FILES['photo']['name']);

        $uploader->fetchMedia('photo');

        if (!$uploader->upload()) {
            $errors = $uploader->getErrors();

            redirect_header("addlisting.php?cid=$cat", 3, $errors);

            return false;
            exit();
        }

        $filename = $uploader->getSavedFileName();
    }

    if (!empty($_FILES['photo2']['name'])) {
        require_once XOOPS_ROOT_PATH . '/class/uploader.php';

        $updir = 'photo2/';

        $allowed_mimetypes = ['image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/x-png'];

        $uploader = new XoopsMediaUploader($updir, $allowed_mimetypes, $photomax, $maxwide, $maxhigh);

        $uploader->setTargetFileName($date . '_' . $_FILES['photo2']['name']);

        $uploader->fetchMedia('photo2');

        if (!$uploader->upload()) {
            $errors = $uploader->getErrors();

            redirect_header("addlisting.php?cid=$cat", 3, $errors);

            return false;
            exit();
        }

        $filename2 = $uploader->getSavedFileName();
    }

    if (!empty($_FILES['photo3']['name'])) {
        require_once XOOPS_ROOT_PATH . '/class/uploader.php';

        $updir = 'photo3/';

        $allowed_mimetypes = ['image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/x-png'];

        $uploader = new XoopsMediaUploader($updir, $allowed_mimetypes, $photomax, $maxwide, $maxhigh);

        $uploader->setTargetFileName($date . '_' . $_FILES['photo3']['name']);

        $uploader->fetchMedia('photo3');

        if (!$uploader->upload()) {
            $errors = $uploader->getErrors();

            redirect_header("addlisting.php?cid=$cat", 3, $errors);

            return false;
            exit();
        }

        $filename3 = $uploader->getSavedFileName();
    }

    $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('ads_listing') . " values ('', '$cat', '$title', '$expire', '$type', '$desctext', '$tel', '$price', '$typeprice', '$date', '$email', '$submitter', '$usid',  '$town', '$country',  '$valid', '$filename', '$filename2', '$filename3', '0', '0', '0', '0', '0', '0')");

    if ('Yes' == $valid) {
        $notificationHandler = xoops_getHandler('notification');

        $lid = $xoopsDB->getInsertId();

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
    }

    redirect_header('index.php', 1, _ADS_ADSADDED);

    exit();
}

#######################################################

foreach ($_POST as $k => $v) {
    ${$k} = $v;
}

if (!isset($_POST['cid']) && isset($_GET['cid'])) {
    $cid = $_GET['cid'];
}

if (!isset($_POST['op']) && isset($_GET['op'])) {
    $op = $_GET['op'];
}

if (!isset($op)) {
    $op = '';
}

    switch ($op) {
        case 'AddAnnoncesOk':
        AddAnnoncesOk($lid, $cid, $title, $expire, $type, $desctext, $tel, $price, $typeprice, $date, $email, $submitter, $usid, $town, $country, $valid, $photo, $photo_size, $photo_name, $photo2, $photo2_size, $photo2_name, $photo3, $photo3_size, $photo3_name, $_FILES);
        break;
        default:
        require XOOPS_ROOT_PATH . '/header.php';
        addindex($cid);
        require XOOPS_ROOT_PATH . '/footer.php';
        break;
    }
