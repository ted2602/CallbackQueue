<?php
/**
 * Copyright (c) 2018.
 * Itach-soft
 * www.itach.by
 * Minsk. Belarus
 */
//получаем временные группы

function qc_timegroups_get_group($timegroup) {
    global $db;

    $sql = "SELECT id, description FROM timegroups_groups WHERE id = $timegroup";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        $results = null;
    }
    $tmparray = array($results[0][0], $results[0][1]);
    return $tmparray;
}

function qc_timegroups_drawgroupselect($elemname, $currentvalue = '', $canbeempty = true, $onchange = '', $default_option = '') {
    global $tabindex;
    $output = '';
    $onchange = ($onchange != '') ? " onchange=\"$onchange\"" : '';

    $output .= "\n\t\t\t<select name=\"$elemname\" tabindex=\"".++$tabindex."\" id=\"$elemname\"$onchange>\n";
    // include blank option if required
    if ($canbeempty) {
        $output .= '<option value="">'.($default_option == '' ? _("--Select a Group--") : $default_option).'</option>';
    }
    // build the options
    $valarray = qc_timegroups_list_groups();
    foreach ($valarray as $item) {
        $itemvalue = (isset($item['value']) ? $item['value'] : '');
        $itemtext = (isset($item['text']) ? _($item['text']) : '');
        $itemselected = ($currentvalue == $itemvalue) ? ' selected' : '';

        $output .= "\t\t\t\t<option value=\"$itemvalue\"$itemselected>$itemtext</option>\n";
    }
    $output .= "\t\t\t</select>\n\t\t";
    return $output;
}
function qc_timegroups_list_groups() {
    global $db;
    $tmparray = array();

    $sql = "SELECT id, description FROM timegroups_groups ORDER BY description";
    $results = $db->getAll($sql);
    if(DB::IsError($results)) {
        $results = null;
    }
    foreach ($results as $val) {
        $tmparray[] = array($val[0], $val[1], "value" => $val[0], "text" => $val[1]);
    }
    return $tmparray;
}



//Добавляем в меню функцию коллбэка

function callbackqueue_destinations()
{
    global $module_page;
    $data = FreePBX::Callbackqueue();
    $qc_queues = $data->qc_getqueues();

    $extens = array();

    if ($module_page == 'callbackqueue') {
        return false;
    }

    // return an associative array with destination and description
    foreach ($qc_queues as $row) {
        $extens[] = array('destination' => 'qc-callback-' . $row['qc_id'] . ',s,1', 'description' => $row['qc_name']);
    }
    return $extens;
}


function callbackqueue_hookGet_config($engine)
{
    global $ext;
    global $amp_conf;
    global $engine;
    $data = FreePBX::Callbackqueue();
    $webroot = $amp_conf['AMPWEBROOT'];
    $qc_queues = $data->qc_getqueues();
    switch ($engine) {
        case "asterisk":
            //Callback  c сайта
            foreach ($qc_queues as $qc_queue) {
                $context = 'qc-callback-' . $qc_queue['qc_id'];
                $exten = 's';

                $ext->add($context, $exten, '', new ext_noop('Queue callback module call ${CHANNEL(linkedid)} number ${CALLERID(num)}'));
                $ext->add($context, $exten, '', new ext_setvar('QCnumber', '${CALLERID(num)}'));
                $ext->add($context, $exten, '', new ext_AGI($webroot . '/admin/modules/callbackqueue/bin/qc_agi.php,' . $qc_queue['qc_id'] . ',${CHANNEL}'));
                $ext->add($context, $exten, '', new ext_DumpChan('3'));
                $ext->add($context, $exten, '', new ext_gotoif('$["${QCMAX}"!="call"]', 'defroute'));


                $ext->add($context, $exten, '', new ext_setvar('QCFirst', '1'));
                $ext->add($context, $exten, '', new ext_Background($webroot . '/admin/modules/callbackqueue/sounds/press1'));
                $ext->add($context, $exten, '', new ext_DigitTimeout('1'));
                $ext->add($context, $exten, '', new ext_WaitExten(8));

                $ext->add($context, $exten, 'defroute', new ext_goto($qc_queue['qc_callbackdest']));

                $ext->add($context, 1, '', new ext_gotoif('$["${QCFirst}"!="1"]', 'qc2'));
                $ext->add($context, 1, '', new ext_setvar('CHANNEL(hangup_handler_push)', 'qc-callback-add' . $qc_queue['qc_id'] . ',s,1'));
                $ext->add($context, 1, '', new ext_setvar('QCFirst', '2'));
                $ext->add($context, 1, '', new ext_Background($webroot . '/admin/modules/callbackqueue/sounds/vashnomer'));
                $ext->add($context, 1, '', new ext_SayDigits('${CALLERID(num)}'));
                $ext->add($context, 1, '', new ext_Background($webroot . '/admin/modules/callbackqueue/sounds/pravil-press1-net-press2'));
                $ext->add($context, 1, '', new ext_setvar('QCFirst', '2'));
                $ext->add($context, 1, '', new ext_DigitTimeout('1'));
                $ext->add($context, 1, '', new ext_WaitExten(8));
                $ext->add($context, 1, 'qc2', new ext_Background($webroot . '/admin/modules/callbackqueue/sounds/svobodny'));
                //$ext->add($context, 1, 'qc2', new ext_DumpChan('3'));
                $ext->add($context, 1, 'qc2', new ext_hangup());

                $ext->add($context, 2, '', new ext_setvar('QCFirst', '2'));
                $ext->add($context, 2, '', new ext_Background($webroot . '/admin/modules/callbackqueue/sounds/primer-nomera'));
                $ext->add($context, 2, '', new ext_DigitTimeout('5'));
                $ext->add($context, 2, '', new ext_WaitExten(8));

                $ext->add($context, '_8XXXXXXXXXX', '', new ext_Background($webroot . '/admin/modules/callbackqueue/sounds/vashnomer'));
                $ext->add($context, '_8XXXXXXXXXX', '', new ext_setvar('QCnumber', '${EXTEN}'));
                $ext->add($context, '_8XXXXXXXXXX', '', new ext_SayDigits('${QCnumber}'));
                $ext->add($context, '_8XXXXXXXXXX', '', new ext_Background($webroot . '/admin/modules/callbackqueue/sounds/pravil-press1-net-press2'));
                $ext->add($context, '_8XXXXXXXXXX', '', new ext_setvar('QCFirst', '2'));
                $ext->add($context, '_8XXXXXXXXXX', '', new ext_DigitTimeout('1'));
                $ext->add($context, '_8XXXXXXXXXX', '', new ext_WaitExten(8));

                $ext->add($context, 'i', '', new ext_goto('qc-callback-' . $qc_queue['qc_id'] . ',s,1'));

                $ext->add($context, 't', '', new ext_hangup());

                $context = 'qc-callback-add' . $qc_queue['qc_id'];
                $exten = 's';
                $ext->add($context, 's', '', new ext_system('php ' . $webroot . '/admin/modules/callbackqueue/bin/addcallback.php --qc_id="' . $qc_queue['qc_id'] . '" --id="${CHANNEL(linkedid)}" --number="${QCnumber}" --datetime="${STRFTIME(${EPOCH},,%Y-%m-%d %H:%M:%S)}" >/dev/null 2>/dev/null &'));
                $ext->add($context, $exten, '', new ext_return(''));


                //Call finish handler
                $context = 'qc-callback-dial';
                $exten = '_X.';


                $ext->add($context, $exten, '', new ext_noop('qc-callback-dial register handler call'));
                $ext->add($context, $exten, '', new ext_setvar('CHANNEL(hangup_handler_push)', 'qc-callback-finish,s,1'));
                $ext->add($context, $exten, '', new ext_wait('2'));
                $ext->add($context, $exten, '', new ext_playback('/var/lib/asterisk/sounds/ru/followme/pls-hold-while-try'));
                $ext->add($context, $exten, '', new ext_goto('from-internal,${EXTEN},1'));

                //$ext->add($context, $exten, '', new ext_system('php '.$webroot.'/bitrix24/api.php --status "'.$status.'" --id="${CHANNEL(linkedid)}"  --did="Callback" --number="${CALLERID(num)}" --datetime="${STRFTIME(${EPOCH},,%Y-%m-%d %H:%M:%S)}" >/dev/null 2>/dev/null &'));
                //$ext->add($context, $exten, '', new ext_setvar('CHANNEL(hangup_handler_push)', 'bitrix24-finish,s,1'));

                $context = 'qc-callback-finish';
                $exten = 's';
                $status = 'finish';

                $ext->add($context, $exten, '', new ext_noop('Finish call DIALSTATUS ${DIALSTATUS} linkedid ${CHANNEL(linkedid)} qc_call ${QCALL} CALLERID(NUM) ${CALLERID(num)} CONNECTEDLINE(num) ${CONNECTEDLINE(num)} Exten ${EXTEN}'));
                $ext->add($context, $exten, '', new ext_system('php ' . $webroot . '/admin/modules/callbackqueue/bin/finishcallback.php --status "' . $status . '" --qc_call="${QCALL}" --dialstatus="${DIALSTATUS}" --number="${CONNECTEDLINE(num)}" --datetime="${STRFTIME(${EPOCH},,%Y-%m-%d %H:%M:%S)}" >/dev/null 2>/dev/null &'));

                //$ext->add($context, $exten, '', new ext_DumpChan('3'));
                $ext->add($context, $exten, '', new ext_return(''));

                //$ext->add($context, $exten, '', new ext_system('php '.$webroot.'/bitrix24/api.php --status "'.$status.'" --dialstatus="${DIALSTATUS}"  --did="Callback" --number="${CALLERID(num)}" --datetime="${STRFTIME(${EPOCH},,%Y-%m-%d %H:%M:%S)}" >/dev/null 2>/dev/null &'));
                //$ext->add($context, $exten, '', new ext_setvar('CHANNEL(hangup_handler_push)', 'bitrix24-finish,s,1'));

            }
    }
}
