<?php

//
// ------------------------------------------------------------------------- //
//               E-Xoops: Content Management for the Masses                  //
//                       < http://www.e-xoops.com >                          //
// ------------------------------------------------------------------------- //
// Original Author: Pascal Le Boustouller
// Author Website : pascal.e-xoops@perso-search.com
// Licence Type   : GPL
// ------------------------------------------------------------------------- //

if ($_POST['submit']) {
    // Define Variables for register_globals Off. contribution by Peekay

    $id = $_REQUEST['id'] ?? null;

    $date = $_REQUEST['date'] ?? null;

    $namep = $_REQUEST['namep'] ?? null;

    $ipnumber = $_REQUEST['ipnumber'] ?? null;

    $message = $_REQUEST['message'] ?? null;

    $typeprice = $_REQUEST['typeprice'] ?? null;

    $price = $_REQUEST['price'] ?? null;

    // end define vars

    include 'header.php';

    $mydirname = basename(__DIR__);

    require_once(XOOPS_ROOT_PATH . "/modules/$mydirname/include/gtickets.php");

    global $xoopsConfig, $xoopsDB, $myts, $meta, $xoopsModuleConfig, $mydirname;

    if (!$GLOBALS['xoopsSecurity']->check(true, $_REQUEST['token'])) {
        redirect_header(XOOPS_URL . "/modules/$mydirname/index.php?pa=viewads&lid=$id", 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
    }

    $lid = $_POST['id'];

    $result = $xoopsDB->query('select email, submitter, title, type, desctext, price, typeprice FROM  ' . $xoopsDB->prefix('ads_listing') . " WHERE lid = '$id'");

    while (list($email, $submitter, $title, $type, $desctext, $price, $typeprice) = $xoopsDB->fetchRow($result)) {
        if ($_POST['tele']) {
            $teles = '' . _ADS_ORAT . '' . $_POST['tele'] . '';
        } else {
            $teles = '';
        }

        if ($price) {
            $price = '' . _ADS_PRICE . ' ' . $xoopsModuleConfig['ads_monnaie'] . " $price - $typeprice";
        } else {
            $price = '';
        }

        // Specification for Japan:

        // $message .= ""._ADS_MESSFROM." $namep "._ADS_FROMANNOF." ".$meta['title']."\n\n";

        // $message .= ""._ADS_REMINDANN."\n$type : $title\nTexte : $desctext\n\n";

        // $message .= "--------------- "._ADS_STARTMESS." $namep -------------------\n";

        // $message .= "$messtext\n\n";

        // $message .= "--------------- "._ADS_ENDMESS." de $namep -------------------\n\n";

        // $message .= ""._ADS_CANJOINT." $namep "._ADS_TO." $post $teles";

        $message .= '' . _ADS_REMINDANN . '' . $xoopsConfig['sitename'] . "\n";

        $message .= "$type : $title\n$price\n" . _ADS_ANNONCE . " :\n $desctext\n\n";

        $message .= '' . _ADS_STARTMESS . "\n";

        $message .= '' . _ADS_MESSFROM . ' ' . $_POST['namep'] . "\n\n";

        $message .= '' . $_POST['messtext'] . "\n\n";

        $message .= '' . _ADS_CANJOINT . ' ' . $_POST['namep'] . ' ' . _ADS_TO . ' ' . $_POST['post'] . " $teles \n\n";

        $message .= '' . _ADS_MESSAGE_END . " \n\n" . _ADS_ENDMESS . "\n\n" . _ADS_SECURE_SEND . "\n";

        $subject = '' . _ADS_CONTACTAFTERANN . '';

        $mail = getMailer();

        $mail->useMail();

        //$mail->setFromName($meta['title']);

        $mail->setFromEmail($_POST['post']);

        $mail->setToEmails($email);

        $mail->setSubject($subject);

        $mail->setBody($message);

        $mail->send();

        echo $mail->getErrors();
    }

    $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('ads_ip_log') . " values ( '', '$lid', '$date', '$namep', '$ipnumber', '" . $_POST['post'] . "')");

    redirect_header('index.php', 1, _ADS_MESSEND);

    exit();
}
    $lid = (int)$_GET['lid'];

    include 'header.php';

    $mydirname = basename(__DIR__);

    require_once(XOOPS_ROOT_PATH . "/modules/$mydirname/include/gtickets.php");
    global $xoopsConfig, $xoopsDB, $myts, $meta, $mydirname;
    $token = $GLOBALS['xoopsSecurity']->createToken();
    require XOOPS_ROOT_PATH . '/header.php';
    echo "<table width='100%' border='0' cellspacing='1' cellpadding='8'><tr class='bg4'><td valign='top'>\n";
    $time = time();
    $ipnumber = (string)$_SERVER[REMOTE_ADDR];
    echo '<script type="text/javascript">
          function verify() {
                var msg = "' . _ADS_VALIDERORMSG . '\\n__________________________________________________\\n\\n";
                var errors = "FALSE";

			
				if (window.document.cont.namep.value == "") {
                        errors = "TRUE";
                        msg += "' . _ADS_VALIDSUBMITTER . '\\n";
                }
				
				if (window.document.cont.post.value == "") {
                        errors = "TRUE";
                        msg += "' . _ADS_VALIDEMAIL . '\\n";
                }
				
				if (window.document.cont.messtext.value == "") {
                        errors = "TRUE";
                        msg += "' . _ADS_VALIDMESS . '\\n";
                }
				
  
                if (errors == "TRUE") {
                        msg += "__________________________________________________\\n\\n' . _ADS_VALIDMSG . '\\n";
                        alert(msg);
                        return false;
                }
          }
          </script>';

    echo '<b>' . _ADS_CONTACTAUTOR . '</b><br><br>';
    echo '' . _ADS_TEXTAUTO . '<br>';
    echo '<form onSubmit="return verify();" method="post" action="contact.php" name="cont">';
    echo "<input type=\"hidden\" name=\"id\" value=\"$lid\">";
    echo '<input type="hidden" name="submit" value="1">';

    echo "<table width='100%' class='outer' cellspacing='1'>
    <tr>
      <td class='head'>" . _ADS_YOURNAME . '</td>';
    if ($xoopsUser) {
        $idd = $xoopsUser->getVar('name', 'E');

        $idde = $xoopsUser->getVar('email', 'E');

        echo "<td class='even'><input type=\"text\" name=\"namep\" size=\"42\" value=\"$idd\">";
    } else {
        echo "<td class='even'><input type=\"text\" name=\"namep\" size=\"42\"></td>";
    }
    echo "</tr>
    <tr>
      <td class='head'>" . _ADS_YOUREMAIL . "</td>
      <td class='even'><input type=\"text\" name=\"post\" size=\"42\" value=\"$idde\"></font></td>
    </tr>
    <tr>
      <td class='head'>" . _ADS_YOURPHONE . "</td>
      <td class='even'><input type=\"text\" name=\"tele\" size=\"42\"></font></td>
    </tr>
    <tr>
      <td class='head'>" . _ADS_YOURMESSAGE . "</td>
      <td class='even'><textarea rows=\"5\" name=\"messtext\" cols=\"40\"></textarea></td>
    </tr>
	</table><table class='outer'><tr><td>" . _ADS_YOUR_IP . '&nbsp;
        <img src="' . XOOPS_URL . "/modules/$mydirname/ip_image.php\" alt=\"\"><br>" . _ADS_IP_LOGGED . '
        </td></tr></table>
	<br>';
    echo "<input type=\"hidden\" name=\"token\" value=\"$token\">";
    echo '<input type="hidden" name="ip_id" value="">';
    echo "<input type=\"hidden\" name=\"lid\" value=\"$lid\">";
    echo "<input type=\"hidden\" name=\"ipnumber\" value=\"$ipnumber\">";
    echo "<input type=\"hidden\" name=\"date\" value=\"$time\">";
      echo '<p><input type="submit" name="submit" value="' . _ADS_SENDFR . '"></p>
	</form>';

    echo '</td></tr></table>';
    require XOOPS_ROOT_PATH . '/footer.php';



