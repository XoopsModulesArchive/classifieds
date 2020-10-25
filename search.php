<?php

// $Id: search.php 506 2006-05-26 23:10:37Z skalpa $
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

$xoopsOption['pagetype'] = 'search';

require dirname(__DIR__, 2) . '/mainfile.php';
$mydirname = basename(__DIR__);
$xmid = $xoopsModule->getVar('mid');
$configHandler = xoops_getHandler('config');
$xoopsConfigSearch = $configHandler->getConfigsByCat(XOOPS_CONF_SEARCH);
if (1 != $xoopsConfigSearch['enable_search']) {
    header("Location: '.XOOPS_URL.'modules/$mydirname/index.php");

    exit();
}
$action = 'search';
if (!empty($_GET['action'])) {
    $action = $_GET['action'];
} elseif (!empty($_POST['action'])) {
    $action = $_POST['action'];
}
$query = '';
if (!empty($_GET['query'])) {
    $query = $_GET['query'];
} elseif (!empty($_POST['query'])) {
    $query = $_POST['query'];
}
$andor = 'AND';
if (!empty($_GET['andor'])) {
    $andor = $_GET['andor'];
} elseif (!empty($_POST['andor'])) {
    $andor = $_POST['andor'];
}
$mid = $uid = $start = 0;
if (!empty($_GET['mid'])) {
    $mid = (int)$_GET['mid'];
} elseif (!empty($_POST['mid'])) {
    $mid = (int)$_POST['mid'];
}
if (!empty($_GET['uid'])) {
    $uid = (int)$_GET['uid'];
} elseif (!empty($_POST['uid'])) {
    $uid = (int)$_POST['uid'];
}
if (!empty($_GET['start'])) {
    $start = (int)$_GET['start'];
} elseif (!empty($_POST['start'])) {
    $start = (int)$_POST['start'];
}
$queries = [];

if ('results' == $action) {
    if ('' == $query) {
        redirect_header('search.php', 1, _SR_PLZENTER);

        exit();
    }
} elseif ('showall' == $action) {
    if ('' == $query || empty($mid)) {
        redirect_header('search.php', 1, _SR_PLZENTER);

        exit();
    }
} elseif ('showallbyuser' == $action) {
    if (empty($mid) || empty($uid)) {
        redirect_header('search.php', 1, _SR_PLZENTER);

        exit();
    }
}

$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
$gpermHandler = xoops_getHandler('groupperm');
$available_modules = $gpermHandler->getItemIds('module_read', $groups);

if ('search' == $action) {
    require XOOPS_ROOT_PATH . '/header.php';

    require __DIR__ . '/include/searchform.php';

    $search_form->display();

    require XOOPS_ROOT_PATH . '/footer.php';

    exit();
}

if ('OR' != $andor && 'exact' != $andor && 'AND' != $andor) {
    $andor = 'AND';
}

$myts = MyTextSanitizer::getInstance();
if ('showallbyuser' != $action) {
    if ('exact' != $andor) {
        $ignored_queries = []; // holds kewords that are shorter than allowed minmum length

        $temp_queries = preg_preg_split('/[\s,]+/', $query);

        foreach ($temp_queries as $q) {
            $q = trim($q);

            if (mb_strlen($q) >= $xoopsConfigSearch['keyword_min']) {
                $queries[] = $myts->addSlashes($q);
            } else {
                $ignored_queries[] = $myts->addSlashes($q);
            }
        }

        if (0 == count($queries)) {
            redirect_header('search.php', 2, sprintf(_SR_KEYTOOSHORT, $xoopsConfigSearch['keyword_min']));

            exit();
        }
    } else {
        $query = trim($query);

        if (mb_strlen($query) < $xoopsConfigSearch['keyword_min']) {
            redirect_header('search.php', 2, sprintf(_SR_KEYTOOSHORT, $xoopsConfigSearch['keyword_min']));

            exit();
        }

        $queries = [$myts->addSlashes($query)];
    }
}
switch ($action) {
    case 'results':
    $moduleHandler = xoops_getHandler('module');
    $criteria = new CriteriaCompo(new Criteria('hassearch', 1));
    $criteria->add(new Criteria('isactive', 1));
    $criteria->add(new Criteria('mid', '(' . implode(',', $available_modules) . ')', 'IN'));
    $modules = $moduleHandler->getObjects($criteria, true);
    $mids = $_REQUEST['mids'] ?? [];
    if (empty($mids) || !is_array($mids)) {
        unset($mids);

        $mids = array_keys($xmid);
    }
    require XOOPS_ROOT_PATH . '/header.php';

// for xoops 2.2.x versions
if (file_exists('language/' . $xoopsConfig['language'] . '/main.php')) {
    require_once 'language/' . $xoopsConfig['language'] . '/main.php';
} else {
    require_once 'language/english/main.php';
}
// end

    echo '<h3>' . _ADS_SEARCHRESULTS . "</h3>\n";
    echo _SR_KEYWORDS . ':';
    if ('exact' != $andor) {
        foreach ($queries as $q) {
            echo ' <b>' . htmlspecialchars(stripslashes($q), ENT_QUOTES | ENT_HTML5) . '</b>';
        }

        if (!empty($ignored_queries)) {
            echo '<br>';

            printf(_SR_IGNOREDWORDS, $xoopsConfigSearch['keyword_min']);

            foreach ($ignored_queries as $q) {
                echo ' <b>' . htmlspecialchars(stripslashes($q), ENT_QUOTES | ENT_HTML5) . '</b>';
            }
        }
    } else {
        echo ' "<b>' . htmlspecialchars(stripslashes($queries[0]), ENT_QUOTES | ENT_HTML5) . '</b>"';
    }
    echo '<br>';
    foreach ($mids as $mid) {
        $mid = (int)$mid;

        if (in_array($mid, $available_modules, true)) {
            $module = &$modules[$mid];

            $results = &$module->search($queries, $andor, 5, 0);

            $count = count($results);

            if (!is_array($results) || 0 == $count) {
                echo '<p>' . _SR_NOMATCH . '</p>';
            } else {
                for ($i = 0; $i < $count; $i++) {
                    echo '<table width="100%" class="outer"><tr>';

                    echo '<td width="30%">';

                    echo '<b>' . htmlspecialchars($results[$i]['type'], ENT_QUOTES | ENT_HTML5) . '</b><br>';

                    if (isset($results[$i]['photo']) && '' != $results[$i]['photo']) {
                        echo "<a href='" . $results[$i]['link'] . "'><img src='" . $results[$i]['sphoto'] . "' alt='' width='100'></a></td>&nbsp;";
                    } else {
                        echo "<a href='" . $results[$i]['link'] . "'><img src='" . $results[$i]['nophoto'] . "' alt='' width='100'></a></td>&nbsp;";
                    }

                    if (!preg_match("/^http[s]*:\/\//i", $results[$i]['link'])) {
                        $results[$i]['link'] = '' . $results[$i]['link'];
                    }

                    echo '<td width="50%">';

                    echo "<b><a href='" . $results[$i]['link'] . "'>" . htmlspecialchars($results[$i]['title'], ENT_QUOTES | ENT_HTML5) . '</a></b><br><br>';

                    if (!XOOPS_USE_MULTIBYTES) {
                        if (mb_strlen($results[$i]['desctext']) >= 14) {
                            $results[$i]['desctext'] = mb_substr($results[$i]['desctext'], 0, 90) . '...';
                        }
                    }

                    echo '' . htmlspecialchars($results[$i]['desctext'], ENT_QUOTES | ENT_HTML5) . '';

                    echo '</td><td width="20%">';

                    echo '' . $xoopsModuleConfig['ads_monnaie'] . '
' . htmlspecialchars($results[$i]['price'], ENT_QUOTES | ENT_HTML5) . '</a>&nbsp;' . htmlspecialchars($results[$i]['typeprice'], ENT_QUOTES | ENT_HTML5) . '</a>';

                    echo '</td></tr><tr><td>';

                    echo '<small>';

                    $results[$i]['uid'] = @(int)$results[$i]['uid'];

                    if (!empty($results[$i]['uid'])) {
                        $uname = XoopsUser::getUnameFromId($results[$i]['uid']);

                        echo '&nbsp;&nbsp;' . _ADS_FROM . "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $results[$i]['uid'] . "'>" . $uname . "</a>\n";
                    }

                    echo !empty($results[$i]['time']) ? ' (' . formatTimestamp((int)$results[$i]['time']) . ')' : '';

                    echo '</small>';

                    echo '</td></tr></table><table>';
                }

                if ($count >= 5) {
                    $search_url = XOOPS_URL . "/modules/$mydirname/search.php?query=" . urlencode(stripslashes(implode(' ', $queries)));

                    $search_url .= "&mid=$mid&action=showall&andor=$andor";

                    echo '<br><a href="' . htmlspecialchars($search_url, ENT_QUOTES | ENT_HTML5) . '">' . _SR_SHOWALLR . '</a>';
                }

                echo '<table>';
            }
        }

        unset($results);

        unset($module);
    }
    include 'include/searchform.php';
    $search_form->display();
    break;
    case 'showall':
    case 'showallbyuser':
    require XOOPS_ROOT_PATH . '/header.php';

// for xoops 2.2.x versions
if (file_exists('language/' . $xoopsConfig['language'] . '/main.php')) {
    require_once 'language/' . $xoopsConfig['language'] . '/main.php';
} else {
    require_once 'language/english/main.php';
}
// end

    $moduleHandler = xoops_getHandler('module');
    $module = $moduleHandler->get($mid);
    $results = $module->search($queries, $andor, 20, $start, $uid);
    $count = count($results);
    if (is_array($results) && $count > 0) {
        $next_results = $module->search($queries, $andor, 1, $start + 20, $uid);

        $next_count = count($next_results);

        $has_next = false;

        if (is_array($next_results) && 1 == $next_count) {
            $has_next = true;
        }

        echo '<h4>' . _ADS_SEARCHRESULTS . "</h4>\n";

        if ('showall' == $action) {
            echo _SR_KEYWORDS . ':';

            if ('exact' != $andor) {
                foreach ($queries as $q) {
                    echo ' <b>' . htmlspecialchars(stripslashes($q), ENT_QUOTES | ENT_HTML5) . '</b>';
                }
            } else {
                echo ' "<b>' . htmlspecialchars(stripslashes($queries[0]), ENT_QUOTES | ENT_HTML5) . '</b>"';
            }

            echo '<br><br>';
        }

        //    printf(_SR_FOUND,$count);

        //    echo "<br>";

        printf(_SR_SHOWING, $start + 1, $start + $count);

        for ($i = 0; $i < $count; $i++) {
            echo '<table width="100%" class="outer"><tr>';

            echo '<td width="30%">';

            echo '<b>' . htmlspecialchars($results[$i]['type'], ENT_QUOTES | ENT_HTML5) . '</b><br>';

            if (isset($results[$i]['photo']) && '' != $results[$i]['photo']) {
                echo "<a href='" . $results[$i]['link'] . "'><img src='" . $results[$i]['sphoto'] . "' alt='' width='100'></a></td>&nbsp;";
            } else {
                echo "<a href='" . $results[$i]['link'] . "'><img src='" . $results[$i]['nophoto'] . "' alt='' width='100'></a></td>&nbsp;";
            }

            if (!preg_match("/^http[s]*:\/\//i", $results[$i]['link'])) {
                $results[$i]['link'] = '' . $results[$i]['link'];
            }

            echo '<td width="50%">';

            echo "<b><a href='" . $results[$i]['link'] . "'>" . htmlspecialchars($results[$i]['title'], ENT_QUOTES | ENT_HTML5) . '</a></b><br><br>';

            if (!XOOPS_USE_MULTIBYTES) {
                if (mb_strlen($results[$i]['desctext']) >= 14) {
                    $results[$i]['desctext'] = mb_substr($results[$i]['desctext'], 0, 90) . '...';
                }
            }

            echo '' . htmlspecialchars($results[$i]['desctext'], ENT_QUOTES | ENT_HTML5) . '';

            echo '</td><td width="20%">';

            echo '' . $xoopsModuleConfig['ads_monnaie'] . '
' . htmlspecialchars($results[$i]['price'], ENT_QUOTES | ENT_HTML5) . '</a>&nbsp;' . htmlspecialchars($results[$i]['typeprice'], ENT_QUOTES | ENT_HTML5) . '</a>';

            echo '</td></tr><tr><td>';

            echo '<small>';

            $results[$i]['uid'] = @(int)$results[$i]['uid'];

            if (!empty($results[$i]['uid'])) {
                $uname = XoopsUser::getUnameFromId($results[$i]['uid']);

                echo '&nbsp;&nbsp;' . _ADS_FROM . "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $results[$i]['uid'] . "'>" . $uname . '</a><br>';
            }

            echo !empty($results[$i]['time']) ? ' (' . formatTimestamp((int)$results[$i]['time']) . ')' : '';

            echo '</small>';

            echo '</td></tr></table><table>';
        }

        echo '
        <table>
          <tr>
        ';

        $search_url = XOOPS_URL . "/modules/$mydirname/search.php?query=" . urlencode(stripslashes(implode(' ', $queries)));

        $search_url .= "&mid=$mid&action=$action&andor=$andor";

        if ('showallbyuser' == $action) {
            $search_url .= "&uid=$uid";
        }

        if ($start > 0) {
            $prev = $start - 20;

            echo '<td align="left">
            ';

            $search_url_prev = $search_url . "&start=$prev";

            echo '<a href="' . htmlspecialchars($search_url_prev, ENT_QUOTES | ENT_HTML5) . '">' . _SR_PREVIOUS . '</a></td>
            ';
        }

        echo '<td>&nbsp;&nbsp;</td>
        ';

        if (false !== $has_next) {
            $next = $start + 20;

            $search_url_next = $search_url . "&start=$next";

            echo '<td align="right"><a href="' . htmlspecialchars($search_url_next, ENT_QUOTES | ENT_HTML5) . '">' . _SR_NEXT . '</a></td>
            ';
        }

        echo '
          </tr>
        </table>
        <p>
        ';
    } else {
        echo '<p>' . _SR_NOMATCH . '</p>';
    }
    include 'include/searchform.php';
    $search_form->display();
    echo '</p>
    ';
    break;
}
require XOOPS_ROOT_PATH . '/footer.php';
