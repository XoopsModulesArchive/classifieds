<?php

// comment callback functions

function ads_com_update($usid, $total_num)
{
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $sql = 'UPDATE ' . $db->prefix('ads_listing') . ' SET comments = ' . $total_num . ' WHERE usid = ' . $usid;

    $db->query($sql);
}

function ads_com_approve(&$comment)
{
    // notification mail here
}
