<?php
//-------------------------------------------------------------------------- //
//  Tutorials Version 2.1 listtutorials.php			                         //
//												                             //
//	Author: Thomas (Todi) Wolf					                             //
//	Mail:	todi@dark-side.de					                             //
//	Homepage: http://www.mytutorials.info		                             //
//												                             //
//	for Xoops   								                             //
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
$xoopsOption['show_rblock'] = 1;
require XOOPS_ROOT_PATH . '/header.php';
$cid = $_GET['cid'];
$xcid = $cid;
OpenTable();
$result = $xoopsDB->query('select scid, cname, cimg, cimgwidth, cimgheight from ' . $xoopsDB->prefix('tutorials_categorys') . " where cid=$cid");
[$scid, $cname, $cimg, $cimgwidth, $cimgheight] = $xoopsDB->fetchRow($result);
$cname = htmlspecialchars($cname, ENT_QUOTES | ENT_HTML5);
$cimg = htmlspecialchars($cimg, ENT_QUOTES | ENT_HTML5);
echo '<center>';
if ('' != $cimg) {
    if (preg_match('http://', $cimg)) {
        $imgpath = $cimg;
    } else {
        $imgpath = '' . IMAGE_URL . "/$cimg";
    }

    echo '<img src="' . $imgpath . '" border="1"><br><br>';
}
printf(_MD_THISISCATEGORY, $cname);
echo '<br><br><br>';
// List all Subcaregorys //
$result = $xoopsDB->query('select cid, cname, cdesc, cimg from ' . $xoopsDB->prefix('tutorials_categorys') . " where scid=$cid order by cname");
if (1 == $columnset) {
    echo '<table width=80% cellspacing=4 cellpadding=0 border=0 valign=top><tr>';
} else {
    echo '<table width=100% cellspacing=4 cellpadding=0 border=0 valign=top><tr>';
}
$subcats = $xoopsDB->getRowsNum($result);
while (list($cid, $cname, $cdesc, $cimg) = $xoopsDB->fetchRow($result)) {
    $cname = htmlspecialchars($cname, ENT_QUOTES | ENT_HTML5);

    $cdesc = htmlspecialchars($cdesc, ENT_QUOTES | ENT_HTML5);

    $cimg = htmlspecialchars($cimg, ENT_QUOTES | ENT_HTML5);

    $result2 = $xoopsDB->query('select tid from ' . $xoopsDB->prefix(tutorials) . " where cid=$cid and status=1 or status=3");

    $number = $xoopsDB->getRowsNum($result2);

    if (1 == $category_visdefault) {
        $show = $category_visualize;
    } else {
        $show = $category_default;
    }

    $arr = [];

    $arr = $mytree->getFirstChild($cid, 'cname');

    $space = 0;

    $chcount = 0;

    $subcat = '';

    foreach ($arr as $ele) {
        $chtitle = htmlspecialchars($ele['cname'], ENT_QUOTES | ENT_HTML5);

        if ($chcount > $maxsubcatshow) {
            $subcat .= '...';

            break;
        }

        if ($space > 0) {
            $subcat .= ', ';
        }

        $subcat .= '<a href="' . XOOPS_URL . '/modules/tutorials/listtutorials.php?cid=' . $ele['cid'] . '">' . $chtitle . '</a>';

        $space++;

        $chcount++;
    }

    if ('' != $cimg) {
        if (preg_match('http://', $cimg)) {
            $imgpath = $cimg;
        } else {
            $imgpath = '' . IMAGE_URL . "/$cimg";
        }

        if ($cimgwidth > 0 && $cimgheight > 0) {
            $setsize = 'width=' . $cimgwidth . ' height=' . $cimgheight;
        } else {
            $setsize = '';
        }

        $show = str_replace('[image]', "<a href=listtutorials.php?cid=$cid><img src=\"" . $imgpath . '" ' . $setsize . " border=1 alt=\"$cname\"></a>", $show);

        $show = str_replace('[image left]', "<a href=listtutorials.php?cid=$cid><img src=\"" . $imgpath . '" ' . $setsize . " border=1 alt=\"$cname\" align=\"left\"></a>", $show);

        $show = str_replace('[image right]', "<a href=listtutorials.php?cid=$cid><img src=\"" . $imgpath . '" ' . $setsize . " border=1 alt=\"$cname\" align=\"right\"></a>", $show);
    } else {
        $show = str_replace('[image]', '', $show);
    }

    $show = str_replace('[title]', "<a href=\"listtutorials.php?cid=$cid\">" . $cname . '</a>', $show);

    $show = str_replace('[subcat]', (string)$subcat, $show);

    $show = str_replace('[description]', (string)$cdesc, $show);

    if (1 == $number) {
        $count = '&nbsp;<small>(' . sprintf(_MD_TUTORIALCOUNTONE, $number) . ')</small>';
    } else {
        $count = '&nbsp;<small>(' . sprintf(_MD_TUTORIALCOUNTMORE, $number) . ')</small>';
    }

    $show = str_replace('[count]', (string)$count, $show);

    $show = str_replace('[link]', "<a href=listtutorials.php?cid=$cid>" . _MD_LETSGO . '</a>', $show);

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
if (1 == $colcount && 2 == $columnset) {
    echo '<td></td>';
}
echo '</tr></table></center>';
if (0 != $subcats) {
    echo '<hr>';
}
//-----------------------------------------------------//
$gid = 0;
echo '<center>';
require XOOPS_ROOT_PATH . '/modules/tutorials/include/showlist.php';
echo '</center>';
$result3 = $xoopsDB->query('select gid, cid, pos, gname from ' . $xoopsDB->prefix('tutorials_groups') . " where cid=$xcid order by pos");
while (list($gid, $cid, $pos, $gname) = $xoopsDB->fetchRow($result3)) {
    $gname = htmlspecialchars($gname, ENT_QUOTES | ENT_HTML5);

    echo '<font size="3"><b>' . $gname . '</b></font><hr>';

    echo '<center>';

    require XOOPS_ROOT_PATH . '/modules/tutorials/include/showlist.php';

    echo '</center>';
}
if ($scid > 0) {
    $result = $xoopsDB->query('select cid, cname from ' . $xoopsDB->prefix('tutorials_categorys') . " where cid=$scid");

    [$cid, $cname] = $xoopsDB->fetchRow($result);

    $cname = htmlspecialchars($cname, ENT_QUOTES | ENT_HTML5);

    $back = "<a href=listtutorials.php?cid=$cid>" . sprintf(_MD_BACK2CATEGORY, $cname) . '</a> | ';
} else {
    $back = '';
}
echo '<br><br><br><center>[ ' . $back . '<a href=index.php>' . _MD_RETURN2INDEX . '</a> ]</center>';
CloseTable();
require XOOPS_ROOT_PATH . '/footer.php';
