<?php
//-------------------------------------------------------------------------- //
//  Tutorials Version 2.1 rate.php      			                         //
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

include '../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';

#if(file_exists(XOOPS_ROOT_PATH."/modules/tutorials/language/".$xoopsConfig['language']."/main.php")){
#	require XOOPS_ROOT_PATH."/modules/tutorials/language/".$xoopsConfig['language']."/main.php";
#}else{
#	require XOOPS_ROOT_PATH."/modules/tutorials/language/english/main.php";
#}

$myts = MyTextSanitizer::getInstance(); // MyTextSanitizer object

if ($_POST['submit']) {
    $eh = new ErrorHandler(); //ErrorHandler object

    if (!$xoopsUser) {
        $ratinguser = 0;
    } else {
        $ratinguser = $xoopsUser->uid();
    }

    //Make sure only 1 anonymous from an IP in a single day.

    $anonwaitdays = 1;

    $ip = getenv('REMOTE_ADDR');

    $tid = $_POST['tid'];

    $rating = $_POST['rating'];

    // Check if Rating is Null

    if ('--' == $rating) {
        redirect_header('rate.php?tid=' . $tid . '', 4, _MD_NORATING);

        exit();
    }

    // Check if REG user is trying to vote twice.

    $result = $xoopsDB->query('SELECT ratinguser FROM ' . $xoopsDB->prefix('tutorials_votedata') . " WHERE tid=$tid");

    while (list($ratinguserDB) = $xoopsDB->fetchRow($result)) {
        if ($ratinguserDB == $ratinguser) {
            redirect_header("index.php?op=listtutorials&cid=$cid", 4, _MD_VOTEONCE);

            exit();
        }
    }

    // Check if ANONYMOUS user is trying to vote more than once per day.

    if (0 == $ratinguser) {
        $yesterday = (time() - (86400 * $anonwaitdays));

        $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('tutorials_votedata') . " WHERE tid=$tid AND ratinguser=0 AND ratinghostname = '$ip'  AND ratingtimestamp > $yesterday");

        [$anonvotecount] = $xoopsDB->fetchRow($result);

        if ($anonvotecount >= 1) {
            redirect_header("index.php?op=listtutorials&cid=$cid", 4, _MD_VOTEONCE);

            exit();
        }
    }

    //All is well.  Add to Line Item Rate to DB.

    $newid = $xoopsDB->genId('tutorials_votedata_ratingid_seq');

    $datetime = time();

    $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('tutorials_votedata') . " (ratingid, tid, ratinguser, rating, ratinghostname, ratingtimestamp) VALUES ($newid, $tid, $ratinguser, $rating, '$ip', $datetime)") or $eh('0013');

    //All is well.  Calculate Score & Add to Summary (for quick retrieval & sorting) to DB.

    //updates rating data in itemtable for a given item

    $query = 'select rating FROM ' . $xoopsDB->prefix('tutorials_votedata') . " WHERE tid = $tid";

    $voteresult = $xoopsDB->query($query);

    $votesDB = $xoopsDB->getRowsNum($voteresult);

    $totalrating = 0;

    while (list($rating) = $xoopsDB->fetchRow($voteresult)) {
        $totalrating += $rating;
    }

    $finalrating = $totalrating / $votesDB;

    $finalrating = number_format($finalrating, 4);

    $query = 'UPDATE ' . $xoopsDB->prefix('tutorials') . " SET rating=$finalrating, votes=$votesDB WHERE tid = $tid";

    $xoopsDB->query($query);

    $ratemessage = _MD_VOTEAPPRE . '<br>' . sprintf(_MD_THANKYOU, $xoopsConfig[sitename]);

    redirect_header("index.php?op=listtutorials&cid=$cid", 4, $ratemessage);

    exit();
}  
    require XOOPS_ROOT_PATH . '/header.php';
    OpenTable();
    $result = $xoopsDB->query('SELECT cid, tname FROM ' . $xoopsDB->prefix('tutorials') . " WHERE tid=$tid");
    [$cid, $tname] = $xoopsDB->fetchRow($result);
    $tname = htmlspecialchars($tname, ENT_QUOTES | ENT_HTML5);
    echo "
    	<hr>
		<table border=0 cellpadding=1 cellspacing=0 width=\"80%\"><tr><td>
    	<h4>$tname</h4>
    	<UL>
     	<LI>" . _MD_VOTEONCE . '
     	<LI>' . _MD_RATINGSCALE . '
     	<LI>' . _MD_BEOBJECTIVE . '
     	<LI>' . _MD_DONOTVOTE . '';
    echo "
     	</UL>
     	</td></tr>
     	<tr><td align=\"center\">
     	<form method=\"POST\" action=\"rate.php\">
     	<input type=\"hidden\" name=\"tid\" value=\"$tid\">
		<input type=\"hidden\" name=\"cid\" value=\"$cid\">
     	<select name=\"rating\">
		<option>--</option>";
    for ($i = 10; $i > 0; $i--) {
        echo '<option value="' . $i . '">' . $i . "</option>\n";
    }
    echo '</select><br><br><input type="submit" name="submit" value="' . _MD_RATEIT . "\"\n>";
    echo '&nbsp;<input type="button" value="' . _MD_CANCEL . "\" onclick=\"javascript:history.go(-1)\">\n";
    echo '</form></td></tr></table>';
    CloseTable();

require XOOPS_ROOT_PATH . '/footer.php';
