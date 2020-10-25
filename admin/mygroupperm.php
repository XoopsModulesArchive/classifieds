<?php

// $Id: groupperm.php,v 1.8 2004/12/26 19:12:04 onokazu Exp $

require dirname(__DIR__, 3) . '/include/cp_header.php';
$modid = isset($_POST['modid']) ? (int)$_POST['modid'] : 0;

// we dont want system module permissions to be changed here
if ($modid <= 1 || !is_object($xoopsUser) || !$xoopsUser->isAdmin($modid)) {
    redirect_header(XOOPS_URL . '/index.php', 1, _NOPERM);

    exit();
}
$moduleHandler = xoops_getHandler('module');
$module = $moduleHandler->get($modid);
if (!is_object($module) || !$module->getVar('isactive')) {
    redirect_header(XOOPS_URL . '/admin.php', 1, _MODULENOEXIST);

    exit();
}
$memberHandler = xoops_getHandler('member');
$group_list = $memberHandler->getGroupList();
if (is_array($_POST['perms']) && !empty($_POST['perms'])) {
    $gpermHandler = xoops_getHandler('groupperm');

    foreach ($_POST['perms'] as $perm_name => $perm_data) {
        if (false !== $gpermHandler->deleteByModule($modid, $perm_name)) {
            foreach ($perm_data['groups'] as $group_id => $item_ids) {
                foreach ($item_ids as $item_id => $selected) {
                    if (1 == $selected) {
                        // make sure that all parent ids are selected as well

                        if ('' != $perm_data['parents'][$item_id]) {
                            $parent_ids = explode(':', $perm_data['parents'][$item_id]);

                            foreach ($parent_ids as $pid) {
                                if (0 != $pid && !array_key_exists($pid, $item_ids)) {
                                    // one of the parent items were not selected, so skip this item

                                    $msg[] = sprintf(_AM_ADS_PERMADDNG, '<b>' . $perm_name . '</b>', '<b>' . $perm_data['itemname'][$item_id] . '</b>', '<b>' . $group_list[$group_id] . '</b>') . ' (' . _AM_ADS_PERMADDNGP . ')';

                                    continue 2;
                                }
                            }
                        }

                        $gperm = $gpermHandler->create();

                        $gperm->setVar('gperm_groupid', $group_id);

                        $gperm->setVar('gperm_name', $perm_name);

                        $gperm->setVar('gperm_modid', $modid);

                        $gperm->setVar('gperm_itemid', $item_id);

                        if (!$gpermHandler->insert($gperm)) {
                            $msg[] = sprintf(_AM_ADS_PERMADDNG, '<b>' . $perm_name . '</b>', '<b>' . $perm_data['itemname'][$item_id] . '</b>', '<b>' . $group_list[$group_id] . '</b>');
                        } else {
                            $msg[] = sprintf(_AM_ADS_PERMADDOK, '<b>' . $perm_name . '</b>', '<b>' . $perm_data['itemname'][$item_id] . '</b>', '<b>' . $group_list[$group_id] . '</b>');
                        }

                        unset($gperm);
                    }
                }
            }
        } else {
            +$msg[] = sprintf(_AM_ADS_PERMRESETNG, $module->getVar('name') . '(' . $perm_name . ')');
        }
    }
}

$backlink = XOOPS_URL . '/admin.php';
if ($module->getVar('hasadmin')) {
    $adminindex = $_POST['redirect_url'] ?? $module->getInfo('adminindex');

    if ($adminindex) {
        $backlink = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/admin/groupperms.php';
    }
}

$msg[] = '<br><br><a href="' . $backlink . '">' . _BACK . '</a>';
xoops_cp_header();
xoops_result($msg);
xoops_cp_footer();
