<?php
/**
 * Copyright (c) 2018.
 * Itach-soft
 * www.itach.by
 * Minsk. Belarus
 */
$heading=_("Statistics");
?>

<h2><?php echo $heading;?></h2>


        <table id="queuestat"
               data-url="ajax.php?module=callbackqueue&command=getJSON&jdata=queuestat"
               data-toolbar="#toolbar-main"
               data-cache="false"
               data-toggle="table"
               data-search="true"
               data-pagination="true"
               data-show-export="true"
               data-show-refresh="true"
               data-page-list="[50, 100, 300, 700, 1000, '10000', 'ALL']"
               class="table table-striped">
            <thead>
            <tr>
                <th data-field="call_id" data-sortable="true"><?php echo _("Call ID") ?></th>
                <th data-field="number" data-sortable="true"><?php echo _("Number") ?></th>
                <th data-field="qc_id" data-sortable="true"><?php echo _("Queue Callback ID") ?></th>
                <th data-field="datetime_in" data-sortable="true"><?php echo _("Datetime callback out in") ?></th>
                <th data-field="datetime_out" data-sortable="true"><?php echo _("Datetime callback finish") ?></th>
                <th data-field="status" data-sortable="true"><?php echo _("Status") ?></th>
                <th data-field="qc_call" data-sortable="true"><?php echo _("Call Retries") ?></th>

            </tr>
            </thead>
        </table>



