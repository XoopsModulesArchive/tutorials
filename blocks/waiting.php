<?php
/****************************************************************************
 * Function: b_waiting_show                                                    *
 *                                                                            *
 * Output  : Returns all tutorials, they are not activated                    *
 ***************************************************************************/

function b_waiting_show()
{
    global $xoopsDB;

    $block = [];

    $block['title'] = _MB_BLOCK_TITLE3;

    $block['content'] = '<div style="text-align:left;">';

    $myts = MyTextSanitizer::getInstance();

    $result = $xoopsDB->query('select tid, tname from ' . $xoopsDB->prefix('tutorials') . ' WHERE status=0 or status=2 order by date');

    $num = $xoopsDB->getRowsNum($result);

    $block['content'] .= "<strong><big>&middot;</big></strong>&nbsp;Waiting Tutorials:&nbsp;$num<br>";

    while (list($tid, $tname) = $xoopsDB->fetchRow($result)) {
        $tname = htmlspecialchars($tname, ENT_QUOTES | ENT_HTML5);

        if (!XOOPS_USE_MULTIBYTES) {
            if (mb_strlen($tname) >= 19) {
                $tname = mb_substr($tname, 0, 18) . '...';
            }
        }

        $block['content'] .= '<strong><big>-</big></strong>&nbsp;<a href="' . XOOPS_URL . "/modules/tutorials/admin/index.php?op=waitTutorial&tid=$tid\">$tname</a><br></div> ";
    }

    return $block;
}
