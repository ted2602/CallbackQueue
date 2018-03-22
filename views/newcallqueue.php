<?php
/**
 * Copyright (c) 2018.
 * Itach-soft
 * www.itach.by
 * Minsk. Belarus
 */
if (isset($_REQUEST['action'])){
    $heading = _("Edit callback rule");
    $qc_queues=$callbackqueue->qc_getqueues($_REQUEST['qc_id']);
    $qc_action="edit_qc";
    $qc_queues=$qc_queues[0];
   // print '<pre>';
   // print_r($qc_queues);
   // print '</pre>';
   if ($qc_queues['qc_agentfirst']==0) {
       $checkqcano = 'checked=""';
   }
        else
        {
            $checkqcayes = 'checked=""';

        }





}else {
    $heading = _("Create new callback rule");
    $qc_action = "new_qc";
    $checkqcayes='checked=""';

    //$qc_queues = $callbackqueue->qc_getqueues();
}
$queues = $callbackqueue->getqueus();



//print '<pre>';
//print_r($callbackqueue->get_timegroup_data($qc_queues['qc_timegroup']));
//print '</pre>';
//var_export($callbackqueue->qc_checkIntervals($callbackqueue->get_timegroup_data($qc_queues['qc_timegroup'])));


?>

<br><br>
<h2><?php echo $heading; ?></h2>
<div class="display full-border">
    <div class="container-fluid">

        <br>
        <div class="display">
            <div class="row">
                <div class="col-sm-12">
                    <div class="fpbx-container">
                        <div class="display full-border">
                            <form autocomplete="off" class="fpbx-submit" name="group"
                                  action="?display=callbackqueue" method="post"
                                  data-fpbx-delete="?display=callbackqueue&amp;delete=true&amp;action=group&amp;edit=1">
                                <input type="hidden" name="display" value="callbackqueue">
                                <input type="hidden" name="action" value="<?php echo $qc_action; ?>">                                <input type="hidden" name="action" value="<?php echo $qc_action; ?>">
                                <input type="hidden" name="qc_id" value="<?php if (isset($qc_queues['qc_id']))echo $qc_queues['qc_id']; ?>">

                                <!--Name-->
                                <div class="element-container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <label class="control-label" for="qcname"><?php echo(_("Name"));?></label>
                                                        <i class="fa fa-question-circle fpbx-help-icon"
                                                           data-for="qcname"></i>
                                                    </div>
                                                    <div class="col-md-9 input">
                                                        <input type="text" class="form-control " name="qc_name" id="qcname" value="<?php echo $qc_queues['qc_name']; ?>" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span id="qcname-help" class="help-block fpbx-help-block"><?php echo(_("Enter name callbackqueue"));?></span>
                                        </div>
                                    </div>
                                </div>
                                <!--END Name-->
                                <!--Select callback queue-->
                                <div class="element-container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <label class="control-label" for="qcqueue"><?php echo(_("Select queue for callback"));?></label>
                                                        <i class="fa fa-question-circle fpbx-help-icon"
                                                           data-for="qcqueue"></i>
                                                    </div>
                                                    <div class="col-md-9 radioset">


                                                        <?php
                                                        $qc_qexten='';
                                                        $qc_qexten=$qc_queues['qc_queue'];
                                                        if ($qc_qexten==''){
                                                            $qc_qexten='none';$def_select='selected';}
                                                        echo('   <select name="qc_queue" class="form-control destdropdown2">
                                    <option '.$def_select.' disabled>'._("Select Queue").'</option>
                                    ');
                                                        //var_dump($res_pusers);
                                                        $i=0;
                                                        $k=count($queues);
                                                        while ($i<$k) {
                                                            $qc=$queues["$i"];
                                                            if ($qc["descr"]!==NULL || $qc["descr"]!==''){$qc_name=$qc["descr"];}
                                                            else {$qc_name=$qc["extension"];}
                                                            echo('<option ');

                                                            if ($qc["extension"]==$qc_qexten){echo (' selected ');}
                                                            echo('value="'.$qc["extension"].'">'.$qc["extension"].'('.$qc_name.')</option>');



                                                            $i++;
                                                        }
                                                        echo('
                                </select>
                                ');
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span id="qcqueue-help" class="help-block fpbx-help-block"><?php echo(_("Select queue for callback"));?></span>
                                        </div>
                                    </div>
                                </div>



                                <!--END Select callback queue-->
                                <!--First call CallBack-->
                                <div class="element-container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <label class="control-label"
                                                               for="qc_first"><?php echo(_("Call first to agent")); ?></label>
                                                        <i class="fa fa-question-circle fpbx-help-icon"
                                                           data-for="qc_first"></i>
                                                    </div>
                                                    <div class="col-md-9 radioset">
                                                        <input type="radio" name="qc_first"
                                                               id="qc_first_yes"
                                                               value="1" <?php echo($checkqcayes) ?>>
                                                        <label for="qc_first_yes"><?php echo(_("Yes")); ?></label>
                                                        <input type="radio" name="qc_first"
                                                               id="qc_first_no"
                                                               value="0" <?php echo($checkqcano) ?>>
                                                        <label for="qc_first_no"><?php echo(_("No")); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span id="qc_first-help"
                                                  class="help-block fpbx-help-block"><?php echo(_("Select yes, if you want to call first Agent.")); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <!--END First call CallBack-->
                                <!--Number max calls-->
                                <div class="element-container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <label class="control-label" for="qc_maxcalls"><?php echo(_("Max calls store"));?></label>
                                                        <i class="fa fa-question-circle fpbx-help-icon"
                                                           data-for="qc_maxcalls"></i>
                                                    </div>
                                                    <div class="col-md-9 input">
                                                        <input type="number" class="form-control " name="qc_maxcalls" id="qc_maxcalls" value="<?php echo $qc_queues['qc_maxcalls']; ?>" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span id="qc_maxcalls-help" class="help-block fpbx-help-block"><?php echo(_("Enter max calls numbers to store for callbackqueue"));?></span>
                                        </div>
                                    </div>
                                </div>
                                <!--Number max calls-->
                                <!--Number max retry-->
                                <div class="element-container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <label class="control-label" for="qc_retry"><?php echo(_("Maximum Retries"));?></label>
                                                        <i class="fa fa-question-circle fpbx-help-icon"
                                                           data-for="qc_retry"></i>
                                                    </div>
                                                    <div class="col-md-9 input">
                                                        <input type="number" class="form-control " name="qc_retry" id="qc_retry" value="<?php echo $qc_queues['qc_retry']; ?>" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span id="qc_retry-help" class="help-block fpbx-help-block"><?php echo(_("Enter the maximum number of retries on failed calls."));?></span>
                                        </div>
                                    </div>
                                </div>
                                <!--Number max retry-->
                                <!--Number min agents in queue-->
                                <div class="element-container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <label class="control-label" for="qc_minagents"><?php echo(_("Min agents"));?></label>
                                                        <i class="fa fa-question-circle fpbx-help-icon"
                                                           data-for="qc_minagents"></i>
                                                    </div>
                                                    <div class="col-md-9 input">
                                                        <input type="number" class="form-control " name="qc_minagents" id="qc_minagents" value="<?php echo $qc_queues['qc_minagents']; ?>" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span id="qc_minagents-help" class="help-block fpbx-help-block"><?php echo(_("Enter min agents avaliable in queue to orginate callback"));?></span>
                                        </div>
                                    </div>
                                </div>
                                <!--Number min agents in queue-->
                                <!-- Направление для callback если больше параметра max agents-->
                                <div class="element-container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <label class="control-label" for="qc_callbackdest"><?php echo(_("Callback destination case max limit"));?></label>
                                                        <i class="fa fa-question-circle fpbx-help-icon"
                                                           data-for="qc_callbackdest"></i>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <?php
                                                        if (isset($qc_queues['qc_callbackdest'])) {
                                                            echo drawselects($qc_queues['qc_callbackdest'],0);;
                                                        }
                                                        else {
                                                            echo drawselects('',0);
                                                        }
                                                        ?>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span id="qc_callbackdest-help" class="help-block fpbx-help-block"><?php echo(_("Choose callback destination when max calls limit"));?></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- END Направление для callback если больше параметра max agents !-->
                                <!-- Время для callback-->
                                <div class="element-container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <label class="control-label" for="qc_timegroup"><?php echo(_("Time Group"));?></label>
                                                        <i class="fa fa-question-circle fpbx-help-icon"
                                                           data-for="qc_timegroup"></i>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <?php
                                                          echo qc_timegroups_drawgroupselect('qc_timegroup', (isset($qc_queues['qc_timegroup']) ? $qc_queues['qc_timegroup'] : ''), true, '');
                                                         ?>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span id="qc_timegroup-help" class="help-block fpbx-help-block"><?php echo(_("Select a Time Group created under Time Groups. Matching times will be sent to matching destination. If no group is selected, call will always go to no-match destination."));?></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Время для callback !-->

                                <div class="element-container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                <input type="submit" name="submit" value="<?php echo(_("Submit")); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

