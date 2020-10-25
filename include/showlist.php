<?php

$result = $xoopsDB->query('select tid from ' . $xoopsDB->prefix('tutorials') . " where cid=$xcid and status=1 or status=3 order by " . $orderby . '');
$number = $xoopsDB->getRowsNum($result);
if (0 == $number) {
    echo '<center>' . _MD_NORESULTS . '</center>';
}
$result = $xoopsDB->query('select tid,cid,gid,tname,tdesc,timg,tlink,tauthor,status,codes,hits,rating,votes,date,submitter,timgwidth,timgheight from ' . $xoopsDB->prefix('tutorials') . " where cid=$xcid and gid=$gid and status=1 or status=3 order by " . $orderby . '');
while (list($tid, $cid, $gid, $tname, $tdesc, $timg, $tlink, $tauthor, $status, $codes, $hits, $rating, $votes, $date, $submitter, $timgwidth, $timgheight) = $xoopsDB->fetchRow($result)) {
    $tname = htmlspecialchars($tname, ENT_QUOTES | ENT_HTML5);

    $tdesc = htmlspecialchars($tdesc, ENT_QUOTES | ENT_HTML5);

    $tdesc = stripslashes($tdesc);

    if ('' != $timg) {
        $timg = htmlspecialchars($timg, ENT_QUOTES | ENT_HTML5);
    }

    $tauthor = htmlspecialchars($tauthor, ENT_QUOTES | ENT_HTML5);

    if ('' != $tlink) {
        $tlink = htmlspecialchars($tlink, ENT_QUOTES | ENT_HTML5);
    }

    $rating = number_format($rating, 2);

    if (1 == $tutorial_visdefault) {
        $show = $tutorial_visualize;
    } else {
        $show = $tutorial_default;
    }

    if ('' != $tlink) {
        if (1 == $framebrowse || $codes >= 10) {
            $link_url = "viewexttutorial.php?tid=$tid";

            $link_target = '';
        } else {
            $link_url = $tlink;

            $link_target = ' target="_blank"';
        }
    } else {
        $link_url = "viewtutorial.php?tid=$tid";

        $link_target = '';
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

        $show = str_replace('[image]', '<a href="' . $link_url . '"' . $link_target . '><img src="' . $imgpath . "\" border=1 alt=\"$tname\" " . $setsize . '></a>', $show);

        $show = str_replace('[image left]', '<a href="' . $link_url . '"' . $link_target . '><img src="' . $imgpath . "\" border=1 alt=\"$tname\" " . $setsize . ' align="left"></a>', $show);

        $show = str_replace('[image right]', '<a href="' . $link_url . '"' . $link_target . '><img src="' . $imgpath . "\" border=1 alt=\"$tname\" " . $setsize . ' align="right"></a>', $show);
    } else {
        $show = str_replace('[image]', '', $show);
    }

    $newupdate = newgraphic($date, $status);

    $pop = popgraphic($hits, $popular);

    $show = str_replace('[title]', '<a href="' . $link_url . '"' . $link_target . '>' . $tname . '</a>' . $newupdate . '' . $pop . '', $show);

    if ($tauthor != XoopsUser::getUnameFromId($submitter)) {
        $showt = str_replace('[author]', sprintf(_MD_WRITTENBY, $tauthor), $show);
    } else {
        $show = str_replace('[author]', sprintf(_MD_WRITTENBY, '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $submitter . '">' . $tauthor . '</a>'), $show);
    }

    $show = str_replace('[author]', sprintf(_MD_AUTHOR, $tauthor), $show);

    $date = formatTimestamp($date, 'm');

    $show = str_replace('[date]', sprintf(_MD_CREATEDATE, $date), $show);

    $show = str_replace('[hits]', '(' . sprintf(_MD_TUTORIALREAD, $hits) . ')', $show);

    if ('0' != $rating || '0.0' != $rating) {
        if (1 == $votes) {
            $votestring = _MD_ONEVOTE;
        } else {
            $votestring = sprintf(_MD_NUMVOTES, $votes);
        }

        $show = str_replace('[rating]', _MD_RATINGC . ": $rating", $show);

        $show = str_replace('[votes]', (string)$votestring, $show);
    } else {
        $show = str_replace('[rating]', '', $show);

        $show = str_replace('[votes]', '', $show);
    }

    $show = str_replace('[ratethis]', "&nbsp;<a href=\"rate.php?tid=$tid\"><b>" . _MD_RATETHIS . '</b></a>', $show);

    $show = str_replace('[description]', (string)$tdesc, $show);

    $show = str_replace('[link]', '<a href="' . $link_url . '"' . $link_target . '>' . _MD_LETSGO . '</a>', $show);

    if ('' == $tlink) {
        $show = str_replace('[print]', "<a href=\"printpage.php?tid=$tid\"><img src=\"" . XOOPS_URL . '/modules/tutorials/images/print.gif" border=0 Alt="Print" width=15 height=11"></a>', $show);
    } else {
        $show = str_replace('[print]', '', $show);
    }

    if ($xoopsUser) {
        if ($xoopsUser->isAdmin($xoopsModule->mid())) {
            $editorlink = '(<a href="' . XOOPS_URL . "/modules/tutorials/admin/index.php?op=editTutorial&tid=$tid\">" . _MD_MODIFY . '</a>)';
        } elseif ($xoopsUser->uid() == $submitter) {
            $editorlink = '(<a href="' . XOOPS_URL . "/modules/tutorials/submit.php?op=editTutorial&tid=$tid\">" . _MD_MODIFY . '</a>)';
        }
    }

    $show = str_replace('[edit]', $editorlink, $show);

    echo "$show <br>";
}
