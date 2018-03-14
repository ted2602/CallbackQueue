<?php
/**
 * Copyright (c) 2018.
 * Itach-soft
 * www.itach.by
 * Minsk. Belarus
 */

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
                $ext->add($context, $exten, '', new ext_setvar('QCnumber','${CALLERID(num)}'));
                $ext->add($context, $exten, '', new ext_AGI($webroot.'/admin/modules/callbackqueue/bin/qc_agi.php,'.$qc_queue['qc_id'].',${CHANNEL}'));
                //$ext->add($context, $exten, '', new ext_DumpChan('3'));
                $ext->add($context, $exten, '', new ext_gotoif('$["${QCMAX}"!="call"]','defroute'));





                $ext->add($context, $exten, '', new ext_setvar('QCFirst','1'));
                $ext->add($context, $exten, '', new ext_Background($webroot.'/admin/modules/callbackqueue/sounds/press1'));
                $ext->add($context, $exten, '', new ext_DigitTimeout('1'));
                $ext->add($context, $exten, '', new ext_WaitExten(8));

                $ext->add($context, $exten, 'defroute', new ext_goto($qc_queue['qc_callbackdest']));

                $ext->add($context, 1, '', new ext_gotoif('$["${QCFirst}"!="1"]','qc2'));
                $ext->add($context, 1, '', new ext_setvar('CHANNEL(hangup_handler_push)', 'qc-callback-add' . $qc_queue['qc_id'].',s,1'));
                $ext->add($context, 1, '', new ext_setvar('QCFirst','2'));
                $ext->add($context, 1, '', new ext_Background($webroot.'/admin/modules/callbackqueue/sounds/vashnomer'));
                $ext->add($context, 1, '', new ext_SayDigits('${CALLERID(num)}'));
                $ext->add($context, 1, '', new ext_Background($webroot.'/admin/modules/callbackqueue/sounds/pravil-press1-net-press2'));
                $ext->add($context, 1, '', new ext_setvar('QCFirst','2'));
                $ext->add($context, 1, '', new ext_DigitTimeout('1'));
                $ext->add($context, 1, '', new ext_WaitExten(8));
                $ext->add($context, 1, 'qc2', new ext_Background($webroot.'/admin/modules/callbackqueue/sounds/svobodny'));
                //$ext->add($context, 1, 'qc2', new ext_DumpChan('3'));
                $ext->add($context, 1, 'qc2', new ext_hangup());

                $ext->add($context, 2, '', new ext_setvar('QCFirst','2'));
                $ext->add($context, 2, '', new ext_Background($webroot.'/admin/modules/callbackqueue/sounds/primer-nomera'));
                $ext->add($context, 2, '', new ext_DigitTimeout('5'));
                $ext->add($context, 2, '', new ext_WaitExten(8));

                $ext->add($context, '_8XXXXXXXXXX', '', new ext_Background($webroot.'/admin/modules/callbackqueue/sounds/vashnomer'));
                $ext->add($context, '_8XXXXXXXXXX', '', new ext_setvar('QCnumber','${EXTEN}'));
                $ext->add($context, '_8XXXXXXXXXX', '', new ext_SayDigits('${QCnumber}'));
                $ext->add($context, '_8XXXXXXXXXX', '', new ext_Background($webroot.'/admin/modules/callbackqueue/sounds/pravil-press1-net-press2'));
                $ext->add($context, '_8XXXXXXXXXX', '', new ext_setvar('QCFirst','2'));
                $ext->add($context, '_8XXXXXXXXXX', '', new ext_DigitTimeout('1'));
                $ext->add($context, '_8XXXXXXXXXX', '', new ext_WaitExten(8));

                $ext->add($context, 'i', '', new ext_goto('qc-callback-' . $qc_queue['qc_id'].',s,1'));

                $ext->add($context, 't', '', new ext_hangup());

                $context = 'qc-callback-add' . $qc_queue['qc_id'];
                $exten = 's';
                $ext->add($context, 's', '', new ext_system('php '.$webroot.'/admin/modules/callbackqueue/bin/addcallback.php --qc_id="'.$qc_queue['qc_id'].'" --id="${CHANNEL(linkedid)}" --number="${QCnumber}" --datetime="${STRFTIME(${EPOCH},,%Y-%m-%d %H:%M:%S)}" >/dev/null 2>/dev/null &'));
                $ext->add($context, $exten, '', new ext_return(''));


                //Call finish handler
                $context = 'qc-callback-dial';
                $exten = '_X.';


                $ext->add($context, $exten, '', new ext_noop('qc-callback-dial register handler call'));
                $ext->add($context, $exten, '', new ext_setvar('CHANNEL(hangup_handler_push)', 'qc-callback-finish,s,1'));
                $ext->add($context, $exten, '', new ext_goto('from-internal,${EXTEN},1'));

                //$ext->add($context, $exten, '', new ext_system('php '.$webroot.'/bitrix24/api.php --status "'.$status.'" --id="${CHANNEL(linkedid)}"  --did="Callback" --number="${CALLERID(num)}" --datetime="${STRFTIME(${EPOCH},,%Y-%m-%d %H:%M:%S)}" >/dev/null 2>/dev/null &'));
                //$ext->add($context, $exten, '', new ext_setvar('CHANNEL(hangup_handler_push)', 'bitrix24-finish,s,1'));

                $context = 'qc-callback-finish';
                $exten = 's';
                $status='finish';

                $ext->add($context, $exten, '', new ext_noop('Finish call DIALSTATUS ${DIALSTATUS} linkedid ${CHANNEL(linkedid)} qc_call ${QCALL} CALLERID(NUM) ${CALLERID(num)} CONNECTEDLINE(num) ${CONNECTEDLINE(num)} Exten ${EXTEN}'));
                $ext->add($context, $exten, '', new ext_system('php '.$webroot.'/admin/modules/callbackqueue/bin/finishcallback.php --status "'.$status.'" --qc_call="${QCALL}" --dialstatus="${DIALSTATUS}" --number="${CONNECTEDLINE(num)}" --datetime="${STRFTIME(${EPOCH},,%Y-%m-%d %H:%M:%S)}" >/dev/null 2>/dev/null &'));

                //$ext->add($context, $exten, '', new ext_DumpChan('3'));
                $ext->add($context, $exten, '', new ext_return(''));

                //$ext->add($context, $exten, '', new ext_system('php '.$webroot.'/bitrix24/api.php --status "'.$status.'" --dialstatus="${DIALSTATUS}"  --did="Callback" --number="${CALLERID(num)}" --datetime="${STRFTIME(${EPOCH},,%Y-%m-%d %H:%M:%S)}" >/dev/null 2>/dev/null &'));
                //$ext->add($context, $exten, '', new ext_setvar('CHANNEL(hangup_handler_push)', 'bitrix24-finish,s,1'));

            }
    }
}
