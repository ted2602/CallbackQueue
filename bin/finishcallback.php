<?php
/**
 * Copyright (c) 2018.
 * Itach-soft
 * www.itach.by
 * Minsk. Belarus
 */
include '/etc/freepbx.conf';

global $amp_conf;

// --qc_id "'.$qc_queue['qc_id'].'" --id="${CHANNEL(linkedid)}" --number="${QCnumber}" --datetime="${STRFTIME(${EPOCH},,%Y-%m-%d %H:%M:%S)}" >/dev/null 2>/dev/null &'));

$longopts = array(
    "status:",
    "number:",
    "datetime:",
    "qc_call:",
    "dialstatus:",
);

if ($_REQUEST == NULL) {
    $shortopts = "";

    $options = getopt($shortopts, $longopts);
} else {
    $options = $_REQUEST;
}
if (isset($options['dialstatus'])) {
    $dialstatus = $options['dialstatus'];
}
if (isset($options['number'])) {
    $number = $options['number'];
}
if (isset($options['datetime'])) {
    $datetime = $options['datetime'];
}
if (isset($options['qc_call'])) {
    $qc_call = $options['qc_call'];
}

var_export($options);
var_export($dialstatus);
finishcall($dialstatus,$qc_call,$datetime);




 function finishcall($dialstatus,$qc_call,$datetime)
{
    global $db;
    $call=1;
    $query = $db->prepare("UPDATE `qc_calls` SET `status`=:dialstatus, `datetime_out`=:datetime, `call`=:call WHERE `call_id` = :qc_call");

    $query->bindParam(':dialstatus', $dialstatus);
    $query->bindParam(':qc_call', $qc_call);
    $query->bindParam(':datetime', $datetime);
    $query->bindParam(':call', $call);
    $query->execute();
}