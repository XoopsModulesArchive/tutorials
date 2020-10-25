<?php

/******************************************************************************
 * Function: b_tutorials_top_show
 * Input   : $options[0] = date for the most recent links
 *                    hits for the most popular tutorials
 *           $block['content'] = The optional above content
 *           $options[1]   = How many reviews are displayes
 * Output  : Returns the desired most recent or most popular tutorials
 *****************************************************************************
 * @param $options
 * @return array
 */
function b_tutorials_top_show($options)
{
    global $xoopsDB, $framebrowse;

    require_once XOOPS_ROOT_PATH . '/modules/tutorials/cache/config.php';

    $block = [];

    $block['content'] = '<div style="text-align:left;"><small>';

    $myts = MyTextSanitizer::getInstance();

    $result = $xoopsDB->query('select tid, tname, tlink, codes, status, hits, date from ' . $xoopsDB->prefix('tutorials') . ' WHERE status=1 or status=3 ORDER BY ' . $options[0] . ' DESC', $options[1], 0);

    while (list($tid, $tname, $tlink, $codes, $status, $hits, $date) = $xoopsDB->fetchRow($result)) {
        $tname = htmlspecialchars($tname, ENT_QUOTES | ENT_HTML5);

        if (!XOOPS_USE_MULTIBYTES) {
            if (mb_strlen($tname) >= 19) {
                $tname = mb_substr($tname, 0, 18) . '...';
            }
        }

        if ('' != $tlink) {
            if (1 == $framebrowse || $codes >= 10) {
                $link_url = '' . XOOPS_URL . "/modules/tutorials/viewexttutorial.php?tid=$tid";

                $link_target = '';
            } else {
                $link_url = $tlink;

                $link_target = ' target="_blank"';
            }

            $block['content'] .= '&nbsp;<strong><big>&middot;</big></strong>&nbsp;<a href="' . $link_url . '" ' . $link_target . '>' . $tname . '</a>';
        } else {
            $block['content'] .= '&nbsp;<strong><big>&middot;</big></strong>&nbsp;<a href="' . XOOPS_URL . "/modules/tutorials/viewtutorial.php?tid=$tid\">$tname</a>";
        }

        $count = 7;    //7 days

        $startdate = (time() - (86400 * $count));

        if ($startdate < $time) {
            if (1 == $status) {
                $block['content'] .= '&nbsp;<img src="' . XOOPS_URL . '/modules/tutorials/images/newred.gif">';
            } elseif (3 == $status) {
                $block['content'] .= '&nbsp;<img src="' . XOOPS_URL . '/modules/tutorials/images/update.gif">';
            }
        }

        if ('date' == $options[0]) {
            $block['content'] .= '&nbsp;<small>(' . formatTimestamp($date, 's') . ')</small><br>';

            $block['title'] = _MB_BLOCK_TITLE1;
        } elseif ('hits' == $options[0]) {
            $block['content'] .= '&nbsp;<small>(' . $hits . ')</small><br>';

            $block['title'] = _MB_BLOCK_TITLE2;
        }
    }

    $block['content'] .= '</small></div>';

    return $block;
}

function b_tutorials_top_edit($options)
{
    $form = '' . _MB_TUTORIALS_DISP . '&nbsp;';

    $form .= "<input type='hidden' name='options[]' value='";

    if ('date' == $options[0]) {
        $form .= "date'";
    } else {
        $form .= "hits'";
    }

    $form .= '>';

    $form .= "<input type='text' name='options[]' value='" . $options[1] . "'>&nbsp;" . _MB_TUTORIALS_TUTS . '';

    return $form;
}
