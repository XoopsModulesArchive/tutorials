<?php
//-------------------------------------------------------------------------- //
//  Tutorials Version 2.1 User Functions  			                         //
//												                             //
//	Author: Thomas (Todi) Wolf					                             //
//	Mail:	todi@dark-side.de					                             //
//	Homepage: http://www.mytutorials.info		                             //
//												                             //
//	for Xoops RC3								                             //
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

$xoopsOption['show_rblock'] = 1;
require XOOPS_ROOT_PATH . '/header.php';
$tid = $_GET['tid'];
$page = !empty($_GET['page']) ? trim($_GET['page']) : '';
if ('' != $page) {
    $page = $_GET['page'];
} else {
    $xoopsDB->queryF('update ' . $xoopsDB->prefix('tutorials') . " set hits=hits+1 where tid=$tid ");
}
OpenTable();
$result = $xoopsDB->query('select tid, cid, tname, timg, tcont, tauthor, codes, hits, rating, votes, date, submitter, timgwidth, timgheight from ' . $xoopsDB->prefix('tutorials') . " where tid=$tid ");
[$tid, $cid, $tname, $timg, $tcont, $tauthor, $codes, $hits, $rating, $votes, $date, $submitter, $timgwidth, $timgheight] = $xoopsDB->fetchRow($result);
$tname = htmlspecialchars($tname, ENT_QUOTES | ENT_HTML5);
$tauthor = htmlspecialchars($tauthor, ENT_QUOTES | ENT_HTML5);

if ($codes >= 10) {
    $codes -= 10;

    $framebrowse = 1;
} else {
    $framebrowse = 0;
}
if (0 == $codes) {
    $html = 1;

    $smiley = 1;
} elseif (1 == $codes) {
    $html = 0;

    $smiley = 1;
} elseif (2 == $codes) {
    $html = 1;

    $smiley = 0;
} else {
    $html = 0;

    $smiley = 0;
}

$tcont = $myts->displayTarea($tcont, $html, $smiley, 1);
$tcont = str_replace('_IMGURL_', IMAGE_URL, $tcont);
if (get_magic_quotes_gpc()) {
    $tcont = stripslashes($tcont);
}
$result2 = $xoopsDB->query('select cid, cname from ' . $xoopsDB->prefix('tutorials_categorys') . " where cid=$cid");
[$cid, $cname] = $xoopsDB->fetchRow($result2);
$cname = htmlspecialchars($cname, ENT_QUOTES | ENT_HTML5);

$words = substr_count($tcont, ' ') + 1;
/* Rip the article into pages. Delimiter string is "[pagebreak]"  */
$contentpages = explode('[pagebreak]', $tcont);
$pageno = count($contentpages);
/* Define the current page	*/
if ('' == $page || $page < 1) {
    $page = 1;
}
if ($page > $pageno) {
    $page = $pageno;
}
$arrayelement = (int)$page;
$arrayelement--;
if ($page < $pageno) {
    $next_pagenumber = $page + 1;

    $next_page = "<a href=\"viewtutorial.php?tid=$tid&page=$next_pagenumber\">" . _MD_NEXTPAGE . ' ' . sprintf('(%s/%s)', $next_pagenumber, $pageno) . '</a>';
} else {
    $next_page = '';
}
if ($page > 1) {
    $previous_pagenumber = $page - 1;

    $previous_page = "<a href=\"viewtutorial.php?tid=$tid&page=$previous_pagenumber\">" . _MD_PREVPAGE . ' ' . sprintf('(%s/%s)', $previous_pagenumber, $pageno) . '</a>';
} else {
    $previous_page = '';
}
if (1 == $content_visdefault) {
    $content = $content_visualize;
} else {
    $content = $content_default;
}
if ('' != $timg) {
    if ($timgwidth > 0 && $timgheight > 0) {
        $setsize = 'width=' . $timgwidth . ' height=' . $timgheight;
    } else {
        $setsize = '';
    }

    $content = str_replace('[image]', '<img src="' . IMAGE_URL . "/$timg\" " . $setsize . ' border=1>', $content);

    $content = str_replace('[image left]', '<img src="' . IMAGE_URL . "/$timg\" " . $setsize . ' border=1 align="left">', $content);

    $content = str_replace('[image right]', '<img src="' . IMAGE_URL . "/$timg\" " . $setsize . ' border=1 align="right">', $content);
} else {
    $content = str_replace('[image]', '', $content);
}
$content = str_replace('[title]', (string)$tname, $content);
$content = str_replace('[content]', ($contentpages[$arrayelement]), $content);

if ($tauthor != XoopsUser::getUnameFromId($submitter)) {
    $content = str_replace('[author]', sprintf(_MD_WRITTENBY, $tauthor), $content);
} else {
    $content = str_replace('[author]', sprintf(_MD_WRITTENBY, '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $submitter . '">' . $tauthor . '</a>'), $content);
}
$content = str_replace('[hits]', (string)$hits . _MD_HITS, $content);
$content = str_replace('[rating]', _MD_RATING . ": $rating", $content);
$content = str_replace('[votes]', (string)$votes . _MD_VOTES, $content);
$content = str_replace('[date]', (string)$date, $content);
echo $content;

echo '<br><table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="#eeeeee"><tr>';
if ('' != $previous_page) {
    echo "<td align=\"left\"><font size=\"2\">&nbsp;$previous_page</font></td>";
}
if ('' != $next_page) {
    echo "<td align=\"right\"><font size=\"2\">$next_page&nbsp;</font></td>";
}
echo '</tr></table><br><br><center>';
echo "[ <a href=listtutorials.php?cid=$cid>" . sprintf(_MD_BACK2CATEGORY, $cname) . '</a> | ';
echo '<a href=index.php>' . _MD_RETURN2INDEX . "</a> | <a href=\"printpage.php?tid=$tid\">";
echo '<img src="' . XOOPS_URL . '/modules/tutorials/images/print.gif" border=0 Alt="Print" width=15 height=11"></a>]';
echo '</center>';
CloseTable();

require XOOPS_ROOT_PATH . '/footer.php';
