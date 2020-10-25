<?php
//-------------------------------------------------------------------------- //
//  Update from Tutorials Version 2.0 to V2.1      	                         //
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

include '../../mainfile.php';
require XOOPS_ROOT_PATH . '/header.php';
$myts = MyTextSanitizer::getInstance();

if (file_exists('language/' . $xoopsConfig['language'] . '/update.php')) {
    require_once 'language/' . $xoopsConfig['language'] . '/update.php';
} elseif (file_exists('language/english/update.php')) {
    require_once 'language/english/update.php';
}

echo '<h3>' . _UPDATE_00 . '</h3>';
echo '<table width="90%" cellspacing="0" cellpadding="0" border="0"><tr><td align="left">';
$status = 0;
$status2 = 0;
$error = 0;
$errortxt = '';
# create dir field ########################
$result = @$GLOBALS['xoopsDB']->queryF('select dir from ' . $xoopsDB->prefix('tutorials') . '');
if (!$result) {
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('tutorials') . " ADD dir int(10) NOT NULL DEFAULT '0'");

    if ($result) {
        $result = $xoopsDB->query('select tid, tname, date from ' . $xoopsDB->prefix('tutorials') . '');

        while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
            $date = formatTimestamp($myrow['date'], 'm');

            $tid = $myrow['tid'];

            $tname = htmlspecialchars($myrow['tname'], ENT_QUOTES | ENT_HTML5);

            $result2 = $xoopsDB->queryF('UPDATE ' . $xoopsDB->prefix('tutorials') . " SET dir='$date' where tid=$tid");

            if ($result2) {
                echo $tname . '(' . $tid . ') -> ' . $date . ' ' . _UPDATE_03 . '<br>';
            } else {
                echo $tname . '(' . $tid . ') -> ' . $date . ' ' . _UPDATE_04 . '<br>';
            }
        }

        $status = 1;
    } else {
        $error += 1;

        $errortxt .= sprintf(_UPDATE_01, 'dir', $xoopsDB->prefix('tutorials'));
    }
} else {
    echo sprintf(_UPDATE_02, 'dir', $xoopsDB->prefix('tutorials'));

    $status2 = 1;
}

# create logowidth field ########################
$result = @$GLOBALS['xoopsDB']->queryF('select logowidth from ' . $xoopsDB->prefix('tutorials') . '');
if (!$result) {
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('tutorials') . " ADD logowidth int(6) NOT NULL DEFAULT '0'");

    if ($result) {
        $status += 10;
    } else {
        $error += 1;

        $errortxt .= sprintf(_UPDATE_01, 'logowidth', $xoopsDB->prefix('tutorials'));
    }
} else {
    echo sprintf(_UPDATE_02, 'logowidth', $xoopsDB->prefix('tutorials'));

    $status2 += 10;
}

# create logoheight field ########################
$result = @$GLOBALS['xoopsDB']->queryF('select logoheight from ' . $xoopsDB->prefix('tutorials') . '');
if (!$result) {
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('tutorials') . " ADD logoheight int(6) NOT NULL DEFAULT '0'");

    if ($result) {
        $status += 100;
    } else {
        $error += 1;

        $errortxt .= sprintf(_UPDATE_01, 'logoheight', $xoopsDB->prefix('tutorials'));
    }
} else {
    echo sprintf(_UPDATE_02, 'logoheight', $xoopsDB->prefix('tutorials'));

    $status2 += 100;
}

# create timgwidth field ########################
$result = @$GLOBALS['xoopsDB']->queryF('select timgwidth from ' . $xoopsDB->prefix('tutorials') . '');
if (!$result) {
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('tutorials') . " ADD timgwidth int(6) NOT NULL DEFAULT '0'");

    if ($result) {
        $status += 1000;
    } else {
        $error += 1;

        $errortxt .= sprintf(_UPDATE_01, 'timgwidth', $xoopsDB->prefix('tutorials'));
    }
} else {
    echo sprintf(_UPDATE_02, 'timgwidth', $xoopsDB->prefix('tutorials'));

    $status2 += 1000;
}

# create timgheight field ########################
$result = @$GLOBALS['xoopsDB']->queryF('select timgheight from ' . $xoopsDB->prefix('tutorials') . '');
if (!$result) {
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('tutorials') . " ADD timgheight int(6) NOT NULL DEFAULT '0'");

    if ($result) {
        $status += 10000;
    } else {
        $error += 1;

        $errortxt .= sprintf(_UPDATE_01, 'timgheight', $xoopsDB->prefix('tutorials'));
    }
} else {
    echo sprintf(_UPDATE_02, 'timgheight', $xoopsDB->prefix('tutorials'));

    $status2 += 10000;
}

# create cimgwidth field ########################
$result = @$GLOBALS['xoopsDB']->queryF('select cimgwidth from ' . $xoopsDB->prefix('tutorials_categorys') . '');
if (!$result) {
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('tutorials_categorys') . " ADD cimgwidth int(6) NOT NULL DEFAULT '0'");

    if ($result) {
        $status += 100000;
    } else {
        $error += 1;

        $errortxt .= sprintf(_UPDATE_01, 'cimgwidth', $xoopsDB->prefix('tutorials_categorys'));
    }
} else {
    echo sprintf(_UPDATE_02, 'cimgwidth', $xoopsDB->prefix('tutorials_categorys'));

    $status2 += 100000;
}

# create cimgheight field ########################
$result = @$GLOBALS['xoopsDB']->queryF('select cimgheight from ' . $xoopsDB->prefix('tutorials_categorys') . '');
if (!$result) {
    $result = $xoopsDB->queryF('ALTER TABLE ' . $xoopsDB->prefix('tutorials_categorys') . " ADD cimgheight int(6) NOT NULL DEFAULT '0'");

    if ($result) {
        $status += 1000000;
    } else {
        $error += 1;

        $errortxt .= sprintf(_UPDATE_01, 'cimgheight', $xoopsDB->prefix('tutorials_categorys'));
    }
} else {
    echo sprintf(_UPDATE_02, 'cimgheight', $xoopsDB->prefix('tutorials_categorys'));

    $status2 += 1000000;
}

if (1111111 == $status2) {
    echo _UPDATE_07;
} elseif (1111111 == $status) {
    echo _UPDATE_06;
} elseif ($error > 0) {
    echo sprintf(_UPDATE_05, $error);

    echo $errortxt;
}
if (0 != $status) {
    $status += $status2;

    if (1111111 == $status && 0 == $error) {
        echo _UPDATE_08;
    }
}

echo '</td></tr></table>';
require XOOPS_ROOT_PATH . '/footer.php';
