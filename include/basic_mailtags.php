<?php

if (!defined('XOOPS_ROOT_PATH')) {
    trigger_error('Access not found');

    exit('Access not found');
}
$tags['LINK_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/contact.php?' . '&lid=' . $lid;
$tags['OTHER_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname');
$tags['CONTACT_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/index.php?pa=viewads' . '&lid=' . $lid;
$sql = 'SELECT ads.lid, ads.cid, ads.title, ads.type, ads.desctext, ads.tel, ads.price, ads.typeprice,  ads.date, ads.email, 
ads.submitter, ads.town, ads.country, ads.valid, ads.view, cat.title 
FROM ' . $xoopsDB->prefix('ads_categories') . ' as cat, ' . $xoopsDB->prefix('ads_listing') . ' as ads WHERE ads.lid =' . $lid . ' and ads.cid = cat.cid limit 1';

$result = $xoopsDB->query($sql);
while (list($tag_lid, $tag_cid, $tag_title, $tag_type, $tag_desctext, $tag_tel, $tag_price, $tag_typeprice, $tag_date,
        $tag_email, $tag_submitter, $tag_town, $tag_view, $tag_valid, $tag_category_title)
        = $xoopsDB->fetchRow($result)) {
    $tags['LID'] = $tag_lid;

    $tags['CID'] = $tag_cid;

    $tags['TITLE'] = $myts->displayTarea($tag_title, $html = 0, $smiley = 0, $xcode = 0, $image = 0, $br = 0);

    $tags['TYPE'] = $myts->displayTarea($tag_type, $html = 0, $smiley = 0, $xcode = 0, $image = 0, $br = 0);

    $tags['DESCRIPTION'] = $myts->displayTarea($tag_desctext, $html = 0, $smiley = 0, $xcode = 0, $image = 0, $br = 0);

    $tags['SUBMITTER'] = $myts->displayTarea($tag_submitter, $html = 0, $smiley = 0, $xcode = 0, $image = 0, $br = 0);

    $tags['TEL'] = $myts->displayTarea($tag_tel, $html = 0, $smiley = 0, $xcode = 0, $image = 0, $br = 0);

    $tags['PRICE'] = $myts->displayTarea($tag_price, $html = 0, $smiley = 0, $xcode = 0, $image = 0, $br = 0);

    $tags['TYPEPRICE'] = $myts->displayTarea($tag_typeprice, $html = 0, $smiley = 0, $xcode = 0, $image = 0, $br = 0);

    $tags['DATE'] = formatTimestamp($myts->displayTarea($tag_date, $html = 0, $smiley = 0, $xcode = 0, $image = 0, $br = 0), 's');

    $tags['EMAIL'] = $tag_email;

    $tags['TOWN'] = $myts->displayTarea($tag_town, $html = 0, $smiley = 0, $xcode = 0, $image = 0, $br = 0);

    $tags['COUNTRY'] = $myts->displayTarea($tag_country, $html = 0, $smiley = 0, $xcode = 0, $image = 0, $br = 0);

    $tags['VIEW'] = $myts->displayTarea($tag_view, $html = 0, $smiley = 0, $xcode = 0, $image = 0, $br = 0);

    $tags['VALID'] = $tag_valid;

    $tags['CATEGORY_TITLE'] = $myts->displayTarea($tag_category_title, $html = 0, $smiley = 0, $xcode = 0, $image = 0, $br = 0);
}
