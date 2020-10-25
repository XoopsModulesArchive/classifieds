<?php

// $Id: groupperms.php,v 1.7 2004/07/26 17:51:25 hthouzard Exp $
// ------------------------------------------------------------------------ //
// XOOPS - PHP Content Management System            				        //
// Copyright (c) 2000 XOOPS.org                           					//
// <https://www.xoops.org>                             						//
// ------------------------------------------------------------------------ //
// This program is free software; you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License, or        //
// (at your option) any later version.                                      //
// 																			//
// You may not change or alter any portion of this comment or credits       //
// of supporting developers from this source code or any supporting         //
// source code which is considered copyrighted (c) material of the          //
// original comment or credit authors.                                      //
// 																			//
// This program is distributed in the hope that it will be useful,          //
// but WITHOUT ANY WARRANTY; without even the implied warranty of           //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
// GNU General Public License for more details.                             //
// 																			//
// You should have received a copy of the GNU General Public License        //
// along with this program; if not, write to the Free Software              //
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------ //
require_once 'admin_header.php';
//require_once XOOPS_ROOT_PATH . "/class/xoopstopic.php";
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/class/mygrouppermform.php';
//include_once( '../class/arbre.php' ) ;

xoops_cp_header();

include('./mymenu.php');
echo '<br><br>';
$permtoset = isset($_POST['permtoset']) ? (int)$_POST['permtoset'] : 1;
$selected = ['', '', '', ''];
$selected[$permtoset - 1] = ' selected';
echo "<form method='post' name='jselperm' action='groupperms.php'><table border=0><tr><td><select name='permtoset' onChange='javascript: document.jselperm.submit()'><option value='1'" . $selected[0] . '>' . _MI_ADS_VIEWFORM . "</option><option value='2'" . $selected[1] . '>' . _MI_ADS_SUBMITFORM . "</option><option value='3'" . $selected[2] . '>' . _MI_ADS_PREMIUM . '</option></select></td><td></tr></table></form>';
$module_id = $xoopsModule->getVar('mid');

switch ($permtoset) {
    case 1:
        $title_of_form = _MI_ADS_VIEWFORM;
        $perm_name = 'ads_view';
        $perm_desc = _MI_ADS_VIEWFORM_DESC;
        $item_list_view = [];
        $block_view = [];
        $result_view = $xoopsDB->query('SELECT cid, title FROM ' . $xoopsDB->prefix('ads_categories') . ' ');
        if ($xoopsDB->getRowsNum($result_view)) {
            while (false !== ($myrow_view = $xoopsDB->fetchArray($result_view))) {
                $item_list_view['cid'] = $myrow_view['cid'];

                $item_list_view['title'] = $myrow_view['title'];

                $permform = new MyXoopsGroupPermForm($title_of_form, $module_id, $perm_name, $perm_desc);

                $block_view[] = $item_list_view;

                foreach ($block_view as $itemlists) {
                    $permform->addItem($itemlists['cid'], $myts->displayTarea($itemlists['title']));
                }
            }
        }
        break;
    case 2:
        $title_of_form = _MI_ADS_SUBMITFORM;
        $perm_name = 'ads_submit';
        $perm_desc = _MI_ADS_SUBMITFORM_DESC;
        $item_list_view = [];
        $block_view = [];
        $result_view = $xoopsDB->query('SELECT cid, title FROM ' . $xoopsDB->prefix('ads_categories') . ' ');
        if ($xoopsDB->getRowsNum($result_view)) {
            while (false !== ($myrow_view = $xoopsDB->fetchArray($result_view))) {
                $item_list_view['cid'] = $myrow_view['cid'];

                $item_list_view['title'] = $myrow_view['title'];

                $permform = new MyXoopsGroupPermForm($title_of_form, $module_id, $perm_name, $perm_desc);

                $block_view[] = $item_list_view;

                foreach ($block_view as $itemlists) {
                    $permform->addItem($itemlists['cid'], $myts->displayTarea($itemlists['title']));
                }
            }
        }
        break;
    case 3:
        $title_of_form = _MI_ADS_PREMIUM;
        $perm_name = 'ads_premium';
        $perm_desc = _MI_ADS_PREMIUM_DESC;
        $item_list_view = [];
        $block_view = [];
        $result_view = $xoopsDB->query('SELECT cid, title FROM ' . $xoopsDB->prefix('ads_categories') . ' ');
        if ($xoopsDB->getRowsNum($result_view)) {
            while (false !== ($myrow_view = $xoopsDB->fetchArray($result_view))) {
                $item_list_view['cid'] = $myrow_view['cid'];

                $item_list_view['title'] = $myrow_view['title'];

                $permform = new MyXoopsGroupPermForm($title_of_form, $module_id, $perm_name, $perm_desc);

                $block_view[] = $item_list_view;

                foreach ($block_view as $itemlists) {
                    $permform->addItem($itemlists['cid'], $myts->displayTarea($itemlists['title']));
                }
            }
        }
    break;
}

echo $permform->render();
echo "<br><br><br><br>\n";
unset($permform);

xoops_cp_footer();
