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
include 'header.php';

$mydirname = basename(__DIR__);

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
    redirect_header(XOOPS_URL . "/modules/$mydirname/index.php", 3, _NOPERM);

    exit();
}

function ListingDel($lid, $ok)
{
    global $xoopsDB, $xoopsUser, $xoopsConfig, $xoopsTheme, $xoopsLogger, $mydirname;

    $result = $xoopsDB->query('select usid, photo, photo2, photo3 FROM ' . $xoopsDB->prefix('ads_listing') . " where lid=$lid");

    [$usid, $photo, $photo2, $photo3] = $xoopsDB->fetchRow($result);

    if ($xoopsUser) {
        $calusern = $xoopsUser->getVar('uid', 'E');

        if ($usid == $calusern) {
            if (1 == $ok) {
                $xoopsDB->queryF('delete from ' . $xoopsDB->prefix('ads_listing') . " where lid=$lid");

                if ($photo) {
                    $destination = XOOPS_ROOT_PATH . "/modules/$mydirname/photo";

                    if (file_exists("$destination/$photo")) {
                        unlink("$destination/$photo");
                    }
                }

                if ($photo2) {
                    $destination2 = XOOPS_ROOT_PATH . "/modules/$mydirname/photo2";

                    if (file_exists("$destination2/$photo2")) {
                        unlink("$destination2/$photo2");
                    }
                }

                if ($photo3) {
                    $destination3 = XOOPS_ROOT_PATH . "/modules/$mydirname/photo3";

                    if (file_exists("$destination3/$photo3")) {
                        unlink("$destination3/$photo3");
                    }
                }

                redirect_header('index.php', 1, _ADS_ANNDEL);

                exit();
            }

            echo "<table width='100%' border='0' cellspacing='1' cellpadding='8'><tr class='bg4'><td valign='top'>\n";

            echo '<br><center>';

            echo '<b>' . _ADS_SURDELANN . '</b><br><br>';

            echo "[ <a href=\"modify.php?op=ListingDel&amp;lid=$lid&amp;ok=1\">" . _ADS_OUI . '</a> | <a href="index.php">' . _ADS_NON . '</a> ]<br><br>';

            //CloseTable();

            echo '</td></tr></table>';

            //			require XOOPS_ROOT_PATH."/footer.php";
        }
    }
}

function ModAd($lid)
{
    global $xoopsDB, $xoopsModule, $xoopsConfig, $xoopsModuleConfig, $xoopsUser, $xoopsTheme, $myts, $xoopsLogger, $mydirname;

    $token = $GLOBALS['xoopsSecurity']->createToken();

    $wysiwyg_folder = XOOPS_ROOT_PATH . '/class/wysiwyg';

    if (file_exists($wysiwyg_folder) && ('1' == $xoopsModuleConfig['ads_koivi'])) {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        require_once XOOPS_ROOT_PATH . '/class/wysiwyg/formwysiwygtextarea.php';
    } else {
        // for XOOPS CODE  by Tom

        require_once(XOOPS_ROOT_PATH . '/include/xoopscodes.php');
    }

    echo "<script language=\"javascript\">\nfunction CLA(CLA) { var MainWindow = window.open (CLA, \"_blank\",\"width=500,height=300,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no\");}\n</script>";

    require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

    $mytree = new XoopsTree($xoopsDB->prefix('ads_categories'), 'cid', 'pid');

    $photomax1 = $xoopsModuleConfig['ads_photomax'] / 1024;

    $result = $xoopsDB->query('select lid, cid, title, expire, type, desctext, tel, price, typeprice, date, email, submitter, usid, town, country, valid, photo, photo2, photo3 from ' . $xoopsDB->prefix('ads_listing') . " where lid=$lid");

    [$lid, $cide, $title, $expire, $type, $desctext, $tel, $price, $typeprice, $date, $email, $submitter, $usid, $town, $country, $valid, $photo_old, $photo2_old, $photo3_old] = $xoopsDB->fetchRow($result);

    if ($xoopsUser) {
        $calusern = $xoopsUser->uid();

        if ($usid == $calusern) {
            echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _ADS_MODIFANN . '</legend><br><br>';

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

            $useroffset = '';

            if ($xoopsUser) {
                $timezone = $xoopsUser->timezone();

                if (isset($timezone)) {
                    $useroffset = $xoopsUser->timezone();
                } else {
                    $useroffset = $xoopsConfig['default_TZ'];
                }
            }

            $dates = ($useroffset * 3600) + $date;

            $dates = formatTimestamp($date, 's');

            echo '<form action="modify.php" method=post enctype="multipart/form-data">
		    <table><tr>
			<td class="head">' . _ADS_NUMANNN . " </td><td class=\"even\">$lid " . _ADS_DU . " $dates</td>
			</tr><tr>
			<td class=\"head\">" . _ADS_SENDBY . " </td><td class=\"even\">$submitter</td>
			</tr><tr>
			<td class=\"head\">" . _ADS_EMAIL . " </td><td class=\"even\"><input type=\"text\" name=\"email\" size=\"50\" value=\"$email\"></td>
			</tr><tr>
			<td class=\"head\">" . _ADS_TEL . " </td><td class=\"even\"><input type=\"text\" name=\"tel\" size=\"50\" value=\"$tel\"></td>
			</tr><tr>
			<td class=\"head\">" . _ADS_TOWN . " </td><td class=\"even\"><input type=\"text\" name=\"town\" size=\"50\" value=\"$town\"></td>
			</tr><tr>
			<td class=\"head\">" . _ADS_TITLE2 . " </td><td class=\"even\"><input type=\"text\" name=\"title\" size=\"50\" value=\"$title\"></td>
			</tr>";

            echo '<tr><td class="head">' . _ADS_PRICE2 . " </td><td class=\"even\"><input type=\"text\" name=\"price\" size=\"20\" value=\"$price\">" . $xoopsModuleConfig['ads_monnaie'] . '';

            $result3 = $xoopsDB->query('select nom_price from ' . $xoopsDB->prefix('ads_price') . ' order by id_price');

            echo " <select name=\"typeprice\"><option value=\"$typeprice\">$typeprice</option>";

            while (list($nom_price) = $xoopsDB->fetchRow($result3)) {
                echo "<option value=\"$nom_price\">$nom_price</option>";
            }

            echo '</select></td></tr>';

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
                echo "<tr>
				<td width='30%' class='head'>" . _ADS_WILL_LAST . " </td><td class='even'>$expire  " . _ADS_DAY . '</td>
				</tr>';

                echo "<input type=\"hidden\" name=\"expire\" value=\"$expire\">";
            } else {
                echo "<tr>
				<td width='30%' class='head'>" . _ADS_HOW_LONG . " </td><td class='even'><input type=\"text\" name=\"expire\" size=\"3\" maxlength=\"3\" value=\"$expire\">  " . _ADS_DAY . '</td>
				</tr>';
            }

            echo '<tr>
			<td class="head">' . _ADS_TYPE . ' </td><td class="even"><select name="type">';

            $result5 = $xoopsDB->query('select nom_type from ' . $xoopsDB->prefix('ads_type') . ' order by nom_type');

            while (list($nom_type) = $xoopsDB->fetchRow($result5)) {
                $sel = '';

                if ($nom_type == $type) {
                    $sel = 'selected';
                }

                echo "<option value=\"$nom_type\" $sel>$nom_type</option>";
            }

            echo '</select></td>';

            echo '</tr><tr>
			<td class="head">' . _ADS_CAT . ' </td><td class="even">';

            $mytree->makeMySelBox('title', 'title', $cide);

            echo '</td>
			</tr><tr>
			<td class="head">' . _ADS_ANNONCE . ' </td><td class="even">';

            //echo "<textarea name=\"desctext\" cols=\"40\" rows=\"10\">$desctext</textarea></td>";

            $wysiwyg_folder = XOOPS_ROOT_PATH . '/class/wysiwyg';

            if (file_exists($wysiwyg_folder) && ('1' == $xoopsModuleConfig['ads_koivi'])) {
                $wysiwyg_text_area = new XoopsFormWysiwygTextArea(_ADS_DESC2, desctext, $desctext, '100%', '200px', 'small');

                echo $wysiwyg_text_area->render();
            } else {
                // add XOOPS CODE by Tom (hidden)

                ob_start();

                $GLOBALS['desctext'] = $desctext;

                xoopsCodeTarea('desctext', 30, 8);

                $xoops_codes_tarea = ob_get_contents();

                ob_end_clean();

                echo $xoops_codes_tarea;
            }

            echo '</td></tr>';

            if ($photo_old) {
                echo '<tr><td class="head">' . _ADS_ACTUALPICT . " </td><td class=\"even\"><a href=\"javascript:CLA('display-image.php?lid=$lid')\">$photo_old</a> <input type=\"hidden\" name=\"photo_old\" value=\"$photo_old\"> <input type=\"checkbox\" name=\"supprim\" value=\"yes\"> " . _ADS_DELPICT . '</td>
				</tr><tr>';

                echo '<td class="head">' . _ADS_NEWPICT . ' </td><td class="even"><input type="hidden" name="MAX_FILE_SIZE" value="' . $xoopsModuleConfig['ads_photomax'] . '"><input type=file name="photo"> (<  ';

                printf('%.2f KB', $photomax1);

                echo ')</td></tr>';
            } else {
                echo '<td class="head">' . _ADS_IMG . ' </td><td class="even"><input type="hidden" name="MAX_FILE_SIZE" value="' . $xoopsModuleConfig['ads_photomax'] . '"><input type=file name="photo"> (<  ';

                printf('%.2f KB', $photomax1);

                echo ')</td></tr>';
            }

            if ($photo2_old) {
                echo '<tr><td class="head">' . _ADS_ACTUALPICT2 . " </td><td class=\"even\"><a href=\"javascript:CLA('display-image.php?lid=$lid')\">$photo2_old</a> <input type=\"hidden\" name=\"photo2_old\" value=\"$photo2_old\"> <input type=\"checkbox\" name=\"supprim2\" value=\"yes\"> " . _ADS_DELPICT . '</td>
				</tr><tr>';

                echo '<td class="head">' . _ADS_NEWPICT2 . ' </td><td class="even"><input type="hidden" name="MAX_FILE_SIZE" value="' . $xoopsModuleConfig['ads_photomax'] . '"><input type=file name="photo2"> (<  ';

                printf('%.2f KB', $photomax1);

                echo ')</td></tr>';
            } else {
                echo '<td class="head">' . _ADS_IMG2 . ' </td><td class="even"><input type="hidden" name="MAX_FILE_SIZE" value="' . $xoopsModuleConfig['ads_photomax'] . '"><input type=file name="photo2"> (<  ';

                printf('%.2f KB', $photomax1);

                echo ')</td></tr>';
            }

            if ($photo3_old) {
                echo '<tr><td class="head">' . _ADS_ACTUALPICT3 . " </td><td class=\"even\"><a href=\"javascript:CLA('display-image.php?lid=$lid')\">$photo3_old</a> <input type=\"hidden\" name=\"photo3_old\" value=\"$photo3_old\"> <input type=\"checkbox\" name=\"supprim3\" value=\"yes\"> " . _ADS_DELPICT . '</td>
				</tr><tr>';

                echo '<td class="head">' . _ADS_NEWPICT2 . ' </td><td class="even"><input type="hidden" name="MAX_FILE_SIZE" value="' . $xoopsModuleConfig['ads_photomax'] . '"><input type=file name="photo3"> (<  ';

                printf('%.2f KB', $photomax1);

                echo ')</td></tr>';
            } else {
                echo '<td class="head">' . _ADS_IMG3 . ' </td><td class="even"><input type="hidden" name="MAX_FILE_SIZE" value="' . $xoopsModuleConfig['ads_photomax'] . '"><input type=file name="photo3"> (<  ';

                printf('%.2f KB', $photomax1);

                echo ')</td></tr>';
            }

            echo '<tr>
			<td colspan=2><br><input type="submit" value="' . _ADS_MODIFANN . '"></td>
			</tr></table>';

            echo '<input type="hidden" name="op" value="ModAdS">';

            if ('1' == $xoopsModuleConfig['ads_moderated']) {
                echo '<input type="hidden" name="valid" value="No">';

                echo '<br>' . _ADS_MODIFBEFORE . '<br>';
            } else {
                echo '<input type="hidden" name="valid" value="Yes">';
            }

            echo "<input type=\"hidden\" name=\"token\" value=\"$token\">";

            echo "<input type=\"hidden\" name=\"lid\" value=\"$lid\">";

            echo "<input type=\"hidden\" name=\"date\" value=\"$date\">";

            echo "<input type=\"hidden\" name=\"submitter\" value=\"$submitter\">
			</form><br>";

            echo '</fieldset><br>';
        }
    }
}

function ModAdS($lid, $cat, $title, $expire, $type, $desctext, $tel, $price, $typeprice, $date, $email, $submitter, $town, $country, $valid, $photo, $photo_old, $photoS_size, $photoS_name, $photo2, $photo2_old, $photo2S_size, $photo2S_name, $photo3, $photo3_old, $photo3S_size, $photo3S_name, $_FILES, $supprim, $supprim2, $supprim3)
{
    global $xoopsDB, $xoopsConfig, $xoopsModuleConfig, $myts, $xoopsLogger, $mydirname;

    if (!$GLOBALS['xoopsSecurity']->check(true, $_REQUEST['token'])) {
        redirect_header(XOOPS_URL . "/modules/$mydirname/index.php", 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    $destination = XOOPS_ROOT_PATH . "/modules/$mydirname/photo";

    $destination2 = XOOPS_ROOT_PATH . "/modules/$mydirname/photo2";

    $destination3 = XOOPS_ROOT_PATH . "/modules/$mydirname/photo3";

    if ('yes' == $supprim) {
        if (file_exists("$destination/$photo_old")) {
            unlink("$destination/$photo_old");
        }

        $photo_old = '';
    }

    if ('yes' == $supprim2) {
        if (file_exists("$destination2/$photo2_old")) {
            unlink("$destination2/$photo2_old");
        }

        $photo2_old = '';
    }

    if ('yes' == $supprim3) {
        if (file_exists("$destination3/$photo3_old")) {
            unlink("$destination3/$photo3_old");
        }

        $photo3_old = '';
    }

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

    if (!empty($_FILES['photo']['name'])) {
        require_once XOOPS_ROOT_PATH . '/class/uploader.php';

        $updir = 'photo/';

        $allowed_mimetypes = ['image/gif', 'image/jpg', 'image/jpeg', 'image/x-png'];

        $uploader = new XoopsMediaUploader($updir, $allowed_mimetypes, $photomax = $xoopsModuleConfig['ads_photomax'], $xoopsModuleConfig['ads_maxwide'], $maxhigh = $xoopsModuleConfig['ads_maxhigh']);

        $uploader->setTargetFileName($date . '_' . $_FILES['photo']['name']);

        $uploader->fetchMedia('photo');

        if (!$uploader->upload()) {
            $errors = $uploader->getErrors();

            redirect_header("modify.php?op=ModAd&amp;lid=$lid", 1, $errors);

            //return False;

            exit();
        }

        if ($photo_old) {
            if (@file_exists("$destination/$photo_old")) {
                unlink("$destination/$photo_old");
            }
        }

        $photo_old = $uploader->getSavedFileName();
    }

    if (!empty($_FILES['photo2']['name'])) {
        require_once XOOPS_ROOT_PATH . '/class/uploader.php';

        $updir = 'photo2/';

        $allowed_mimetypes = ['image/gif', 'image/jpg', 'image/jpeg', 'image/x-png'];

        $uploader = new XoopsMediaUploader($updir, $allowed_mimetypes, $photomax = $xoopsModuleConfig['ads_photomax'], $xoopsModuleConfig['ads_maxwide'], $maxhigh = $xoopsModuleConfig['ads_maxhigh']);

        $uploader->setTargetFileName($date . '_' . $_FILES['photo2']['name']);

        $uploader->fetchMedia('photo2');

        if (!$uploader->upload()) {
            $errors = $uploader->getErrors();

            redirect_header("modify.php?op=ModAd&amp;lid=$lid", 1, $errors);

            //return False;

            exit();
        }

        if ($photo2_old) {
            if (@file_exists("$destination2/$photo2_old")) {
                unlink("$destination2/$photo2_old");
            }
        }

        $photo2_old = $uploader->getSavedFileName();
    }

    if (!empty($_FILES['photo3']['name'])) {
        require_once XOOPS_ROOT_PATH . '/class/uploader.php';

        $updir = 'photo3/';

        $allowed_mimetypes = ['image/gif', 'image/jpg', 'image/jpeg', 'image/x-png'];

        $uploader = new XoopsMediaUploader($updir, $allowed_mimetypes, $photomax = $xoopsModuleConfig['ads_photomax'], $xoopsModuleConfig['ads_maxwide'], $maxhigh = $xoopsModuleConfig['ads_maxhigh']);

        $uploader->setTargetFileName($date . '_' . $_FILES['photo3']['name']);

        $uploader->fetchMedia('photo3');

        if (!$uploader->upload()) {
            $errors = $uploader->getErrors();

            redirect_header("modify.php?op=ModAd&amp;lid=$lid", 1, $errors);

            //return False;

            exit();
        }

        if ($photo3_old) {
            if (@file_exists("$destination3/$photo3_old")) {
                unlink("$destination3/$photo3_old");
            }
        }

        $photo3_old = $uploader->getSavedFileName();
    }

    $xoopsDB->query('update ' . $xoopsDB->prefix('ads_listing') . " set cid='$cat', title='$title',  expire='$expire', type='$type', desctext='$desctext', tel='$tel', price='$price', typeprice='$typeprice', email='$email', submitter='$submitter', town='$town', country='$country', valid='$valid', photo='$photo_old', photo2='$photo2_old', photo3='$photo3_old' where lid=$lid");

    redirect_header('index.php', 1, _ADS_ANNMOD2);

    exit();
}

####################################################
foreach ($_POST as $k => $v) {
    ${$k} = $v;
}

$ok = $_GET['ok'] ?? '';

if (!isset($_POST['lid']) && isset($_GET['lid'])) {
    $lid = $_GET['lid'];
}
if (!isset($_POST['op']) && isset($_GET['op'])) {
    $op = $_GET['op'];
}

switch ($op) {
    case 'ModAd':
        require XOOPS_ROOT_PATH . '/header.php';
        ModAd($lid);
        require XOOPS_ROOT_PATH . '/footer.php';
    break;
    case 'ModAdS':
            ModAdS($lid, $cid, $title, $expire, $type, $desctext, $tel, $price, $typeprice, $date, $email, $submitter, $town, $country, $valid, $photo, $photo_old, $photo_size, $photo_name, $photo2, $photo2_old, $photo2_size, $photo2_name, $photo3, $photo3_old, $photo3_size, $photo3_name, $_FILES, $supprim, $supprim2, $supprim3);
    break;
        case 'ListingDel':
        require XOOPS_ROOT_PATH . '/header.php';
        ListingDel($lid, $ok);
        require XOOPS_ROOT_PATH . '/footer.php';
        break;
    default:
    redirect_header('index.php', 1, '' . _RETURNANN . '');
    break;
}
