<?php
//-------------------------------------------------------------------------- //
//  Tutorials Version 2.1 User Submit Functions 	                         //
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
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
$myts = MyTextSanitizer::getInstance();
$eh = new ErrorHandler();
$mytree = new XoopsTree($xoopsDB->prefix('tutorials_categorys'), 'cid', 'scid');
define('IMAGE_PATH', XOOPS_ROOT_PATH . '/modules/tutorials/images');
define('IMAGE_URL', XOOPS_URL . '/modules/tutorials/images');

if (!$xoopsUser) {
    redirect_header(XOOPS_URL . '/user.php', 2, _MD_MUSTREGFIRST);

    exit();
}
// -----------------------------------------------------------------------------------------------------------//
function Tutorials()
{
    global $xoopsDB, $xoopsConfig, $xoopsTheme, $mytree, $useruploads, $xoopsUser, $xoopsLogger, $xoopsMF;

    require XOOPS_ROOT_PATH . '/header.php';

    OpenTable();

    $result = $xoopsDB->query('select count(*) from ' . $xoopsDB->prefix('tutorials_categorys') . '');

    [$numrows] = $xoopsDB->fetchRow($result);

    if ($numrows > 0) {
        // Add new Tutorial ------------------//

        echo '<center><form method=post action=submit.php><h4>' . _MD_TUTORIAL . '</h4><br>';

        echo _MD_CHOICECATEGORY . '<br><br>';

        echo _MD_INCATEGORY;

        $mytree->makeMySelBox('cname', 'cname');

        if (1 == $useruploads) {
            echo '<input type=hidden name=op value=questForPics>';
        } else {
            echo '<input type=hidden name=op value=addTutorial>';
        }

        echo '<input type=submit value=' . _MD_GO . ">\n";

        echo '</form></center>';
    } else {
        echo _MD_NOCATEGORY;
    }

    CloseTable();

    require XOOPS_ROOT_PATH . '/footer.php';
}

// -----------------------------------------------------------------------------------------------------------//
function questForPics()
{
    global $_POST, $xoopsConfig, $xoopsTheme, $xoopsUser, $xoopsLogger, $xoopsMF;

    require XOOPS_ROOT_PATH . '/header.php';

    $cid = $_POST['cid'];

    OpenTable();

    echo '<p align="center"><b>' . _MD_QUESTPIC . '</b></p>';

    echo '<table align="center"><tr><td>';

    echo TextForm("submit.php?op=addTutorial&createdir=1&cid=$cid", _MD_YES);

    echo '</td><td>';

    echo TextForm("submit.php?op=addTutorial&createdir=0&cid=$cid", _MD_NO);

    echo '</td></tr></table>';

    CloseTable();

    require XOOPS_ROOT_PATH . '/footer.php';
}

// -----------------------------------------------------------------------------------------------------------//
function addTutorial()
{
    global $xoopsDB, $xoopsConfig, $xoopsUser, $xoopsLogger, $xoopsMF, $_GET, $_POST, $myts, $xoopsTheme, $useruploads, $imgwidth, $imgheight;

    require XOOPS_ROOT_PATH . '/header.php';

    if (1 == $useruploads) {
        $cid = $_GET['cid'];

        $createdir = $_GET['createdir'];
    } else {
        $cid = $_POST['cid'];

        $createdir = 0;
    }

    // Add new Tutorial ------------------//

    $result = $xoopsDB->query('select scid, cname, cdesc, cimg from ' . $xoopsDB->prefix('tutorials_categorys') . " where cid=$cid");

    [$scid, $cname, $cdesc, $cimg] = $xoopsDB->fetchRow($result);

    $cname = htmlspecialchars($cname, ENT_QUOTES | ENT_HTML5);

    $cdesc = $myts->displayTarea($cdesc);

    OpenTable();

    $time = time();

    $dir = $time;

    echo '<h4>' . _MD_ADDTUTORIAL . '</h4><hr>';

    if (file_exists(IMAGE_PATH . "/$cid")) {
        $imgdirexists = 1;

        if (1 == $createdir) {
            $path = IMAGE_PATH . "/$cid/$dir";

            if (false === mkdir($path, 0777)) {
                echo '<p align="center"><h4><font color="red"><b>' . _MD_ERRCREATEDIR . '</b></font></h4></p>';

                $imgsubdirexists = 0;
            } else {
                #    			echo "<p align=\"center\"><h5><font color=\"red\"><b>"._MD_DIRCREATED."</b></font></h4></p>";

                $imgsubdirexists = 1;
            }
        }
    } else {
        $imgdirexists = 0;

        $imgsubdirexists = 0;
    }

    $status = 0;

    $img_path = "$cid/";

    $img_path2 = "$cid/$dir/";

    $scriptname = 'submit.php';

    $hits = 0;

    $rating = 0;

    $votes = 0;

    $framebrowse = 0;

    $submitter = $xoopsUser->uid();

    $tauthor = XoopsUser::getUnameFromId($submitter);

    require_once XOOPS_ROOT_PATH . '/modules/tutorials/include/form.php';

    CloseTable();

    require XOOPS_ROOT_PATH . '/footer.php';
}

// -----------------------------------------------------------------------------------------------------------//
function PreviewTutorial()
{
    global $xoopsDB, $xoopsConfig, $xoopsTheme, $_POST, $myts, $mytree, $content_visdefault, $content_default, $content_visualize, $imgwidth, $imgheight, $useruploads, $xoopsUser, $xoopsLogger, $xoopsMF;

    require XOOPS_ROOT_PATH . '/header.php';

    if ($_POST['tid']) {
        $tid = $_POST['tid'];
    }

    $cid = $_POST['cid'];

    $gid = $_POST['gid'];

    $tname = $_POST['tname'];

    $tauthor = $_POST['tauthor'];

    $submitter = $_POST['submitter'];

    $timg = $_POST['timg'];

    $tdesc = $_POST['tdesc'];

    $tlink = $_POST['tlink'];

    $dir = $_POST['dir'];

    $time = $_POST['time'];

    $status = $_POST['status'];

    $hits = $_POST['hits'];

    $rating = $_POST['rating'];

    $votes = $_POST['votes'];

    $framebrowse = $_POST['framebrowse'];

    $timgwidth = $_POST['timgwidth'];

    $timgheight = $_POST['timgheight'];

    if (1 == $_POST['maketdir']) {
        if (file_exists(IMAGE_PATH . "/$cid")) {
            $imgdirexists = 1;

            $path = IMAGE_PATH . "/$cid/$dir";

            if (false === mkdir($path, 0777)) {
                echo '<p align="center"><h4><font color="red"><b>' . _MD_ERRCREATEDIR . '</b></font></h4></p>';

                $imgsubdirexists = 0;
            } else {
                echo '<p align="center"><h5><font color="red"><b>' . _MD_DIRCREATED . '</b></font></h4></p>';

                $imgsubdirexists = 1;
            }
        } else {
            $imgdirexists = 0;

            $imgsubdirexists = 0;
        }
    }

    if (preg_match('http://', $timg)) {
        $timgwidth = '';

        $timgheight = '';
    }

    // ShowPreview ---------------------//

    if ('' == $tlink) {
        if (!empty($_POST['xsmiley']) && !empty($_POST['xhtml'])) {
            $content = $myts->previewTarea($_POST['tcont'], 0, 0, 1);

            $tcont = htmlspecialchars($_POST['tcont'], 0, 0, 1);
        } elseif (!empty($_POST['xhtml'])) {
            $content = $myts->previewTarea($_POST['tcont'], 0, 1, 1);

            $tcont = htmlspecialchars($_POST['tcont'], 0, 1, 1);
        } elseif (!empty($_POST['xsmiley'])) {
            $content = $myts->previewTarea($_POST['tcont'], 1, 0, 1);

            $tcont = htmlspecialchars($_POST['tcont'], 1, 0, 1);
        } else {
            $content = $myts->previewTarea($_POST['tcont'], 1, 1, 1);

            $tcont = htmlspecialchars($_POST['tcont'], 1, 1, 1);
        }

        $content = str_replace('_IMGURL_', IMAGE_URL, $content);

        $content = str_replace('[pagebreak]', '<table width=100%><tr><td width=10%>' . _MD_NEXTPAGE . '</td><td width=90%><hr></td></tr></table>', $content);

        $date = formatTimestamp($time, 'm');

        if (function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc()) {
            $content = stripslashes($content);
        }

        OpenTable();

        echo '<h4>Preview</h4>';

        if (1 == $content_visdefault) {
            $preview = $content_visualize;
        } else {
            $preview = $content_default;
        }

        if ('' != $timg) {
            if (preg_match('http://', $timg)) {
                $imgpath = $timg;
            } else {
                $imgpath = '' . IMAGE_URL . "/$timg";
            }

            if ($timgwidth > 0 && $timgheight > 0) {
                $setsize = 'width=' . $timgwidth . ' height=' . $timgheight;
            } else {
                $setsize = '';
            }

            $preview = str_replace('[image]', '<img src="' . $imgpath . '" name=timage ' . $setsize . ' border=1>', $preview);
        } else {
            $preview = str_replace('[image]', '', $preview);
        }

        $preview = str_replace('[title]', "<h4>$tname</h4>", $preview);

        $preview = str_replace('[content]', $content, $preview);

        $preview = str_replace('[author]', sprintf(_MD_WRITTENBY, $tauthor), $preview);

        $preview = str_replace('[hits]', '0' . _MD_HITS, $preview);

        $preview = str_replace('[rating]', _MD_RATING . ': 0', $preview);

        $preview = str_replace('[votes]', '0' . _MD_VOTES, $preview);

        $preview = str_replace('[date]', (string)$date, $preview);

        echo (string)$preview;
    } else {
        OpenTable();

        if (1 == $framebrowse) {
            echo '<iframe SRC="' . $tlink . '" WIDTH="100%"  HEIGHT="1200"  FRAMESPACING=0 FRAMEBORDER=no  BORDER=0 SCROLLING=auto></iframe>';
        } else {
            $content = $tlink;

            echo '<center>' . _MD_EXTLINK . "$tlink&nbsp;->&nbsp;<a href=\"$tlink\" target=\"_blank\"><b>" . _MD_SHOWLINK . '</b></a></center><hr>';
        }
    }

    // ShowForm ----------------------//

    $xsmiley = (int)$_POST['xsmiley'];

    $xhtml = (int)$_POST['xhtml'];

    $tdesc = htmlspecialchars($tdesc, ENT_QUOTES | ENT_HTML5);

    $result = $xoopsDB->query('select scid, cname, cdesc, cimg from ' . $xoopsDB->prefix('tutorials_categorys') . " where cid=$cid");

    [$scid, $cname, $cdesc, $cimg] = $xoopsDB->fetchRow($result);

    $cname = htmlspecialchars($cname, ENT_QUOTES | ENT_HTML5);

    $cdesc = $myts->displayTarea($cdesc);

    if (file_exists(IMAGE_PATH . "/$cid")) {
        $imgdirexists = 1;
    } else {
        $imgdirexists = 0;
    }

    if (file_exists(IMAGE_PATH . "/$cid/$dir")) {
        $imgsubdirexists = 1;
    } else {
        $imgsubdirexists = 0;
    }

    $img_path = "$cid/";

    $img_path2 = "$cid/$dir/";

    $scriptname = 'submit.php';

    require XOOPS_ROOT_PATH . '/modules/tutorials/include/form.php';

    CloseTable();

    require XOOPS_ROOT_PATH . '/footer.php';
}

// -----------------------------------------------------------------------------------------------------------//
function SaveTutorial()
{
    global $xoopsDB, $xoopsConfig, $_POST, $myts, $eh, $xoopsUser, $xoopsLogger, $xoopsMF;

    if ($_POST['tid']) {
        $tid = $_POST['tid'];
    } else {
        $tid = 0;
    }

    $cid = $_POST['cid'];

    $smiley = (int)$_POST['xsmiley'];

    $html = (int)$_POST['xhtml'];

    $status = $_POST['status'];

    $dir = $_POST['dir'];

    $time = $_POST['time'];

    $hits = $_POST['hits'];

    $rating = $_POST['rating'];

    $votes = $_POST['votes'];

    $framebrowse = $_POST['framebrowse'];

    if ((0 == $html) && (0 == $smiley)) {
        $codes = 0;
    } elseif ((1 == $html) && (0 == $smiley)) {
        $codes = 1;
    } elseif ((0 == $html) && (1 == $smiley)) {
        $codes = 2;
    } else {
        $codes = 3;
    }

    if (1 == $framebrowse) {
        $codes += 10;
    }

    if ($_POST['gid']) {
        $gid = $_POST['gid'];
    } else {
        $gid = 0;
    }

    $tname = $myts->addSlashes($_POST['tname']);

    $tauthor = $myts->addSlashes($_POST['tauthor']);

    $timg = $myts->addSlashes($_POST['timg']);

    if (preg_match('http://', $timg) || '' == $timg) {
        $timgwidth = 0;

        $timgheight = 0;
    } else {
        $timgwidth = $_POST['timgwidth'];

        $timgheight = $_POST['timgheight'];
    }

    $tdesc = $myts->addSlashes($_POST['tdesc']);

    $tcont = $myts->addSlashes($_POST['tcont'], $html, $smiley, 1);

    $submitter = $_POST['submitter'];

    $message = '';

    if (!empty($_POST['tlink'])) {
        $tlink = $myts->addSlashes($_POST['tlink']);
    }

    // Check if Title exist

    if ('' == $tname) {
        $message .= '<h4><font color="#ff0000">';

        $message .= _MD_ERRORNAME . '</font></h4><br>';

        $error = 1;
    }

    // Check if Description exist

    if ('' == $tdesc) {
        $message .= '<h4><font color="#ff0000">';

        $message .= _MD_ERRORDESC . '</font></h4><br>';

        $error = 1;
    }

    // Check if Content exist

    if (('' == $tcont) && ('' == $tlink)) {
        $message .= '<h4><font color="#ff0000">';

        $message .= _MD_ERRORCONT . '</font></h4><br>';

        $error = 1;
    }

    if (1 == $error) {
        require XOOPS_ROOT_PATH . '/header.php';

        OpenTable();

        echo $message;

        echo '<center><input type="button" value="' . _MD_GOBACK . '" onclick="javascript:history.go(-1)"></center>';

        CloseTable();

        require XOOPS_ROOT_PATH . '/footer.php';

        exit();
    }

    if (0 == $tid) {
        $newid = $xoopsDB->genId('tutorials_tid_seq');

        $xoopsDB->query(
            'INSERT INTO '
            . $xoopsDB->prefix('tutorials')
            . " (tid, cid, gid, tname, tdesc, timg, tcont, tlink, tauthor, status, codes, hits, rating, votes, date, submitter, dir, timgwidth, timgheight) VALUES ($newid, $cid, $gid, '$tname', '$tdesc', '$timg', '$tcont', '$tlink', '$tauthor', $status, $codes, 0, 0, 0, $time, $submitter, $dir, $timgwidth, $timgheight)"
        ) or $eh::show('0013');
    } elseif (0 == $status) {
        $xoopsDB->query(
            'UPDATE '
            . $xoopsDB->prefix('tutorials')
            . " set tid=$tid, cid=$cid, gid=$gid, tname='$tname', tdesc='$tdesc', timg='$timg', tcont='$tcont', tlink='$tlink', tauthor='$tauthor', status=$status, codes=$codes, hits=$hits, rating=$rating, votes=$votes, date=$time, timgwidth=$timgwidth, timgheight=$timgheight where tid=$tid"
        ) or $eh::show('0013');
    } elseif ($tid > 0 && $status >= 1) {
        $time = time();

        $xoopsDB->query(
            'UPDATE '
            . $xoopsDB->prefix('tutorials')
            . " set tid=$tid, cid=$cid, gid=$gid, tname='$tname', tdesc='$tdesc', timg='$timg', tcont='$tcont', tlink='$tlink', tauthor='$tauthor', status=$status, codes=$codes, hits=$hits, rating=$rating, votes=$votes, date=$time, timgwidth=$timgwidth, timgheight=$timgheight where tid=$tid"
        ) or $eh::show('0013');
    }

    redirect_header('index.php', 1, _MD_RECEIVED . '<br>' . _MD_WHENAPPROVED . '');
}

// -----------------------------------------------------------------------------------------------------------//
function editTutorial()
{
    global $xoopsDB, $xoopsConfig, $_GET, $myts, $eh, $mytree, $useruploads, $imgwidth, $imgheight, $xoopsUser, $xoopsLogger, $xoopsMF;

    require XOOPS_ROOT_PATH . '/header.php';

    $tid = $_GET['tid'];

    $result = $xoopsDB->query('select tid, cid, gid, tname,tdesc, timg, tcont, tlink, tauthor, status, codes, hits, rating, votes, date, submitter, dir, timgwidth, timgheight from ' . $xoopsDB->prefix('tutorials') . " where tid=$tid");

    [$tid, $cid, $gid, $tname, $tdesc, $timg, $tcont, $tlink, $tauthor, $status, $codes, $hits, $rating, $votes, $time, $submitter, $dir, $timgwidth, $timgheight] = $xoopsDB->fetchRow($result);

    if ($codes >= 10) {
        $codes -= 10;

        $framebrowse = 1;
    } else {
        $framebrowse = 0;
    }

    if (0 == $codes) {
        $xhtml = 0;

        $xsmiley = 0;
    } elseif (1 == $codes) {
        $xhtml = 1;

        $xsmiley = 0;
    } elseif (2 == $codes) {
        $xhtml = 0;

        $xsmiley = 1;
    } else {
        $xhtml = 1;

        $xsmiley = 1;
    }

    $tname = htmlspecialchars($tname, ENT_QUOTES | ENT_HTML5);

    $tauthor = htmlspecialchars($tauthor, ENT_QUOTES | ENT_HTML5);

    $timg = htmlspecialchars($timg, ENT_QUOTES | ENT_HTML5);

    $tdesc = htmlspecialchars($tdesc, ENT_QUOTES | ENT_HTML5);

    $tlink = htmlspecialchars($tlink, ENT_QUOTES | ENT_HTML5);

    $tcont = htmlspecialchars($tcont, $html, $smiley, 1);

    $result = $xoopsDB->query('select scid, cname, cdesc, cimg from ' . $xoopsDB->prefix('tutorials_categorys') . " where cid=$cid");

    [$scid, $cname, $cdesc, $cimg] = $xoopsDB->fetchRow($result);

    $cname = htmlspecialchars($cname, ENT_QUOTES | ENT_HTML5);

    $cdesc = $myts->displayTarea($cdesc);

    OpenTable();

    echo '<h4>' . _MD_EDITTUTORIAL . '</h4><hr>';

    if (file_exists(IMAGE_PATH . "/$cid")) {
        $imgdirexists = 1;
    } else {
        $imgdirexists = 0;
    }

    if (file_exists(IMAGE_PATH . "/$cid/$dir")) {
        $imgsubdirexists = 1;
    } else {
        $imgsubdirexists = 0;
    }

    $status = 2;

    $img_path = "$cid/";

    $img_path2 = "$cid/$dir/";

    $scriptname = 'submit.php';

    require XOOPS_ROOT_PATH . '/modules/tutorials/include/form.php';

    CloseTable();

    require XOOPS_ROOT_PATH . '/footer.php';
}

// -----------------------------------------------------------------------------------------------------------//

switch ($op) {
    default:
        Tutorials();
        break;
    case 'questForPics':
        questForPics();
        break;
    case 'addTutorial':
        addTutorial();
        break;
    case 'PreviewTutorial':
        PreviewTutorial();
        break;
    case 'SaveTutorial':
        SaveTutorial();
        break;
    case 'editTutorial':
        editTutorial();
        break;
}
