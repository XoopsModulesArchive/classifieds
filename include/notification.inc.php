<?php

// $Id: notification.inc.php,v 1.1.1.1 2004/08/08 17:32:06 Administrator Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <https://www.xoops.org>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
if (!defined('XOOPS_ROOT_PATH')) {
    trigger_error('Access not found');

    exit('Access not found');
}
    $mydirname = basename(dirname(__FILE__, 2));

function ads_notify_iteminfo($category, $item_id)
{
    $moduleHandler = xoops_getHandler('module');

    $module = $moduleHandler->getByDirname('$mydirname');

    if ('global' == $category) {
        $item['name'] = '';

        $item['url'] = '';

        return $item;
    }

    global $xoopsDB;

    if ('category' == $category) {
        // Assume we have a valid topid id

        //新規記事が投稿されたとき、カテゴリ名と記事タイトル、記事タイプを取り出す。

        $sql = 'SELECT title  FROM ' . $xoopsDB->prefix('ads_categories') . ' WHERE cid = ' . $item_id . ' limit 1';

        //echo $sql;
        $result = $xoopsDB->query($sql); // TODO: error check
        $result_array = $xoopsDB->fetchArray($result);

        $item['name'] = $result_array['title'];

        //		$item['type'] = $result_array['ann.type'];

        $item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/index.php?pa=adsview&amp;cid=' . $item_id;

        return $item;
    }

    if ('listing' == $category) {
        // Assume we have a valid post id

        //コメントが投稿されたとき。カテゴリ名と記事タイトルと記事タイプを取り出す

        $sql = 'SELECT title FROM ' . $xoopsDB->prefix('ads_listing') . ' WHERE lid = ' . $item_id . ' LIMIT 1';

        $result = $xoopsDB->query($sql);

        $result_array = $xoopsDB->fetchArray($result);

        $item['name'] = $result_array['title'];

        //		$item['catname'] = $result_array['cat.title'];

        $item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/index.php?pa=viewads&lid= ' . $item_id;

        return $item;
    }
}
