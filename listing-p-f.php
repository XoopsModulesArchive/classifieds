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

    require XOOPS_ROOT_PATH . "/modules/$mydirname/include/functions.php";

function SendFriend($lid)
{
    global $xoopsConfig, $xoopsDB, $xoopsUser, $xoopsTheme, $xoopsLogger;

    $result = $xoopsDB->query('select lid, title, type FROM ' . $xoopsDB->prefix('ads_listing') . " where lid=$lid");

    [$lid, $title, $type] = $xoopsDB->fetchRow($result);

    OpenTable();

    echo '
	    <b>' . _ADS_SENDTO . " $lid \"<b>$type : $title</b>\" " . _ADS_FRIEND . "<br><br>
	    <form action=\"listing-p-f.php\" method=post>
	    <input type=\"hidden\" name=\"lid\" value=\"$lid\">";

    if ($xoopsUser) {
        $idd = $iddds = $xoopsUser->getVar('name', 'E');

        $idde = $iddds = $xoopsUser->getVar('email', 'E');
    }

    echo "
	<table width='99%' class='outer' cellspacing='1'>
    <tr>
      <td class='head' width='30%'>" . _ADS_NAME . " </td>
      <td class='even'><input class=\"textbox\" type=\"text\" name=\"yname\" value=\"$idd\"></td>
    </tr>
    <tr>
      <td class='head'>" . _ADS_MAIL . " </td>
      <td class='even'><input class=\"textbox\" type=\"text\" name=\"ymail\" value=\"$idde\"></td>
    </tr>
    <tr>
      <td colspan=2 class='even'>&nbsp;</td>
    </tr>
    <tr>
      <td class='head'>" . _ADS_NAMEFR . " </td>
      <td class='even'><input class=\"textbox\" type=\"text\" name=\"fname\"></td>
    </tr>
    <tr>
      <td class='head'>" . _ADS_MAILFR . " </td>
      <td class='even'><input class=\"textbox\" type=\"text\" name=\"fmail\"></td>
    </tr>
	</table><br>
    <input type=hidden name=op value=MailAd>
    <input type=submit value=" . _ADS_SENDFR . '>
    </form>     ';

    CloseTable();

    //	Copyright();
//	require XOOPS_ROOT_PATH."/footer.php";
}

function MailAd($lid, $yname, $ymail, $fname, $fmail)
{
    global $xoopsConfig, $xoopsUser, $xoopsDB, $xoopsModuleConfig, $myts, $xoopsLogger, $mydirname;

    $result = $xoopsDB->query('select lid, title, expire, type, desctext, tel, price, typeprice, date, email, submitter, town, country, photo FROM ' . $xoopsDB->prefix('ads_listing') . " where lid=$lid");

    [$lid, $title, $expire, $type, $desctext, $tel, $price, $typeprice, $date, $email, $submitter, $town, $country, $photo] = $xoopsDB->fetchRow($result);

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

    //	Specification for Japan:

    //	$message .= ""._ADS_HELLO." $fname,\n\n$yname "._ADS_MESSAGE."\n\n";

    $subject = '' . _ADS_SUBJET . ' ' . $xoopsConfig['sitename'] . '';

    $message .= '' . _ADS_HELLO . " $fname,\n\n$yname " . _ADS_MESSAGE . "\n\n";

    $message .= "$type :  $title\n";

    if ($price > 0) {
        $message .= '' . _ADS_PRICE2 . ' ' . $xoopsModuleConfig['ads_monnaie'] . " $price - $typeprice\n";
    }

    $message .= "$desctext\n\n";

    $message .= '' . _ADS_NOMAIL2 . ' ' . XOOPS_URL . "/modules/$mydirname/contact.php?lid=$lid\n";

    if ($tel) {
        $message .= '' . _ADS_TEL2 . " $tel\n";
    }

    if ($town) {
        $message .= '' . _ADS_TOWN . " $town\n";
    }

    if ($country) {
        $message .= '' . _ADS_COUNTRY . " $country\n";
    }

    $message .= "\n" . _ADS_INTERESS . ' ' . $xoopsConfig['sitename'] . "\n" . XOOPS_URL . "/modules/$mydirname/";

    //   mail($fmail, $subject, $message, "From: \"$yname\" <$ymail>\nX-MailAder: PHP/" . phpversion());

    $mail = getMailer();

    $mail->useMail();

    $mail->setFromEmail($ymail);

    $mail->setToEmails($fmail);

    $mail->setSubject($subject);

    $mail->setBody($message);

    $mail->send();

    echo $mail->getErrors();

    redirect_header('index.php', 1, _ADS_ANNSEND);

    exit();
}

function PrintAd($lid)
{
    //global $xoopsConfig, $xoopsDB, $monnaie, $useroffset, $claday, $ynprice, $myts,$xoopsLogger;

    global $xoopsConfig, $xoopsUser, $xoopsDB, $xoopsModuleConfig, $useroffset, $myts, $xoopsLogger, $mydirname;

    $currenttheme = getTheme();

    $result = $xoopsDB->query('select lid, title, expire, type, desctext, tel, price, typeprice, date, email, submitter, town, country, photo FROM ' . $xoopsDB->prefix('ads_listing') . " where lid=$lid");

    [$lid, $title, $expire, $type, $desctext, $tel, $price, $typeprice, $date, $email, $submitter, $town, $country, $photo] = $xoopsDB->fetchRow($result);

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

    echo '
    <html>
    <head><title>' . $xoopsConfig['sitename'] . '</title>
	<link rel="StyleSheet" href="../../themes/' . $currenttheme . '/style/style.css" type="text/css">
	</head>
    <body bgcolor="#FFFFFF" text="#000000">
    <table border=0><tr><td>
    
    <table border=0 width=100% cellpadding=0 cellspacing=1 bgcolor="#000000"><tr><td>
    <table border=0 width=100% cellpadding=15 cellspacing=1 bgcolor="#FFFFFF"><tr><td>';

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

    $date2 = $date + ($expire * 86400);

    $date = formatTimestamp($date, 's');

    $date2 = formatTimestamp($date2, 's');

    echo '<br><br><table width=99% border=0>
	    <tr>
      <td>' . _ADS_CLASSIFIED . " (No. $lid ) <br>" . _ADS_FROM . " $submitter <br><br>";

    echo " <b>$type :</b> <i>$title</i><br>";

    if ($price > 0) {
        echo '<b>' . _ADS_PRICE2 . '</b> ' . $xoopsModuleConfig['ads_monnaie'] . " $price  - $typeprice<br>";
    }

    if ($photo) {
        echo "<tr><td><left><img src=\"photo/$photo\" border=0></center>";
    }

    echo '</td>
	      </tr>
    <tr>
      <td><b>' . _ADS_ANNONCE . "</b><br><br><div style=\"text-align:justify;\">$desctext</div><p>";

    if ($tel) {
        echo '<br><b>' . _ADS_TEL . "</b> $tel";
    }

    if ($town) {
        echo '<br><b>' . _ADS_TOWN . "</b> $town";
    }

    if ($country) {
        echo '<br><b>' . _ADS_COUNTRY . "</b> $country";
    }

    echo '<hr>';

    echo '' . _ADS_NOMAIL . ' <br>' . XOOPS_URL . "/modules/$mydirname/index.php?pa=viewads&lid=$lid<br>";

    echo '<br><br>' . _ADS_DATE2 . " $date " . _ADS_AND . ' ' . _ADS_DISPO . " $date2<br><br>";

    echo '</td>
	</tr>
	</table>';

    echo '<br><br></td></tr></table></td></tr></table>
    <br><br><center>
    ' . _ADS_EXTRANN . ' <b>' . $xoopsConfig['sitename'] . '</b><br>
    <a href="' . XOOPS_URL . "/modules/$mydirname/\">" . XOOPS_URL . "/modules/$mydirname/</a>
    </td></tr></table>
    </body>
    </html>";
}

##############################################################

$yname = !empty($_POST['yname']) ? $myts->addSlashes($_POST['yname']) : '';
$ymail = !empty($_POST['ymail']) ? $myts->addSlashes($_POST['ymail']) : '';
$fname = !empty($_POST['fname']) ? $myts->addSlashes($_POST['fname']) : '';
$fmail = !empty($_POST['fmail']) ? $myts->addSlashes($_POST['fmail']) : '';

if (!isset($_POST['lid']) && isset($_GET['lid'])) {
    $lid = (int)$_GET['lid'];
} else {
    $lid = (int)$_POST['lid'];
}

$op = '';
if (!empty($_GET['op'])) {
    $op = $_GET['op'];
} elseif (!empty($_POST['op'])) {
    $op = $_POST['op'];
}

switch ($op) {
    case 'SendFriend':
    require XOOPS_ROOT_PATH . '/header.php';
    SendFriend($lid);
    require XOOPS_ROOT_PATH . '/footer.php';
    break;
    case 'MailAd':
    MailAd($lid, $yname, $ymail, $fname, $fmail);
    break;
    case 'PrintAd':
    PrintAd($lid);
    break;
    default:
    redirect_header('index.php', 1, '' . _RETURNGLO . '');
    break;
}
