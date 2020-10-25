<?php
//-------------------------------------------------------------------------- //
//  Tutorials Version 2.1 Admin Functions  			                         //
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

include 'admin_header.php';
require XOOPS_ROOT_PATH . '/modules/tutorials/cache/config.php';
require_once XOOPS_ROOT_PATH . '/modules/tutorials/include/functions.php';
require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
$myts = MyTextSanitizer::getInstance();
$eh = new ErrorHandler();
$mytree = new XoopsTree($xoopsDB->prefix('tutorials_categorys'), 'cid', 'scid');
define('IMAGE_PATH', XOOPS_ROOT_PATH . '/modules/tutorials/images');
define('IMAGE_URL', XOOPS_URL . '/modules/tutorials/images');
// -----------------------------------------------------------------------------------------------------------//
function tutorials()
{
    global $xoopsDB, $myts;

    xoops_cp_header();

    OpenTable();

    echo '<h4>' . _MD_TUTORIALSADMIN . '</h4>';

    echo ' - <a href=index.php?op=TutorialsConfigAdmin>' . _MD_GENERALSET . '</a>';

    echo '<br><br>';

    echo ' - <a href=index.php?op=TutorialsConfigMenuC>' . _MD_ADDMODDELETEC . '</a>';

    echo '<br><br>';

    echo ' - <a href=index.php?op=TutorialsConfigMenuG>' . _MD_ADDMODDELETEG . '</a>';

    echo '<br><br>';

    echo ' - <a href=index.php?op=TutorialsConfigMenuT>' . _MD_ADDMODDELETET . '</a>';

    echo '<br><br>';

    $result = $xoopsDB->query('select count(*) from ' . $xoopsDB->prefix('tutorials') . ' where status=0');

    [$numrows] = $xoopsDB->fetchRow($result);

    if ($numrows > 0) {
        echo '<hr><h4>' . _MD_WAITINGTUTORIALS . '</h4><br>';

        echo '<table width=100% border=0 cellpadding=2 cellspacing=2>';

        echo '<th align=left>' . _MD_DATE . '</th><th align=left>' . _MD_TITLE . '</th><th align=left>' . _MD_BYAUTHOR . '</th>';

        $result = $xoopsDB->query('select tid, tname, tauthor, date from ' . $xoopsDB->prefix('tutorials') . ' where status=0 or status=2');

        while (list($tid, $tname, $tauthor, $date) = $xoopsDB->fetchRow($result)) {
            $date = formatTimestamp($date, 'm');

            $tname = htmlspecialchars($tname, ENT_QUOTES | ENT_HTML5);

            $tauthor = htmlspecialchars($tauthor, ENT_QUOTES | ENT_HTML5);

            echo "<tr><td>$date</td><td>$tname</td><td>$tauthor</td><td><a href=\"index.php?op=waitTutorial&tid=$tid\">" . _MD_CHECK . '</a></td></tr>';
        }

        echo '</table>';
    }

    CloseTable();

    xoops_cp_footer();
}

// -----------------------------------------------------------------------------------------------------------//
function TutorialsConfigMenuC()
{
    global $xoopsDB, $xoopsConfig, $mytree;

    xoops_cp_header();

    OpenTable();

    echo '<table width=600 border=0 cellspacing=8 align=center><tr><td nowrap>';

    // Add a New Main Category ----------------//

    echo "<form name=categoryform method=post action=index.php>\n";

    echo '<h4>' . _MD_ADDMAINC . '</h4><br>' . _MD_CNAME . '<br><input type=text name=cname size=30 maxlength=50><br><br>';

    echo '' . _MD_CIMAGE . "<br><input type=\"text\" id=\"cimg\" name=\"cimg\" size=\"50\" maxlength=\"150\" value=\"\">\n";

    if (file_exists(IMAGE_PATH)) {
        $img_path = '';

        echo "<input type='button' value='Upload' onclick='javascript:openWithSelfMain(\"" . XOOPS_URL . "/modules/tutorials/upload.php?img_path=$img_path&target=cimg&logo=1&target2=cimgwidth&target3=cimgheight\",\"upload\",450,450);'>\n";

        echo '<br>' . IMAGE_URL . '/';
    } else {
        echo '<br><font color="#ff0000">' . _MD_DIRNOTEXISTS . '</font>';
    }

    echo "<input type=\"hidden\" name=\"cimgwidth\" size=\"6\" value=\"\">\n";

    echo "<input type=\"hidden\" name=\"cimgheight\" size=\"6\" value=\"\">\n";

    echo '<br><br>';

    echo '' . _MD_CDESCRIPTION . '<br><textarea name=cdesc rows=5 cols=50></textarea><br><br>';

    echo "<input type=hidden name=cid value=0>\n";

    echo '<input type=hidden name=op value=addCat>';

    echo '<input type=submit value=' . _MD_ADD . ">\n";

    echo '&nbsp;<input type="button" value="' . _MD_CANCEL . '" onclick="javascript:history.go(-1)">';

    echo '</form><br><br>';

    // Add a New Sub-Category -----------------//

    $result = $xoopsDB->query('select count(*) from ' . $xoopsDB->prefix('tutorials_categorys') . '');

    [$numrows] = $xoopsDB->fetchRow($result);

    if ($numrows > 0) {
        echo '<form name=subcatform method=post action=index.php>';

        echo '<h4>' . _MD_ADDSUBC . '</h4><br>';

        echo _MD_CNAME . '<br><input type=text name=cname size=30 maxlength=50 value="">&nbsp;' . _MD_IN . '&nbsp;';

        $mytree->makeMySelBox('cname', 'cname');

        echo '<br><br>' . _MD_CIMAGE . "<br><input type=\"text\" id=\"cimg2\" name=\"cimg2\" size=\"50\" maxlength=\"150\">\n";

        if (file_exists(IMAGE_PATH)) {
            $img_path = '';

            echo "<input type='button' value='Upload' onclick='javascript:openWithSelfMain(\"" . XOOPS_URL . "/modules/tutorials/upload.php?img_path=$img_path&target=cimg2&logo=1&target2=cimgwidth2&target3=cimgheight2\",\"upload\",450,450);'>\n";

            echo '<br>' . IMAGE_URL . '/';
        } else {
            echo '<br><font color="#ff0000">' . _MD_DIRNOTEXISTS . '</font>';
        }

        echo "<input type=\"hidden\" name=\"cimgwidth2\" size=\"6\" value=\"\">\n";

        echo "<input type=\"hidden\" name=\"cimgheight2\" size=\"6\" value=\"\">\n";

        echo '<br><br>' . _MD_CDESCRIPTION . '<br><textarea name=cdesc rows=5 cols=50></textarea>';

        echo '<input type=hidden name=op value=addCat><br><br>';

        echo '<input type=submit value=' . _MD_ADD . ">\n";

        echo '&nbsp;<input type="button" value="' . _MD_CANCEL . '" onclick="javascript:history.go(-1)">';

        echo '</form><br><br>';

        // Modify Category -------------------------//

        echo '<center><form method=post action=index.php><h4>' . _MD_MODCAT . '</h4><br>';

        echo _MD_CATEGORYC;

        $mytree->makeMySelBox('cname', 'cname');

        echo "<input type=hidden name=op value=modCat>\n";

        echo '<input type=submit value=' . _MD_MODIFY . ">\n";

        echo '&nbsp;<input type="button" value="' . _MD_CANCEL . '" onclick="javascript:history.go(-1)">';

        echo '</form></center>';

        echo '<br>';
    }

    echo '</td></tr></table>';

    CloseTable();

    xoops_cp_footer();
}

// -----------------------------------------------------------------------------------------------------------//
function addCat()
{
    global $xoopsDB, $_POST, $myts, $eh;

    $scid = $_POST['cid'];

    $cname = $_POST['cname'];

    $cdesc = $_POST['cdesc'];

    if (0 != $scid) {
        if (($_POST['cimg2']) || ('' != $_POST['cimg2'])) {
            $cimg = $myts->addSlashes($_POST['cimg2']);

            $cimgwidth = $_POST['cimgwidth2'];

            $cimgheight = $_POST['cimgheight2'];
        }
    } else {
        if (($_POST['cimg']) || ('' != $_POST['cimg'])) {
            $cimg = $myts->addSlashes($_POST['cimg']);

            $cimgwidth = $_POST['cimgwidth'];

            $cimgheight = $_POST['cimgheight'];
        }
    }

    if ('' == $cimgwidth) {
        $cimgwidth = 0;
    }

    if ('' == $cimgheight) {
        $cimgheight = 0;
    }

    $cname = $myts->addSlashes($cname);

    $cdesc = $myts->addSlashes($cdesc);

    #	$newid = $xoopsDB->genId("tutorials_categorys_cid_seq");

    $newid = $xoopsDB->genId($xoopsDB->prefix('tutorials_categorys') . '_cid_seq');

    $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('tutorials_categorys') . " (cid, scid, cname, cdesc, cimg, cimgwidth, cimgheight) VALUES ($newid, $scid, '$cname', '$cdesc', '$cimg', $cimgwidth, $cimgheight)") or $eh::show('0013');

    $result = $xoopsDB->query('select cid from ' . $xoopsDB->prefix('tutorials_categorys') . " where cname='$cname'");

    [$cid] = $xoopsDB->fetchRow($result);

    $path = IMAGE_PATH . '/' . $cid;

    if (false === mkdir($path, 0777)) {
        $dirmessage = _MD_ERRCREATEDIRC . "<br>$path";

        $xoopsDB->query('delete from ' . $xoopsDB->prefix('tutorials_categorys') . " where cid=$newid") or $eh::show('0013');
    } else {
        $dirmessage = _MD_DIRCREATEDC;
    }

    redirect_header('index.php', 1, _MD_NEWCATADDED . '<br>' . (string)$dirmessage);
}

// -----------------------------------------------------------------------------------------------------------//
function modCat()
{
    global $xoopsDB, $_POST, $myts, $mytree, $imgwidth, $imgheight;

    xoops_cp_header();

    $cid = $_POST['cid'];

    OpenTable();

    echo '<h4>' . _MD_MODCAT . '</h4><br>';

    $result = $xoopsDB->query('select scid, cname, cdesc, cimg, cimgwidth, cimgheight from ' . $xoopsDB->prefix('tutorials_categorys') . " where cid=$cid");

    [$scid, $cname, $cdesc, $cimg, $cimgwidth, $cimgheight] = $xoopsDB->fetchRow($result);

    $cname = htmlspecialchars($cname, ENT_QUOTES | ENT_HTML5);

    $cimg = htmlspecialchars($cimg, ENT_QUOTES | ENT_HTML5);

    $cdesc = $myts->addSlashes($cdesc);

    if (preg_match('http://', $cimg)) {
        $imgpath = $cimg;
    } else {
        $imgpath = '' . IMAGE_URL . "/$cimg";
    }

    $imagesize = getimagesize($imgpath);

    $imagewidth = $imagesize[0];

    $imageheight = $imagesize[1];

    if ('' != $cimg) {
        if (0 == $cimgwidth) {
            $cimgwidth = $imagewidth;
        }

        if (0 == $cimgheight) {
            $cimgheight = $imageheight;
        }
    } ?>
    <!--xoopsCode start-->
    <script type="text/javascript">
        <!--
        <?php
        if ('' != $cimg) {
            ?>
        var backup_cimgwidth = <?php echo $cimgwidth; ?>;
        var backup_cimgheight = <?php echo $cimgheight; ?>;
        var cimgwidthmax = <?php echo $imgwidth; ?>;
        var cimgheightmax = <?php echo $imgheight; ?>;

        function calcPicsize(nr) {
            var faktor = 0;
            var buffer = 0;
            if (nr == 1) {
                for (i = 0; i < document.categoryform.cimgwidth.value.length; ++i)
                    if (document.categoryform.cimgwidth.value.charAt(i) < "0" || document.categoryform.cimgwidth.value.charAt(i) > "9")
                        document.categoryform.cimgwidth.value = backup_cimgwidth;

                if (document.categoryform.cimgwidth.value < 0) {
                    document.categoryform.cimgwidth.value = backup_cimgwidth;
                }
                if (document.categoryform.cimgwidth.value > cimgwidthmax) {
                    document.categoryform.cimgwidth.value = cimgwidthmax;
                }
                faktor = (document.categoryform.cimgwidth.value * 1) / backup_cimgwidth;
                buffer = Math.round(backup_cimgheight * faktor);
                if (buffer > cimgheightmax) {
                    document.categoryform.cimgheight.value = cimgheightmax;
                    faktor = (document.categoryform.cimgheight.value * 1) / backup_cimgheight;
                    document.categoryform.cimgwidth.value = Math.round(backup_cimgwidth * faktor);
                } else {
                    document.categoryform.cimgheight.value = buffer;
                }
            }
            if (nr == 2) {
                for (i = 0; i < document.categoryform.cimgheight.value.length; ++i)
                    if (document.categoryform.cimgheight.value.charAt(i) < "0" || document.categoryform.cimgheight.value.charAt(i) > "9")
                        document.categoryform.cimgheight.value = backup_cimgheight;

                if (document.categoryform.cimgheight.value < 0) {
                    document.categoryform.cimgheight.value = backup_cimgheight;
                }
                if (document.categoryform.cimgheight.value > cimgheightmax) {
                    document.categoryform.cimgheight.value = cimgheightmax;
                }
                faktor = (document.categoryform.cimgheight.value * 1) / backup_cimgheight;
                buffer = Math.round(backup_cimgwidth * faktor);
                if (buffer > cimgwidthmax) {
                    document.categoryform.cimgwidth.value = cimgwidthmax;
                    faktor = (document.categoryform.cimgwidth.value * 1) / backup_cimgwidth;
                    document.categoryform.cimgheight.value = Math.round(backup_cimgheight * faktor);
                } else {
                    document.categoryform.cimgwidth.value = buffer;
                }
            }
            backup_cimgwidth = (document.categoryform.cimgwidth.value * 1);
            backup_cimgheight = (document.categoryform.cimgheight.value * 1);
            document.cimage.width = backup_cimgwidth;
            document.cimage.height = backup_cimgheight;
        }

        function setorgsize() {
            document.categoryform.cimgwidth.value = <?php echo $imagewidth; ?>;
            document.categoryform.cimgheight.value = <?php echo $imageheight; ?>;
            backup_cimgwidth = (document.categoryform.cimgwidth.value * 1);
            backup_cimgheight = (document.categoryform.cimgheight.value * 1);
            document.cimage.width = backup_cimgwidth;
            document.cimage.height = backup_cimgheight;
        }
        <?php
        } ?>
        function previewname() {
            document.getElementsByTagName("b")[0].firstChild.data = document.categoryform.cname.value;
        }

        function previewdesc(cdescvalue) {
            document.getElementsByTagName("div")[0].firstChild.data = cdescvalue;
        }


        //-->
    </script>
    <?php
    echo '<table width=600 border=0 cellspacing=8 align=left';

    echo "<tr><td><fieldset style=\"padding:5px;\"><legend><b>$cname</b></legend><img src=\"" . $imgpath . '" name=cimage width=' . $cimgwidth . ' height=' . $cimgheight . " align=right><div>$cdesc</div><br></fieldset></td></tr></table>";

    echo '<form name=categoryform action=index.php method=post>' . _MD_CNAME . "<br><input type=text name=cname value=\"$cname\" size=50 maxlength=50 onchange='previewname();'><br><br>";

    echo '' . _MD_CIMAGE . "<br><input type=text id=\"cimg\" name=\"cimg\" value=\"$cimg\" size=50 maxlength=150>\n";

    if (file_exists(IMAGE_PATH)) {
        $img_path = '';

        echo "<input type='button' value='Upload' onclick='javascript:openWithSelfMain(\"" . XOOPS_URL . "/modules/tutorials/upload.php?img_path=$img_path&target=cimg&logo=1&target2=cimgwidth&target3=cimgheight\",\"upload\",450,450);'>\n";

        echo '<br>' . IMAGE_URL . '/';
    } else {
        echo '<br><font color="#ff0000">' . _MD_DIRNOTEXISTS . '</font>';
    }

    if ('' != $cimg) {
        echo '<br><br>' . _MD_IMGWIDTH . '&nbsp;<input type="text" id="cimgwidth" name="cimgwidth" size="6" value="';

        if (isset($cimgwidth)) {
            echo $cimgwidth;
        }

        echo "\" onchange='calcPicsize(1);'>\n";

        echo '&nbsp;&nbsp;' . _MD_IMGHEIGHT . '&nbsp;<input type="text" id="cimgheight" name="cimgheight" size="6" value="';

        if (isset($cimgheight)) {
            echo $cimgheight;
        }

        echo "\" onchange='calcPicsize(2);'>\n";

        echo "&nbsp;&nbsp;<input type=button value='Reset' onclick='setorgsize();'>\n";
    }

    echo '<br><br>' . _MD_PARENT . '&nbsp;';

    $catid = $cid;

    echo "<select name='scid'>\n";

    $result = $xoopsDB->query('SELECT cid, cname FROM ' . $xoopsDB->prefix('tutorials_categorys') . ' WHERE scid=0 ORDER BY cname');

    echo "<option value='0'>----</option>\n";

    while (list($cid, $cname) = $xoopsDB->fetchRow($result)) {
        if ($catid != $cid) {
            if ($catid == $cid) {
                $sel = " selected='selected'";
            }

            echo "<option value='$cid'$sel>$cname</option>\n";

            $sel = '';

            $arr = $mytree->getChildTreeArray($cid, 'cname');

            foreach ($arr as $option) {
                if ($catid != $option['cid']) {
                    $option['prefix'] = str_replace('.', '--', $option['prefix']);

                    $catpath = $option['prefix'] . '&nbsp;' . htmlspecialchars($option['cname'], ENT_QUOTES | ENT_HTML5);

                    if ($option['cid'] == $scid) {
                        $sel = " selected='selected'";
                    }

                    echo "<option value='" . $option['cid'] . "'$sel>$catpath</option>\n";
                }

                $sel = '';
            }
        }
    }

    echo "</select>\n";

    echo '<br><input type="hidden" name="cid" value="' . $catid . '">';

    echo '<br><br>' . _MD_CDESCRIPTION . "<br><textarea name=cdesc rows=5 cols=50 onchange='previewdesc(this.value);'>$cdesc</textarea>";

    echo '<input type="hidden" name="op" value="modCatS"><br><br>';

    echo '<input type="submit" value="' . _MD_SAVE . "\">\n";

    echo '<input type="button" value="' . _MD_CLEAR . "\" onClick=\"location='index.php?scid=$scid&cid=$catid&op=delCat'\">\n";

    echo '<input type="button" value="' . _MD_CANCEL . '" onclick="javascript:history.go(-1)">';

    echo '</form>';

    CloseTable();

    xoops_cp_footer();
}

// -----------------------------------------------------------------------------------------------------------//
function modCatS()
{
    global $xoopsDB, $_POST, $myts, $eh;

    $cid = $_POST['cid'];

    $scid = $_POST['scid'];

    $cname = $myts->addSlashes($_POST['cname']);

    $cdesc = $myts->addSlashes($_POST['cdesc']);

    if (($_POST['cimg']) || ('' != $_POST['cimg'])) {
        $cimg = $myts->addSlashes($_POST['cimg']);

        $cimgwidth = $myts->addSlashes($_POST['cimgwidth']);

        $cimgheight = $myts->addSlashes($_POST['cimgheight']);

        $xoopsDB->query('update ' . $xoopsDB->prefix('tutorials_categorys') . " set scid=$scid, cname='$cname', cdesc='$cdesc', cimg='$cimg', cimgwidth=$cimgwidth, cimgheight=$cimgheight where cid=$cid") or $eh::show('0013');
    } else {
        $xoopsDB->query('update ' . $xoopsDB->prefix('tutorials_categorys') . " set scid=$scid, cname='$cname', cdesc='$cdesc' where cid=$cid") or $eh::show('0013');
    }

    redirect_header('index.php', 2, _MD_DBUPDATED);
}

// -----------------------------------------------------------------------------------------------------------//
function delCat()
{
    global $xoopsDB, $_GET, $eh, $mytree;

    $cid = $_GET['cid'];

    if ($_GET['ok']) {
        $ok = $_GET['ok'];
    }

    if (1 == $ok) {
        //get all subcategories under the specified category

        $arr = $mytree->getAllChildId($cid);

        for ($i = 0, $iMax = count($arr); $i < $iMax; $i++) {
            //get all links in each subcategory

            $result = $xoopsDB->query('select tid from ' . $xoopsDB->prefix('tutorials') . ' where cid=' . $arr[$i] . '') or $eh::show('0013');

            //now for each link, delete the text data and vote ata associated with the link

            while (list($tid) = $xoopsDB->fetchRow($result)) {
                $xoopsDB->query('delete from ' . $xoopsDB->prefix('tutorials_votedata') . ' where tid=' . $tid . '') or $eh::show('0013');

                $xoopsDB->query('delete from ' . $xoopsDB->prefix('tutorials') . ' where tid=' . $tid . '') or $eh::show('0013');
            }

            //all links for each subcategory is deleted, now delete the subcategory data

            $xoopsDB->query('delete from ' . $xoopsDB->prefix('tutorials_groups') . ' where cid=' . $cid . '') or $eh::show('0013');

            $xoopsDB->query('delete from ' . $xoopsDB->prefix('tutorials_categorys') . ' where cid=' . $arr[$i] . '') or $eh::show('0013');
        }

        //all subcategory and associated data are deleted, now delete category data and its associated data

        $result = $xoopsDB->query('select tid from ' . $xoopsDB->prefix('tutorials') . ' where cid=' . $cid . '') or $eh::show('0013');

        while (list($tid) = $xoopsDB->fetchRow($result)) {
            $xoopsDB->query('delete from ' . $xoopsDB->prefix('tutorials') . " where tid=$tid") or $eh::show('0013');

            $xoopsDB->query('delete from ' . $xoopsDB->prefix('tutorials_votedata') . ' where tid=' . $tid . '') or $eh::show('0013');
        }

        $xoopsDB->query('delete from ' . $xoopsDB->prefix('tutorials_groups') . ' where cid=' . $cid . '') or $eh::show('0013');

        $xoopsDB->query('delete from ' . $xoopsDB->prefix('tutorials_categorys') . " where cid=$cid") or $eh::show('0013');

        redirect_header('index.php', 2, _MD_CATDELETED);
    } else {
        xoops_cp_header();

        OpenTable();

        echo '<center>';

        echo '<h4><font color="#ff0000">';

        echo _MD_WARNINGC . '</font></h4><br>';

        echo "<table><tr><td>\n";

        echo TextForm("index.php?op=delCat&cid=$cid&ok=1", _MD_YES);

        echo "</td><td>\n";

        echo TextForm('index.php', _MD_NO);

        echo "</td></tr></table>\n";

        CloseTable();

        xoops_cp_footer();
    }
}

// -----------------------------------------------------------------------------------------------------------//
function TutorialsConfigMenuG()
{
    global $xoopsDB, $xoopsConfig, $myts, $eh, $mytree;

    xoops_cp_header();

    // Add a New Group ------------------------//

    OpenTable();

    echo '<table width=600 border=0 cellspacing=8 align=center><tr><td nowrap>';

    echo "<form method=post action=index.php>\n";

    echo '<h4>' . _MD_ADDMAING . '</h4><br>' . _MD_GNAME . '<br><input type=text name=gname size=30 maxlength=50><br><br>';

    echo _MD_CATEGORYC;

    $mytree->makeMySelBox('cname', 'cname');

    echo "<br><br>\n";

    echo '' . _MD_GPOSITION . '<input type=text name=pos value="0" size=5 maxlength=5><br><br>';

    echo "<input type=hidden name=gid value=0>\n";

    echo '<input type=hidden name=op value=addGroup>';

    echo '<input type=submit value=' . _MD_ADD . ">\n";

    echo '<input type="button" value="' . _MD_CANCEL . '" onclick="javascript:history.go(-1)">';

    echo '</form><br><br>';

    // Modify Group ----------------------------//

    $result = $xoopsDB->query('select count(*) from ' . $xoopsDB->prefix('tutorials_groups') . '');

    [$numrows] = $xoopsDB->fetchRow($result);

    if ($numrows > 0) {
        echo '<h4>' . _MD_MODGROUP . '</h4><br>';

        echo '<table width=100% border=0 cellspacing=0 cellpadding=4><tr><td>' . _MD_CATEGORYC . '</td>';

        echo '<td colspan=2>' . _MD_GROUP . '</td></tr>';

        $result2 = $xoopsDB->query('select cid from ' . $xoopsDB->prefix('tutorials_groups') . ' order by cid');

        $cidmerk = 0;

        while (list($cid) = $xoopsDB->fetchRow($result2)) {
            if ($cid != $cidmerk) {
                $result3 = $xoopsDB->query('select cid, cname from ' . $xoopsDB->prefix('tutorials_categorys') . " where cid='$cid'");

                [$cid, $cname] = $xoopsDB->fetchRow($result3);

                $cname = htmlspecialchars($cname, ENT_QUOTES | ENT_HTML5);

                echo "<tr><form method=post action=index.php><td>$cname</td>";

                echo "<td><select name='gid'>";

                $result = $xoopsDB->query('select gid, pos, gname from ' . $xoopsDB->prefix('tutorials_groups') . " where cid='$cid' order by pos");

                while (list($gid, $pos, $gname) = $xoopsDB->fetchRow($result)) {
                    $gname = htmlspecialchars($gname, ENT_QUOTES | ENT_HTML5);

                    echo "<option value='$gid'>$gname</option>";
                }

                echo '</select></td><td>';

                echo "<input type=hidden name=op value=modGroup>\n";

                echo '<input type=submit value=' . _MD_MODIFY . ">\n";

                echo '<input type="button" value="' . _MD_CANCEL . '" onclick="javascript:history.go(-1)">';

                $cidmerk = $cid;

                echo '</td></form></tr>';
            }
        }

        echo "</table><br><br>\n";

        echo '<br>';
    }

    echo '</td></tr></table>';

    CloseTable();

    xoops_cp_footer();
}

// -----------------------------------------------------------------------------------------------------------//
function addGroup()
{
    global $xoopsDB, $_POST, $myts, $eh;

    $gname = $_POST['gname'];

    $cid = $_POST['cid'];

    $pos = $_POST['pos'];

    $gname = $myts->addSlashes($gname);

    $newid = $xoopsDB->genId('tutorials_groups_gid_seq');

    $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('tutorials_groups') . " (gid, cid, pos, gname) VALUES ($newid, $cid, $pos, '$gname')") or $eh::show('0013');

    redirect_header('index.php', 2, _MD_NEWGROUPADDED);
}

// -----------------------------------------------------------------------------------------------------------//
function modGroup()
{
    global $xoopsDB, $_POST, $myts, $mytree;

    xoops_cp_header();

    $gid = $_POST['gid'];

    OpenTable();

    echo '<h4>' . _MD_MODCAT . '</h4><br>';

    $result = $xoopsDB->query('select cid, pos, gname from ' . $xoopsDB->prefix('tutorials_groups') . " where gid=$gid");

    [$cid, $pos, $gname] = $xoopsDB->fetchRow($result);

    $gname = htmlspecialchars($gname, ENT_QUOTES | ENT_HTML5);

    $result2 = $xoopsDB->query('select scid, cname from ' . $xoopsDB->prefix('tutorials_categorys') . " where cid=$cid");

    [$scid, $cname] = $xoopsDB->fetchRow($result2);

    $cname = htmlspecialchars($cname, ENT_QUOTES | ENT_HTML5);

    echo '<form action=index.php method=post>' . _MD_GNAME . "<input type=text name=gname value=\"$gname\" size=50 maxlength=50><br><br>";

    echo '' . _MD_GPOSITION . "<input type=text name=pos value=\"$pos\" size=5 maxlength=5><br><br>";

    echo _MD_PARENT . '&nbsp;';

    $mytree->makeMySelBox('cname', 'cname', (string)$cid, 0, 'cid');

    echo '<br><input type="hidden" name="gid" value="' . $gid . '">';

    echo '<input type="hidden" name="op" value="modGroupS"><br><br>';

    echo '<input type="submit" value="' . _MD_SAVE . "\">\n";

    echo '<input type="button" value="' . _MD_CLEAR . "\" onClick=\"location='index.php?gid=$gid&cid=$cid&op=delGroup'\">\n";

    echo '<input type="button" value="' . _MD_CANCEL . '" onclick="javascript:history.go(-1)">';

    echo '</form>';

    CloseTable();

    xoops_cp_footer();
}

// -----------------------------------------------------------------------------------------------------------//
function modGroupS()
{
    global $xoopsDB, $_POST, $myts, $eh;

    $gid = $_POST['gid'];

    $cid = $_POST['cid'];

    $pos = $_POST['pos'];

    $gname = $myts->addSlashes($_POST['gname']);

    $xoopsDB->query('update ' . $xoopsDB->prefix('tutorials_groups') . " set cid=$cid, pos=$pos, gname='$gname' where gid=$gid") or $eh::show('0013');

    redirect_header('index.php', 2, _MD_DBUPDATED);
}

// -----------------------------------------------------------------------------------------------------------//
function delGroup()
{
    global $xoopsDB, $_GET, $eh, $mytree;

    $gid = $_GET['gid'];

    if ($_GET['ok']) {
        $ok = $_GET['ok'];
    }

    if (1 == $ok) {
        $result = $xoopsDB->query('select tid from ' . $xoopsDB->prefix('tutorials') . ' where gid=' . $gid . '') or $eh::show('0013');

        //now for each link, delete the text data and vote ata associated with the link

        while (list($tid) = $xoopsDB->fetchRow($result)) {
            $xoopsDB->query('delete from ' . $xoopsDB->prefix('tutorials_votedata') . ' where tid=' . $tid . '') or $eh::show('0013');

            $xoopsDB->query('delete from ' . $xoopsDB->prefix('tutorials') . ' where tid=' . $tid . '') or $eh::show('0013');
        }

        //all links for each subcategory is deleted, now delete the subcategory data

        $xoopsDB->query('delete from ' . $xoopsDB->prefix('tutorials_groups') . ' where gid=' . $gid . '') or $eh::show('0013');

        redirect_header('index.php', 2, _MD_GROUPDELETED);
    } elseif (2 == $ok) {
        // delete Group and set GroupID for Tutorials = 0

        $xoopsDB->query('delete from ' . $xoopsDB->prefix('tutorials_groups') . ' where gid=' . $gid . '') or $eh::show('0013');

        $result = $xoopsDB->query('select tid, cid from ' . $xoopsDB->prefix('tutorials') . ' where gid=' . $gid . '') or $eh::show('0013');

        while (list($tid, $cid) = $xoopsDB->fetchRow($result)) {
            $xoopsDB->query('update ' . $xoopsDB->prefix('tutorials') . " set gid=0 where tid=$tid") or $eh::show('0013');
        }

        $xoopsDB->query('delete from ' . $xoopsDB->prefix('tutorials_groups') . ' where gid=' . $gid . '') or $eh::show('0013');

        redirect_header('index.php', 2, _MD_GROUPONLYDELETED);
    } else {
        xoops_cp_header();

        OpenTable();

        echo '<center>';

        echo '<h4><font color="#ff0000">';

        echo _MD_WARNINGG . '</font></h4><br>';

        echo "<table><tr><td>\n";

        echo TextForm("index.php?op=delGroup&gid=$gid&ok=1", _MD_COMPLETEGROUP);

        echo "</td><td>\n";

        echo TextForm("index.php?op=delGroup&gid=$gid&ok=2", _MD_ONLYGROUP);

        echo "</td><td>\n";

        echo TextForm('index.php', _MD_NO);

        echo "</td></tr></table>\n";

        CloseTable();

        xoops_cp_footer();
    }
}

// -----------------------------------------------------------------------------------------------------------//
function TutorialsConfigMenuT()
{
    global $xoopsDB, $xoopsConfig, $mytree;

    xoops_cp_header();

    OpenTable();

    $result = $xoopsDB->query('select count(*) from ' . $xoopsDB->prefix('tutorials_categorys') . '');

    [$numrows] = $xoopsDB->fetchRow($result);

    if ($numrows > 0) {
        // Add new Tutorial ------------------//

        echo '<center><form method=post action=index.php><h4>' . _MD_TUTORIAL . '</h4><br>';

        echo '<input type=radio name=op value=questForPics checked>&nbsp;' . _MD_ADD . '&nbsp;</input>';

        echo '<input type=radio name=op value=modTutorial>&nbsp;' . _MD_MODIFY . '&nbsp;</input>';

        echo '<input type=radio name=op value=delTutorial>&nbsp;' . _MD_CLEAR . '&nbsp;</input><br><br>';

        echo _MD_INCATEGORY;

        $mytree->makeMySelBox('cname', 'cname');

        echo '<input type=submit value=' . _MD_GO . ">\n";

        echo '<input type="button" value="' . _MD_CANCEL . '" onclick="javascript:history.go(-1)">';

        echo '</form></center>';
    } else {
        echo _MD_NOCATEGORY;
    }

    CloseTable();

    xoops_cp_footer();
}

// -----------------------------------------------------------------------------------------------------------//
function questForPics()
{
    global $_POST;

    xoops_cp_header();

    $cid = $_POST['cid'];

    OpenTable();

    echo '<p align="center"><b>' . _MD_QUESTPIC . '</b></p>';

    echo '<table align="center"><tr><td>';

    echo TextForm("index.php?op=addTutorial&createdir=1&cid=$cid", _MD_YES);

    echo '</td><td>';

    echo TextForm("index.php?op=addTutorial&createdir=0&cid=$cid", _MD_NO);

    echo '</td></tr></table>';

    CloseTable();

    xoops_cp_footer();
}

// -----------------------------------------------------------------------------------------------------------//
function addTutorial()
{
    global $xoopsDB, $xoopsUser, $_GET, $myts, $imgwidth, $imgheight;

    xoops_cp_header();

    $cid = $_GET['cid'];

    $createdir = $_GET['createdir'];

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
                echo '<p align="center"><h5><font color="red"><b>' . _MD_DIRCREATED . '</b></font></h4></p>';

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

    $scriptname = 'index.php';

    $submitter = $xoopsUser->uid();

    $tauthor = XoopsUser::getUnameFromId($submitter);

    require XOOPS_ROOT_PATH . '/modules/tutorials/include/form.php';

    CloseTable();

    xoops_cp_footer();
}

// -----------------------------------------------------------------------------------------------------------//
function PreviewTutorial()
{
    global $xoopsDB, $xoopsConfig, $_POST, $myts, $mytree, $content_visdefault, $content_default, $content_visualize, $imgwidth, $imgheight;

    xoops_cp_header();

    if ($_POST['tid']) {
        $tid = $_POST['tid'];
    }

    $cid = $_POST['cid'];

    $gid = !empty($_POST['gid']);

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

    $framebrowse = !empty($_POST['framebrowse']);

    $maketdir = !empty($_POST['maketdir']) ? (int)$_POST['maketdir'] : 0;

    if (1 == $maketdir) {
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
    } else {
        $timgwidth = $_POST['timgwidth'];

        $timgheight = $_POST['timgheight'];
    }

    OpenTable();

    echo '<h4>Preview</h4>';

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
        $content = $tlink;

        echo '<center>' . _MD_EXTLINK . "$tlink&nbsp;->&nbsp;<a href=\"$tlink\" target=\"_blank\"><b>" . _MD_SHOWLINK . '</b></a></center><hr>';
    }

    // ShowForm ----------------------//

    $xsmiley = !empty($_POST['xsmiley']) ? (int)$_POST['xsmiley'] : 0;

    $xhtml = !empty($_POST['xhtml']) ? (int)$_POST['xhtml'] : 0;

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

    $scriptname = 'index.php';

    $createdir = 0;

    require XOOPS_ROOT_PATH . '/modules/tutorials/include/form.php';

    CloseTable();

    xoops_cp_footer();
}

// -----------------------------------------------------------------------------------------------------------//
function SaveTutorial()
{
    global $xoopsDB, $xoopsConfig, $xoopsUser, $_POST, $myts, $eh;

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
        xoops_cp_header();

        OpenTable();

        echo $message;

        echo '<center><input type="button" value="' . _MD_GOBACK . '" onclick="javascript:history.go(-1)"></center>';

        CloseTable();

        xoops_cp_footer();

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
            . " set tid=$tid, cid=$cid, gid=$gid, tname='$tname', tdesc='$tdesc', timg='$timg', tcont='$tcont', tlink='$tlink', tauthor='$tauthor', status=$status, codes=$codes, hits=0, rating=0, votes=0, date=$time, timgwidth=$timgwidth, timgheight=$timgheight where tid=$tid"
        ) or $eh::show('0013');
    } elseif ($tid > 0 && 3 == $status) {
        $result = $xoopsDB->query('SELECT status, date FROM ' . $xoopsDB->prefix('tutorials') . " WHERE tid=$tid");

        [$statusdb, $date] = $xoopsDB->fetchRow($result);

        if (2 != $statusdb) {
            $time = time();
        }

        $date = $time;

        $xoopsDB->query(
            'UPDATE ' . $xoopsDB->prefix('tutorials') . " set tid=$tid, cid=$cid, gid=$gid, tname='$tname', tdesc='$tdesc', timg='$timg', tcont='$tcont', tlink='$tlink', tauthor='$tauthor', status=$status, codes=$codes, date=$date, timgwidth=$timgwidth, timgheight=$timgheight where tid=$tid"
        ) or $eh::show('0013');
    } else {
        $date = time();

        $xoopsDB->query(
            'UPDATE ' . $xoopsDB->prefix('tutorials') . " set tid=$tid, cid=$cid, gid=$gid, tname='$tname', tdesc='$tdesc', timg='$timg', tcont='$tcont', tlink='$tlink', tauthor='$tauthor', status=$status, codes=$codes, date=$date, timgwidth=$timgwidth, timgheight=$timgheight where tid=$tid"
        ) or $eh::show('0013');
    }

    if (1 == $status || 3 == $status) {
        $result = $xoopsDB->query('SELECT submitter FROM ' . $xoopsDB->prefix('tutorials') . " WHERE tid=$tid");

        [$submitter] = $xoopsDB->fetchRow($result);

        if ($xoopsUser->uid() != $submitter) {
            $submitter = new XoopsUser($submitter);

            $subject = sprintf(_MD_YOURFILEAT, $xoopsConfig['sitename']);

            $message = sprintf(_MD_HELLO, $submitter->uname());

            if (1 == $status) {
                $message .= "\n\n" . _MD_WEAPPROVED . "\n\n";
            }

            if (3 == $status) {
                $message .= "\n\n" . _MD_WEAPPROVEDMOD . "\n\n";
            }

            $siteurl = XOOPS_URL . '/modules/tutorials/';

            $message .= sprintf(_MD_VISITAT, $siteurl);

            $message .= "\n\n" . _MD_THANKSSUBMIT . "\n\n" . $xoopsConfig['sitename'] . "\n" . XOOPS_URL . "\n" . $xoopsConfig['adminmail'] . '';

            $xoopsMailer = getMailer();

            $xoopsMailer->useMail();

            $xoopsMailer->setToEmails($submitter->getVar('email'));

            $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);

            $xoopsMailer->setFromName($xoopsConfig['sitename']);

            $xoopsMailer->setSubject($subject);

            $xoopsMailer->setBody($message);

            $xoopsMailer->send();
        }
    }

    redirect_header('index.php', 1, _MD_DBUPDATED);

    exit();
}

// -----------------------------------------------------------------------------------------------------------//
function modTutorial()
{
    global $xoopsDB, $xoopsConfig, $_POST, $myts;

    $cid = $_POST['cid'];

    $result = $xoopsDB->query('select count(*) from ' . $xoopsDB->prefix('tutorials') . " where cid=$cid");

    [$numrows] = $xoopsDB->fetchRow($result);

    if ($numrows > 0) {
        xoops_cp_header();

        $result = $xoopsDB->query('select tid, tname from ' . $xoopsDB->prefix('tutorials') . " where cid=$cid");

        OpenTable();

        echo '<center><h4>' . _MD_MODTUTORIAL . '</h4>';

        echo '<form method=post action=index.php>';

        echo "<select name='tid'>\n";

        echo '<option value=0>' . _MD_SELECT . '</option>';

        while (list($tid, $tname) = $xoopsDB->fetchRow($result)) {
            $tname = htmlspecialchars($tname, ENT_QUOTES | ENT_HTML5);

            echo "<option value='$tid'>$tname</option>\n";
        }

        echo "</select>\n";

        echo "<input type=hidden name=op value=editTutorial>\n";

        echo '<input type="submit" value="' . _MD_MODIFY . "\">\n";

        echo '<input type="button" value="' . _MD_CANCEL . '" onclick="javascript:history.go(-1)">';

        echo '</form></center>';

        CloseTable();

        xoops_cp_footer();
    } else {
        redirect_header('index.php', 2, _MD_NOTUTSAVAILABLE);
    }
}

// -----------------------------------------------------------------------------------------------------------//
function editTutorial()
{
    global $xoopsDB, $xoopsConfig, $_POST, $_GET, $myts, $eh, $mytree, $imgwidth, $imgheight;

    xoops_cp_header();

    if (!empty($_GET['tid'])) {
        $tid = $_GET['tid'];
    } elseif (!empty($_POST['tid'])) {
        $tid = $_POST['tid'];
    }

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

        $html = 1;

        $smiley = 1;
    } elseif (1 == $codes) {
        $xhtml = 1;

        $xsmiley = 0;

        $html = 0;

        $smiley = 1;
    } elseif (2 == $codes) {
        $xhtml = 0;

        $xsmiley = 1;

        $html = 1;

        $smiley = 0;
    } else {
        $xhtml = 1;

        $xsmiley = 1;

        $html = 0;

        $smiley = 0;
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

    $img_path = "$cid/";

    $img_path2 = "$cid/$dir/";

    $scriptname = 'index.php';

    $createdir = 0;

    require XOOPS_ROOT_PATH . '/modules/tutorials/include/form.php';

    CloseTable();

    xoops_cp_footer();
}

// -----------------------------------------------------------------------------------------------------------//
function waitTutorial()
{
    global $xoopsDB, $xoopsConfig, $_GET, $myts, $eh, $mytree, $content_visdefault, $content_default, $content_visualize;

    xoops_cp_header();

    $tid = $_GET['tid'];

    $result = $xoopsDB->query('select tid, cid, gid, tname,tdesc, timg, tcont, tlink, tauthor, status, hits, rating, votes, codes, date, submitter, dir, timgwidth, timgheight from ' . $xoopsDB->prefix('tutorials') . " where tid=$tid");

    [$tid, $cid, $gid, $tname, $tdesc, $timg, $tcont, $tlink, $tauthor, $status, $hits, $rating, $votes, $codes, $time, $submitter, $dir, $timgwidth, $timgheight] = $xoopsDB->fetchRow($result);

    if ($codes >= 10) {
        $codes -= 10;

        $framebrowse = 1;
    } else {
        $framebrowse = 0;
    }

    if (0 == $codes) {
        $xhtml = 0;

        $xsmiley = 0;

        $html = 1;

        $smiley = 1;
    } elseif (1 == $codes) {
        $xhtml = 1;

        $xsmiley = 0;

        $html = 0;

        $smiley = 1;
    } elseif (2 == $codes) {
        $xhtml = 0;

        $xsmiley = 1;

        $html = 1;

        $smiley = 0;
    } else {
        $xhtml = 1;

        $xsmiley = 1;

        $html = 0;

        $smiley = 0;
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

    $img_path2 = "cid/$dir/";

    $scriptname = 'index.php';

    $createdir = 0;

    require XOOPS_ROOT_PATH . '/modules/tutorials/include/form.php';

    CloseTable();

    xoops_cp_footer();
}

// -----------------------------------------------------------------------------------------------------------//
function delTutorial()
{
    global $xoopsDB, $xoopsConfig, $_POST, $_GET, $myts, $eh;

    if ($_GET['ok']) {
        $ok = $_GET['ok'];
    } elseif ($_POST['ok']) {
        $ok = $_POST['ok'];
    }

    if (1 == $ok) {
        xoops_cp_header();

        if ($_GET['tid']) {
            $tid = $_GET['tid'];
        } else {
            $tid = $_POST['tid'];
        }

        OpenTable();

        echo '<center>';

        echo '<h4><font color="#ff0000">';

        echo _MD_WARNINGT . '</font></h4><br>';

        echo "<table><tr><td>\n";

        echo TextForm("index.php?op=delTutorial&tid=$tid&ok=2", _MD_YES);

        echo "</td><td>\n";

        echo TextForm('index.php', _MD_NO);

        echo "</td></tr></table>\n";

        CloseTable();

        xoops_cp_footer();
    } elseif (2 == $ok) {
        $tid = $_GET['tid'];

        $xoopsDB->query('delete from ' . $xoopsDB->prefix('tutorials') . ' where tid=' . $tid . '') or $eh::show('0013');

        redirect_header('index.php', 1, _MD_TUTORIALDELETED);
    } else {
        $cid = $_POST['cid'];

        $result = $xoopsDB->query('select count(*) from ' . $xoopsDB->prefix('tutorials') . " where cid=$cid");

        [$numrows] = $xoopsDB->fetchRow($result);

        if ($numrows > 0) {
            xoops_cp_header();

            $result = $xoopsDB->query('select tid, tname from ' . $xoopsDB->prefix('tutorials') . " where cid=$cid");

            OpenTable();

            echo '<center><h4>' . _MD_DELETETUTORIAL . '</h4>';

            echo '<form method=post action=index.php>';

            echo "<select name='tid'>";

            echo '<option value=0>' . _MD_SELECT . '</option>';

            while (list($tid, $tname) = $xoopsDB->fetchRow($result)) {
                $tname = htmlspecialchars($tname, ENT_QUOTES | ENT_HTML5);

                echo "<option value='$tid'>$tname</option>";
            }

            echo '</select>';

            echo "<input type=hidden name=op value=delTutorial>\n";

            echo "<input type=hidden name=ok value=1>\n";

            echo '<input type=submit value=' . _MD_CLEAR . ">\n";

            echo '</form></center>';

            CloseTable();

            xoops_cp_footer();
        } else {
            redirect_header('index.php', 2, _MD_NOTUTSAVAILABLE);
        }
    }
}

// -----------------------------------------------------------------------------------------------------------//
function TutorialsConfigAdmin()
{
    global $xoopsConfig, $category_default, $tutorial_default, $content_default, $columnset, $imgwidth, $imgheight, $framebrowse, $orderby;

    global $category_visdefault, $tutorial_visdefault, $content_visdefault, $category_visualize, $tutorial_visualize, $content_visualize;

    global $maximgwidth, $maximgheight, $maxfilesize, $popular, $maxsubcatshow, $useruploads, $heading;

    xoops_cp_header();

    $category_visualize = stripslashes($category_visualize);

    $tutorial_visualize = stripslashes($tutorial_visualize);

    $content_visualize = stripslashes($content_visualize); ?>
    <!--xoopsCode start-->
    <script type="text/javascript">
        <!--
        function xoopsCodePagebreak(id) {
            var dom = xoopsGetElementById(id);
            dom.value += "[pagebreak]"
            dom.focus();
        }

        //-->
    </script>
    <?php
    OpenTable();

    # Image Dir Permission

    $dir = XOOPS_ROOT_PATH . '/modules/tutorials/images';

    $decperms = fileperms($dir);

    $octalperms = sprintf('%o', $decperms);

    $perms = (mb_substr($octalperms, 1));

    echo '<h4>' . _MD_GENERALSET . '</h4><br>';

    echo '<form action="index.php" method="post">';

    echo '<table width=600 border=0 cellspacing=8 align=center><tr><td nowrap>';

    if ('0755' != $perms && '0777' != $perms) {
        echo "<span style='color:red'>" . sprintf(_MD_PERMERROR, $perms) . '</span>';

        echo "<br \><br \>" . _MD_PERMISSIONSET . "<input type=button value='0755' onclick='location.href=\"index.php?op=setPerm&perm=0755\"'> " . _MD_OR . " <input type=button value='0777' onclick='location.href=\"index.php?op=setPerm&perm=0777\"'>";
    } else {
        echo '</td></tr><th align="left">' . _MD_HEADING . '</th>';

        echo "<tr><td><input type=\"text\" name=\"xheading\" value=\"$heading\" size=\"50\"><hr></td></tr><tr><td>";

        echo '<table border=0><th colspan=2>';

        echo _MD_IMAGESIZE . '</th>';

        echo '<tr><td>' . _MD_MAXIMGWIDTH . "</td><td><input type=\"text\" name=\"ximgwidth\" value=\"$imgwidth\" size=\"6\"></td></tr>";

        echo '<tr><td>' . _MD_MAXIMGHEIGHT . "</td><td><input type=\"text\" name=\"ximgheight\" value=\"$imgheight\" size=\"6\"></td></tr></table>";

        echo '</td></tr><th align=left>' . _MD_TWOCOLUMNSET . '</th><tr><td nowrap>';

        if (1 == $columnset) {
            echo '<INPUT TYPE="RADIO" NAME="xcolumnset" VALUE="1" CHECKED>&nbsp;' . _MD_ONECOLUMN . '&nbsp;</INPUT>';

            echo '<INPUT TYPE="RADIO" NAME="xcolumnset" VALUE="2">&nbsp;' . _MD_TWOCOLUMN . '&nbsp;</INPUT>';
        } else {
            echo '<INPUT TYPE="RADIO" NAME="xcolumnset" VALUE="1">&nbsp;' . _MD_ONECOLUMN . '&nbsp;</INPUT>';

            echo '<INPUT TYPE="RADIO" NAME="xcolumnset" VALUE="2" CHECKED>&nbsp;' . _MD_TWOCOLUMN . '&nbsp;</INPUT>';
        }

        echo '</td></tr><tr><td>' . _MD_MAXSUBCATSHOW . ' ';

        echo '<select name="xmaxsubcatshow">';

        for ($i = 1; $i < 10; $i++) {
            if ($i != $maxsubcatshow) {
                echo "<option value=\"$i\">&nbsp;&nbsp;&nbsp;" . $i . '&nbsp;&nbsp;&nbsp;</option>';
            } else {
                echo "<option value=\"$i\" selected>&nbsp;&nbsp;&nbsp;" . $i . '&nbsp;&nbsp;&nbsp;</option>';
            }
        }

        echo '</select>';

        echo '<hr></td></tr><tr><td nowrap>';

        echo '<table border=0><th colspan=2 align=left>';

        echo _MD_IMAGECONFIG . '</th>';

        echo '<tr><td colspan="2">';

        echo '<input type="checkbox" name="xuseruploads" value="1"';

        if (1 == $useruploads) {
            echo ' checked="checked"';
        }

        echo '> ' . _MD_ALLOWUSERUPLOAD . '';

        echo '</td></tr>';

        echo '<tr><td>' . _MD_MAXFILESIZE . "</td><td><input type=\"text\" name=\"xmaxfilesize\" value=\"$maxfilesize\" size=\"10\"> Bytes</td></tr>";

        echo '<tr><td>' . _MD_MAXIMGWIDTH . "</td><td><input type=\"text\" name=\"xmaximgwidth\" value=\"$maximgwidth\" size=\"6\"></td></tr>";

        echo '<tr><td>' . _MD_MAXIMGHEIGHT . "</td><td><input type=\"text\" name=\"xmaximgheight\" value=\"$maximgheight\" size=\"6\"></td></tr></table>";
    }

    echo '<hr></td></tr><tr><td nowrap>';

    echo '<u><b>' . _MD_VISCATEGORY . '</b></u><br>';

    echo "<br><textarea id=xcategory_visualize name=xcategory_visualize rows=10 cols=60>$category_visualize</textarea><br>";

    $target = 'xcategory_visualize';

    echo "<input type='button' value='Help' onclick='openWithSelfMain(\"" . XOOPS_URL . "/modules/tutorials/include/options.php?target=$target\",\"options\",450,500);'>";

    echo ' <input type="checkbox" name="xcategory_visdefault" value="1"';

    if (1 == $category_visdefault) {
        echo ' checked="checked"';
    }

    echo '> ' . _MD_ACTIVATE . '';

    echo '<hr></td></tr><tr><td>';

    echo '<u><b>' . _MD_VISTUTORIAL . '</b></u><br>';

    echo "<br><textarea id=xtutorial_visualize name=xtutorial_visualize rows=10 cols=60>$tutorial_visualize</textarea><br>";

    $target = 'xtutorial_visualize';

    echo "<input type='button' value='Help' onclick='openWithSelfMain(\"" . XOOPS_URL . "/modules/tutorials/include/options.php?target=xtutorial_visualize\",\"options\",450,500);'>";

    echo ' <input type="checkbox" name="xtutorial_visdefault" value="1"';

    if (1 == $tutorial_visdefault) {
        echo ' checked="checked"';
    }

    echo '> ' . _MD_ACTIVATE . '';

    echo '<hr></td></tr><tr><td>';

    echo '<u><b>' . _MD_VISCONTENT . '</b></u><br>';

    echo "<br><textarea id=xcontent_visualize name=xcontent_visualize rows=10 cols=60>$content_visualize</textarea><br>";

    $target = 'xcontent_visualize';

    echo "<input type='button' value='Help' onclick='openWithSelfMain(\"" . XOOPS_URL . "/modules/tutorials/include/options.php?target=$target\",\"options\",450,500);'>";

    echo ' <input type="checkbox" name="xcontent_visdefault" value="1"';

    if (1 == $content_visdefault) {
        echo ' checked="checked"';
    }

    echo '> ' . _MD_ACTIVATE . '';

    echo '<hr></td></tr><th align=left>' . _MD_SHOWEXTLINK . '</th><tr><td>';

    if (1 == $framebrowse) {
        echo '<INPUT TYPE="RADIO" NAME="xframebrowse" VALUE="1" CHECKED>&nbsp;' . _MD_FRAMEBROWSE . '&nbsp;</INPUT>';

        echo '<INPUT TYPE="RADIO" NAME="xframebrowse" VALUE="0">&nbsp;' . _MD_NEWWINDOW . '&nbsp;</INPUT>';
    } else {
        echo '<INPUT TYPE="RADIO" NAME="xframebrowse" VALUE="1">&nbsp;' . _MD_FRAMEBROWSE . '&nbsp;</INPUT>';

        echo '<INPUT TYPE="RADIO" NAME="xframebrowse" VALUE="0" CHECKED>&nbsp;' . _MD_NEWWINDOW . '&nbsp;</INPUT>';
    }

    echo '<hr></td></tr><tr><td>';

    echo '' . _MD_ORDERBY . ' ';

    $orderop = ['' . _MD_ORDERBYNAME . '', 'tname', '' . _MD_ORDERBYDATE . '', 'date DESC', '' . _MD_ORDERBYHITS . '', 'hits DESC', '' . _MD_ORDERBYRATING . '', 'rating DESC'];

    echo "<select name='xorderby'>";

    for ($i = 0, $iMax = count($orderop); $i < $iMax; $i += 2) {
        if ($orderby == $orderop[$i + 1]) {
            echo '<option value="' . $orderop[$i + 1] . '" selected>' . $orderop[$i] . '</option>';
        } else {
            echo '<option value="' . $orderop[$i + 1] . '">' . $orderop[$i] . '</option>';
        }
    }

    echo '</select>';

    echo '<hr></td></tr><tr><td>';

    echo '' . _MD_POPULAR . "<input type=\"text\" name=\"xpopular\" value=\"$popular\" size=\"6\">";

    echo '<hr></td></tr><tr><td>';

    echo '<input type="hidden" name="op" value="TutorialsConfigChange">';

    echo '<input type="submit" value="' . _MD_SAVE . '">';

    echo '&nbsp;<input type="button" value="' . _MD_CANCEL . '" onclick="javascript:history.go(-1)">';

    echo '</td></tr></table>';

    echo '</form>';

    CloseTable();

    xoops_cp_footer();
}

// -----------------------------------------------------------------------------------------------------------//
function setPermission()
{
    global $_GET;

    $perms = $_GET['perm'];

    if (false === chmod(XOOPS_ROOT_PATH . '/modules/tutorials/images', octdec($perms))) {
        redirect_header('index.php', 1, _MD_PERMSETERR);

        exit();
    }  

    redirect_header('index.php?op=TutorialsConfigAdmin', 1, sprintf(_MD_PERMSET, $perms));

    exit();
}

// -----------------------------------------------------------------------------------------------------------//

function TutorialsConfigChange()
{
    global $xoopsConfig, $_POST, $myts;

    $xcolumnset = $_POST['xcolumnset'];

    $ximgwidth = $_POST['ximgwidth'];

    $ximgheight = $_POST['ximgheight'];

    $xmaximgwidth = $_POST['xmaximgwidth'];

    $xmaximgheight = $_POST['xmaximgheight'];

    $xmaxfilesize = $_POST['xmaxfilesize'];

    $xframebrowse = $_POST['xframebrowse'];

    $xcategory_visdefault = $_POST['xcategory_visdefault'];

    $xtutorial_visdefault = $_POST['xtutorial_visdefault'];

    $xcontent_visdefault = $_POST['xcontent_visdefault'];

    if (empty($xcategory_visdefault)) {
        $xcategory_visdefault = 0;
    }

    if (empty($xtutorial_visdefault)) {
        $xtutorial_visdefault = 0;
    }

    if (empty($xcontent_visdefault)) {
        $xcontent_visdefault = 0;
    }

    $xcategory_visualize = $_POST['xcategory_visualize'];

    $xtutorial_visualize = $_POST['xtutorial_visualize'];

    $xcontent_visualize = $_POST['xcontent_visualize'];

    $xorderby = $_POST['xorderby'];

    $xpopular = $_POST['xpopular'];

    $xmaxsubcatshow = $_POST['xmaxsubcatshow'];

    $xuseruploads = $_POST['xuseruploads'];

    $xheading = $_POST['xheading'];

    if (empty($xuseruploads)) {
        $xuseruploads = 0;
    }

    $filename = XOOPS_ROOT_PATH . '/modules/tutorials/cache/config.php';

    $file = fopen($filename, 'wb');

    $content = '';

    $content .= "<?PHP\n";

    $content .= "########################################################\n";

    $content .= "# Content Management System                            #\n";

    $content .= "# Tutorials V2.1 for xoops       Configuration         #\n";

    $content .= "#                                                      #\n";

    $content .= "########################################################\n";

    $content .= "\$columnset = $xcolumnset;\n";

    $content .= "\$imgwidth = $ximgwidth;\n";

    $content .= "\$imgheight = $ximgheight;\n";

    #	$content .= "// content image config //\n";

    $content .= "\$maximgwidth = $xmaximgwidth;\n";

    $content .= "\$maximgheight = $xmaximgheight;\n";

    $content .= "\$maxfilesize = $xmaxfilesize;\n";

    $content .= "\$framebrowse = $xframebrowse;\n";

    #	$content .= "// if *_visdefault = 1 then the default visuals is active //\n";

    $content .= "\$category_visdefault = $xcategory_visdefault;\n";

    $content .= "\$tutorial_visdefault = $xtutorial_visdefault;\n";

    $content .= "\$content_visdefault = $xcontent_visdefault;\n";

    #	$content .= "// this your own visualized Tutorials  //\n";

    $content .= "\$category_visualize = \"$xcategory_visualize\";\n";

    $content .= "\$tutorial_visualize = \"$xtutorial_visualize\";\n";

    $content .= "\$content_visualize = \"$xcontent_visualize\";\n";

    #	$content .= "// default visualisation //\n";

    $content .= "\$category_default = \"<table width=100% border=0 cellspacing=0 cellpadding=1 class=bg4><tr><td><table width=100% border=0 cellpadding=4 cellspacing=0 class=bg2><tr><td valign=top>[image]</td><td width=100% valign=top align=left><b>[title]</b> <small>[count]</small><br><i>[subcat]</i><br>[description][link]</td></tr></table></td></tr></table>\";\n";

    $content .= "\$tutorial_default = \"<table width=80% border=0 cellspacing=0 cellpadding=1 class=bg4><tr><td><table width=100% border=0 cellpadding=4 cellspacing=0 class=bg2><tr><td valign=top rowspan=2>[image]</td><td width=100% valign=top align=left style='border-bottom:1pt solid #000000;'><b>[title]</b> [author] [date]  [print]<br>[description][link]</td></tr><tr><td valign=top>[hits][rating][votes][ratethis]<td></tr></table></td></tr></table>\";\n";

    $content .= "\$content_default = \"<table width=100% border=0 cellspacing=0 cellpadding=1 class=bg4><tr><td><table width=100% cellspacing=0 cellpadding=4 border=0 class=bg2><tr><td align=center>[image]<h4>[title]</h4></td></tr><tr><td>[content]</td></tr></table></td></tr></table>\";\n";

    $content .= "\n";

    $content .= '$orderby = "' . $xorderby . "\";\n";

    $content .= '$popular = ' . $xpopular . ";\n";

    $content .= '$maxsubcatshow = ' . $xmaxsubcatshow . ";\n";

    $content .= '$useruploads = ' . $xuseruploads . ";\n";

    $content .= '$heading = "' . $xheading . "\";\n";

    $content .= "?>\n";

    fwrite($file, $content);

    fclose($file);

    redirect_header('index.php', 1, _MD_CONFUPDATED);

    exit();
}

// -----------------------------------------------------------------------------------------------------------//

switch ($op) {
    default:
        tutorials();
        break;
    case 'addCat':
        addCat();
        break;
    case 'addGroup':
        addGroup();
        break;
    case 'questForPics':
        questForPics();
        break;
    case 'addTutorial':
        addTutorial();
        break;
    case 'modCat':
        modCat();
        break;
    case 'modGroup':
        modGroup();
        break;
    case 'modTutorial':
        modTutorial();
        break;
    case 'editTutorial':
        editTutorial();
        break;
    case 'waitTutorial':
        waitTutorial();
        break;
    case 'modCatS':
        modCatS();
        break;
    case 'modGroupS':
        modGroupS();
        break;
    case 'delCat':
        delCat();
        break;
    case 'delGroup':
        delGroup();
        break;
    case 'delTutorial':
        delTutorial();
        break;
    case 'TutorialsConfigAdmin':
        TutorialsConfigAdmin();
        break;
    case 'TutorialsConfigChange':
        TutorialsConfigChange();
        break;
    case 'TutorialsConfigMenuC':
        TutorialsConfigMenuC();
        break;
    case 'TutorialsConfigMenuG':
        TutorialsConfigMenuG();
        break;
    case 'TutorialsConfigMenuT':
        TutorialsConfigMenuT();
        break;
    case 'PreviewTutorial':
        PreviewTutorial();
        break;
    case 'SaveTutorial':
        SaveTutorial();
        break;
    case 'setPerm':
        setPermission();
        break;
}

?>
