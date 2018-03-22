<?php
/**
 * Copyright (c) 2018.
 * Itach-soft
 * www.itach.by
 * Minsk. Belarus
 */
//Check if user is "logged in"
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }
//Handling form stuff....
$request=$_REQUEST;
$heading=_("Callback queues module for FreePBX.");
$callbackqueue=new FreePBX\modules\Callbackqueue;
//print '<pre>';
//print_r($callbackqueue->get_timegroup_data(2));
//print '</pre>';
?>

<h2><?php echo $heading;?></h2>

<div class = "display full-border">
    <div class="container-fluid">


<?php
switch ($request['view']) {
    case 'queuestat':
        echo ("<a href=?display=callbackqueue class=\"btn btn-default\">");
        echo _("Back");
        echo ("</a>");
        include("views/queuestat.php");
        break;
    case 'newcallqueue':
        echo ("<a href=?display=callbackqueue class=\"btn btn-default\">");
        echo _("Back");
        echo ("</a>");
        include("views/newcallqueue.php");
        break;
    case 'settings':
        echo ("<a href=?display=callbackqueue class=\"btn btn-default\">");
        echo _("Back");
        echo ("</a>");
        include("views/settings.php");
        break;


    default:
        echo ("<a href=?display=callbackqueue&view=newcallqueue class=\"btn btn-default\">");
        echo _("New Callback Queue");
        echo ("</a>");


        echo ("<a href=?display=callbackqueue&view=queuestat class=\"btn btn-default\">");
        echo _("Callback Queue stat");
        echo ("</a>");

        echo ("<a href=?display=callbackqueue&view=settings class=\"btn btn-default\">");
        echo _("Settings");
        echo ("</a>");
        ?>


<table id="callbackqueues"
               data-url="ajax.php?module=callbackqueue&command=getJSON&jdata=qc_queues"
               data-toolbar="#toolbar-main"
               data-cache="false"
               data-toggle="table"
               data-search="true"
               data-pagination="true"
               data-show-export="true"
               data-show-refresh="true"
               data-page-list="[50, 100, 300, 700, 1000, '10000', 'All']"
               class="table table-striped">
        <thead>
        <tr>
            <th data-field="qc_id" data-sortable="true"><?php echo _("ID") ?></th>
            <th data-field="qc_name" data-sortable="true"><?php echo _("Name") ?></th>
            <th data-field="qc_queue" data-sortable="true"><?php echo _("Queue") ?></th>
            <th data-field="qc_agentfirst" data-sortable="true"><?php echo _("First call agent") ?></th>
            <th data-field="qc_maxcalls" data-sortable="true"><?php echo _("Max calls store") ?></th>
            <th data-field="qc_minagents" data-sortable="true"><?php echo _("Min agents") ?></th>
            <th data-field="qc_retry" data-sortable="true"><?php echo _("Maximum Retries") ?></th>
            <th data-field="qc_timegroup_name" data-sortable="true"><?php echo _("Time Group") ?></th>
            <th data-field="qc_callwait" data-sortable="true"><?php echo _("Call wait") ?></th>
            <th data-field="qc_edit" data-sortable="false" data-formatter="qc_edit"><?php echo _("Actions")?></th>

        </tr>
        </thead>
    </table>


<?php
break;
}
echo("
     </div>
    </div>
<h6 align='center'>"._("Callback queues module for FreePBX.")._(" <a target=\"_blank\" href=http://www.itach.by>Itach-soft LLC</a>. Minsk ".date(Y))."</h6>");


?>
