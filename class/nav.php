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

class PageNav
{
    public $total;

    public $perpage;

    public $current;

    public $url;

    public function __construct($total_items, $items_perpage, $current_start, $start_name = 'start', $extra_arg = '')
    {
        $this->total = (int)$total_items;

        $this->perpage = (int)$items_perpage;

        $this->current = (int)$current_start;

        if ('' != $extra_arg && ('&amp;' != mb_substr($extra_arg, -5) || '&amp;' != mb_substr($extra_arg, -1))) {
            $extra_arg .= '&amp;';
        }

        $this->url = $_SERVER['REQUEST_URI'] . '?' . $extra_arg . trim($start_name) . '=';
    }

    public function renderNav($offset = 4)
    {
        if ($this->total < $this->perpage) {
            return;
        }

        $total_pages = ceil($this->total / $this->perpage);

        if ($total_pages > 1) {
            $ret = '';

            $prev = $this->current - $this->perpage;

            $ret .= '<table width=100% border=0><tr><td height=1 bgcolor="#000000" colspan=3></td></tr><tr>';

            if ($prev >= 0) {
                $ret .= '<td align="left"><a href="' . $this->url . $prev . '">&laquo;&laquo; ' . _ADS_PREV . '</a></td>';
            } else {
                $ret .= '<td align="left"><font color="#C0C0C0">&laquo;&laquo; ' . _ADS_PREV . '</font></td>';
            }

            $ret .= '<td align="center">';

            $counter = 1;

            $current_page = (int)floor(($this->current + $this->perpage) / $this->perpage);

            while ($counter <= $total_pages) {
                if ($counter == $current_page) {
                    $ret .= '<b>' . $counter . '</b> ';
                } elseif (($counter > $current_page - $offset && $counter < $current_page + $offset) || 1 == $counter || $counter == $total_pages) {
                    if ($counter == $total_pages && $current_page < $total_pages - $offset) {
                        $ret .= '... ';
                    }

                    $ret .= '<a href="' . $this->url . (($counter - 1) * $this->perpage) . '">' . $counter . '</a> ';

                    if (1 == $counter && $current_page > 1 + $offset) {
                        $ret .= '... ';
                    }
                }

                $counter++;
            }

            $ret .= '</td>';

            $next = $this->current + $this->perpage;

            if ($this->total > $next) {
                $ret .= '<td align="right"><a href="' . $this->url . $next . '">' . _ADS_NEXT . ' &raquo;&raquo;</a></td>';
            } else {
                $ret .= '<td align="right"><font color="#C0C0C0">' . _ADS_NEXT . ' &raquo;&raquo;</font></td>';
            }

            $ret .= '</tr><tr><td height=1 bgcolor="#000000" colspan=3></td></tr></table>';
        }

        return $ret;
    }
}
