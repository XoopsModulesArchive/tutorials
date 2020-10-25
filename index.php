<?php
//-------------------------------------------------------------------------- //
//  Tutorials Version 2.1 index.php      			                         //
//												                             //
//	Author: Thomas (Todi) Wolf					                             //
//	Mail:	todi@dark-side.de					                             //
//	Homepage: http://www.mytutorials.info		                             //
//												                             //
//	for Xoops								                                 //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------- //

include 'header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
$mytree = new XoopsTree($xoopsDB->prefix('tutorials_categorys'), 'cid', 'scid');
$myts = MyTextSanitizer::getInstance(); // MyTextSanitizer object
define('IMAGE_PATH', XOOPS_ROOT_PATH . '/modules/tutorials/images');
define('IMAGE_URL', XOOPS_URL . '/modules/tutorials/images');
// -----------------------------------------------------------------------------------------------------------//
$colcount = 0;
if ('tutorials' == $xoopsConfig['startpage']) {
    $xoopsOption['show_rblock'] = 1;

    require XOOPS_ROOT_PATH . '/header.php';

    make_cblock();
} else {
    $xoopsOption['show_rblock'] = 0;

    require XOOPS_ROOT_PATH . '/header.php';
}
OpenTable();
$result = $xoopsDB->query('select cid, cname, cdesc, cimg, cimgwidth, cimgheight from ' . $xoopsDB->prefix('tutorials_categorys') . ' where scid=0 order by cname');
$catcount = $xoopsDB->getRowsNum($result);
echo '<center><img src="images/tutorials.gif" border="0" Alt="Tutorials"><br><br>';
if ('' == $heading) {
    printf(_MD_WELCOMETOTUTS, $xoopsConfig['sitename']);
} else {
    echo $heading;
}
echo '<br><br>';
echo '<table width=80% cellspacing=4 cellpadding=0 border=0 valign=top><tr>';
while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
    $cname = htmlspecialchars($myrow['cname'], ENT_QUOTES | ENT_HTML5);

    $cdesc = htmlspecialchars($myrow['cdesc'], ENT_QUOTES | ENT_HTML5);

    $cimg = htmlspecialchars($myrow['cimg'], ENT_QUOTES | ENT_HTML5);

    $number = getTotalItems($myrow['cid'], 1, 3);

    if (1 == $category_visdefault) {
        $show = $category_visualize;
    } else {
        $show = $category_default;
    }

    $arr = [];

    $arr = $mytree->getFirstChild($myrow['cid'], 'cname');

    $space = 0;

    $chcount = 0;

    $subcats = '';

    foreach ($arr as $ele) {
        $chtitle = htmlspecialchars($ele['cname'], ENT_QUOTES | ENT_HTML5);

        if ($chcount > $maxsubcatshow) {
            $subcats .= '...';

            break;
        }

        if ($space > 0) {
            $subcats .= ', ';
        }

        $subcats .= '<a href="' . XOOPS_URL . '/modules/tutorials/listtutorials.php?cid=' . $ele['cid'] . '">' . $chtitle . '</a>';

        $space++;

        $chcount++;
    }

    if ('' != $cimg) {
        if (preg_match('http://', $cimg)) {
            $imgpath = $cimg;
        } else {
            $imgpath = '' . IMAGE_URL . "/$cimg";
        }

        if ($myrow['cimgwidth'] > 0 && $myrow['cimgheight'] > 0) {
            $setsize = 'width=' . $myrow['cimgwidth'] . ' height=' . $myrow['cimgheight'];
        } else {
            $setsize = '';
        }

        $show = str_replace('[image]', '<a href=listtutorials.php?cid=' . $myrow['cid'] . '><img src="' . $imgpath . '" ' . $setsize . " border=1 alt=\"$cname\"></a>", $show);

        $show = str_replace('[image right]', '<a href=listtutorials.php?cid=' . $myrow['cid'] . '><img src="' . $imgpath . '" ' . $setsize . " border=1 alt=\"$cname\" align=\"right\"></a>", $show);

        $show = str_replace('[image left]', '<a href=listtutorials.php?cid=' . $myrow['cid'] . '><img src="' . $imgpath . '" ' . $setsize . " border=1 alt=\"$cname\" align=\"left\"></a>", $show);
    } else {
        $show = str_replace('[image]', '', $show);
    }

    $show = str_replace('[title]', '<a href=listtutorials.php?cid=' . $myrow['cid'] . '>' . $cname . '</a>', $show);

    $show = str_replace('[subcat]', (string)$subcats, $show);

    $show = str_replace('[description]', (string)$cdesc, $show);

    if (1 == $number) {
        $count = '&nbsp;<small>(' . sprintf(_MD_TUTORIALCOUNTONE, $number) . ')</small>';
    } else {
        $count = '&nbsp;<small>(' . sprintf(_MD_TUTORIALCOUNTMORE, $number) . ')</small>';
    }

    $show = str_replace('[count]', (string)$count, $show);

    $show = str_replace('[link]', '<a href=listtutorials.php?cid=' . $myrow['cid'] . '>' . _MD_LETSGO . '</a>', $show);

    if (1 == $columnset) {
        echo '<td>' . $show . '</td>';

        echo '</tr><tr>';
    } else {
        echo '<td width="50%" valign="top">' . $show . '</td>';

        if (2 == ++$colcount) {
            $colcount = 0;

            echo '</tr><tr>';
        }
    }
}
echo '</tr></table></center>';
CloseTable();
require XOOPS_ROOT_PATH . '/footer.php';
