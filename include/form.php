<?php

if (isset($gid)) {
    $xgid = $gid;
}
if ('' != $cimg) {
    if (preg_match('http://', $cimg)) {
        $imgpath = $cimg;
    } else {
        $imgpath = '' . IMAGE_URL . "/$cimg";
    }
}
$imagesize = getimagesize($imgpath);
$imagewidth = $imagesize[0];
$imageheight = $imagesize[1];

if ('' != $timg && 0 == $timgwidth) {
    $timgwidth = $imagewidth;
}
if ('' != $timg && 0 == $timgheight) {
    $timgheight = $imageheight;
}
?>
    <!--xoopsCode start-->
    <script type="text/javascript">
        <!--
        var backup_timgwidth = <?php echo $timgwidth; ?>;
        var backup_timgheight = <?php echo $timgheight; ?>;
        var timgwidthmax = <?php echo $imgwidth; ?>;
        var timgheightmax = <?php echo $imgheight; ?>;

        function calcPicsize(nr) {
            var faktor = 0;
            var buffer = 0;
            if (nr == 1) {
                for (i = 0; i < document.tutorialform.timgwidth.value.length; ++i)
                    if (document.tutorialform.timgwidth.value.charAt(i) < "0" || document.tutorialform.timgwidth.value.charAt(i) > "9")
                        document.tutorialform.timgwidth.value = backup_timgwidth;

                if (document.tutorialform.timgwidth.value < 0) {
                    document.tutorialform.timgwidth.value = backup_timgwidth;
                }
                if (document.tutorialform.timgwidth.value > timgwidthmax) {
                    document.tutorialform.timgwidth.value = timgwidthmax;
                }
                faktor = (document.tutorialform.timgwidth.value * 1) / backup_timgwidth;
                buffer = Math.round(backup_timgheight * faktor);
                if (buffer > timgheightmax) {
                    document.tutorialform.timgheight.value = timgheightmax;
                    faktor = (document.tutorialform.timgheight.value * 1) / backup_timgheight;
                    document.tutorialform.timgwidth.value = Math.round(backup_timgwidth * faktor);
                } else {
                    document.tutorialform.timgheight.value = buffer;
                }
            }
            if (nr == 2) {
                for (i = 0; i < document.tutorialform.timgheight.value.length; ++i)
                    if (document.tutorialform.timgheight.value.charAt(i) < "0" || document.tutorialform.timgheight.value.charAt(i) > "9")
                        document.tutorialform.timgheight.value = backup_timgheight;

                if (document.tutorialform.timgheight.value < 0) {
                    document.tutorialform.timgheight.value = backup_timgheight;
                }
                if (document.tutorialform.timgheight.value > timgheightmax) {
                    document.tutorialform.timgheight.value = timgheightmax;
                }
                faktor = (document.tutorialform.timgheight.value * 1) / backup_timgheight;
                buffer = Math.round(backup_timgwidth * faktor);
                if (buffer > timgwidthmax) {
                    document.tutorialform.timgwidth.value = timgwidthmax;
                    faktor = (document.tutorialform.timgwidth.value * 1) / backup_timgwidth;
                    document.tutorialform.timgheight.value = Math.round(backup_timgheight * faktor);
                } else {
                    document.tutorialform.timgwidth.value = buffer;
                }
            }
            backup_timgwidth = (document.tutorialform.timgwidth.value * 1);
            backup_timgheight = (document.tutorialform.timgheight.value * 1);
            document.timage.width = backup_timgwidth;
            document.timage.height = backup_timgheight;
        }

        function setorgsize() {
            document.tutorialform.timgwidth.value = <?php echo $imagewidth; ?>;
            document.tutorialform.timgheight.value = <?php echo $imageheight; ?>;
            backup_timgwidth = (document.tutorialform.timgwidth.value * 1);
            backup_timgheight = (document.tutorialform.timgheight.value * 1);
            document.timage.width = backup_timgwidth;
            document.timage.height = backup_timgheight;
        }

        //-->
    </script>
<?php
echo "<form name=\"tutorialform\" action=\"$scriptname\" method=\"post\">";
echo '<table width=600 border=0 cellspacing=8 align=center>';

echo "<tr><td><fieldset style=\"padding:5px;\"><legend><b>$cname</b></legend>";
if ('' != $imgpath) {
    echo '<img src="' . $imgpath . '" align=right>';
}
echo "$cdesc<br></fieldset></td></tr>";

if ((0 == $imgsubdirexists && 'index.php' == $scriptname) || (0 == $imgsubdirexists && 1 == $useruploads && 'submit.php' == $scriptname)) {
    echo '<tr><td>';

    echo _MD_QUESTDIR . '<input type="checkbox" name="maketdir" value="1" onclick="submit();"><br>';

    echo '</td></tr>';
}

echo '<tr><td>';
if (isset($tid)) {
    echo "<input type=\"hidden\" name=\"tid\" value=\"$tid\">";

    echo _MD_CATEGORYC;

    $mytree->makeMySelBox('cname', 'cname', (string)$cid, 0, 'cid');

    echo '</td></tr>';
} else {
    echo '<input type="hidden" name="tid" value="0">';

    echo "<input type=\"hidden\" name=\"cid\" value=\"$cid\">";
}
echo "<input type=\"hidden\" name=\"dir\" value=\"$dir\">";
echo "<input type=\"hidden\" name=\"time\" value=\"$time\">";
echo "<input type=\"hidden\" name=\"hits\" value=\"$hits\">";
echo "<input type=\"hidden\" name=\"rating\" value=\"$rating\">";
echo "<input type=\"hidden\" name=\"votes\" value=\"$votes\">";

$result2 = $xoopsDB->query('select count(*) from ' . $xoopsDB->prefix('tutorials_groups') . " where cid=$cid");
[$numrows] = $xoopsDB->fetchRow($result2);
if ($numrows > 0) {
    echo '<tr><td>' . _MD_TGROUP . "<select name='gid'>";

    if (0 == $xgid) {
        echo "<option value='$xgid' selected>" . _MD_NOGROUPSELELECT . '</option>';
    } else {
        echo "<option value='$xgid'>" . _MD_NOGROUPSELELECT . '</option>';
    }

    $result3 = $xoopsDB->query('select gid, gname from ' . $xoopsDB->prefix(tutorials_groups) . " where cid=$cid");

    while (list($gid, $gname) = $xoopsDB->fetchRow($result3)) {
        $gname = htmlspecialchars($gname, ENT_QUOTES | ENT_HTML5);

        if ($xgid == $gid) {
            echo "<option value='$gid' selected>$gname</option>";
        } else {
            echo "<option value='$gid'>$gname</option>";
        }
    }

    echo '</select></td></tr>';
}
?>
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
// Tutorials Form -----------------------//
echo '<tr><td>' . _MD_TNAME . '<br><input type="text" name="tname" size="60" value="';
if (isset($tname)) {
    echo $tname;
}
echo '"></td></tr>';
echo '<tr><td>' . _MD_TAUTHOR . '<br><input type="text" name="tauthor" size="60" value="';
if (isset($tauthor)) {
    echo $tauthor;
}
echo '"></td></tr>';
echo '<tr><td>' . _MD_TIMAGE . '<br><input type="text" id="timg" name="timg" size="60" maxlength="150" value="';
if (isset($timg)) {
    echo $timg;
}
echo "\">\n";
if ('index.php' == $scriptname) {
    if (1 == $imgdirexists) {
        echo "<input type='button' value='Upload' onclick='javascript:openWithSelfMain(\"" . XOOPS_URL . "/modules/tutorials/upload.php?img_path=$img_path&target=timg&logo=1&target2=timgwidth&target3=timgheight\",\"upload\",450,450);'>\n";

        echo '<br>' . _MD_EG . ': ' . IMAGE_URL . '/';
    } else {
        echo '<br><font color="#ff0000">' . _MD_DIRNOTEXISTS . '</font>';
    }
} else {
    if (1 == $imgdirexists && 1 == $useruploads) {
        echo "<input type='button' value='Upload' onclick='javascript:openWithSelfMain(\"" . XOOPS_URL . "/modules/tutorials/upload.php?img_path=$img_path&target=timg&logo=1&target2=timgwidth&target3=timgheight\",\"upload\",450,450);'>\n";

        echo '<br>' . _MD_EG . ': ' . IMAGE_URL . '/';
    } elseif (0 == $imgdirexists && 1 == $useruploads) {
        echo '<br><font color="#ff0000">' . _MD_DIRNOTEXISTS . '</font>';
    } else {
        echo '<br>http://www.domain.de/images/pic.gif';
    }
}
echo '</td></tr>';
echo '<tr><td>';

if (isset($timgwidth) && isset($timgheight) && $timgwidth > 0 && $timgheight > 0) {
    echo _MD_IMGWIDTH . '&nbsp;<input type="text" id="timgwidth" name="timgwidth" size="6" value="' . $timgwidth . "\" onchange='calcPicsize(1);'>\n";

    echo '&nbsp;&nbsp;' . _MD_IMGHEIGHT . '&nbsp;<input type="text" id="timgheight" name="timgheight" size="6" value="' . $timgheight . "\" onchange='calcPicsize(2);'>\n";

    echo "&nbsp;&nbsp;<input type=button value='Reset' onclick='setorgsize();'>\n";
} else {
    echo "<input type=\"hidden\" id=\"timgwidth\" name=\"timgwidth\" value=\"\">\n";

    echo "<input type=\"hidden\" id=\"timgheight\" name=\"timgheight\" value=\"\">\n";
}
echo '</td></tr>';
echo '<tr><td>' . _MD_DESCRIPTION . '<br><textarea name=tdesc rows=5 cols=50>';
if (isset($tdesc)) {
    echo $tdesc;
}
echo '</textarea></td></tr>';
echo '<tr><td><hr><br>' . _MD_TLINK . ' (' . _MD_LINKINFO . ')<br><input type="text" name="tlink" size="90" value="';
if (isset($tlink)) {
    echo $tlink;
}
echo '"><br><input type="checkbox" name="framebrowse" value="1" ';
if (1 == $framebrowse) {
    echo 'checked="checked"';
}
echo '> ' . _MD_FRAMEBROWSE . '<br>';
echo '<b><font color=red>' . _MD_LINKWARNING . '</font></b></td></tr>';
echo '<tr><td><hr><br><b>' . _MD_CONTENT . "</b><br><br>\n";

echo "<input type='button' value='URL' onclick='xoopsCodeUrl(\"tcont\");'>\n";
echo "<input type='button' value='EMAIL' onclick='xoopsCodeEmail(\"tcont\");'>\n";
echo "<input type='button' value='Insert Image' onclick='xoopsCodeImg(\"tcont\");'>\n";
if (1 == $createdir || 1 == $imgsubdirexists) {
    #		echo "<input type='button' value='Insert Image' onclick='xoopsCodeImg(\"tcont\");'>\n";

    echo "<input type='button' value='Upload & Insert Image' onclick='javascript:openWithSelfMain(\"" . XOOPS_URL . "/modules/tutorials/upload.php?img_path=$img_path2&target=tcont&logo=0\",\"upload\",450,450);'>\n";

    #	} else {
    #		echo "<input type='button' value='Insert Image' onclick='xoopsCodeImg(\"tcont\");'>\n";
}
echo "<input type='button' value='" . _MD_NEXTPAGE . "' onclick='javascript:xoopsCodePagebreak(\"tcont\");'>\n";
echo '<br>';
$sizearray = ['xx-small', 'x-small', 'small', 'medium', 'large', 'x-large', 'xx-large'];
echo "<select id='tcontSize' onchange='setVisible(\"hiddenText\");setElementSize(\"hiddenText\",this.options[this.selectedIndex].value);'>\n";
echo "<option value='SIZE'>" . _SIZE . "</option>\n";
foreach ($sizearray as $size) {
    echo "<option value='$size'>$size</option>\n";
}
echo "</select>\n";

$fontarray = ['Arial', 'Courier', 'Georgia', 'Helvetica', 'Impact', 'Tahoma', 'Verdana'];
echo "<select id='tcontFont' onchange='setVisible(\"hiddenText\");setElementFont(\"hiddenText\",this.options[this.selectedIndex].value);'>\n";
echo "<option value='FONT'>" . _FONT . "</option>\n";
foreach ($fontarray as $font) {
    echo "<option value='$font'>$font</option>\n";
}
echo "</select>\n";

$colorarray = ['00', '33', '66', '99', 'CC', 'FF'];
echo "<select id='tcontColor' onchange='setVisible(\"hiddenText\");setElementColor(\"hiddenText\",this.options[this.selectedIndex].value);'>\n";
echo "<option value='COLOR'>" . _COLOR . "</option>\n";
foreach ($colorarray as $color1) {
    foreach ($colorarray as $color2) {
        foreach ($colorarray as $color3) {
            echo "<option value='" . $color1 . $color2 . $color3 . "' style='background-color:#" . $color1 . $color2 . $color3 . ';color:#' . $color1 . $color2 . $color3 . ";'>#" . $color1 . $color2 . $color3 . "</option>\n";
        }
    }
}
echo "</select><span id='hiddenText'>" . _EXAMPLE . "</span>\n";

echo "<br><input type='checkbox' id='tcontBold' onclick='setVisible(\"hiddenText\");makeBold(\"hiddenText\");'><b>B</b>&nbsp;<input type='checkbox' id='tcontItalic' onclick='setVisible(\"hiddenText\");makeItalic(\"hiddenText\");'><i>I</i>&nbsp;<input type='checkbox' id='tcontUnderline' onclick='setVisible(\"hiddenText\");makeUnderline(\"hiddenText\");'><u>U</u>&nbsp;&nbsp;<input type='textbox' id='tcontAddtext' size='20'>&nbsp;<input type='button' onclick='xoopsCodeText(\"tcont\")' value='"
     . _ADD
     . "'><br><br><textarea id='tcont' name='tcont' wrap='virtual' cols='80' rows='20'>";
if (isset($tcont)) {
    echo $tcont;
}
echo "</textarea><br>\n";
xoopsSmilies('tcont');
echo '</td></tr>';

if (!empty($xoopsConfig['allow_html'])) {
    echo '<tr><td><p>' . _MD_ALLOWEDHTML . '<br>';

    echo get_allowed_html();

    echo '</p></td></tr>';
}

echo '<tr><td><input type="checkbox" name="xsmiley" value="1"';
if (!empty($xsmiley)) {
    echo ' checked="checked"';
}
echo '> ' . _MD_DISSMILEY . '</td></tr>';
echo '<tr><td><input type="checkbox" name="xhtml" value="1"';
if (!empty($xhtml)) {
    echo ' checked="checked"';
}
echo '> ' . _MD_DISHTML . '</td></tr>';
echo '<tr><td>';

if ('index.php' == $scriptname) {
    echo '<br><b>' . _MD_ACTIVATED . '</b><br>';

    if (isset($status)) {
        if (1 == $status) {
            echo '<INPUT TYPE="RADIO" NAME="status" VALUE="1" CHECKED>&nbsp;' . _MD_YES . '&nbsp;</INPUT>';

            echo '<INPUT TYPE="RADIO" NAME="status" VALUE="0">&nbsp;' . _MD_NO . '&nbsp;</INPUT>';
        } elseif (0 == $status) {
            echo '<INPUT TYPE="RADIO" NAME="status" VALUE="1">&nbsp;' . _MD_YES . '&nbsp;</INPUT>';

            echo '<INPUT TYPE="RADIO" NAME="status" VALUE="0" CHECKED>&nbsp;' . _MD_NO . '&nbsp;</INPUT>';
        }

        if (3 == $status) {
            echo '<INPUT TYPE="RADIO" NAME="status" VALUE="3" CHECKED>&nbsp;' . _MD_YES . '&nbsp;</INPUT>';

            echo '<INPUT TYPE="RADIO" NAME="status" VALUE="2">&nbsp;' . _MD_NO . '&nbsp;</INPUT>';
        } elseif (2 == $status) {
            echo '<INPUT TYPE="RADIO" NAME="status" VALUE="3">&nbsp;' . _MD_YES . '&nbsp;</INPUT>';

            echo '<INPUT TYPE="RADIO" NAME="status" VALUE="2" CHECKED>&nbsp;' . _MD_NO . '&nbsp;</INPUT>';
        }
    } else {
        echo '<INPUT TYPE="RADIO" NAME="status" VALUE="1">&nbsp;' . _MD_YES . '&nbsp;</INPUT>';

        echo '<INPUT TYPE="RADIO" NAME="status" VALUE="0" CHECKED>&nbsp;' . _MD_NO . '&nbsp;</INPUT>';
    }
} else {
    echo "<input type=\"hidden\" name=\"status\" value=\"$status\">";
}
echo "<input type=\"hidden\" name=\"submitter\" value=\"$submitter\">";
echo '</td></tr>';
echo "<tr><td><select name='op'>\n";
echo "<option value='PreviewTutorial' selected='selected'>" . _MD_PREVIEW . "</option>\n";
echo "<option value='SaveTutorial'>" . _MD_SAVE . "</option>\n";
echo '</select>';
echo "&nbsp;<input type='submit' value='" . _MD_GO . "'>\n";
if (isset($timg) || isset($tlink) || isset($tcont) || isset($tname) || isset($tdesc)) {
    if (isset($tid)) {
        echo "<input type='button' value='" . _MD_BREAKOFF . "' onclick=\"location='index.php'\">\n";

        if ('index.php' == $scriptname) {
            echo "<input type='button' value='" . _MD_CLEAR . "' onclick=\"location='index.php?op=delTutorial&tid=$tid&ok=1'\">\n";
        }
    } else {
        echo "<input type='button' value='" . _MD_BREAKOFF . "' onclick=\"location='index.php'\">\n";
    }
} else {
    echo "<input type='reset' value='" . _MD_CLEAR . "'>\n";
}
echo '</td></tr></table>';
echo '</form>';

?>
